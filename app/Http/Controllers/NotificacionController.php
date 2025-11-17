<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class NotificacionController extends Controller
{
    /** ========================
     *  LISTAR
     *  ======================== */
    public function index()
{
    $notificaciones = DB::table('notificaciones')->get();

    // traer solo usuarios con correo válido
    $usuarios = DB::table('usuarios')
        ->whereNotNull('correo')
        ->where('correo', '!=', '')
        ->get();

    return view('empleado.notificaciones', compact('notificaciones', 'usuarios'));
}


    /** ========================
     *  REGISTRAR
     *  ======================== */
    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|string|max:50',
            'mensaje' => 'required|string',
            'fecha_envio' => 'required|string|max:20',
            'estado' => 'required|in:A,I'
        ]);

        Notificacion::create([
            'id_usuario' => auth()->id(),
            'tipo' => $request->tipo,
            'mensaje' => $request->mensaje,
            'fecha_envio' => $request->fecha_envio,
            'estado' => $request->estado,
            'usuario_creacion' => auth()->id(),
            'fecha_creacion' => now(),
        ]);

        return redirect()->back()->with('success', 'Notificación registrada correctamente.');
    }

    /** ========================
     *  ACTUALIZAR
     *  ======================== */
    public function update(Request $request)
    {
        $request->validate([
            'id_notificacion' => 'required|integer',
            'tipo' => 'required|string|max:50',
            'mensaje' => 'required|string',
            'fecha_envio' => 'required|string|max:20',
            'estado' => 'required|in:A,I'
        ]);

        $n = Notificacion::findOrFail($request->id_notificacion);

        $n->update([
            'tipo' => $request->tipo,
            'mensaje' => $request->mensaje,
            'fecha_envio' => $request->fecha_envio,
            'estado' => $request->estado,
            'usuario_actualizacion' => auth()->id(),
            'fecha_actualizacion' => now(),
        ]);

        return redirect()->back()->with('success', 'Notificación actualizada correctamente.');
    }

    /** ========================
     *  CAMBIAR ESTADO (AJAX)
     *  ======================== */
    public function cambiarEstado(Request $request)
    {
        $n = Notificacion::findOrFail($request->id);

        $n->estado = $n->estado === 'A' ? 'I' : 'A';
        $n->usuario_actualizacion = auth()->id();
        $n->fecha_actualizacion = now();
        $n->save();

        return response()->json(['ok' => true, 'estado' => $n->estado]);
    }

    /** ========================
     *  PROBAR NOTIFICACIÓN
     *  ======================== */
    public function probar($id)
    {
        $n = Notificacion::findOrFail($id);

        $correo = auth()->user()->email;

        try {
            Mail::raw($n->mensaje, function ($m) use ($correo, $n) {
                $m->to($correo)
                  ->subject("Prueba de notificación: " . $n->tipo);
            });

            return back()->with('success', 'Se envió la notificación al correo: ' . $correo);

        } catch (\Exception $e) {
            return back()->with('error', 'Error enviando correo: ' . $e->getMessage());
        }
    }
    
public function ejecutar($id)
{
    $notificacion = Notificacion::findOrFail($id);

    $comando = "notificaciones:" . $notificacion->tipo;

    try {
        Artisan::call($comando);

        return back()->with("success", "Comando ejecutado: $comando");
    } catch (\Exception $e) {
        return back()->with("error", "Error al ejecutar $comando: " . $e->getMessage());
    }
}
public function enviarCorreoPersonalizado(Request $request)
{
    $request->validate([
        'usuarios' => 'required|array',
        'asunto'   => 'required|string',
        'mensaje'  => 'required|string',
    ]);

    foreach ($request->usuarios as $correo) {
        try {
            \Mail::send('emails.personalizado', [
                'mensaje' => $request->mensaje
            ], function ($m) use ($correo, $request) {
                $m->to($correo)
                  ->subject($request->asunto);
            });

        } catch (\Exception $e) {
            return back()->with('error', "Error enviando a $correo: " . $e->getMessage());
        }
    }

    return back()->with('success', '¡Correos enviados correctamente!');
}

}

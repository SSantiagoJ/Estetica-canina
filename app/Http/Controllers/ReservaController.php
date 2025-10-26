<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use App\Models\Mascota;
use App\Models\Cliente;
use App\Models\Reserva;
use App\Models\DetalleReserva;
use App\Models\Servicio;
use App\Models\Pago; // modelo del pago
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReservaController extends Controller
{
    // 1. Selección de Mascota
    public function seleccionMascota()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para continuar.');
        }

        $cliente = Cliente::where('id_persona', Auth::user()->id_persona)->first();

        if (!$cliente) {
            return redirect()->back()->with('error', 'No se encontró cliente asociado.');
        }

        $mascotas = Mascota::where('id_cliente', $cliente->id_cliente)->get();

        return view('reservas.seleccion-mascota', compact('mascotas'));
    }

    // 2. Selección de Servicios
    public function seleccionServicio(Request $request)
        {
            $mascotasSeleccionadas = $request->input('mascotas', []);

               if (empty($mascotasSeleccionadas)) {
                return redirect()->route('reservas.seleccionMascota')
                    ->with('error', 'Debes seleccionar al menos una mascota.');
            }

            // Guardamos mascotas + detalles en sesión
            session([
                'mascotas_seleccionadas' => $mascotasSeleccionadas,
                'fecha'                  => $request->input('fecha'),
                'hora'                   => $request->input('hora'),
                'enfermedad'             => $request->has('enfermedad') ? 1 : 0,
                'vacuna'                 => $request->has('vacuna') ? 1 : 0,
                'alergia'                => $request->has('alergia') ? 1 : 0,
                'descripcion_alergia'    => $request->input('descripcion_alergia')
            ]);

            // Consultamos los servicios
            $servicios = \App\Models\Servicio::where('estado', 'A')
                ->where('tipo_servicio', '!=', 'Adicional')
                ->get();

            $adicionales = \App\Models\Servicio::where('estado', 'A')
                ->where('tipo_servicio', 'Adicional')
                ->get();

            return view('reservas.seleccion-servicio', compact('servicios', 'adicionales'));
}



    // 3. Pago
    public function pago(Request $request)
        {
            // Guardamos TODO lo que viene del form de servicios
            session([
                'servicios_seleccionados'   => $request->input('servicios', []),
                'adicionales_seleccionados' => $request->input('adicionales', []),
                'fecha'                     => $request->input('fecha'),
                'hora'                      => $request->input('hora'),
                'enfermedad'                => $request->has('enfermedad') ? 1 : 0,
                'vacuna'                    => $request->has('vacuna') ? 1 : 0,
                'alergia'                   => $request->has('alergia') ? 1 : 0,
                'descripcion_alergia'       => $request->input('descripcion_alergia')
            ]);

            // Mascotas seleccionadas
            $mascotasIds = session('mascotas_seleccionadas', []);
            $mascotas = Mascota::whereIn('id_mascota', $mascotasIds)->get();

            // Servicios seleccionados
            $serviciosIds = session('servicios_seleccionados', []);
            $servicios = Servicio::whereIn('id_servicio', $serviciosIds)->get();

            // Adicionales seleccionados
            $adicionalesIds = session('adicionales_seleccionados', []);
            $adicionales = Servicio::whereIn('id_servicio', $adicionalesIds)->get();

            return view('reservas.pago', compact('mascotas', 'servicios', 'adicionales'));
        }


    // 4. Finalizar Reserva
    public function finalizar(Request $request)
    {
        $metodo = $request->input('metodo_pago');

        // Insertar en tabla reservas
        $reserva = new Reserva();
        $reserva->id_mascota = session('mascotas_seleccionadas')[0]; // primera mascota
        $cliente = Cliente::where('id_persona', Auth::user()->id_persona)->first();
        if (!$cliente) {
            return redirect()->back()->with('error', 'No se encontró cliente asociado.');
        }
        $reserva->id_cliente = $cliente->id_cliente;
        $reserva->id_usuario = Auth::id();
        $reserva->fecha = session('fecha');
        $reserva->hora = session('hora');
        $reserva->enfermedad = session('enfermedad', 0);
        $reserva->vacuna = session('vacuna', 0);
        $reserva->alergia = session('alergia', 0);
        $reserva->descripcion_alergia = session('descripcion_alergia', null);
        $reserva->estado = ($metodo === 'tarjeta') ? 'P' : 'N'; // P = Pagado, N = Pendiente
        $reserva->usuario_creacion = Auth::user()->correo;
        $reserva->save();

        // Insertar detalles de servicios
        foreach (session('servicios_seleccionados', []) as $idServicio) {
            $servicio = Servicio::find($idServicio);
            if ($servicio) {
                DetalleReserva::create([
                    'id_reserva' => $reserva->id_reserva,
                    'id_servicio' => $idServicio,
                    'precio_unitario' => $servicio->costo,
                    'igv' => $servicio->costo * 0.18,
                    'total' => $servicio->costo * 1.18,
                    'estado' => 'A',
                    'usuario_creacion' => Auth::user()->correo,
                ]);
            }
        }

        // Insertar detalles de adicionales
        foreach (session('adicionales_seleccionados', []) as $idServicio) {
            $servicio = Servicio::find($idServicio);
            if ($servicio) {
                DetalleReserva::create([
                    'id_reserva' => $reserva->id_reserva,
                    'id_servicio' => $idServicio,
                    'precio_unitario' => $servicio->costo,
                    'igv' => $servicio->costo * 0.18,
                    'total' => $servicio->costo * 1.18,
                    'estado' => 'A',
                    'usuario_creacion' => Auth::user()->correo,
                ]);
            }
        }

        // Dependiendo del método de pago mostramos confirmación o pendiente
        if ($metodo === 'tarjeta') {
            return view('reservas.completada');
        } else {
            return view('reservas.pendiente');
        }
    }
    public function resumenPago()
{
    $mascotas = Mascota::whereIn('id_mascota', session('mascotas_seleccionadas', []))->get();
    $servicios = Servicio::whereIn('id_servicio', session('servicios_seleccionados', []))->get();
    $adicionales = Servicio::whereIn('id_servicio', session('adicionales_seleccionados', []))->get();

    return view('reservas.pago_resumen', compact('mascotas', 'servicios', 'adicionales'));
}
public function guardarPago()
{
    // 1) Trae la última reserva del usuario
    $reserva = \App\Models\Reserva::where('id_usuario', auth()->id())->latest('id_reserva')->first();
    if (!$reserva) {
        return redirect()->route('reservas.pago')->with('error', 'No se encontró la reserva.');
    }

    // 2) Calcula el total REAL (usa el campo 'total' del detalle si lo tienes)
    //    Si aún no guardas IGV/total en detalle, usa sum('precio_unitario')
    $total = $reserva->detalles->sum('total'); 
    if ($total == 0) {
        $total = $reserva->detalles->sum('precio_unitario') * 1.18; // fallback
    }

    // 3) Genera serie
    $serie = 'BOL-' . str_pad(Pago::count() + 1, 5, '0', STR_PAD_LEFT);

    // 4) Crea el pago
    $pago = Pago::create([
        'id_reserva'        => $reserva->id_reserva,
        'monto'             => $total,           // 💡 Ya no 50%
        'metodo_pago'       => 'paypal',
        'fecha'             => Carbon::now()->toDateString(),
        'hora'              => Carbon::now()->toTimeString(),
        'estado'            => 'P',
        'usuario_creacion'  => auth()->user()->correo,
        'series'            => $serie,
    ]);

    // 5) Genera PDF y guarda en storage/app/public/boletas
    $pdf = Pdf::loadView('reservas.boleta', [
        'reserva'   => $reserva,
        'pago'      => $pago,
        'cliente'   => $reserva->cliente,
        'servicios' => $reserva->detalles,
    ])->setPaper('A4', 'portrait');

    $dir = storage_path('app/public/boletas');
    if (!File::exists($dir)) {
        File::makeDirectory($dir, 0777, true);
    }
    $path = $dir . DIRECTORY_SEPARATOR . $pago->series . '.pdf';
    $pdf->save($path);

    // 6) Muestra la vista "completada" PASANDO $pago (para evitar "Undefined variable $pago")
    return view('reservas.completada', compact('pago'));
}
public function generarBoleta($id_pago)
{
    $pago = Pago::findOrFail($id_pago);
    $reserva = $pago->reserva;
    $cliente = $reserva->cliente;
    $servicios = $reserva->detalles;

    $pdf = Pdf::loadView('reservas.boleta', compact('pago', 'reserva', 'cliente', 'servicios'))
              ->setPaper('A4', 'portrait');

    // 📂 Ruta personalizada (no storage)
    $directory = base_path('app/public/Boletas');

    // 🔒 Crear la carpeta si no existe
    if (!File::exists($directory)) {
        File::makeDirectory($directory, 0777, true);
    }

    // 📄 Nombre del archivo
    $fileName = 'BOL-' . str_pad($pago->id_pago, 5, '0', STR_PAD_LEFT) . '.pdf';

    // 🧭 Ruta completa personalizada
    $path = $directory . DIRECTORY_SEPARATOR . $fileName;

    // 💾 Guarda directamente en app/public/Boletas/
    $pdf->save($path);

    // ✅ Muestra el PDF en navegador
    return response()->file($path);
}

}

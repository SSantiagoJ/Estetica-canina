<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Turno;
use App\Models\Reserva;
use App\Models\DetalleReserva;
use App\Models\Mascota;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Novedad;
use App\Models\Atencion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
 /**
     * Guarda la atención de una reserva con descripción y comentarios
     */
    public function guardarAtencion(Request $request)
    {
        // Validar los datos
        $request->validate([
            'id_reserva' => 'required|exists:reservas,id_reserva',
            'descripcion' => 'required|max:500',
            'comentarios' => 'nullable|max:500',
        ]);

        try {
            // Crear registro en tabla atenciones
            Atencion::create([
                'id_reserva' => $request->id_reserva,
                'descripcion' => $request->descripcion,
                'comentarios' => $request->comentarios,
                'usuario_creacion' => Auth::user()->usuario ?? 'sistema',
                'fecha_creacion' => now(),
            ]);

            // Actualizar estado de la reserva a "Atendido" (A)
            $reserva = Reserva::findOrFail($request->id_reserva);
            $reserva->update([
                'estado' => 'A',
                'usuario_actualizacion' => Auth::user()->usuario ?? 'sistema',
            ]);

            return redirect()->route('empleado.bandeja.reservas')
                ->with('success', 'Atención registrada correctamente y reserva marcada como atendida');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al guardar la atención: ' . $e->getMessage());
        }
    }


    /**
     * Muestra el panel del día con reservas asignadas y dashboards
     */
    public function panelDelDia()
    {
        $empleadoActual = Auth::user()->empleado ?? null;
        $fechaHoy = now()->format('Y-m-d');
        
        // Obtener reservas del día asignadas al empleado actual
        $reservasDelDia = collect();
        if ($empleadoActual) {
            $reservasDelDia = Reserva::with([
                'mascota',
                'cliente.persona',
                'detalles.servicio'
            ])
            ->where('fecha', $fechaHoy)
            ->where('id_empleado', $empleadoActual->id_empleado)
            ->orderBy('hora', 'asc')
            ->get();
        }
        
        // Estadísticas para dashboards
        $stats = [
            'reservas_pendientes' => $reservasDelDia->where('estado', 'P')->count(),
            'reservas_atendidas' => $reservasDelDia->where('estado', 'A')->count(),
            'total_reservas' => $reservasDelDia->count(),
            'proxima_reserva' => $reservasDelDia->where('estado', 'P')->first(),
        ];
        
        // Estadísticas generales para dashboards adicionales
        $statsGenerales = [
            'reservas_mes' => Reserva::whereMonth('fecha', now()->month)
                               ->whereYear('fecha', now()->year)
                               ->where('id_empleado', $empleadoActual?->id_empleado ?? 0)
                               ->count(),
            'servicios_populares' => DetalleReserva::join('servicios', 'detalles_reservas.id_servicio', '=', 'servicios.id_servicio')
                                      ->join('reservas', 'detalles_reservas.id_reserva', '=', 'reservas.id_reserva')
                                      ->where('reservas.id_empleado', $empleadoActual?->id_empleado ?? 0)
                                      ->selectRaw('servicios.nombre_servicio, COUNT(*) as total')
                                      ->groupBy('servicios.id_servicio', 'servicios.nombre_servicio')
                                      ->orderByDesc('total')
                                      ->limit(3)
                                      ->get(),
            'clientes_atendidos' => Reserva::where('id_empleado', $empleadoActual?->id_empleado ?? 0)
                                     ->where('estado', 'A')
                                     ->distinct('id_cliente')
                                     ->count('id_cliente')
        ];
        
        // Obtener comentarios de 5 estrellas que el empleado recibió
        $comentarios5Estrellas = collect();
        if ($empleadoActual) {
            $comentarios5Estrellas = DB::table('feedbacks')
                ->join('reservas', 'feedbacks.id_reserva', '=', 'reservas.id_reserva')
                ->join('mascotas', 'reservas.id_mascota', '=', 'mascotas.id_mascota')
                ->join('clientes', 'reservas.id_cliente', '=', 'clientes.id_cliente')
                ->join('personas', 'clientes.id_persona', '=', 'personas.id_persona')
                ->where('reservas.id_empleado', $empleadoActual->id_empleado)
                ->where('feedbacks.calificacion', 5)
                ->whereNotNull('feedbacks.comentarios')
                ->where('feedbacks.comentarios', '!=', '')
                ->select(
                    'feedbacks.comentarios',
                    'feedbacks.calificacion',
                    'personas.nombres',
                    'mascotas.nombre as mascota_nombre',
                    'feedbacks.fecha_creacion',
                    'reservas.fecha as fecha_servicio'
                )
                ->orderBy('feedbacks.fecha_creacion', 'desc')
                ->limit(5)
                ->get();
        }
        
        return view('empleado.panel-del-dia', compact('reservasDelDia', 'stats', 'statsGenerales', 'fechaHoy', 'empleadoActual', 'comentarios5Estrellas'));
    }

     /**
     * Muestra la bandeja de reservas
     */
    public function bandejaReservas()
    {
        // Obtener todas las reservas con sus relaciones
        $reservas = Reserva::with([
            'mascota',
            'cliente.persona',
            'empleado.persona',
            'detalles.servicio',
        ])
        ->orderBy('fecha', 'desc')
        ->orderBy('hora', 'desc')
        ->get();

        $empleados = Empleado::with('persona')
            ->get();
        
        return view('empleado.bandeja-reservas', compact('reservas', 'empleados'));
    }

    /**
     * Ver detalles de una reserva
     */
    public function verReserva($id)
    {
        $reserva = Reserva::with([
            'mascota',
            'cliente.persona',
            'empleado.persona',
            'detalles.servicio'
        ])->findOrFail($id);
        
        return view('empleado.ver-reserva', compact('reserva'));
    }

    /**
     * Atender una reserva (cambiar estado a Atendido)
     */
    public function atenderReserva($id)
    {
        $reserva = Reserva::findOrFail($id);
        
        // Cambiar estado a Atendido (A)
        $reserva->update([
            'estado' => 'A',
            'usuario_actualizacion' => Auth::user()->usuario ?? 'sistema',
        ]);
        
        return redirect()->route('empleado.bandeja.reservas')
            ->with('success', 'Reserva atendida correctamente');
    }
    /**
     * Muestra la vista de gestionar turnos
     */
    public function gestionarTurnos()
    {
        // Obtener todos los turnos con la información del empleado y persona
        $turnos = Turno::with('empleado.persona')
            ->orderBy('fecha', 'desc')
            ->get();
        
        // Obtener lista de empleados para los filtros y el modal
        $empleados = Empleado::with('persona')->get();
        
        // Obtener lista de servicios para los filtros
        //$servicios = Servicio::where('estado', 'A')->get();
        
        return view('empleado.gestionar-turnos', compact('turnos', 'empleados'));
    }
    

    /**
     * Guarda un nuevo turno
     */
    public function storeTurno(Request $request)
    {
        // Validar los datos que vienen del formulario
        $request->validate([
            'id_empleado' => 'required|exists:empleados,id_empleado',
            'fecha' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'estado' => 'required|in:D,O', // D=DISPONIBLE, O=OCUPADO
        ]);

        // Crear el nuevo turno
        Turno::create([
            'id_empleado' => $request->id_empleado,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'estado' => $request->estado,
            'usuario_creacion' => Auth::user()->usuario ?? 'sistema',
        ]);

        return redirect()->route('empleado.gestionar.turnos')
            ->with('success', 'Turno registrado correctamente');
    }

    /**
     * Actualiza un turno existente
     */
    public function updateTurno(Request $request, $id)
    {
        // Validar los datos
        $request->validate([
            'id_empleado' => 'required|exists:empleados,id_empleado',
            'fecha' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'estado' => 'required|in:D,O',
        ]);

        // Buscar el turno y actualizarlo
        $turno = Turno::findOrFail($id);
        $turno->update([
            'id_empleado' => $request->id_empleado,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'estado' => $request->estado,
            'usuario_actualizacion' => Auth::user()->usuario ?? 'sistema',
        ]);

        return redirect()->route('empleado.gestionar.turnos')
            ->with('success', 'Turno actualizado correctamente');
    }

    /**
     * Elimina un turno
     */
    public function destroyTurno($id)
    {
        $turno = Turno::findOrFail($id);
        $turno->delete();

        return redirect()->route('empleado.gestionar.turnos')
            ->with('success', 'Turno eliminado correctamente');
    }

    /**
 * Muestra la vista de gestionar novedades
 */
public function gestionarNovedades()
{
    // Obtener todas las novedades ordenadas por fecha de publicación
    $novedades = Novedad::orderBy('fecha_publicacion', 'desc')->get();
    
    return view('empleado.gestionar-novedades', compact('novedades'));
}

/**
 * Guarda una nueva novedad
 */
public function storeNovedad(Request $request)
{
    // Validar los datos
    $request->validate([
        'titulo' => 'required|max:200',
        'resumen' => 'required|max:500',
        'descripcion' => 'required|max:500',
        'categoria' => 'required|max:50',
        'fecha_publicacion' => 'required|date',
        'estado' => 'required|in:A,I,B,P', // A=Activo, I=Inactivo, B=Borrador, P=Publicado
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Procesar la imagen si existe
    $imagenPath = null;
    if ($request->hasFile('imagen')) {
        $imagen = $request->file('imagen');
        $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
        $imagen->move(public_path('images/novedades'), $nombreImagen);
        $imagenPath = 'images/novedades/' . $nombreImagen;
    }

    // Crear la novedad
    Novedad::create([
        'titulo' => $request->titulo,
        'resumen' => $request->resumen,
        'descripcion' => $request->descripcion,
        'categoria' => $request->categoria,
        'imagen' => $imagenPath,
        'fecha_publicacion' => $request->fecha_publicacion,
        'estado' => $request->estado,
        'usuario_creacion' => Auth::user()->usuario ?? 'sistema',
        'usuario_actualizacion' => Auth::user()->usuario ?? 'sistema',
    ]);

    return redirect()->route('empleado.gestionar.novedades')
        ->with('success', 'Novedad registrada correctamente');
}

/**
 * Actualiza una novedad existente
 */
public function updateNovedad(Request $request, $id)
{
    // Validar los datos
    $request->validate([
        'titulo' => 'required|max:200',
        'resumen' => 'required|max:500',
        'descripcion' => 'required|max:500',
        'categoria' => 'required|max:50',
        'fecha_publicacion' => 'required|date',
        'estado' => 'required|in:A,I,B,P',
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Buscar la novedad
    $novedad = Novedad::findOrFail($id);

    // Procesar la imagen si existe
    if ($request->hasFile('imagen')) {
        // Eliminar imagen anterior si existe
        if ($novedad->imagen && file_exists(public_path($novedad->imagen))) {
            unlink(public_path($novedad->imagen));
        }
        
        $imagen = $request->file('imagen');
        $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
        $imagen->move(public_path('images/novedades'), $nombreImagen);
        $imagenPath = 'images/novedades/' . $nombreImagen;
    } else {
        $imagenPath = $novedad->imagen; // Mantener la imagen actual
    }

    // Actualizar la novedad
    $novedad->update([
        'titulo' => $request->titulo,
        'resumen' => $request->resumen,
        'descripcion' => $request->descripcion,
        'categoria' => $request->categoria,
        'imagen' => $imagenPath,
        'fecha_publicacion' => $request->fecha_publicacion,
        'estado' => $request->estado,
        'usuario_actualizacion' => Auth::user()->usuario ?? 'sistema',
    ]);

    return redirect()->route('empleado.gestionar.novedades')
        ->with('success', 'Novedad actualizada correctamente');
}

/**
 * Elimina una novedad
 */
public function destroyNovedad($id)
{
    $novedad = Novedad::findOrFail($id);
    
    // Eliminar imagen si existe
    if ($novedad->imagen && file_exists(public_path($novedad->imagen))) {
        unlink(public_path($novedad->imagen));
    }
    
    $novedad->delete();

    return redirect()->route('empleado.gestionar.novedades')
        ->with('success', 'Novedad eliminada correctamente');
}
}
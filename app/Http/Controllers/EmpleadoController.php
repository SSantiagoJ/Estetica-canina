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
use Illuminate\Support\Facades\Auth;

class EmpleadoController extends Controller
{
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
            'detalles.servicio'
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
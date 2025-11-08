<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CalificacionController extends Controller
{
    // Guardar calificación
    public function guardarCalificacion(Request $request)
    {
        $request->validate([
            'id_reserva' => 'required|exists:reservas,id_reserva',
            'calificacion' => 'required|integer|min:1|max:5',
            'comentarios' => 'nullable|string|max:500'
        ]);
        
        $usuario = Auth::user();
        
        // Verificar si ya existe una calificación para esta reserva
        $existente = DB::table('feedbacks')
            ->where('id_reserva', $request->id_reserva)
            ->first();
        
        if ($existente) {
            return response()->json([
                'success' => false,
                'message' => 'Esta reserva ya ha sido calificada'
            ], 400);
        }
        
        // Guardar la calificación
        DB::table('feedbacks')->insert([
            'id_reserva' => $request->id_reserva,
            'calificacion' => $request->calificacion,
            'comentarios' => $request->comentarios,
            'usuario_creacion' => $usuario->correo,
            'fecha_creacion' => now(),
            'usuario_actualizacion' => $usuario->correo,
            'fecha_actualizacion' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Calificación guardada correctamente'
        ]);
    }
    
    // Obtener calificaciones de 5 estrellas para mostrar en menú
    public function calificacionesDestacadas()
    {
        $calificaciones = DB::table('feedbacks')
            ->join('reservas', 'feedbacks.id_reserva', '=', 'reservas.id_reserva')
            ->join('mascotas', 'reservas.id_mascota', '=', 'mascotas.id_mascota')
            ->join('clientes', 'reservas.id_cliente', '=', 'clientes.id_cliente')
            ->join('personas', 'clientes.id_persona', '=', 'personas.id_persona')
            ->where('feedbacks.calificacion', 5)
            ->select(
                'feedbacks.comentarios',
                'personas.nombres',
                'mascotas.nombre as mascota_nombre',
                'feedbacks.fecha_creacion'
            )
            ->orderBy('feedbacks.fecha_creacion', 'desc')
            ->limit(5)
            ->get();
        
        return $calificaciones;
    }
}

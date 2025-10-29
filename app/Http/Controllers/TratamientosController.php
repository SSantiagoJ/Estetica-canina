<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TratamientosController extends Controller
{
    // Mostrar lista de tratamientos comprados por el cliente
    public function misTratamientos()
    {
        $usuario = Auth::user();
        
        // Obtener el cliente asociado al usuario
        $cliente = DB::table('clientes')
            ->where('id_persona', $usuario->id_persona)
            ->first();
        
        if (!$cliente) {
            return redirect()->back()->with('error', 'No se encontró información del cliente');
        }
        
        // Obtener todas las reservas del cliente con sus servicios
        $tratamientos = DB::table('reservas')
            ->join('mascotas', 'reservas.id_mascota', '=', 'mascotas.id_mascota')
            ->join('detalles_reservas', 'reservas.id_reserva', '=', 'detalles_reservas.id_reserva')
            ->join('servicios', 'detalles_reservas.id_servicio', '=', 'servicios.id_servicio')
            ->where('reservas.id_cliente', $cliente->id_cliente)
            ->select(
                'reservas.id_reserva',
                'reservas.fecha',
                'reservas.hora',
                'mascotas.nombre as mascota_nombre',
                'servicios.nombre_servicio',
                'servicios.categoria',
                'detalles_reservas.precio_unitario',
                'detalles_reservas.total',
                'reservas.estado'
            )
            ->orderBy('reservas.fecha', 'desc')
            ->get();
        
        return view('cliente.mis-tratamientos', compact('tratamientos'));
    }
}

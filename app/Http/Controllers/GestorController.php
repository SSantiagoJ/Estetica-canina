<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Usuario;
use App\Models\Mascota;
use App\Models\Servicio;

class GestorController extends Controller
{
    // Dashboard Admin
    public function index()
    {
        $reservas = Reserva::with(['mascota', 'cliente.persona', 'detalles.servicio'])->get();
        return view('admin_dashboard', compact('reservas'));
    }


    // Gestor de Usuarios
    public function usuarios()
    {
        $usuarios = Usuario::all();
        return view('admin_usuarios', compact('usuarios'));
    }

    // Gestor de Mascotas
    public function mascotas()
    {
        $mascotas = Mascota::with('cliente')->get();
        return view('admin_mascotas', compact('mascotas'));
    }

    // Gestor de Reservas
    public function reservas()
    {
      $reservas = Reserva::with(['mascota', 'cliente.persona', 'servicios'])->get();



        return view('admin_reservas', compact('reservas'));
    }

    // Gestor de Servicios
    public function servicios()
    {
        $servicios = Servicio::orderBy('categoria')
            ->orderBy('nombre_servicio')
            ->get()
            ->groupBy('categoria');
        
        return view('admin_servicios', compact('servicios'));
    }
      public function update(Request $request)
{
    try {
        $updates = $request->input('updates', []);
        \Log::info('Datos recibidos en update:', $updates);

        foreach ($updates as $upd) {
            $reserva = Reserva::find($upd['id']);
            if ($reserva) {
                $data = $upd['data'];

                if (isset($data['fecha'])) {
                    $reserva->fecha = $data['fecha'];
                }
                if (isset($data['hora'])) {
                    $reserva->hora = $data['hora'];
                }
                if (isset($data['estado'])) {
                    $reserva->estado = $data['estado'];
                }

                if (isset($data['mascota']) && $reserva->mascota) {
                        $reserva->mascota->nombre = $data['mascota'];
                        $reserva->mascota->save();
                    }


                $reserva->save();
            }
        }

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        \Log::error('Error en update reservas: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

    
}

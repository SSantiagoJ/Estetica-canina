<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mascota;
use App\Models\Cliente;
use App\Models\Reserva;
use App\Models\DetalleReserva;
use App\Models\Servicio;

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
}

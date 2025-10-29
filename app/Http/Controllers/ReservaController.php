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

//BELEN

/**
 * Muestra la vista de "Mis Reservas" con próximas reservas e historial
 */
public function misReservas()
{
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus reservas.');
    }

    $cliente = Cliente::where('id_persona', Auth::user()->id_persona)->first();

    if (!$cliente) {
        return redirect()->back()->with('error', 'No se encontró cliente asociado.');
    }

    $hoy = Carbon::now()->toDateString();

    // Próximas reservas (fecha >= hoy)
    $proximasReservas = Reserva::with(['mascota', 'detalles.servicio'])
        ->where('id_cliente', $cliente->id_cliente)
        ->where('fecha', '>=', $hoy)
        ->whereIn('estado', ['P', 'N']) // Pagado o Pendiente
        ->orderBy('fecha', 'asc')
        ->orderBy('hora', 'asc')
        ->get()
        ->map(function($reserva) {
            $reserva->fecha_formateada = $this->formatearFecha($reserva->fecha);
            $reserva->servicios_texto = $this->formatearServicios($reserva->detalles);
            return $reserva;
        });

    // Historial (fecha < hoy o estado completado)
    $historialReservas = Reserva::with(['mascota', 'detalles.servicio'])
        ->where('id_cliente', $cliente->id_cliente)
        ->where(function($query) use ($hoy) {
            $query->where('fecha', '<', $hoy)
                  ->orWhere('estado', 'C'); // Completado
        })
        ->orderBy('fecha', 'desc')
        ->orderBy('hora', 'desc')
        ->get()
        ->map(function($reserva) {
            $reserva->fecha_formateada = $this->formatearFecha($reserva->fecha);
            $reserva->servicios_texto = $this->formatearServicios($reserva->detalles);
            return $reserva;
        });

    return view('reservas.mis-reservas', compact('proximasReservas', 'historialReservas'));
}

/**
 * Formatea la fecha en español (ej: "Miércoles 15 de Octubre del 2025")
 */
private function formatearFecha($fecha)
{
    $carbon = Carbon::parse($fecha);
    $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
              'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    
    return $dias[$carbon->dayOfWeek] . ' ' . $carbon->day . ' de ' . $meses[$carbon->month - 1] . ' del ' . $carbon->year;
}

/**
 * Formatea los servicios de una reserva (ej: "Baño Básico + Corte + Limado de Uñas")
 */
private function formatearServicios($detalles)
{
    if ($detalles->isEmpty()) {
        return 'Sin servicios';
    }
    
    $servicios = $detalles->map(function($detalle) {
        return $detalle->servicio->nombre_servicio ?? 'Servicio';
    })->toArray();
    
    return implode(' + ', $servicios);
}

/**
 * Muestra el detalle de una reserva específica
 */
public function show($id)
{
    $reserva = Reserva::with(['mascota', 'detalles.servicio', 'cliente'])
        ->findOrFail($id);

    // Verificar que la reserva pertenece al usuario actual
    $cliente = Cliente::where('id_persona', Auth::user()->id_persona)->first();
    
    if ($reserva->id_cliente !== $cliente->id_cliente) {
        return redirect()->route('reservas.mis-reservas')
            ->with('error', 'No tienes permiso para ver esta reserva.');
    }

    $reserva->fecha_formateada = $this->formatearFecha($reserva->fecha);
    $reserva->servicios_texto = $this->formatearServicios($reserva->detalles);

    return view('reservas.detalle', compact('reserva'));
}

/**
 * Muestra el formulario de edición de una reserva
 */
public function edit($id)
{
    $reserva = Reserva::with(['mascota', 'detalles.servicio'])
        ->findOrFail($id);

    // Verificar que la reserva pertenece al usuario actual
    $cliente = Cliente::where('id_persona', Auth::user()->id_persona)->first();
    
    if ($reserva->id_cliente !== $cliente->id_cliente) {
        return redirect()->route('reservas.mis-reservas')
            ->with('error', 'No tienes permiso para editar esta reserva.');
    }

    // Solo permitir editar reservas futuras
    if ($reserva->fecha < Carbon::now()->toDateString()) {
        return redirect()->route('reservas.mis-reservas')
            ->with('error', 'No puedes editar reservas pasadas.');
    }

    $servicios = Servicio::where('estado', 'A')
        ->where('tipo_servicio', '!=', 'Adicional')
        ->get();

    $adicionales = Servicio::where('estado', 'A')
        ->where('tipo_servicio', 'Adicional')
        ->get();

    return view('reservas.editar', compact('reserva', 'servicios', 'adicionales'));
}

/**
 * Actualiza una reserva existente
 */
public function update(Request $request, $id)
{
    $reserva = Reserva::findOrFail($id);

    // Verificar que la reserva pertenece al usuario actual
    $cliente = Cliente::where('id_persona', Auth::user()->id_persona)->first();
    
    if ($reserva->id_cliente !== $cliente->id_cliente) {
        return redirect()->route('reservas.mis-reservas')
            ->with('error', 'No tienes permiso para editar esta reserva.');
    }

    // Solo permitir editar reservas futuras
    if ($reserva->fecha < Carbon::now()->toDateString()) {
        return redirect()->route('reservas.mis-reservas')
            ->with('error', 'No puedes editar reservas pasadas.');
    }

    // Actualizar los datos de la reserva
    $reserva->enfermedad = $request->has('enfermedad') ? $request->input('enfermedad') : 0;
    $reserva->vacuna = $request->has('vacuna') ? $request->input('vacuna') : 0;
    $reserva->alergia = $request->has('alergia') ? $request->input('alergia') : 0;
    $reserva->descripcion_alergia = $request->input('descripcion_alergia');
    
    // Solo agregar usuario_actualizacion si existe en la tabla
    if (\Schema::hasColumn('reservas', 'usuario_actualizacion')) {
        $reserva->usuario_actualizacion = Auth::user()->correo;
    }
    
    if (\Schema::hasColumn('reservas', 'fecha_actualizacion')) {
        $reserva->fecha_actualizacion = Carbon::now();
    }
    
    $reserva->save();

    return redirect()->route('reservas.mis-reservas')
        ->with('success', 'Reserva actualizada correctamente.');
}

}

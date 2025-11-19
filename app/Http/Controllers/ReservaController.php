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
use App\Models\Empleado;
use App\Models\Turno;
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
            $servicios = Servicio::where('estado', 'A')
                ->where('tipo_servicio', '!=', 'Adicional')
                ->get();

            $adicionales = Servicio::where('estado', 'A')
                ->where('tipo_servicio', 'Adicional')
                ->get();

            // Obtener empleados activos con sus datos personales
            $empleados = Empleado::with('persona')
                ->whereHas('persona', function($query) {
                    $query->where('estado', 'A');
                })
                ->get();

            return view('reservas.seleccion-servicio', compact('servicios', 'adicionales', 'empleados'));
    }

    // 2.5 Obtener horarios disponibles por empleado (AJAX)
    public function obtenerHorariosDisponibles(Request $request)
    {
        $fecha = $request->input('fecha');
        $idEmpleado = $request->input('id_empleado');
        $duracionServicios = $request->input('duracion', 0); // en minutos

        if (!$fecha || !$idEmpleado) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }

        $horariosDisponibles = [];
        $horaInicio = 8; // 8:00 AM
        $horaFin = 20; // 8:00 PM

        // Generar todos los horarios posibles en intervalos de 30 minutos
        for ($hora = $horaInicio; $hora < $horaFin; $hora++) {
            foreach ([0, 30] as $minuto) {
                $horaActual = sprintf('%02d:%02d', $hora, $minuto);
                $horaFinTurno = $this->calcularHoraFin($horaActual, $duracionServicios);

                // Verificar si pasa las 20:00
                $horaFinCarbon = Carbon::createFromFormat('H:i', $horaFinTurno);
                if ($horaFinCarbon->hour >= 20 && $horaFinCarbon->minute > 0) {
                    continue; // Saltar este horario
                }

                // Verificar si el horario está disponible
                $disponible = $this->verificarDisponibilidad($fecha, $idEmpleado, $horaActual, $duracionServicios);

                $horariosDisponibles[] = [
                    'hora' => $horaActual,
                    'disponible' => $disponible
                ];
            }
        }

        return response()->json($horariosDisponibles);
    }

    // Helper: Calcular hora fin del turno
    private function calcularHoraFin($horaInicio, $duracionMinutos)
    {
        $fecha = Carbon::createFromFormat('H:i', $horaInicio);
        $fecha->addMinutes($duracionMinutos);
        return $fecha->format('H:i');
    }

    // Helper: Verificar disponibilidad del empleado
    private function verificarDisponibilidad($fecha, $idEmpleado, $horaInicio, $duracionMinutos)
    {
        // Convertir hora inicio a Carbon
        $inicio = Carbon::parse($fecha . ' ' . $horaInicio);
        $fin = Carbon::parse($fecha . ' ' . $horaInicio)->addMinutes($duracionMinutos);

        // Buscar reservas del empleado en esa fecha
        $reservasExistentes = Reserva::where('fecha', $fecha)
            ->where('id_empleado', $idEmpleado)
            ->whereIn('estado', ['P', 'N', 'C']) // Pendiente, Nuevo, Completado
            ->get();

        foreach ($reservasExistentes as $reserva) {
            // Calcular duración total de la reserva existente
            $duracionReserva = $reserva->detalles->sum(function($detalle) {
                return $detalle->servicio->duracion ?? 60; // 60 minutos por defecto
            });

            $reservaInicio = Carbon::parse($reserva->fecha . ' ' . $reserva->hora);
            $reservaFin = Carbon::parse($reserva->fecha . ' ' . $reserva->hora)->addMinutes($duracionReserva);

            // Verificar solapamiento
            if (($inicio >= $reservaInicio && $inicio < $reservaFin) ||
                ($fin > $reservaInicio && $fin <= $reservaFin) ||
                ($inicio <= $reservaInicio && $fin >= $reservaFin)) {
                return false; // Hay solapamiento
            }
        }

        return true; // Horario disponible
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
                'id_empleado'               => $request->input('id_empleado'), // Trabajador asignado
                'enfermedad'                => $request->has('enfermedad') ? 1 : 0,
                'vacuna'                    => $request->has('vacuna') ? 1 : 0,
                'alergia'                   => $request->has('alergia') ? 1 : 0,
                'descripcion_alergia'       => $request->input('descripcion_alergia'),
                // Datos de delivery
                'requiere_delivery'         => $request->has('requiere_delivery') ? 1 : 0,
                'direccion_recojo'          => $request->input('direccion_recojo'),
                'direccion_entrega'         => $request->input('direccion_entrega'),
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

            // Costo de delivery
            $costoDelivery = session('requiere_delivery', 0) ? 20.00 : 0;

            return view('reservas.pago', compact('mascotas', 'servicios', 'adicionales', 'costoDelivery'));
        }


    // 4. Finalizar Reserva
    public function finalizar(Request $request)
{
    try {
        // 🧩 Verificar usuario autenticado
        if (!Auth::check()) {
            return $this->handleResponse($request, false, 'Sesión expirada. Inicia sesión nuevamente.');
        }

        // 🐾 Verificar mascotas seleccionadas
        $mascotasSeleccionadas = session('mascotas_seleccionadas', []);
        if (empty($mascotasSeleccionadas)) {
            return $this->handleResponse($request, false, 'No hay mascotas seleccionadas en la sesión.');
        }

        // 👤 Verificar cliente asociado
        $cliente = Cliente::where('id_persona', Auth::user()->id_persona)->first();
        if (!$cliente) {
            return $this->handleResponse($request, false, 'No se encontró cliente asociado al usuario.');
        }

        // 📅 Crear reserva
        $reserva = new Reserva();
        $reserva->id_mascota = $mascotasSeleccionadas[0];
        $reserva->id_cliente = $cliente->id_cliente;
        $reserva->id_usuario = Auth::id();
        $reserva->fecha = session('fecha');
        $reserva->hora = session('hora');
        $reserva->id_empleado = session('id_empleado'); // Trabajador asignado
        $reserva->enfermedad = session('enfermedad', 0);
        $reserva->vacuna = session('vacuna', 0);
        $reserva->alergia = session('alergia', 0);
        $reserva->descripcion_alergia = session('descripcion_alergia', null);
        $reserva->estado = 'P'; // Pendiente
        $reserva->usuario_creacion = Auth::user()->correo;
        $reserva->save();

        // 🧴 Detalles de servicios
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

        // 🎁 Detalles adicionales
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

        // 🚗 Guardar delivery si fue solicitado - SOLO en tabla deliveries
        if (session('requiere_delivery', 0) == 1) {
            // Guardar información de delivery en su propia tabla
            // Costo: 16.95 (sin IGV) * 1.18 = 20.00 (con IGV)
            try {
                \DB::table('deliveries')->insert([
                    'id_reserva' => $reserva->id_reserva,
                    'direccion_recojo' => session('direccion_recojo'),
                    'direccion_entrega' => session('direccion_entrega') ?: session('direccion_recojo'),
                    'costo_delivery' => 16.95,
                    'estado' => 'P', // P = Pendiente (el campo solo acepta 1 carácter)
                    'usuario_creacion' => Auth::user()->correo,
                    'usuario_actualizacion' => Auth::user()->correo,
                    'fecha_creacion' => now(),
                    'fecha_actualizacion' => now(),
                ]);
            } catch (\Exception $e) {
                \Log::error('Error al guardar delivery: ' . $e->getMessage());
                throw $e; // Re-lanzar para que sea capturado por el catch externo
            }
        }

        // ✅ Respuesta
        return $this->handleResponse($request, true, 'Reserva creada correctamente.', [
            'reserva_id' => $reserva->id_reserva
        ]);

    } catch (\Exception $e) {
        return $this->handleResponse($request, false, 'Error interno: ' . $e->getMessage());
    }
}

/**
 * 🔧 Helper que decide si devolver JSON (PayPal) o vista normal (Yape/tarjeta)
 */
private function handleResponse(Request $request, $success, $message, $extra = [])
{
    // Si la petición vino de fetch (PayPal)
    if ($request->expectsJson() || $request->ajax()) {
        return response()->json(array_merge([
            'success' => $success,
            'message' => $message,
        ], $extra));
    }

    // Si vino de un formulario tradicional
    if ($success) {
        return view('reservas.completada')->with('success', $message);
    } else {
        return redirect()->back()->with('error', $message);
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
    $reserva = Reserva::where('id_usuario', auth()->id())->latest('id_reserva')->first();
    if (!$reserva) {
        return redirect()->route('reservas.pago')->with('error', 'No se encontró la reserva.');
    }

    // 2) Calcula el total de servicios
    $totalServicios = $reserva->detalles->sum('total'); 
    if ($totalServicios == 0) {
        $totalServicios = $reserva->detalles->sum('precio_unitario') * 1.18; // fallback
    }

    // 3) Agregar costo de delivery si existe
    $costoDelivery = \DB::table('deliveries')
        ->where('id_reserva', $reserva->id_reserva)
        ->value('costo_delivery') ?? 0;
    
    // Aplicar IGV al delivery
    $totalDelivery = $costoDelivery * 1.18;
    
    // Total final
    $total = $totalServicios + $totalDelivery;

    // 4) Genera serie
    $serie = 'BOL-' . str_pad(Pago::count() + 1, 5, '0', STR_PAD_LEFT);

    // 5) Crea el pago
    $pago = Pago::create([
        'id_reserva'        => $reserva->id_reserva,
        'monto'             => $total,
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

    // Obtener empleados activos
    $empleados = Empleado::with('persona')
        ->whereHas('persona', function($query) {
            $query->where('estado', 'A');
        })
        ->get();

    // Próximas (fecha >= hoy y estado pendiente o pagado)
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
            // Verificar si tiene delivery
            $reserva->tiene_delivery = \DB::table('deliveries')
                ->where('id_reserva', $reserva->id_reserva)
                ->exists();
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
            // Verificar si tiene delivery
            $reserva->tiene_delivery = \DB::table('deliveries')
                ->where('id_reserva', $reserva->id_reserva)
                ->exists();
            return $reserva;
        });

    return view('reservas.mis-reservas', compact('proximasReservas', 'historialReservas', 'empleados'));
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

    // Verificar si intenta cambiar fecha/hora
    if ($request->filled('nueva_fecha') && $request->filled('nueva_hora')) {
        // Verificar que no hayan pasado 48 horas desde la creación
        $fechaCreacion = Carbon::parse($reserva->fecha_creacion);
        $horasTranscurridas = $fechaCreacion->diffInHours(Carbon::now());
        
        if ($horasTranscurridas > 48) {
            return redirect()->route('reservas.mis-reservas')
                ->with('error', 'No es posible reprogramar la reserva después de 48 horas de su creación.');
        }

        // Validar que la nueva fecha sea futura
        $nuevaFecha = $request->input('nueva_fecha');
        if ($nuevaFecha < Carbon::now()->toDateString()) {
            return redirect()->route('reservas.mis-reservas')
                ->with('error', 'La nueva fecha debe ser futura.');
        }

        // Validar que se haya seleccionado un trabajador
        if (!$request->filled('nuevo_id_empleado')) {
            return redirect()->route('reservas.mis-reservas')
                ->with('error', 'Debes seleccionar un trabajador para la nueva fecha.');
        }

        // Actualizar fecha, hora y trabajador
        $reserva->fecha = $nuevaFecha;
        $reserva->hora = $request->input('nueva_hora');
        $reserva->id_empleado = $request->input('nuevo_id_empleado');
    }

    // Actualizar los datos de salud de la reserva
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

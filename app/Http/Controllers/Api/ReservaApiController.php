<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Reservations\AvailabilityProvider;
use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReservaResource;
use App\Models\Cliente;
use App\Models\DetalleReserva;
use App\Models\Mascota;
use App\Models\Reserva;
use App\Models\Servicio;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReservaApiController extends Controller
{
    use ApiResponse;

    public function horariosDisponibles(Request $request, AvailabilityProvider $availability): JsonResponse
    {
        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'id_empleado' => ['required', 'integer', 'exists:empleados,id_empleado'],
            'duracion' => ['nullable', 'integer', 'min:30', 'max:480'],
            'servicios' => ['nullable', 'array'],
            'servicios.*' => ['integer', 'exists:servicios,id_servicio'],
        ]);

        $duracion = $data['duracion'] ?? $this->duracionServicios($data['servicios'] ?? [], $availability);

        return $this->success([
            'fecha' => $data['fecha'],
            'id_empleado' => (int) $data['id_empleado'],
            'duracion_minutos' => $duracion,
            'horarios' => $availability->availableSlots($data['fecha'], (int) $data['id_empleado'], $duracion),
        ], 'Horarios disponibles obtenidos correctamente.');
    }

    public function index(Request $request)
    {
        $query = Reserva::with([
            'mascota',
            'cliente.persona',
            'empleado.persona',
            'detalles.servicio',
            'atencion',
        ])->latest('id_reserva');

        $this->aplicarAlcancePorRol($request, $query);

        $query
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->input('estado')))
            ->when($request->filled('fecha'), fn ($q) => $q->whereDate('fecha', $request->input('fecha')))
            ->when($request->filled('fecha_desde'), fn ($q) => $q->whereDate('fecha', '>=', $request->input('fecha_desde')))
            ->when($request->filled('fecha_hasta'), fn ($q) => $q->whereDate('fecha', '<=', $request->input('fecha_hasta')))
            ->when($request->filled('id_empleado'), fn ($q) => $q->where('id_empleado', $request->input('id_empleado')));

        return ReservaResource::collection($query->paginate((int) $request->input('per_page', 15)));
    }

    public function store(Request $request, AvailabilityProvider $availability): JsonResponse
    {
        abort_unless($request->user()->rol === 'Cliente', 403, 'Solo los clientes pueden crear reservas por este endpoint.');

        $cliente = $this->clienteActual($request);
        $data = $request->validate([
            'id_mascota' => ['required', 'integer', 'exists:mascotas,id_mascota'],
            'id_empleado' => ['required', 'integer', 'exists:empleados,id_empleado'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['required', 'date_format:H:i'],
            'servicios' => ['required', 'array', 'min:1'],
            'servicios.*' => ['integer', 'exists:servicios,id_servicio'],
            'adicionales' => ['nullable', 'array'],
            'adicionales.*' => ['integer', 'exists:servicios,id_servicio'],
            'enfermedad' => ['nullable', 'boolean'],
            'vacuna' => ['nullable', 'boolean'],
            'alergia' => ['nullable', 'boolean'],
            'descripcion_alergia' => ['nullable', 'string', 'max:500'],
            'requiere_delivery' => ['nullable', 'boolean'],
            'direccion_recojo' => ['nullable', 'string', 'max:255'],
            'direccion_entrega' => ['nullable', 'string', 'max:255'],
        ]);

        $mascota = Mascota::where('id_cliente', $cliente->id_cliente)
            ->where('id_mascota', $data['id_mascota'])
            ->firstOrFail();

        $idsServicios = array_values(array_unique(array_merge($data['servicios'], $data['adicionales'] ?? [])));
        $duracion = $this->duracionServicios($idsServicios, $availability);

        abort_unless(
            $availability->isAvailable($data['fecha'], (int) $data['id_empleado'], $data['hora'], $duracion),
            400,
            'El horario seleccionado ya no esta disponible.'
        );

        $reserva = DB::transaction(function () use ($request, $cliente, $mascota, $data, $idsServicios) {
            $reserva = Reserva::create([
                'id_mascota' => $mascota->id_mascota,
                'id_cliente' => $cliente->id_cliente,
                'id_usuario' => $request->user()->id_usuario,
                'id_empleado' => $data['id_empleado'],
                'fecha' => $data['fecha'],
                'hora' => $data['hora'],
                'enfermedad' => (bool) ($data['enfermedad'] ?? false),
                'vacuna' => (bool) ($data['vacuna'] ?? false),
                'alergia' => (bool) ($data['alergia'] ?? false),
                'descripcion_alergia' => $data['descripcion_alergia'] ?? null,
                'estado' => 'P',
                'usuario_creacion' => $request->user()->correo,
                'fecha_creacion' => now(),
            ]);

            foreach ($idsServicios as $idServicio) {
                $this->crearDetalle($request, $reserva, (int) $idServicio);
            }

            if (($data['requiere_delivery'] ?? false) && Schema::hasTable('deliveries')) {
                DB::table('deliveries')->insert([
                    'id_reserva' => $reserva->id_reserva,
                    'direccion_recojo' => $data['direccion_recojo'] ?? null,
                    'direccion_entrega' => $data['direccion_entrega'] ?? ($data['direccion_recojo'] ?? null),
                    'costo_delivery' => 16.95,
                    'estado' => 'P',
                    'usuario_creacion' => $request->user()->correo,
                    'usuario_actualizacion' => $request->user()->correo,
                    'fecha_creacion' => now(),
                    'fecha_actualizacion' => now(),
                ]);
            }

            return $reserva;
        });

        $reserva->load(['mascota', 'cliente.persona', 'empleado.persona', 'detalles.servicio']);

        return $this->success(new ReservaResource($reserva), 'Reserva creada correctamente.', 201);
    }

    public function show(Request $request, Reserva $reserva): ReservaResource
    {
        $this->autorizarReserva($request, $reserva);

        return new ReservaResource($reserva->load([
            'mascota',
            'cliente.persona',
            'empleado.persona',
            'detalles.servicio',
            'atencion',
        ]));
    }

    public function update(Request $request, Reserva $reserva, AvailabilityProvider $availability): JsonResponse
    {
        $this->autorizarReserva($request, $reserva);

        $data = $request->validate([
            'fecha' => ['sometimes', 'date', 'after_or_equal:today'],
            'hora' => ['sometimes', 'date_format:H:i'],
            'id_empleado' => ['sometimes', 'integer', 'exists:empleados,id_empleado'],
            'enfermedad' => ['nullable', 'boolean'],
            'vacuna' => ['nullable', 'boolean'],
            'alergia' => ['nullable', 'boolean'],
            'descripcion_alergia' => ['nullable', 'string', 'max:500'],
        ]);

        if ($reserva->fecha < Carbon::now()->toDateString()) {
            return $this->badRequest('No puedes editar reservas pasadas.');
        }

        $fecha = $data['fecha'] ?? $reserva->fecha;
        $hora = $data['hora'] ?? substr((string) $reserva->hora, 0, 5);
        $idEmpleado = (int) ($data['id_empleado'] ?? $reserva->id_empleado);

        if (isset($data['fecha'], $data['hora']) || isset($data['id_empleado'])) {
            $duracion = max((int) $reserva->detalles->sum(fn ($detalle) => $availability->normalizeServiceDuration($detalle->servicio->duracion ?? 60)), 60);
            $disponible = $availability->isAvailable($fecha, $idEmpleado, $hora, $duracion, $reserva->id_reserva);

            if (!$disponible) {
                return $this->badRequest('El nuevo horario no esta disponible.');
            }
        }

        $reserva->fill($data);

        if (Schema::hasColumn('reservas', 'usuario_actualizacion')) {
            $reserva->usuario_actualizacion = $request->user()->correo;
        }

        if (Schema::hasColumn('reservas', 'fecha_actualizacion')) {
            $reserva->fecha_actualizacion = now();
        }

        $reserva->save();

        return $this->success(
            new ReservaResource($reserva->fresh()->load(['mascota', 'cliente.persona', 'empleado.persona', 'detalles.servicio'])),
            'Reserva actualizada correctamente.'
        );
    }

    public function destroy(Request $request, Reserva $reserva): JsonResponse
    {
        $this->autorizarReserva($request, $reserva);

        $reserva->estado = 'C';

        if (Schema::hasColumn('reservas', 'usuario_actualizacion')) {
            $reserva->usuario_actualizacion = $request->user()->correo;
        }

        if (Schema::hasColumn('reservas', 'fecha_actualizacion')) {
            $reserva->fecha_actualizacion = now();
        }

        $reserva->save();

        return $this->success(new ReservaResource($reserva), 'Reserva cancelada correctamente.');
    }

    private function clienteActual(Request $request): Cliente
    {
        $usuario = $request->user();
        $cliente = Cliente::where('id_persona', $usuario->id_persona)->first();

        if (!$cliente) {
            $cliente = new Cliente([
                'id_persona' => $usuario->id_persona,
                'usuario_creacion' => $usuario->correo,
            ]);
            $cliente->fecha_creacion = now();
            $cliente->fecha_actualizacion = now();
            $cliente->save();
        }

        return $cliente;
    }

    private function aplicarAlcancePorRol(Request $request, $query): void
    {
        $user = $request->user();

        if ($user->rol === 'Cliente') {
            $query->where('id_cliente', $this->clienteActual($request)->id_cliente);
            return;
        }

        if ($user->rol === 'Empleado') {
            $empleado = $user->empleado;

            if ($empleado) {
                $query->where('id_empleado', $empleado->id_empleado);
            }
        }
    }

    private function autorizarReserva(Request $request, Reserva $reserva): void
    {
        $user = $request->user();

        if (in_array($user->rol, ['Admin', 'Supervisor'], true)) {
            return;
        }

        if ($user->rol === 'Empleado') {
            abort_unless($user->empleado && (int) $reserva->id_empleado === (int) $user->empleado->id_empleado, 403);
            return;
        }

        abort_unless((int) $reserva->id_cliente === (int) $this->clienteActual($request)->id_cliente, 403);
    }

    private function duracionServicios(array $idsServicios, AvailabilityProvider $availability): int
    {
        if (empty($idsServicios)) {
            return 60;
        }

        $duracion = Servicio::whereIn('id_servicio', $idsServicios)
            ->get()
            ->sum(fn ($servicio) => $availability->normalizeServiceDuration($servicio->duracion));

        return max((int) $duracion, 60);
    }

    private function crearDetalle(Request $request, Reserva $reserva, int $idServicio): void
    {
        $servicio = Servicio::find($idServicio);

        if (!$servicio) {
            return;
        }

        DetalleReserva::create([
            'id_reserva' => $reserva->id_reserva,
            'id_servicio' => $idServicio,
            'precio_unitario' => $servicio->costo,
            'igv' => $servicio->costo * 0.18,
            'total' => $servicio->costo * 1.18,
            'estado' => 'A',
            'usuario_creacion' => $request->user()->correo,
            'fecha_creacion' => now(),
        ]);
    }
}

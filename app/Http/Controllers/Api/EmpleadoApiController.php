<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReservaResource;
use App\Models\Atencion;
use App\Models\DetalleReserva;
use App\Models\Empleado;
use App\Models\Feedback;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class EmpleadoApiController extends Controller
{
    use ApiResponse;

    public function panelDelDia(Request $request): JsonResponse
    {
        $fecha = $request->input('fecha', now()->toDateString());
        $empleado = $this->empleadoParaConsulta($request);

        $reservas = Reserva::with([
            'mascota',
            'cliente.persona',
            'empleado.persona',
            'detalles.servicio',
            'atencion',
        ])
            ->whereDate('fecha', $fecha)
            ->when($empleado, fn ($query) => $query->where('id_empleado', $empleado->id_empleado))
            ->orderBy('hora')
            ->get();

        $pendientes = $reservas->whereIn('estado', ['P', 'N']);

        $serviciosPopulares = DetalleReserva::join('servicios', 'detalles_reservas.id_servicio', '=', 'servicios.id_servicio')
            ->join('reservas', 'detalles_reservas.id_reserva', '=', 'reservas.id_reserva')
            ->when($empleado, fn ($query) => $query->where('reservas.id_empleado', $empleado->id_empleado))
            ->selectRaw('servicios.nombre_servicio, COUNT(*) as total')
            ->groupBy('servicios.id_servicio', 'servicios.nombre_servicio')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $comentarios = collect();
        try {
            $comentarios = Feedback::where('calificacion', 5)
                ->whereNotNull('comentarios')
                ->where('comentarios', '!=', '')
                ->whereHas('reserva', function ($query) use ($empleado) {
                    if ($empleado) {
                        $query->where('id_empleado', $empleado->id_empleado);
                    }
                })
                ->with(['reserva.cliente.persona', 'reserva.mascota'])
                ->latest('fecha_creacion')
                ->limit(5)
                ->get()
                ->map(fn ($feedback) => [
                    'comentario' => $feedback->comentarios,
                    'cliente' => $feedback->reserva?->cliente?->persona?->nombres,
                    'mascota' => $feedback->reserva?->mascota?->nombre,
                    'fecha' => $feedback->fecha_creacion,
                ]);
        } catch (\Throwable) {
            $comentarios = collect();
        }

        return $this->success([
            'fecha' => $fecha,
            'empleado' => $empleado ? [
                'id' => $empleado->id_empleado,
                'nombre' => trim(($empleado->persona->nombres ?? '') . ' ' . ($empleado->persona->apellidos ?? '')),
            ] : null,
            'resumen' => [
                'total_reservas' => $reservas->count(),
                'pendientes' => $pendientes->count(),
                'atendidas' => $reservas->where('estado', 'A')->count(),
                'clientes_atendidos' => $reservas->where('estado', 'A')->unique('id_cliente')->count(),
                'proxima_reserva' => $pendientes->first() ? new ReservaResource($pendientes->first()) : null,
            ],
            'reservas' => ReservaResource::collection($reservas),
            'servicios_populares' => $serviciosPopulares,
            'comentarios_destacados' => $comentarios,
        ], 'Panel del dia obtenido correctamente.');
    }

    public function reservas(Request $request)
    {
        $empleado = $this->empleadoParaConsulta($request);

        $reservas = Reserva::with([
            'mascota',
            'cliente.persona',
            'empleado.persona',
            'detalles.servicio',
            'atencion',
        ])
            ->when($empleado, fn ($query) => $query->where('id_empleado', $empleado->id_empleado))
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->input('estado')))
            ->when($request->filled('fecha'), fn ($query) => $query->whereDate('fecha', $request->input('fecha')))
            ->when($request->filled('cliente'), function ($query) use ($request) {
                $query->whereHas('cliente.persona', function ($subQuery) use ($request) {
                    $subQuery->where('nombres', 'like', '%' . $request->input('cliente') . '%')
                        ->orWhere('apellidos', 'like', '%' . $request->input('cliente') . '%');
                });
            })
            ->latest('id_reserva')
            ->paginate((int) $request->input('per_page', 15));

        return ReservaResource::collection($reservas);
    }

    public function atender(Request $request, Reserva $reserva): JsonResponse
    {
        $this->autorizarReservaEmpleado($request, $reserva);

        $data = $request->validate([
            'descripcion' => ['nullable', 'string', 'max:500'],
            'comentarios' => ['nullable', 'string', 'max:500'],
        ]);

        if (($data['descripcion'] ?? null) && class_exists(Atencion::class)) {
            Atencion::updateOrCreate(
                ['id_reserva' => $reserva->id_reserva],
                [
                    'descripcion' => $data['descripcion'],
                    'comentarios' => $data['comentarios'] ?? null,
                    'usuario_creacion' => $request->user()->correo,
                    'fecha_creacion' => now(),
                ]
            );
        }

        $reserva->estado = 'A';

        if (Schema::hasColumn('reservas', 'usuario_actualizacion')) {
            $reserva->usuario_actualizacion = $request->user()->correo;
        }

        if (Schema::hasColumn('reservas', 'fecha_actualizacion')) {
            $reserva->fecha_actualizacion = now();
        }

        $reserva->save();

        return $this->success(
            new ReservaResource($reserva->fresh()->load(['mascota', 'cliente.persona', 'empleado.persona', 'detalles.servicio', 'atencion'])),
            'Reserva marcada como atendida.'
        );
    }

    private function empleadoParaConsulta(Request $request): ?Empleado
    {
        $user = $request->user();

        if ($user->rol === 'Empleado') {
            return $user->empleado;
        }

        if ($request->filled('id_empleado')) {
            return Empleado::with('persona')->findOrFail($request->input('id_empleado'));
        }

        return null;
    }

    private function autorizarReservaEmpleado(Request $request, Reserva $reserva): void
    {
        if (in_array($request->user()->rol, ['Admin', 'Supervisor'], true)) {
            return;
        }

        abort_unless(
            $request->user()->empleado && (int) $reserva->id_empleado === (int) $request->user()->empleado->id_empleado,
            403,
            'No tienes permiso para atender esta reserva.'
        );
    }
}

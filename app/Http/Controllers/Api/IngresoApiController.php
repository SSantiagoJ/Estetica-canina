<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PagoResource;
use App\Models\Empleado;
use App\Models\Pago;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IngresoApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        [$mes, $inicioMes, $finMes, $pagos, $empleadoActual] = $this->pagosDelPeriodo($request);
        $totalMes = (float) $pagos->sum('monto');
        $cantidadPagos = $pagos->count();
        $puedeVerDetalle = in_array($user->rol, ['Admin', 'Supervisor'], true);
        $ingresosPorEmpleado = $puedeVerDetalle ? $this->ingresosPorEmpleado($pagos) : collect();

        return $this->success([
            'mes' => $mes,
            'inicio' => $inicioMes->toDateString(),
            'fin' => $finMes->toDateString(),
            'resumen' => [
                'total_mes' => $totalMes,
                'total_hoy' => (float) $pagos->where('fecha', now()->toDateString())->sum('monto'),
                'cantidad_pagos' => $cantidadPagos,
                'ticket_promedio' => $cantidadPagos > 0 ? round($totalMes / $cantidadPagos, 2) : 0,
            ],
            'ingresos_por_empleado' => $ingresosPorEmpleado,
            'pagos' => $puedeVerDetalle ? PagoResource::collection($pagos) : [],
            'empleado' => $empleadoActual ? [
                'id' => $empleadoActual->id_empleado,
                'nombre' => trim(($empleadoActual->persona->nombres ?? '') . ' ' . ($empleadoActual->persona->apellidos ?? '')),
            ] : null,
        ], 'Ingresos obtenidos correctamente.');
    }

    public function metricas(Request $request): JsonResponse
    {
        abort_unless(in_array($request->user()->rol, ['Admin', 'Supervisor'], true), 403);

        [$mes, $inicioMes, $finMes, $pagos] = $this->pagosDelPeriodo($request);

        $pagosPorDia = $pagos
            ->groupBy('fecha')
            ->map(fn ($items) => [
                'pagos' => $items->count(),
                'total' => (float) $items->sum('monto'),
            ])
            ->sortKeys();

        return $this->success([
            'mes' => $mes,
            'periodo' => [
                'inicio' => $inicioMes->toDateString(),
                'fin' => $finMes->toDateString(),
            ],
            'total_mes' => (float) $pagos->sum('monto'),
            'pagos_confirmados' => $pagos->count(),
            'ingresos_por_empleado' => $this->ingresosPorEmpleado($pagos),
            'pagos_por_dia' => $pagosPorDia,
            'servicios_mas_vendidos' => $this->serviciosMasVendidos($pagos),
        ], 'Metricas de ingresos obtenidas correctamente.');
    }

    public function reporteExcel(Request $request)
    {
        abort_unless(in_array($request->user()->rol, ['Admin', 'Supervisor'], true), 403);

        [$mesSeleccionado, $inicioMes, $finMes, $pagos] = $this->pagosDelPeriodo($request);
        $rolActual = $request->user()->rol;
        $totalMes = (float) $pagos->sum('monto');
        $cantidadPagos = $pagos->count();
        $ticketPromedio = $cantidadPagos > 0 ? $totalMes / $cantidadPagos : 0;
        $ingresosPorEmpleado = $this->ingresosPorEmpleado($pagos);
        $generadoPor = $request->user()->correo ?? 'Sistema';
        $fechaGeneracion = now()->format('d/m/Y H:i');

        $html = view('empleado.reportes.ingresos-excel', compact(
            'rolActual',
            'mesSeleccionado',
            'inicioMes',
            'finMes',
            'pagos',
            'totalMes',
            'cantidadPagos',
            'ticketPromedio',
            'ingresosPorEmpleado',
            'generadoPor',
            'fechaGeneracion'
        ))->render();

        return response("\xEF\xBB\xBF" . $html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="reporte-ingresos-' . $mesSeleccionado . '.xls"',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    private function pagosDelPeriodo(Request $request): array
    {
        $user = $request->user();
        $empleadoActual = null;

        if ($user->rol === 'Empleado') {
            $empleadoActual = Empleado::with('persona')->where('id_persona', $user->id_persona)->first();
            abort_unless($empleadoActual, 403, 'No tienes una ficha de empleado asignada.');
        }

        $mes = $request->input('mes', now()->format('Y-m'));

        try {
            $inicioMes = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        } catch (\Throwable) {
            $inicioMes = now()->startOfMonth();
            $mes = $inicioMes->format('Y-m');
        }

        $finMes = $inicioMes->copy()->endOfMonth();

        $pagos = Pago::with([
            'reserva.cliente.persona',
            'reserva.empleado.persona',
            'reserva.mascota',
            'reserva.detalles.servicio',
        ])
            ->whereBetween('fecha', [$inicioMes->toDateString(), $finMes->toDateString()])
            ->where(function ($query) {
                $query->where('estado_gateway', 'APROBADO')
                    ->orWhereNull('estado_gateway');
            })
            ->whereIn('estado', ['P', 'A'])
            ->when($empleadoActual, function ($query) use ($empleadoActual) {
                $query->whereHas('reserva', fn ($subQuery) => $subQuery->where('id_empleado', $empleadoActual->id_empleado));
            })
            ->latest('id_pago')
            ->get();

        return [$mes, $inicioMes, $finMes, $pagos, $empleadoActual];
    }

    private function ingresosPorEmpleado($pagos)
    {
        return $pagos
            ->groupBy(fn ($pago) => $pago->reserva->empleado->id_empleado ?? 'sin_asignar')
            ->map(function ($items) {
                $primerPago = $items->first();
                $persona = $primerPago->reserva->empleado->persona ?? null;

                return [
                    'empleado' => $persona ? trim(($persona->nombres ?? '') . ' ' . ($persona->apellidos ?? '')) : 'Sin empleado asignado',
                    'pagos' => $items->count(),
                    'total' => (float) $items->sum('monto'),
                ];
            })
            ->sortByDesc('total')
            ->values();
    }

    private function serviciosMasVendidos($pagos)
    {
        return $pagos
            ->flatMap(fn ($pago) => $pago->reserva?->detalles ?? collect())
            ->groupBy(fn ($detalle) => $detalle->servicio->nombre_servicio ?? 'Servicio')
            ->map(fn ($items, $nombre) => [
                'servicio' => $nombre,
                'cantidad' => $items->count(),
                'total' => (float) $items->sum('total'),
            ])
            ->sortByDesc('cantidad')
            ->values()
            ->take(10);
    }
}

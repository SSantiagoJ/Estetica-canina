<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PagoResource;
use App\Models\Pago;
use App\Models\PagoNotificacion;
use App\Models\Reserva;
use App\Models\Usuario;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class PagoApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request, Reserva $reserva)
    {
        $this->autorizarReserva($request, $reserva);

        $pagos = Pago::where('id_reserva', $reserva->id_reserva)
            ->latest('id_pago')
            ->paginate((int) $request->input('per_page', 15));

        return PagoResource::collection($pagos);
    }

    public function store(Request $request, Reserva $reserva): JsonResponse
    {
        $this->autorizarReserva($request, $reserva);

        $data = $request->validate([
            'metodo_pago' => ['nullable', 'string', 'max:50'],
            'codigo_operacion' => ['nullable', 'string', 'max:120'],
        ]);

        $pago = DB::transaction(function () use ($request, $reserva, $data) {
            $pagoExistente = Pago::where('id_reserva', $reserva->id_reserva)
                ->whereIn('estado', ['P', 'A'])
                ->latest('id_pago')
                ->first();

            if ($pagoExistente) {
                return $pagoExistente;
            }

            $total = $this->calcularTotalReserva($reserva);
            $serie = 'BOL-' . str_pad(((int) Pago::max('id_pago')) + 1, 5, '0', STR_PAD_LEFT);
            $metodoPago = $data['metodo_pago'] ?? 'interno';
            $codigoOperacion = $data['codigo_operacion'] ?: 'PG-' . now()->format('YmdHis') . '-' . str_pad($reserva->id_reserva, 5, '0', STR_PAD_LEFT);

            $pago = Pago::create([
                'id_reserva' => $reserva->id_reserva,
                'monto' => $total,
                'monto_neto' => round($total / 1.18, 2),
                'metodo_pago' => $metodoPago,
                'gateway' => $metodoPago,
                'provider_payment_id' => $codigoOperacion,
                'estado_gateway' => 'APROBADO',
                'fecha_confirmacion' => now(),
                'fecha' => Carbon::now()->toDateString(),
                'hora' => Carbon::now()->toTimeString(),
                'estado' => 'P',
                'usuario_creacion' => $request->user()->correo,
                'series' => $serie,
                'codigo_operacion' => $codigoOperacion,
            ]);

            $this->registrarNotificacionesPago($request, $pago);

            return $pago;
        });

        $path = $this->guardarArchivoBoleta($pago);
        $this->enviarCorreoBoleta($pago, $path);

        return $this->success(new PagoResource($pago->fresh()), 'Pago registrado y boleta generada correctamente.', 201);
    }

    public function boleta(Request $request, Pago $pago)
    {
        $this->autorizarPago($request, $pago);

        return response()->file($this->guardarArchivoBoleta($pago), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function boletaReserva(Request $request, Reserva $reserva)
    {
        $this->autorizarReserva($request, $reserva);
        $pago = Pago::where('id_reserva', $reserva->id_reserva)
            ->latest('id_pago')
            ->firstOrFail();

        return $this->boleta($request, $pago);
    }

    public function descargarBoletaReserva(Request $request, Reserva $reserva)
    {
        $this->autorizarReserva($request, $reserva);
        $pago = Pago::where('id_reserva', $reserva->id_reserva)
            ->latest('id_pago')
            ->firstOrFail();

        return $this->descargarBoleta($request, $pago);
    }

    public function descargarBoleta(Request $request, Pago $pago)
    {
        $this->autorizarPago($request, $pago);
        $path = $this->guardarArchivoBoleta($pago);

        return response()->download($path, basename($path), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function autorizarPago(Request $request, Pago $pago): void
    {
        $pago->loadMissing('reserva');
        $this->autorizarReserva($request, $pago->reserva);
    }

    private function autorizarReserva(Request $request, ?Reserva $reserva): void
    {
        abort_unless($reserva, 404);

        $user = $request->user();

        if (in_array($user->rol, ['Admin', 'Supervisor'], true)) {
            return;
        }

        if ($user->rol === 'Empleado') {
            abort_unless($user->empleado && (int) $reserva->id_empleado === (int) $user->empleado->id_empleado, 403);
            return;
        }

        abort_unless((int) $reserva->id_usuario === (int) $user->id_usuario, 403);
    }

    private function calcularTotalReserva(Reserva $reserva): float
    {
        $reserva->loadMissing('detalles');
        $totalServicios = (float) $reserva->detalles->sum('total');

        if ($totalServicios <= 0) {
            $totalServicios = (float) $reserva->detalles->sum('precio_unitario') * 1.18;
        }

        $costoDelivery = 0;
        if (Schema::hasTable('deliveries')) {
            $costoDelivery = (float) (DB::table('deliveries')
                ->where('id_reserva', $reserva->id_reserva)
                ->value('costo_delivery') ?? 0);
        }

        return round($totalServicios + ($costoDelivery * 1.18), 2);
    }

    private function registrarNotificacionesPago(Request $request, Pago $pago): void
    {
        $pago->loadMissing(['reserva.cliente.persona']);
        $reserva = $pago->reserva;

        if (!$reserva) {
            return;
        }

        $mensaje = "Pago {$pago->series} aprobado por S/ " . number_format($pago->monto, 2) . " para la reserva {$reserva->id_reserva}.";

        PagoNotificacion::create([
            'id_pago' => $pago->id_pago,
            'id_usuario' => $reserva->id_usuario,
            'rol_destino' => 'Cliente',
            'canal' => 'correo',
            'titulo' => 'Pago de reserva confirmado',
            'mensaje' => $mensaje,
            'estado' => 'E',
            'fecha_envio' => now(),
            'usuario_creacion' => $request->user()->correo,
        ]);

        Usuario::whereIn('rol', ['Admin', 'Supervisor'])
            ->where('estado', 'A')
            ->get()
            ->each(function (Usuario $usuario) use ($request, $pago, $mensaje) {
                PagoNotificacion::create([
                    'id_pago' => $pago->id_pago,
                    'id_usuario' => $usuario->id_usuario,
                    'rol_destino' => $usuario->rol,
                    'canal' => 'sistema',
                    'titulo' => 'Nuevo pago registrado',
                    'mensaje' => $mensaje,
                    'estado' => 'P',
                    'fecha_envio' => now(),
                    'usuario_creacion' => $request->user()->correo,
                ]);
            });
    }

    private function guardarArchivoBoleta(Pago $pago): string
    {
        $pago->loadMissing([
            'reserva.cliente.persona',
            'reserva.usuario',
            'reserva.mascota',
            'reserva.empleado.persona',
            'reserva.detalles.servicio',
        ]);

        $directory = storage_path('app/boletas');
        File::ensureDirectoryExists($directory);

        $serie = $pago->series ?: 'BOL-' . str_pad($pago->id_pago, 5, '0', STR_PAD_LEFT);
        $fileName = preg_replace('/[^A-Za-z0-9_-]/', '_', $serie) . '.pdf';
        $path = $directory . DIRECTORY_SEPARATOR . $fileName;
        $delivery = Schema::hasTable('deliveries')
            ? DB::table('deliveries')->where('id_reserva', $pago->reserva->id_reserva)->first()
            : null;

        Pdf::loadView('reservas.boleta', [
            'pago' => $pago,
            'reserva' => $pago->reserva,
            'cliente' => $pago->reserva->cliente,
            'servicios' => $pago->reserva->detalles,
            'delivery' => $delivery,
        ])->setPaper('A4', 'portrait')->save($path);

        if (Schema::hasColumn('pagos', 'comprobante_path') && !$pago->comprobante_path) {
            $pago->comprobante_path = $path;
            $pago->save();
        }

        return $path;
    }

    private function enviarCorreoBoleta(Pago $pago, string $path): void
    {
        $pago->loadMissing(['reserva.usuario', 'reserva.cliente.persona', 'reserva.mascota', 'reserva.detalles.servicio']);

        if (!$pago->reserva?->usuario?->correo) {
            return;
        }

        try {
            Mail::send('emails.pago-confirmado', [
                'pago' => $pago,
                'reserva' => $pago->reserva,
                'cliente' => $pago->reserva->cliente,
            ], function ($message) use ($pago, $path) {
                $message->to($pago->reserva->usuario->correo)
                    ->subject("Boleta electronica {$pago->series} - Pet Grooming")
                    ->attach($path, [
                        'as' => "{$pago->series}.pdf",
                        'mime' => 'application/pdf',
                    ]);
            });
        } catch (\Throwable) {
            // El pago no debe fallar por un problema temporal de correo.
        }
    }
}

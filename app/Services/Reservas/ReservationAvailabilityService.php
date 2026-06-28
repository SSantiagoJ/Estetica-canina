<?php

namespace App\Services\Reservas;

use App\Models\Reserva;
use Carbon\Carbon;

class ReservationAvailabilityService
{
    public function availableSlots(string $fecha, int $idEmpleado, int $duracionMinutos = 60): array
    {
        $duracionMinutos = max($duracionMinutos, 60);
        $horarios = [];

        for ($hora = 8; $hora < 20; $hora++) {
            foreach ([0, 30] as $minuto) {
                $horaActual = sprintf('%02d:%02d', $hora, $minuto);
                $horaFin = $this->calculateEndTime($horaActual, $duracionMinutos);
                $horaFinCarbon = Carbon::createFromFormat('H:i', $horaFin);

                if ($horaFinCarbon->hour >= 20 && $horaFinCarbon->minute > 0) {
                    continue;
                }

                $horarios[] = [
                    'hora' => $horaActual,
                    'hora_fin' => $horaFin,
                    'disponible' => $this->isAvailable($fecha, $idEmpleado, $horaActual, $duracionMinutos),
                ];
            }
        }

        return $horarios;
    }

    public function calculateEndTime(string $horaInicio, int $duracionMinutos): string
    {
        return Carbon::createFromFormat('H:i', $horaInicio)
            ->addMinutes(max($duracionMinutos, 60))
            ->format('H:i');
    }

    public function normalizeServiceDuration(mixed $duracion): int
    {
        $valor = (float) ($duracion ?? 0);

        if ($valor <= 0) {
            return 60;
        }

        if ($valor <= 8) {
            return (int) round($valor * 60);
        }

        return (int) round($valor);
    }

    public function isAvailable(string $fecha, int $idEmpleado, string $horaInicio, int $duracionMinutos, ?int $exceptReservaId = null): bool
    {
        $inicio = Carbon::parse($fecha . ' ' . $horaInicio);
        $fin = Carbon::parse($fecha . ' ' . $horaInicio)->addMinutes(max($duracionMinutos, 60));

        $reservasExistentes = Reserva::with('detalles.servicio')
            ->where('fecha', $fecha)
            ->where('id_empleado', $idEmpleado)
            ->whereIn('estado', ['P', 'N', 'A'])
            ->when($exceptReservaId, fn ($query) => $query->where('id_reserva', '!=', $exceptReservaId))
            ->get();

        foreach ($reservasExistentes as $reserva) {
            $duracionReserva = $reserva->detalles->sum(function ($detalle) {
                return $this->normalizeServiceDuration($detalle->servicio->duracion ?? 60);
            });

            $duracionReserva = max((int) $duracionReserva, 60);
            $reservaInicio = Carbon::parse($reserva->fecha . ' ' . $reserva->hora);
            $reservaFin = Carbon::parse($reserva->fecha . ' ' . $reserva->hora)->addMinutes($duracionReserva);

            if (
                ($inicio >= $reservaInicio && $inicio < $reservaFin) ||
                ($fin > $reservaInicio && $fin <= $reservaFin) ||
                ($inicio <= $reservaInicio && $fin >= $reservaFin)
            ) {
                return false;
            }
        }

        return true;
    }
}

<?php

namespace App\Contracts\Reservations;

interface AvailabilityProvider
{
    public function availableSlots(string $fecha, int $idEmpleado, int $duracionMinutos = 60): array;

    public function calculateEndTime(string $horaInicio, int $duracionMinutos): string;

    public function normalizeServiceDuration(mixed $duracion): int;

    public function isAvailable(string $fecha, int $idEmpleado, string $horaInicio, int $duracionMinutos, ?int $exceptReservaId = null): bool;
}

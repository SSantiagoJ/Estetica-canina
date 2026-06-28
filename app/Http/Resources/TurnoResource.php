<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TurnoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $persona = $this->empleado?->persona;

        return [
            'id' => $this->id_turno,
            'id_empleado' => $this->id_empleado,
            'empleado' => $persona ? trim(($persona->nombres ?? '') . ' ' . ($persona->apellidos ?? '')) : null,
            'fecha' => $this->fecha,
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'estado' => $this->estado,
        ];
    }
}

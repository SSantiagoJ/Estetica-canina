<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MascotaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_mascota,
            'nombre' => $this->nombre,
            'fecha_nacimiento' => optional($this->fecha_nacimiento)->format('Y-m-d') ?: $this->fecha_nacimiento,
            'edad' => $this->edad,
            'sexo' => $this->sexo,
            'especie' => $this->especie,
            'raza' => $this->raza,
            'tamano' => $this->tamano,
            'peso' => $this->peso !== null ? (float) $this->peso : null,
            'descripcion' => $this->descripcion,
            'foto_url' => $this->foto,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServicioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_servicio,
            'categoria' => $this->categoria,
            'tipo_servicio' => $this->tipo_servicio,
            'nombre' => $this->nombre_servicio,
            'descripcion' => $this->descripcion,
            'costo' => (float) $this->costo,
            'especie' => $this->especie,
            'duracion' => $this->duracion,
            'imagen_url' => $this->imagen_url,
            'estado' => $this->estado,
        ];
    }
}

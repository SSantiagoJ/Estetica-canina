<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RazaImagenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_raza_imagen,
            'especie' => $this->especie,
            'raza' => $this->raza,
            'slug' => $this->slug,
            'imagen_url' => $this->url,
            'tamano_bytes' => $this->tamano_bytes,
            'tamano_legible' => $this->tamano_legible,
            'mime_type' => $this->mime_type,
            'estado' => $this->estado,
        ];
    }
}

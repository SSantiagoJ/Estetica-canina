<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NovedadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_novedades,
            'titulo' => $this->titulo,
            'resumen' => $this->resumen,
            'descripcion' => $this->descripcion,
            'categoria' => $this->categoria,
            'imagen' => $this->imagen,
            'fecha_publicacion' => optional($this->fecha_publicacion)->format('Y-m-d') ?: $this->fecha_publicacion,
            'estado' => $this->estado,
        ];
    }
}

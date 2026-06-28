<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $persona = $this->persona;

        return [
            'id' => $this->id_usuario,
            'correo' => $this->correo,
            'rol' => $this->rol,
            'estado' => $this->estado,
            'mfa_enabled' => (bool) $this->mfa_enabled,
            'mfa_bypass' => (bool) $this->mfa_bypass,
            'persona' => $persona ? [
                'id' => $persona->id_persona,
                'nombres' => $persona->nombres,
                'apellidos' => $persona->apellidos ?? trim(($persona->apellido_paterno ?? '') . ' ' . ($persona->apellido_materno ?? '')),
                'tipo_doc' => $persona->tipo_doc ?? $persona->tipo_documento ?? null,
                'nro_documento' => $persona->nro_documento,
                'telefono' => $persona->telefono,
                'direccion' => $persona->direccion,
            ] : null,
        ];
    }
}

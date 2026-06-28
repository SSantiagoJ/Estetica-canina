<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $clientePersona = $this->cliente?->persona;
        $empleadoPersona = $this->empleado?->persona;

        return [
            'id' => $this->id_reserva,
            'fecha' => $this->fecha,
            'hora' => $this->hora,
            'estado' => $this->estado,
            'salud' => [
                'enfermedad' => (bool) $this->enfermedad,
                'vacuna' => (bool) $this->vacuna,
                'alergia' => (bool) $this->alergia,
                'descripcion_alergia' => $this->descripcion_alergia,
            ],
            'mascota' => $this->whenLoaded('mascota', fn () => new MascotaResource($this->mascota)),
            'cliente' => $this->whenLoaded('cliente', fn () => [
                'id' => $this->cliente?->id_cliente,
                'nombre' => $clientePersona ? trim(($clientePersona->nombres ?? '') . ' ' . ($clientePersona->apellidos ?? '')) : null,
                'documento' => $clientePersona?->nro_documento,
            ]),
            'empleado' => $this->whenLoaded('empleado', fn () => [
                'id' => $this->empleado?->id_empleado,
                'nombre' => $empleadoPersona ? trim(($empleadoPersona->nombres ?? '') . ' ' . ($empleadoPersona->apellidos ?? '')) : null,
            ]),
            'servicios' => $this->whenLoaded('detalles', fn () => $this->detalles->map(fn ($detalle) => [
                'id_detalle' => $detalle->id_detalle,
                'id_servicio' => $detalle->id_servicio,
                'nombre' => $detalle->servicio?->nombre_servicio,
                'precio_unitario' => (float) $detalle->precio_unitario,
                'igv' => (float) $detalle->igv,
                'total' => (float) $detalle->total,
                'estado' => $detalle->estado,
            ])->values()),
            'atencion' => $this->whenLoaded('atencion', fn () => $this->atencion ? [
                'descripcion' => $this->atencion->descripcion,
                'comentarios' => $this->atencion->comentarios,
            ] : null),
        ];
    }
}

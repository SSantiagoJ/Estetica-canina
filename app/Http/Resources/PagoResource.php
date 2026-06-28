<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PagoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_pago,
            'id_reserva' => $this->id_reserva,
            'monto' => (float) $this->monto,
            'monto_neto' => $this->monto_neto !== null ? (float) $this->monto_neto : null,
            'metodo_pago' => $this->metodo_pago,
            'gateway' => $this->gateway,
            'codigo_operacion' => $this->codigo_operacion,
            'serie' => $this->series,
            'estado_gateway' => $this->estado_gateway,
            'fecha_confirmacion' => optional($this->fecha_confirmacion)->toDateTimeString(),
            'fecha' => $this->fecha,
            'hora' => $this->hora,
            'estado' => $this->estado,
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';
    protected $primaryKey = 'id_pago';
    public $timestamps = false;

    protected $fillable = [
        'id_reserva',
        'monto',
        'monto_neto',
        'metodo_pago',
        'gateway',
        'provider_payment_id',
        'estado_gateway',
        'fecha_confirmacion',
        'fecha',
        'hora',
        'estado',
        'usuario_creacion',
        'series',
        'codigo_operacion',
        'comprobante_path'
    ];

    protected $casts = [
        'fecha_confirmacion' => 'datetime',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva', 'id_reserva');
    }

    public function notificaciones()
    {
        return $this->hasMany(PagoNotificacion::class, 'id_pago', 'id_pago');
    }
}

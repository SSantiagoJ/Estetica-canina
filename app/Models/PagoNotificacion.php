<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoNotificacion extends Model
{
    protected $table = 'pago_notificaciones';
    protected $primaryKey = 'id_pago_notificacion';

    protected $fillable = [
        'id_pago',
        'id_usuario',
        'rol_destino',
        'canal',
        'titulo',
        'mensaje',
        'estado',
        'fecha_envio',
        'usuario_creacion',
        'usuario_actualizacion',
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
    ];

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'id_pago', 'id_pago');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}

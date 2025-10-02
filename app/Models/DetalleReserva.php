<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleReserva extends Model
{
    protected $table = 'detalles_reservas';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_reserva',
        'id_servicio',
        'id_promocion',
        'precio_unitario',
        'igv',
        'total',
        'estado',
        'usuario_creacion',
        'fecha_creacion',
        'usuario_actualizacion',
        'fecha_actualizacion'
    ];
}

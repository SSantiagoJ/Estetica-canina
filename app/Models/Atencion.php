<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atencion extends Model
{
    protected $table = 'atenciones';
    protected $primaryKey = 'id_atencion';
    public $timestamps = false;

    protected $fillable = [
        'id_reserva',
        'descripcion',
        'comentarios',
        'usuario_creacion',
        'fecha_creacion',
        'usuario_actualizacion',
        'fecha_actualizacion',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];

    /**
     * Relación con Reserva
     */
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva', 'id_reserva');
    }
}

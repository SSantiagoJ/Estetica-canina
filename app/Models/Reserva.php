<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservas';
    protected $primaryKey = 'id_reserva';
    public $timestamps = false;

    protected $fillable = [
        'id_mascota',
        'id_cliente',
        'id_usuario',
        'id_empleado',
        'fecha',
        'hora',
        'enfermedad',
        'vacuna',
        'alergia',
        'descripcion_alergia',
        'estado',
        'usuario_creacion',
        'fecha_creacion',
        'usuario_actualizacion',
        'fecha_actualizacion'
    ];
}

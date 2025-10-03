<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservas';
    protected $primaryKey = 'id_reserva';
    public $timestamps = false;

    protected $fillable = [
        'id_mascota','id_cliente','id_usuario','id_empleado',
        'fecha','hora','enfermedad','vacuna','alergia',
        'descripcion_alergia','estado',
        'usuario_creacion','fecha_creacion',
        'usuario_actualizacion','fecha_actualizacion'
    ];

    // 🔗 Relaciones
    public function mascota()
{
    return $this->belongsTo(Mascota::class, 'id_mascota', 'id_mascota');
}

public function detalles()
{
    return $this->hasMany(DetalleReserva::class, 'id_reserva', 'id_reserva');
}

public function servicios()
{
    return $this->belongsToMany(Servicio::class, 'detalles_reservas', 'id_reserva', 'id_servicio');
}
public function cliente()
{
    return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
}

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $primaryKey = 'id_notificacion';
    public $timestamps = false;


    protected $fillable = [
        'id_usuario',
        'tipo',
        'mensaje',
        'fecha_envio',
        'estado',
        'usuario_creacion',
        'fecha_creacion',
        'usuario_actualizacion',
        'fecha_actualizacion'
    ];

    // Relación con el usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}

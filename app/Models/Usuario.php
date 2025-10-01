<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuarios';          // 👈 tu tabla
    protected $primaryKey = 'id_usuario';   // 👈 tu PK real
    public $timestamps = false;             // 👈 si no usas created_at / updated_at

    protected $fillable = [
        'id_persona',
        'correo',
        'contrasena',
        'rol',
        'estado',
        'fecha_creacion',
        'fecha_actualizacion'
    ];

    protected $hidden = [
        'contrasena'
    ];

    // 👇 Laravel por defecto espera "password", pero tu campo es "contrasena"
    public function getAuthPassword()
    {
        return $this->contrasena;
    }
}

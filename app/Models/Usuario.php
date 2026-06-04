<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable; // ✅ Esto debe ir aquí, justo dentro de la clase, al inicio

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'id_persona',
        'correo',
        'contrasena',
        'rol',
        'estado',
        'mfa_enabled',
        'mfa_verified_at',
        'mfa_bypass',
        'fecha_creacion',
        'fecha_actualizacion'
    ];

    protected $hidden = [
        'contrasena'
    ];

    protected $casts = [
        'mfa_enabled' => 'boolean',
        'mfa_verified_at' => 'datetime',
        'mfa_bypass' => 'boolean',
    ];

    // Laravel por defecto espera "password", pero tu campo es "contrasena"
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function getAuthIdentifierName()
    {
        return 'id_usuario';
    }

    // 🔗 Relaciones
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona', 'id_persona');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function empleado()
    {
        return $this->hasOne(Empleado::class, 'id_persona', 'id_persona');
    }

    // ⚡️ Campo usado para enviar correos de notificación
    public function routeNotificationForMail($notification)
    {
        return $this->correo;
    }
}

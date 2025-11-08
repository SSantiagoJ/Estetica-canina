<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'empleados';
    
    // Clave primaria
    protected $primaryKey = 'id_empleado';
    
    // Campos de timestamp personalizados
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    
    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'id_persona',
        'puesto',
    ];

    /**
     * Relación con Persona
     * Un empleado pertenece a una persona
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona', 'id_persona');
    }

    /**
     * Relación con Turnos
     * Un empleado tiene muchos turnos
     */
    public function turnos()
    {
        return $this->hasMany(Turno::class, 'id_empleado', 'id_empleado');
    }
}
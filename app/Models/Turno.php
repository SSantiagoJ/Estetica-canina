<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'turnos_empleados';
    
    // Clave primaria
    protected $primaryKey = 'id_turno';
    
    // Campos de timestamp personalizados
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    
    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'id_empleado',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado',
        'usuario_creacion',
        'usuario_actualizacion',
    ];

    /**
     * Relación con Empleado
     * Un turno pertenece a un empleado
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado', 'id_empleado');
    }
}
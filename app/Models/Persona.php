<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'personas';
    protected $primaryKey = 'id_persona';
    public $timestamps = false;
    
    protected $fillable = [
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'tipo_documento',
        'nro_documento',
        'telefono',
        'direccion',
        'usuario_creacion',
        'usuario_actualizacion'
    ];
    
    protected $dates = [
        'fecha_creacion',
        'fecha_actualizacion'
    ];
    
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id_persona', 'id_persona');
    }
    
    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'id_persona', 'id_persona');
    }
}
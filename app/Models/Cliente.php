<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    public $timestamps = false;
    
    protected $fillable = [
        'id_persona',
        'usuario_creacion',
        'usuario_actualizacion'
    ];
    
    protected $dates = [
        'fecha_creacion',
        'fecha_actualizacion'
    ];
    
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona', 'id_persona');
    }
    
    public function mascotas()
    {
        return $this->hasMany(Mascota::class, 'id_cliente', 'id_cliente');
    }
}
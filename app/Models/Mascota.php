<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Mascota extends Model
{
    protected $table = 'mascotas';
    protected $primaryKey = 'id_mascota';
    public $timestamps = false;

    protected $fillable = [
        'nombre','fecha_nacimiento','sexo','raza','tamano',
        'especie','peso','descripcion','id_cliente','usuario_creacion'
    ];

  
    protected $dates = [
        'fecha_nacimiento',
    ];

    // 🔗 Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_mascota', 'id_mascota');
    }

 
    public function getEdadAttribute()
    {
        if (empty($this->fecha_nacimiento)) {
            return null;
        }

        $dt = $this->fecha_nacimiento instanceof Carbon ? $this->fecha_nacimiento : Carbon::parse($this->fecha_nacimiento);
        return $dt->age;
    }
}

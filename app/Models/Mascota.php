<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mascota extends Model
{
    protected $table = 'mascotas';
    protected $primaryKey = 'id_mascota';
    public $timestamps = false;

    protected $fillable = [
        'nombre', 'fecha_nacimiento', 'sexo', 'raza',
        'tamano', 'especie', 'peso', 'descripcion',
        'id_cliente', 'usuario_creacion'
    ];
}

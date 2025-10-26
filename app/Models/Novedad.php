<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Novedad extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'novedades';
    
    // Clave primaria
    protected $primaryKey = 'id_novedades';
    
    // Campos de timestamp personalizados
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    
    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'titulo',
        'resumen',
        'descripcion',
        'categoria',
        'imagen',
        'fecha_publicacion',
        'estado',
        'usuario_creacion',
        'usuario_actualizacion',
    ];

    // Castear fecha_publicacion como date
    protected $casts = [
        'fecha_publicacion' => 'date',
    ];
}
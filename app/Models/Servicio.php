<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Servicio extends Model
{
    protected $table = 'servicios';
    protected $primaryKey = 'id_servicio';
    public $timestamps = false;

    protected $fillable = [
        'categoria', 
        'tipo_servicio', 
        'nombre_servicio',
        'descripcion',
        'costo', 
        'especie', 
        'duracion', 
        'imagen_referencial',
        'estado', 
        'usuario_creacion', 
        'fecha_creacion',
        'usuario_actualizacion',
        'fecha_actualizacion'
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleReserva::class, 'id_servicio', 'id_servicio');
    }

    public function getImagenUrlAttribute(): string
    {
        $imagen = (string) $this->imagen_referencial;

        if (blank($imagen)) {
            return asset('images/servicios/default.jpg');
        }

        if (Str::startsWith($imagen, ['http://', 'https://', '/'])) {
            return $imagen;
        }

        return route('servicios.imagenes.show', $this);
    }
}

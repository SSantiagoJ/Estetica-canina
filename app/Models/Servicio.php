<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'servicios';
    protected $primaryKey = 'id_servicio';
    public $timestamps = false;

    protected $fillable = [
        'categoria', 'tipo_servicio', 'nombre_servicio',
        'costo', 'especie', 'duracion', 'imagen_referencial',
        'estado', 'usuario_creacion', 'fecha_creacion'
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleReserva::class, 'id_servicio', 'id_servicio');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';
    protected $primaryKey = 'id_feedback';
    
    public $timestamps = false; // Ya que usas fecha_creacion y fecha_actualizacion personalizadas

    protected $fillable = [
        'id_reserva',
        'calificacion',
        'comentarios',
        'usuario_creacion',
        'fecha_creacion',
        'usuario_actualizacion',
        'fecha_actualizacion'
    ];

    protected $dates = [
        'fecha_creacion',
        'fecha_actualizacion'
    ];

    /**
     * Relación con Reserva
     */
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva', 'id_reserva');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    /**
     * Muestra el catálogo público de servicios
     */
    public function index()
    {
        // Obtener solo servicios activos, ordenados por categoría y nombre
        $servicios = Servicio::where('estado', 'A')
            ->orderBy('categoria')
            ->orderBy('nombre_servicio')
            ->get()
            ->groupBy('categoria');

        return view('catalogo', compact('servicios'));
    }
}

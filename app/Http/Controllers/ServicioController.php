<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServicioController extends Controller
{
    /**
     * Muestra la lista de servicios en el panel admin
     */
    public function index()
    {
        $servicios = Servicio::orderBy('categoria', 'asc')
            ->orderBy('nombre_servicio', 'asc')
            ->get();

        return view('admin.servicios.index', compact('servicios'));
    }

    /**
     * Muestra el formulario para crear un nuevo servicio
     */
    public function create()
    {
        $categorias = ['BAÑOS', 'PELUQUERÍA', 'TRATAMIENTOS', 'ADICIONALES'];
        $especies = ['Perro', 'Gato', 'Conejo', 'Hámster', 'Todas'];

        return view('admin.servicios.create', compact('categorias', 'especies'));
    }

    /**
     * Guarda un nuevo servicio en la base de datos
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria' => 'required|string',
            'tipo_servicio' => 'required|string',
            'nombre_servicio' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'costo' => 'required|numeric|min:0',
            'especie' => 'required|string',
            'duracion' => 'nullable|numeric|min:0',
            'imagen_referencial' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('imagen_referencial')) {
            $path = $request->file('imagen_referencial')->store('servicios', 'public');
            $validated['imagen_referencial'] = $path;
        }

        $validated['estado'] = 'A';
        $validated['usuario_creacion'] = auth()->user()->id_usuario ?? 1;

        Servicio::create($validated);

        return redirect()->route('admin.servicios')
            ->with('success', 'Servicio creado exitosamente');
    }

    /**
     * Muestra el formulario para editar un servicio
     */
    public function edit($id)
    {
        $servicio = Servicio::findOrFail($id);
        $categorias = ['BAÑOS', 'PELUQUERÍA', 'TRATAMIENTOS', 'ADICIONALES'];
        $especies = ['Perro', 'Gato', 'Conejo', 'Hámster', 'Todas'];

        return view('admin.servicios.edit', compact('servicio', 'categorias', 'especies'));
    }

    /**
     * Actualiza un servicio en la base de datos
     */
    public function update(Request $request, $id)
    {
        $servicio = Servicio::findOrFail($id);

        $validated = $request->validate([
            'categoria' => 'required|string',
            'tipo_servicio' => 'required|string',
            'nombre_servicio' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'costo' => 'required|numeric|min:0',
            'especie' => 'required|string',
            'duracion' => 'nullable|numeric|min:0',
            'imagen_referencial' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('imagen_referencial')) {
            // Eliminar imagen anterior si existe
            if ($servicio->imagen_referencial) {
                Storage::disk('public')->delete($servicio->imagen_referencial);
            }
            $path = $request->file('imagen_referencial')->store('servicios', 'public');
            $validated['imagen_referencial'] = $path;
        }

        $validated['usuario_actualizacion'] = auth()->user()->id_usuario ?? 1;
        $validated['fecha_actualizacion'] = now();

        $servicio->update($validated);

        return redirect()->route('admin.servicios')
            ->with('success', 'Servicio actualizado exitosamente');
    }

    /**
     * Elimina un servicio
     */
    public function destroy($id)
    {
        $servicio = Servicio::findOrFail($id);

        // Eliminar imagen si existe
        if ($servicio->imagen_referencial) {
            Storage::disk('public')->delete($servicio->imagen_referencial);
        }

        $servicio->delete();

        return redirect()->route('admin.servicios')
            ->with('success', 'Servicio eliminado exitosamente');
    }

    /**
     * Sube una imagen para un servicio (AJAX)
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $path = $request->file('imagen')->store('servicios', 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => asset('storage/' . $path),
        ]);
    }
}
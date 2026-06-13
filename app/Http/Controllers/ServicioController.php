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
            'imagen_referencial' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:51200',
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
            'imagen_referencial' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:51200',
        ]);

        if ($request->hasFile('imagen_referencial')) {
            // Eliminar imagen anterior si existe
            if ($servicio->imagen_referencial && str_starts_with($servicio->getRawOriginal('imagen_referencial'), 'servicios/')) {
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
        if ($servicio->imagen_referencial && str_starts_with($servicio->getRawOriginal('imagen_referencial'), 'servicios/')) {
            Storage::disk('public')->delete($servicio->getRawOriginal('imagen_referencial'));
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
        $data = $request->validate([
            'id_servicio' => 'required|exists:servicios,id_servicio',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:51200',
        ]);

        $servicio = Servicio::findOrFail($data['id_servicio']);

        if ($servicio->imagen_referencial && str_starts_with($servicio->getRawOriginal('imagen_referencial'), 'servicios/')) {
            Storage::disk('public')->delete($servicio->getRawOriginal('imagen_referencial'));
        }

        $path = $request->file('imagen')->store('servicios', 'public');
        $servicio->update([
            'imagen_referencial' => $path,
            'usuario_actualizacion' => auth()->user()->id_usuario ?? 1,
            'fecha_actualizacion' => now(),
        ]);

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => $servicio->fresh()->imagen_url,
        ]);
    }

    public function mostrarImagen(Servicio $servicio)
    {
        $imagen = (string) $servicio->getRawOriginal('imagen_referencial');

        if (filled($imagen)) {
            if (str_starts_with($imagen, 'servicios/') && Storage::disk('public')->exists($imagen)) {
                return $this->responderImagenServicio(Storage::disk('public')->path($imagen));
            }

            $publicPath = public_path('images/servicios/' . ltrim($imagen, '/'));

            if (is_file($publicPath)) {
                return $this->responderImagenServicio($publicPath);
            }
        }

        $defaultPath = public_path('images/servicios/default.jpg');

        if (is_file($defaultPath)) {
            return $this->responderImagenServicio($defaultPath);
        }

        abort(404);
    }

    private function responderImagenServicio(string $path)
    {
        return response()->file($path, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServicioResource;
use App\Models\Servicio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServicioApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $servicios = Servicio::query()
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->input('estado')))
            ->when(!$request->filled('estado'), fn ($query) => $query->where('estado', 'A'))
            ->when($request->filled('categoria'), fn ($query) => $query->where('categoria', $request->input('categoria')))
            ->when($request->filled('tipo_servicio'), fn ($query) => $query->where('tipo_servicio', $request->input('tipo_servicio')))
            ->when($request->filled('especie'), function ($query) use ($request) {
                $query->where(function ($subQuery) use ($request) {
                    $subQuery->where('especie', $request->input('especie'))
                        ->orWhere('especie', 'Todas');
                });
            })
            ->orderBy('categoria')
            ->orderBy('nombre_servicio')
            ->paginate((int) $request->input('per_page', 15));

        return ServicioResource::collection($servicios);
    }

    public function show(Servicio $servicio): ServicioResource
    {
        return new ServicioResource($servicio);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateServicio($request);

        if ($request->hasFile('imagen_referencial')) {
            $data['imagen_referencial'] = $request->file('imagen_referencial')->store('servicios', 'public');
        }

        $data['estado'] = $data['estado'] ?? 'A';
        $data['usuario_creacion'] = $request->user()->correo ?? $request->user()->id_usuario;

        $servicio = Servicio::create($data);

        return $this->success(new ServicioResource($servicio), 'Servicio creado correctamente.', 201);
    }

    public function update(Request $request, Servicio $servicio): JsonResponse
    {
        $data = $this->validateServicio($request, true);

        if ($request->hasFile('imagen_referencial')) {
            $imagenActual = $servicio->getRawOriginal('imagen_referencial');

            if ($imagenActual && str_starts_with($imagenActual, 'servicios/')) {
                Storage::disk('public')->delete($imagenActual);
            }

            $data['imagen_referencial'] = $request->file('imagen_referencial')->store('servicios', 'public');
        }

        $data['usuario_actualizacion'] = $request->user()->correo ?? $request->user()->id_usuario;
        $data['fecha_actualizacion'] = now();

        $servicio->update($data);

        return $this->success(new ServicioResource($servicio->fresh()), 'Servicio actualizado correctamente.');
    }

    public function uploadImage(Request $request, Servicio $servicio): JsonResponse
    {
        $data = $request->validate([
            'imagen' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:51200'],
        ]);

        $imagenActual = $servicio->getRawOriginal('imagen_referencial');

        if ($imagenActual && str_starts_with($imagenActual, 'servicios/')) {
            Storage::disk('public')->delete($imagenActual);
        }

        $servicio->update([
            'imagen_referencial' => $data['imagen']->store('servicios', 'public'),
            'usuario_actualizacion' => $request->user()->correo ?? $request->user()->id_usuario,
            'fecha_actualizacion' => now(),
        ]);

        return $this->success(new ServicioResource($servicio->fresh()), 'Imagen de servicio actualizada correctamente.');
    }

    public function destroy(Servicio $servicio): JsonResponse
    {
        $imagenActual = $servicio->getRawOriginal('imagen_referencial');

        if ($imagenActual && str_starts_with($imagenActual, 'servicios/')) {
            Storage::disk('public')->delete($imagenActual);
        }

        $servicio->delete();

        return $this->success(null, 'Servicio eliminado correctamente.');
    }

    private function validateServicio(Request $request, bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return $request->validate([
            'categoria' => [$required, 'string', 'max:100'],
            'tipo_servicio' => [$required, 'string', 'max:100'],
            'nombre_servicio' => [$required, 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'costo' => [$required, 'numeric', 'min:0'],
            'especie' => [$required, 'string', 'max:50'],
            'duracion' => ['nullable', 'numeric', 'min:0'],
            'estado' => ['nullable', 'in:A,I'],
            'imagen_referencial' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:51200'],
        ]);
    }
}

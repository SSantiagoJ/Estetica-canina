<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\NovedadResource;
use App\Models\Novedad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NovedadApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $novedades = Novedad::query()
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->input('estado')))
            ->when(!$request->filled('estado'), fn ($query) => $query->where('estado', 'A'))
            ->when($request->filled('categoria'), fn ($query) => $query->where('categoria', $request->input('categoria')))
            ->orderByDesc('fecha_publicacion')
            ->paginate((int) $request->input('per_page', 15));

        return NovedadResource::collection($novedades);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateNovedad($request);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('novedades', 'public');
        }

        $data['estado'] = $data['estado'] ?? 'A';
        $data['usuario_creacion'] = $request->user()->correo;

        $novedad = Novedad::create($data);

        return $this->success(new NovedadResource($novedad), 'Novedad creada correctamente.', 201);
    }

    public function show(Novedad $novedad): NovedadResource
    {
        return new NovedadResource($novedad);
    }

    public function update(Request $request, Novedad $novedad): JsonResponse
    {
        $data = $this->validateNovedad($request, true);

        if ($request->hasFile('imagen')) {
            if ($novedad->imagen && str_starts_with($novedad->imagen, 'novedades/')) {
                Storage::disk('public')->delete($novedad->imagen);
            }

            $data['imagen'] = $request->file('imagen')->store('novedades', 'public');
        }

        $data['usuario_actualizacion'] = $request->user()->correo;
        $novedad->update($data);

        return $this->success(new NovedadResource($novedad->fresh()), 'Novedad actualizada correctamente.');
    }

    public function destroy(Novedad $novedad): JsonResponse
    {
        if ($novedad->imagen && str_starts_with($novedad->imagen, 'novedades/')) {
            Storage::disk('public')->delete($novedad->imagen);
        }

        $novedad->delete();

        return $this->success(null, 'Novedad eliminada correctamente.');
    }

    private function validateNovedad(Request $request, bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return $request->validate([
            'titulo' => [$required, 'string', 'max:150'],
            'resumen' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'categoria' => ['nullable', 'string', 'max:80'],
            'fecha_publicacion' => ['nullable', 'date'],
            'estado' => ['nullable', 'in:A,I'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:51200'],
        ]);
    }
}

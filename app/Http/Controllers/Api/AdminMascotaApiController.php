<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\MascotaResource;
use App\Models\Mascota;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminMascotaApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $mascotas = Mascota::with('cliente.persona')
            ->when($request->filled('especie'), fn ($query) => $query->where('especie', $request->input('especie')))
            ->when($request->filled('raza'), fn ($query) => $query->where('raza', $request->input('raza')))
            ->when($request->filled('cliente'), function ($query) use ($request) {
                $query->whereHas('cliente.persona', function ($subQuery) use ($request) {
                    $subQuery->where('nombres', 'like', '%' . $request->input('cliente') . '%')
                        ->orWhere('apellidos', 'like', '%' . $request->input('cliente') . '%');
                });
            })
            ->latest('id_mascota')
            ->paginate((int) $request->input('per_page', 15));

        return MascotaResource::collection($mascotas);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateMascota($request);
        $data['usuario_creacion'] = $request->user()->correo;

        $mascota = Mascota::create($data);

        return $this->success(new MascotaResource($mascota), 'Mascota registrada correctamente.', 201);
    }

    public function show(Mascota $mascota): MascotaResource
    {
        return new MascotaResource($mascota->load('cliente.persona'));
    }

    public function update(Request $request, Mascota $mascota): JsonResponse
    {
        $data = $this->validateMascota($request, true);

        if (Schema::hasColumn('mascotas', 'usuario_actualizacion')) {
            $data['usuario_actualizacion'] = $request->user()->correo;
        }

        if (Schema::hasColumn('mascotas', 'fecha_actualizacion')) {
            $data['fecha_actualizacion'] = now();
        }

        $mascota->update($data);

        return $this->success(new MascotaResource($mascota->fresh()->load('cliente.persona')), 'Mascota actualizada correctamente.');
    }

    public function destroy(Mascota $mascota): JsonResponse
    {
        $mascota->delete();

        return $this->success(null, 'Mascota eliminada correctamente.');
    }

    private function validateMascota(Request $request, bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return $request->validate([
            'id_cliente' => [$required, 'integer', 'exists:clientes,id_cliente'],
            'nombre' => [$required, 'string', 'max:100'],
            'fecha_nacimiento' => [$required, 'date'],
            'sexo' => [$required, 'string', 'max:20'],
            'raza' => ['nullable', 'string', 'max:120'],
            'tamano' => ['nullable', 'string', 'max:20'],
            'especie' => [$required, 'string', 'in:Perro,Gato,Otro'],
            'peso' => ['nullable', 'numeric', 'min:0'],
            'descripcion' => ['nullable', 'string', 'max:500'],
        ]);
    }
}

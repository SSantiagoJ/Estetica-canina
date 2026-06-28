<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\MascotaResource;
use App\Models\Cliente;
use App\Models\Mascota;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class MascotaApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $cliente = $this->clienteActual($request);

        $mascotas = Mascota::where('id_cliente', $cliente->id_cliente)
            ->orderBy('nombre')
            ->get();

        return MascotaResource::collection($mascotas);
    }

    public function store(Request $request): JsonResponse
    {
        $cliente = $this->clienteActual($request);
        $data = $this->validateMascota($request);
        $data['id_cliente'] = $cliente->id_cliente;
        $data['usuario_creacion'] = $request->user()->correo;

        $mascota = Mascota::create($data);

        return $this->success(new MascotaResource($mascota), 'Mascota creada correctamente.', 201);
    }

    public function show(Request $request, Mascota $mascota): MascotaResource|JsonResponse
    {
        $this->autorizarMascota($request, $mascota);

        return new MascotaResource($mascota);
    }

    public function update(Request $request, Mascota $mascota): JsonResponse
    {
        $this->autorizarMascota($request, $mascota);

        $data = $this->validateMascota($request, true);

        if (Schema::hasColumn('mascotas', 'usuario_actualizacion')) {
            $data['usuario_actualizacion'] = $request->user()->correo;
        }

        if (Schema::hasColumn('mascotas', 'fecha_actualizacion')) {
            $data['fecha_actualizacion'] = now();
        }

        $mascota->update($data);

        return $this->success(new MascotaResource($mascota->fresh()), 'Mascota actualizada correctamente.');
    }

    public function destroy(Request $request, Mascota $mascota): JsonResponse
    {
        $this->autorizarMascota($request, $mascota);
        $mascota->delete();

        return $this->success(null, 'Mascota eliminada correctamente.');
    }

    private function validateMascota(Request $request, bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return $request->validate([
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

    private function clienteActual(Request $request): Cliente
    {
        $usuario = $request->user();

        $cliente = Cliente::where('id_persona', $usuario->id_persona)->first();

        if (!$cliente) {
            $cliente = new Cliente([
                'id_persona' => $usuario->id_persona,
                'usuario_creacion' => $usuario->correo,
            ]);
            $cliente->fecha_creacion = now();
            $cliente->fecha_actualizacion = now();
            $cliente->save();
        }

        return $cliente;
    }

    private function autorizarMascota(Request $request, Mascota $mascota): void
    {
        $cliente = $this->clienteActual($request);

        abort_unless((int) $mascota->id_cliente === (int) $cliente->id_cliente, 403, 'No tienes permiso para esta mascota.');
    }
}

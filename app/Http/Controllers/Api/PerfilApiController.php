<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\MascotaResource;
use App\Http\Resources\UsuarioResource;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PerfilApiController extends Controller
{
    use ApiResponse;

    public function show(Request $request): JsonResponse
    {
        $usuario = $request->user()->load('persona');
        $cliente = Cliente::with('mascotas')
            ->where('id_persona', $usuario->id_persona)
            ->first();

        return $this->success([
            'usuario' => new UsuarioResource($usuario),
            'cliente' => $cliente ? [
                'id' => $cliente->id_cliente,
                'mascotas' => MascotaResource::collection($cliente->mascotas),
            ] : null,
        ], 'Perfil obtenido correctamente.');
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'fecha_nacimiento' => ['nullable', 'date'],
        ]);

        $usuario = $request->user()->load('persona');
        $persona = $usuario->persona;

        abort_unless($persona, 404, 'No se encontro informacion de perfil.');

        foreach ($data as $campo => $valor) {
            if (Schema::hasColumn('personas', $campo)) {
                $persona->{$campo} = $valor;
            }
        }

        if (Schema::hasColumn('personas', 'fecha_actualizacion')) {
            $persona->fecha_actualizacion = now();
        }

        $persona->save();

        return $this->success(new UsuarioResource($usuario->fresh()->load('persona')), 'Perfil actualizado correctamente.');
    }
}

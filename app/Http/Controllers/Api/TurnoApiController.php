<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\TurnoResource;
use App\Models\Turno;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TurnoApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $turnos = Turno::with('empleado.persona')
            ->when($request->filled('id_empleado'), fn ($query) => $query->where('id_empleado', $request->input('id_empleado')))
            ->when($request->filled('fecha'), fn ($query) => $query->whereDate('fecha', $request->input('fecha')))
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->input('estado')))
            ->orderByDesc('fecha')
            ->orderBy('hora_inicio')
            ->paginate((int) $request->input('per_page', 15));

        return TurnoResource::collection($turnos);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_empleado' => ['required', 'integer', 'exists:empleados,id_empleado'],
            'fecha' => ['required', 'date'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'estado' => ['nullable', 'in:A,I'],
        ]);

        $data['estado'] = $data['estado'] ?? 'A';
        $data['usuario_creacion'] = $request->user()->correo;

        $turno = Turno::create($data);

        return $this->success(new TurnoResource($turno->load('empleado.persona')), 'Turno creado correctamente.', 201);
    }

    public function show(Turno $turno): TurnoResource
    {
        return new TurnoResource($turno->load('empleado.persona'));
    }

    public function update(Request $request, Turno $turno): JsonResponse
    {
        $data = $request->validate([
            'id_empleado' => ['sometimes', 'integer', 'exists:empleados,id_empleado'],
            'fecha' => ['sometimes', 'date'],
            'hora_inicio' => ['sometimes', 'date_format:H:i'],
            'hora_fin' => ['sometimes', 'date_format:H:i'],
            'estado' => ['nullable', 'in:A,I'],
        ]);

        if (isset($data['hora_inicio'], $data['hora_fin'])) {
            $request->validate(['hora_fin' => ['after:hora_inicio']]);
        }

        if (Schema::hasColumn('turnos_empleados', 'usuario_actualizacion')) {
            $data['usuario_actualizacion'] = $request->user()->correo;
        }

        $turno->update($data);

        return $this->success(new TurnoResource($turno->fresh()->load('empleado.persona')), 'Turno actualizado correctamente.');
    }

    public function destroy(Turno $turno): JsonResponse
    {
        $turno->delete();

        return $this->success(null, 'Turno eliminado correctamente.');
    }
}

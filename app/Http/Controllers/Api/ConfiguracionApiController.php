<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ConfiguracionApiController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        return $this->success([
            'version_api' => 'v1',
            'roles' => ['Cliente', 'Empleado', 'Supervisor', 'Admin'],
            'estados_usuario' => [
                'A' => 'Activo',
                'I' => 'Inactivo',
            ],
            'estados_reserva' => [
                'P' => 'Pendiente',
                'N' => 'Nueva',
                'A' => 'Atendida',
                'C' => 'Cancelada',
            ],
            'especies' => ['Perro', 'Gato', 'Otro'],
            'metodos_http' => [
                'consultar' => 'GET',
                'registrar' => 'POST',
                'actualizar' => 'PUT/PATCH',
                'eliminar' => 'DELETE',
            ],
        ], 'Configuracion de API obtenida correctamente.');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\RazaImagenResource;
use App\Http\Resources\ServicioResource;
use App\Models\RazaImagen;
use App\Models\Servicio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogoApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $servicios = Servicio::query()
            ->where('estado', $request->input('estado', 'A'))
            ->when($request->filled('especie'), function ($query) use ($request) {
                $query->where(function ($subQuery) use ($request) {
                    $subQuery->where('especie', $request->input('especie'))
                        ->orWhere('especie', 'Todas');
                });
            })
            ->orderBy('categoria')
            ->orderBy('nombre_servicio')
            ->get();

        $razas = RazaImagen::where('estado', 'A')
            ->orderBy('especie')
            ->orderBy('raza')
            ->get();

        return $this->success([
            'servicios' => ServicioResource::collection($servicios)->resolve($request),
            'servicios_por_categoria' => $servicios
                ->groupBy('categoria')
                ->map(fn ($items) => ServicioResource::collection($items)->resolve($request)),
            'razas' => RazaImagenResource::collection($razas)->resolve($request),
        ], 'Catalogo obtenido correctamente.');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\RazaImagenResource;
use App\Models\Mascota;
use App\Models\RazaImagen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RazaApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $razasConFoto = RazaImagen::where('estado', 'A')
            ->when($request->filled('especie'), fn ($query) => $query->where('especie', RazaImagen::normalizarEspecie($request->input('especie'))))
            ->orderBy('especie')
            ->orderBy('raza')
            ->get();

        if ($request->boolean('solo_nombres')) {
            $razasRegistradas = Mascota::whereNotNull('raza')
                ->where('raza', '<>', '')
                ->when($request->filled('especie'), fn ($query) => $query->where('especie', RazaImagen::normalizarEspecie($request->input('especie'))))
                ->select('especie', 'raza')
                ->distinct()
                ->get();

            return $this->success(
                $razasRegistradas->merge($razasConFoto)
                    ->map(fn ($item) => [
                        'especie' => RazaImagen::normalizarEspecie($item->especie),
                        'raza' => trim((string) $item->raza),
                    ])
                    ->filter(fn ($item) => filled($item['raza']))
                    ->groupBy('especie')
                    ->map(fn ($items) => $items->pluck('raza')->unique()->sort()->values())
            );
        }

        return RazaImagenResource::collection($razasConFoto);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'especie' => ['required', 'string', 'in:Perro,Gato,Otro'],
            'raza' => ['required', 'string', 'min:2', 'max:120'],
            'imagen' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:51200'],
        ]);

        $especie = RazaImagen::normalizarEspecie($data['especie']);
        $raza = trim($data['raza']);
        $slug = RazaImagen::crearSlugRaza($raza);
        $archivo = $request->file('imagen');
        $extension = strtolower($archivo->getClientOriginalExtension() ?: $archivo->extension());
        $nombreArchivo = Str::slug($especie) . '-' . $slug . '-' . now()->format('YmdHis') . '.' . $extension;
        $path = $archivo->storeAs('razas', $nombreArchivo, 'public');

        $imagen = RazaImagen::where('especie', $especie)
            ->where('slug', $slug)
            ->first();

        if ($imagen && $imagen->imagen_path) {
            Storage::disk('public')->delete($imagen->imagen_path);
        }

        $razaImagen = RazaImagen::updateOrCreate(
            ['especie' => $especie, 'slug' => $slug],
            [
                'raza' => $raza,
                'imagen_path' => $path,
                'tamano_bytes' => $archivo->getSize(),
                'mime_type' => $archivo->getMimeType(),
                'estado' => 'A',
                'usuario_creacion' => $request->user()->correo ?? null,
                'usuario_actualizacion' => $request->user()->correo ?? null,
            ]
        );

        return $this->success(new RazaImagenResource($razaImagen), 'Foto de raza guardada correctamente.', 201);
    }

    public function show(RazaImagen $raza): RazaImagenResource
    {
        return new RazaImagenResource($raza);
    }

    public function destroy(RazaImagen $raza): JsonResponse
    {
        if ($raza->imagen_path) {
            Storage::disk('public')->delete($raza->imagen_path);
        }

        $raza->delete();

        return $this->success(null, 'Foto de raza eliminada correctamente.');
    }
}

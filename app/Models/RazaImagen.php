<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RazaImagen extends Model
{
    protected $table = 'raza_imagenes';
    protected $primaryKey = 'id_raza_imagen';

    protected $fillable = [
        'especie',
        'raza',
        'slug',
        'imagen_path',
        'tamano_bytes',
        'mime_type',
        'estado',
        'usuario_creacion',
        'usuario_actualizacion',
    ];

    public static function normalizarEspecie(?string $especie): string
    {
        $valor = Str::lower(trim((string) $especie));

        return match ($valor) {
            'perro', 'canino' => 'Perro',
            'gato', 'felino' => 'Gato',
            default => 'Otro',
        };
    }

    public static function crearSlugRaza(?string $raza): string
    {
        $slug = Str::slug(Str::lower(trim((string) $raza)), '-');

        return $slug !== '' ? $slug : 'sin-raza';
    }

    public static function fotoPara(?string $especie, ?string $raza): ?string
    {
        if (blank($raza)) {
            return null;
        }

        static $cache = null;

        if ($cache === null) {
            $cache = self::where('estado', 'A')
                ->get()
                ->keyBy(fn (self $item) => $item->especie . '|' . $item->slug);
        }

        $key = self::normalizarEspecie($especie) . '|' . self::crearSlugRaza($raza);
        $imagen = $cache->get($key);

        return $imagen?->url;
    }

    public function getUrlAttribute(): ?string
    {
        if (blank($this->imagen_path)) {
            return null;
        }

        return route('razas.imagenes.show', $this);
    }

    public function getTamanoLegibleAttribute(): string
    {
        $bytes = (int) ($this->tamano_bytes ?? 0);

        if ($bytes <= 0) {
            return 'N/A';
        }

        return number_format($bytes / 1024 / 1024, 2) . ' MB';
    }
}

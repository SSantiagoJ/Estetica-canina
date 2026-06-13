<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raza_imagenes', function (Blueprint $table) {
            $table->id('id_raza_imagen');
            $table->string('especie', 50);
            $table->string('raza', 120);
            $table->string('slug', 140);
            $table->string('imagen_path');
            $table->unsignedBigInteger('tamano_bytes')->nullable();
            $table->string('mime_type', 80)->nullable();
            $table->char('estado', 1)->default('A');
            $table->string('usuario_creacion')->nullable();
            $table->string('usuario_actualizacion')->nullable();
            $table->timestamps();

            $table->unique(['especie', 'slug']);
            $table->index(['estado', 'especie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raza_imagenes');
    }
};

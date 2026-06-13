@extends('layouts.app')

@section('title', 'Crear Servicio - Pet Grooming')

@section('header')
    @include('partials.admin_header')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_toolbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
@endpush

@section('content')
@include('partials.admin_toolbar')

<main class="admin-content admin-role-panel admin-service-form">
    <section class="admin-shell-hero">
        <div>
            <span class="admin-eyebrow">Nuevo servicio</span>
            <h1>Crear Servicio</h1>
            <p>Agrega un servicio al catálogo con costo, duración, especie e imagen referencial.</p>
        </div>
        <a href="{{ route('admin.servicios') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
    </section>

    <section class="admin-page-card">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.servicios.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="categoria" class="form-label fw-semibold">Categoría *</label>
                    <select class="form-select" id="categoria" name="categoria" required>
                        <option value="">Seleccionar categoría</option>
                        @foreach(['Baños', 'Peluquería', 'Tratamientos', 'Servicios Adicionales'] as $categoria)
                            <option value="{{ $categoria }}" {{ old('categoria') === $categoria ? 'selected' : '' }}>
                                {{ $categoria }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tipo_servicio" class="form-label fw-semibold">Tipo de Servicio *</label>
                    <input type="text" class="form-control" id="tipo_servicio" name="tipo_servicio"
                           value="{{ old('tipo_servicio') }}" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nombre_servicio" class="form-label fw-semibold">Nombre del Servicio *</label>
                    <input type="text" class="form-control" id="nombre_servicio" name="nombre_servicio"
                           value="{{ old('nombre_servicio') }}" maxlength="100" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="especie" class="form-label fw-semibold">Especie *</label>
                    <select class="form-select" id="especie" name="especie" required>
                        <option value="">Seleccionar especie</option>
                        @foreach(['Perro', 'Gato', 'Ambos'] as $especie)
                            <option value="{{ $especie }}" {{ old('especie') === $especie ? 'selected' : '' }}>
                                {{ $especie }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="costo" class="form-label fw-semibold">Costo (S/) *</label>
                    <input type="number" class="form-control" id="costo" name="costo"
                           value="{{ old('costo') }}" step="0.01" min="0" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="duracion" class="form-label fw-semibold">Duración (minutos)</label>
                    <input type="number" class="form-control" id="duracion" name="duracion"
                           value="{{ old('duracion') }}" min="0">
                </div>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion"
                          rows="3" maxlength="255">{{ old('descripcion') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="imagen_referencial" class="form-label fw-semibold">Imagen Referencial</label>
                <input type="file" class="form-control" id="imagen_referencial" name="imagen_referencial"
                       accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                <small class="text-muted">Formatos permitidos: JPG, PNG, GIF, WEBP. Tamano maximo: 50MB.</small>
            </div>

            <div class="admin-form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Guardar Servicio
                </button>
                <a href="{{ route('admin.servicios') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i> Cancelar
                </a>
            </div>
        </form>
    </section>
</main>
@endsection

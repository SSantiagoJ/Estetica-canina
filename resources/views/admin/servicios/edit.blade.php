@extends('layouts.app')

@section('title', 'Editar Servicio')

@section('header')
    @include('partials.admin_header')
@endsection

@push('styles')
<style>
    footer {
        display: none !important;
    }
</style>
@endpush

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin_toolbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">

<aside class="admin-toolbar bg-primary text-white shadow-sm d-flex flex-column pt-4">
    <ul class="nav flex-column px-2">
        <li class="nav-item mb-2">
            <a href="{{ route('admin.usuarios') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-users fs-5"></i>
                <span class="fw-semibold">Usuarios</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('admin.mascotas') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-dog fs-5"></i>
                <span class="fw-semibold">Mascotas</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('admin.reservas') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-calendar-check fs-5"></i>
                <span class="fw-semibold">Reservas</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('admin.servicios') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect active">
                <i class="fas fa-cut fs-5"></i>
                <span class="fw-semibold">Servicios</span>
            </a>
        </li>
    </ul>
</aside>

<!-- Contenido principal -->
<main class="admin-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary"><i class="fas fa-edit me-2"></i> Editar Servicio</h2>
        <a href="{{ route('admin.servicios') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
    </div>

    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.servicios.update', $servicio->id_servicio) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="categoria" class="form-label fw-semibold">Categoría *</label>
                        <select class="form-select" id="categoria" name="categoria" required>
                            <option value="">Seleccionar categoría</option>
                            <option value="Baños" {{ (old('categoria', $servicio->categoria) == 'Baños') ? 'selected' : '' }}>Baños</option>
                            <option value="Peluquería" {{ (old('categoria', $servicio->categoria) == 'Peluquería') ? 'selected' : '' }}>Peluquería</option>
                            <option value="Tratamientos" {{ (old('categoria', $servicio->categoria) == 'Tratamientos') ? 'selected' : '' }}>Tratamientos</option>
                            <option value="Servicios Adicionales" {{ (old('categoria', $servicio->categoria) == 'Servicios Adicionales') ? 'selected' : '' }}>Servicios Adicionales</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tipo_servicio" class="form-label fw-semibold">Tipo de Servicio *</label>
                        <input type="text" class="form-control" id="tipo_servicio" name="tipo_servicio" 
                               value="{{ old('tipo_servicio', $servicio->tipo_servicio) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre_servicio" class="form-label fw-semibold">Nombre del Servicio *</label>
                        <input type="text" class="form-control" id="nombre_servicio" name="nombre_servicio" 
                               value="{{ old('nombre_servicio', $servicio->nombre_servicio) }}" maxlength="100" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="especie" class="form-label fw-semibold">Especie *</label>
                        <select class="form-select" id="especie" name="especie" required>
                            <option value="">Seleccionar especie</option>
                            <option value="Perro" {{ (old('especie', $servicio->especie) == 'Perro') ? 'selected' : '' }}>Perro</option>
                            <option value="Gato" {{ (old('especie', $servicio->especie) == 'Gato') ? 'selected' : '' }}>Gato</option>
                            <option value="Ambos" {{ (old('especie', $servicio->especie) == 'Ambos') ? 'selected' : '' }}>Ambos</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="costo" class="form-label fw-semibold">Costo (S/) *</label>
                        <input type="number" class="form-control" id="costo" name="costo" 
                               value="{{ old('costo', $servicio->costo) }}" step="0.01" min="0" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="duracion" class="form-label fw-semibold">Duración (minutos)</label>
                        <input type="number" class="form-control" id="duracion" name="duracion" 
                               value="{{ old('duracion', $servicio->duracion) }}" min="0">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" 
                              rows="3" maxlength="255">{{ old('descripcion', $servicio->descripcion) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Imagen Actual</label>
                    @if($servicio->imagen_referencial)
                        @php
                            if (str_starts_with($servicio->imagen_referencial, 'servicios/')) {
                                $imagenUrl = asset('storage/' . $servicio->imagen_referencial);
                            } else {
                                $imagenUrl = asset('images/servicios/' . $servicio->imagen_referencial);
                            }
                        @endphp
                        <div class="mb-2">
                            <img src="{{ $imagenUrl }}" 
                                 alt="{{ $servicio->nombre_servicio }}" 
                                 class="img-thumbnail"
                                 style="max-width: 200px; max-height: 200px;">
                        </div>
                    @else
                        <p class="text-muted">No hay imagen cargada</p>
                    @endif
                </div>

                <div class="mb-4">
                    <label for="imagen_referencial" class="form-label fw-semibold">Nueva Imagen Referencial</label>
                    <input type="file" class="form-control" id="imagen_referencial" name="imagen_referencial" 
                           accept="image/jpeg,image/png,image/jpg,image/gif">
                    <small class="text-muted">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5MB. Dejar vacío para mantener la imagen actual.</small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Actualizar Servicio
                    </button>
                    <a href="{{ route('admin.servicios') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection

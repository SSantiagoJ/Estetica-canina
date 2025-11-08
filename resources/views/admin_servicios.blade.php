@extends('layouts.app')

@section('title', 'Gestión de Servicios')

@section('header')
    @include('partials.admin_header')
@endsection

@push('styles')
<style>
    footer {
        display: none !important;
    }
    
    /* Asegurar que el contenido use todo el espacio */
    .main-container {
        min-height: 100vh;
    }
</style>
@endpush

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin_toolbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-servicios.css') }}">

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
<main class="admin-content servicios-container">
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-check-circle me-2"></i>¡Éxito!</strong> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="servicios-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-scissors me-3"></i>Gestión de Servicios</h1>
            <a href="{{ route('admin.servicios.create') }}" class="btn-nuevo-servicio">
                <i class="fas fa-plus-circle"></i> Nuevo Servicio
            </a>
        </div>
    </div>

    @php
        $iconosCategorias = [
            'Baños' => 'fa-shower',
            'Peluquería' => 'fa-cut',
            'Tratamientos' => 'fa-spa',
            'Servicios Adicionales' => 'fa-paw'
        ];
        
        $clasesCategoria = [
            'Baños' => 'categoria-banos',
            'Peluquería' => 'categoria-peluqueria',
            'Tratamientos' => 'categoria-tratamientos',
            'Servicios Adicionales' => 'categoria-adicionales'
        ];
    @endphp

    @forelse($servicios as $categoria => $listaServicios)
        <div class="categoria-section {{ $clasesCategoria[$categoria] ?? 'categoria-default' }}">
            <div class="categoria-header">
                <i class="fas {{ $iconosCategorias[$categoria] ?? 'fa-star' }} categoria-icon"></i>
                <span>{{ $categoria }}</span>
                <span class="ms-auto badge bg-white text-dark">{{ $listaServicios->count() }} servicio(s)</span>
            </div>
            
            <div class="table-responsive">
                <table class="servicios-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Costo</th>
                            <th>Especie</th>
                            <th class="text-center">Imagen</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($listaServicios as $servicio)
                        <tr>
                            <td class="codigo">#{{ $servicio->id_servicio }}</td>
                            <td class="nombre">{{ $servicio->nombre_servicio }}</td>
                            <td class="descripcion" title="{{ $servicio->descripcion }}">
                                {{ $servicio->descripcion ?? 'Sin descripción' }}
                            </td>
                            <td class="costo">S/ {{ number_format($servicio->costo, 2) }}</td>
                            <td class="especie">
                                <i class="fas fa-dog me-1"></i>{{ $servicio->especie }}
                            </td>
                            <td class="imagen">
                                @if($servicio->imagen_referencial)
                                    @php
                                        if (str_starts_with($servicio->imagen_referencial, 'servicios/')) {
                                            $imagenUrl = asset('storage/' . $servicio->imagen_referencial);
                                        } else {
                                            $imagenUrl = asset('images/servicios/' . $servicio->imagen_referencial);
                                        }
                                    @endphp
                                    <img src="{{ $imagenUrl }}" 
                                         alt="{{ $servicio->nombre_servicio }}" 
                                         class="thumb-imagen"
                                         title="{{ $servicio->nombre_servicio }}">
                                @else
                                    <button class="btn-subir-imagen" 
                                            data-id="{{ $servicio->id_servicio }}">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </button>
                                @endif
                            </td>
                            <td class="acciones">
                                <a href="{{ route('admin.servicios.edit', $servicio->id_servicio) }}" 
                                   class="btn-accion btn-editar" 
                                   title="Editar servicio">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.servicios.destroy', $servicio->id_servicio) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('¿Estás seguro de eliminar este servicio?\n\n{{ $servicio->nombre_servicio }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-accion btn-eliminar" title="Eliminar servicio">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="fas fa-inbox" style="font-size: 5rem; color: #cbd5e0;"></i>
            <h3 class="mt-4 text-muted">No hay servicios registrados</h3>
            <p class="text-muted">Comienza agregando tu primer servicio</p>
            <a href="{{ route('admin.servicios.create') }}" class="btn-nuevo-servicio mt-3">
                <i class="fas fa-plus-circle"></i> Crear Primer Servicio
            </a>
        </div>
    @endforelse
</main>

<!-- Modal para subir imagen -->
<div class="modal fade" id="modalSubirImagen" tabindex="-1" aria-labelledby="modalSubirImagenLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSubirImagenLabel">Subir Imagen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSubirImagen" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="imagenInput" class="form-label">Seleccionar imagen</label>
                        <input type="file" class="form-control" id="imagenInput" name="imagen" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i> Subir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/servicios.js') }}"></script>
@endsection

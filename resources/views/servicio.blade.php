@extends('layouts.app')

@section('title', 'Gestión de Servicios')

@section('header')
    @include('partials.admin_header')
@endsection

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin/admin_toolbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/admin_dashboard.css') }}">

<!-- Toolbar -->
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
            <a href="{{ route('admin.servicios') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-cut fs-5"></i>
                <span class="fw-semibold">Servicios</span>
            </a>
        </li>
    </ul>
</aside>

<!-- Contenido principal -->
<main class="admin-content">
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary"><i class="fas fa-cut me-2"></i> Gestión de Servicios</h2>
        <a href="{{ route('admin.servicios.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Nuevo Servicio
        </a>
    </div>

    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle text-center mb-0">
                    <thead class="table-primary text-uppercase">
                        <tr>
                            <th>Código</th>
                            <th>Categoría</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Costo</th>
                            <th>Especie</th>
                            <th>Imagen</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($servicios as $servicio)
                        <tr>
                            <td class="fw-semibold">{{ $servicio->id_servicio }}</td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $servicio->categoria }}</span>
                            </td>
                            <td class="fw-semibold">{{ $servicio->nombre_servicio }}</td>
                            <td class="text-muted">{{ Str::limit($servicio->descripcion, 50) }}</td>
                            <td>S/ {{ number_format($servicio->costo, 2) }}</td>
                            <td>{{ $servicio->especie }}</td>
                            <td>
                                @if($servicio->imagen_referencial)
                                    <img src="{{ asset('storage/' . $servicio->imagen_referencial) }}" 
                                         alt="{{ $servicio->nombre_servicio }}" 
                                         class="rounded"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <button class="btn btn-sm btn-outline-secondary btn-subir-imagen" 
                                            data-id="{{ $servicio->id_servicio }}">
                                        <i class="fas fa-upload"></i>
                                    </button>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.servicios.edit', $servicio->id_servicio) }}" 
                                   class="btn btn-sm btn-warning me-1" 
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.servicios.destroy', $servicio->id_servicio) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('¿Estás seguro de que deseas eliminar este servicio?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fs-1 mb-3 d-block"></i>
                                No hay servicios registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
@extends('layouts.app')

@section('title', 'Gestión de Servicios - Pet Grooming')

@section('header')
    @include('partials.admin_header')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_toolbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-servicios.css') }}">
@endpush

@section('content')
@include('partials.admin_toolbar')

<main class="admin-content admin-role-panel servicios-container">
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-check-circle me-2"></i>¡Éxito!</strong> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <section class="admin-shell-hero">
        <div>
            <span class="admin-eyebrow">Catálogo de atención</span>
            <h1>Gestión de Servicios</h1>
            <p>Organiza baños, peluquería, tratamientos y servicios adicionales para tus clientes.</p>
        </div>
        <a href="{{ route('admin.servicios.create') }}" class="btn-nuevo-servicio">
            <i class="fas fa-plus-circle"></i> Nuevo Servicio
        </a>
    </section>

    @php
        $iconosCategorias = [
            'Baños' => 'fa-shower',
            'Peluquería' => 'fa-scissors',
            'Tratamientos' => 'fa-spa',
            'Servicios Adicionales' => 'fa-paw',
        ];

        $clasesCategoria = [
            'Baños' => 'categoria-banos',
            'Peluquería' => 'categoria-peluqueria',
            'Tratamientos' => 'categoria-tratamientos',
            'Servicios Adicionales' => 'categoria-adicionales',
        ];
    @endphp

    @forelse($servicios as $categoria => $listaServicios)
        @php
            $categoriaNombre = str_replace(['BaÃ±os', 'PeluquerÃ­a'], ['Baños', 'Peluquería'], $categoria);
        @endphp
        <section class="categoria-section {{ $clasesCategoria[$categoriaNombre] ?? 'categoria-default' }}">
            <div class="categoria-header">
                <i class="fas {{ $iconosCategorias[$categoriaNombre] ?? 'fa-star' }} categoria-icon"></i>
                <span>{{ $categoriaNombre }}</span>
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
                                    <i class="fas fa-paw me-1"></i>{{ $servicio->especie }}
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
                                                data-id="{{ $servicio->id_servicio }}"
                                                type="button"
                                                title="Subir imagen">
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
        </section>
    @empty
        <section class="admin-page-card">
            <div class="admin-empty-state">
                <i class="fas fa-inbox"></i>
                <h3>No hay servicios registrados</h3>
                <p>Comienza agregando el primer servicio del catálogo.</p>
                <a href="{{ route('admin.servicios.create') }}" class="btn-nuevo-servicio mt-3">
                    <i class="fas fa-plus-circle"></i> Crear Primer Servicio
                </a>
            </div>
        </section>
    @endforelse
</main>

<div class="modal fade" id="modalSubirImagen" tabindex="-1" aria-labelledby="modalSubirImagenLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSubirImagenLabel">Subir Imagen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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
@endsection

@push('scripts')
<script src="{{ asset('js/servicios.js') }}"></script>
@endpush

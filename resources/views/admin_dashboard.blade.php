@extends('layouts.app')

@section('title', 'Gestor de Reservas - Pet Grooming')

@section('header')
    @include('partials.admin_header')
@endsection

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin_toolbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">

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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary"><i class="fas fa-calendar-alt me-2"></i> Bandeja de Reservas</h2>
        <div>
            <button id="btn-editar" class="btn btn-warning me-2" disabled>
                <i class="fas fa-edit"></i> Editar
            </button>
            <button id="btn-guardar" class="btn btn-success" disabled>
                <i class="fas fa-save"></i> Guardar
            </button>
        </div>
    </div>

    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle text-center mb-0" id="tabla-reservas">
                        <thead class="table-primary text-uppercase">
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>Código</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Servicio</th>
                                <th>Cliente</th>
                                <th>Mascota</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservas as $reserva)
                                <tr data-id="{{ $reserva->id_reserva }}">
                                    <td><input type="checkbox" class="select-row"></td>

                                    <td>{{ $reserva->id_reserva }}</td>

                                    {{-- EDITABLE: FECHA --}}
                                    <td class="editable fecha">{{ $reserva->fecha }}</td>

                                    {{-- EDITABLE: HORA --}}
                                    <td class="editable hora">{{ $reserva->hora }}</td>

                                    {{-- NO editable: lista de servicios --}}
                                    <td>
                                        {{ $reserva->detalles->pluck('servicio.nombre_servicio')->implode(', ') ?: 'N/A' }}
                                    </td>

                                    {{-- NO editable: cliente --}}
                                    <td>{{ $reserva->cliente->persona->nombres ?? 'N/A' }}</td>

                                    {{-- EDITABLE: MASCOTA (por nombre). También guardo el id para futura mejora --}}
                                    <td class="editable mascota"
                                        data-mascota-id="{{ $reserva->mascota->id_mascota ?? '' }}">
                                        {{ $reserva->mascota->nombre ?? 'N/A' }}
                                    </td>

                                    {{-- EDITABLE: ESTADO (con data-value para que el JS lea el valor real) --}}
                                    <td class="editable estado" data-value="{{ $reserva->estado }}">
                                        <span class="badge px-3 py-2 rounded-pill
                                            {{ $reserva->estado == 'N' ? 'bg-warning text-dark' : 'bg-success' }}">
                                            {{ $reserva->estado == 'N' ? 'Pendiente' : 'Atendido' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

            </div>
        </div>
    </div>
</main>

<script src="{{ asset('js/admin_reserva.js') }}"></script>
@endsection

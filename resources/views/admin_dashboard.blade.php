@extends('layouts.app')

@section('title', 'Panel Administrativo - Pet Grooming')

@section('header')
    @include('partials.admin_header')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_toolbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
@endpush

@php
    $totalReservas = $reservas->count();
    $pendientes = $reservas->whereIn('estado', ['P', 'N', null])->count();
    $atendidas = $reservas->where('estado', 'A')->count();
    $clientesUnicos = $reservas->pluck('id_cliente')->filter()->unique()->count();

    $estadoLabel = function ($estado) {
        return match ($estado) {
            'A' => 'Atendido',
            'C' => 'Cancelado',
            'P', 'N' => 'Pendiente',
            default => 'Sin estado',
        };
    };

    $estadoClass = function ($estado) {
        return match ($estado) {
            'A' => 'bg-info text-white',
            'C' => 'bg-secondary',
            'P', 'N' => 'bg-warning text-dark',
            default => 'bg-secondary',
        };
    };
@endphp

@section('content')
@include('partials.admin_toolbar')

<main class="admin-content admin-role-panel">
    <section class="admin-shell-hero">
        <div>
            <span class="admin-eyebrow">Centro administrativo</span>
            <h1>Panel Administrativo</h1>
            <p>Gestiona las reservas, clientes, mascotas y servicios desde una vista clara y ordenada.</p>
        </div>
        <div class="admin-hero-card">
            <i class="fas fa-paw"></i>
            <div>
                <strong>{{ $totalReservas }}</strong>
                <span>reservas registradas</span>
            </div>
        </div>
    </section>

    <section class="admin-stats-grid" aria-label="Resumen administrativo">
        <article class="admin-stat-card">
            <span class="admin-stat-icon"><i class="fas fa-calendar-check"></i></span>
            <div>
                <strong>{{ $totalReservas }}</strong>
                <span>Total reservas</span>
            </div>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-icon admin-stat-icon--soft"><i class="fas fa-hourglass-half"></i></span>
            <div>
                <strong>{{ $pendientes }}</strong>
                <span>Pendientes</span>
            </div>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-icon admin-stat-icon--mint"><i class="fas fa-heart-circle-check"></i></span>
            <div>
                <strong>{{ $atendidas }}</strong>
                <span>Atendidas</span>
            </div>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-icon"><i class="fas fa-users"></i></span>
            <div>
                <strong>{{ $clientesUnicos }}</strong>
                <span>Clientes con reserva</span>
            </div>
        </article>
    </section>

    <section class="admin-page-card">
        <div class="admin-section-heading">
            <div>
                <span class="admin-eyebrow">Agenda</span>
                <h2>Bandeja de Reservas</h2>
            </div>
            <div class="admin-actions">
                <button id="btn-editar" class="btn btn-secondary" disabled>
                    <i class="fas fa-edit me-1"></i> Editar
                </button>
                <button id="btn-guardar" class="btn btn-primary" disabled>
                    <i class="fas fa-save me-1"></i> Guardar
                </button>
            </div>
        </div>

        <div class="table-responsive admin-table-wrap">
            <table class="table table-hover align-middle text-center mb-0 admin-table" id="tabla-reservas">
                <thead>
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
                    @forelse($reservas as $reserva)
                        <tr data-id="{{ $reserva->id_reserva }}">
                            <td><input type="checkbox" class="select-row"></td>
                            <td class="fw-bold">#{{ $reserva->id_reserva }}</td>
                            <td class="editable fecha">{{ $reserva->fecha ?? 'N/A' }}</td>
                            <td class="editable hora">{{ $reserva->hora ?? 'N/A' }}</td>
                            <td>{{ $reserva->detalles->pluck('servicio.nombre_servicio')->filter()->implode(', ') ?: 'N/A' }}</td>
                            <td>{{ trim(($reserva->cliente->persona->nombres ?? '') . ' ' . ($reserva->cliente->persona->apellido_paterno ?? '')) ?: 'N/A' }}</td>
                            <td class="editable mascota" data-mascota-id="{{ $reserva->mascota->id_mascota ?? '' }}">
                                {{ $reserva->mascota->nombre ?? 'N/A' }}
                            </td>
                            <td class="editable estado" data-value="{{ $reserva->estado }}">
                                <span class="badge {{ $estadoClass($reserva->estado) }}">
                                    {{ $estadoLabel($reserva->estado) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="admin-empty-state">
                                    <i class="fas fa-calendar-day"></i>
                                    <h3>No hay reservas registradas</h3>
                                    <p>Cuando los clientes agenden citas, aparecerán en esta bandeja.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/admin_reserva.js') }}"></script>
@endpush

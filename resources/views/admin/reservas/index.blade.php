@extends('layouts.app')

@section('title', 'Reservas - Pet Grooming')

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
    $canceladas = $reservas->where('estado', 'C')->count();

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
            <span class="admin-eyebrow">Agenda general</span>
            <h1>Reservas</h1>
            <p>Supervisa todas las citas registradas y su estado de atención.</p>
        </div>
        <div class="admin-hero-card">
            <i class="fas fa-calendar-check"></i>
            <div>
                <strong>{{ $totalReservas }}</strong>
                <span>reservas totales</span>
            </div>
        </div>
    </section>

    <section class="admin-stats-grid" aria-label="Resumen de reservas">
        <article class="admin-stat-card">
            <span class="admin-stat-icon"><i class="fas fa-calendar-days"></i></span>
            <div>
                <strong>{{ $totalReservas }}</strong>
                <span>Total</span>
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
            <span class="admin-stat-icon admin-stat-icon--mint"><i class="fas fa-circle-check"></i></span>
            <div>
                <strong>{{ $atendidas }}</strong>
                <span>Atendidas</span>
            </div>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-icon"><i class="fas fa-ban"></i></span>
            <div>
                <strong>{{ $canceladas }}</strong>
                <span>Canceladas</span>
            </div>
        </article>
    </section>

    <section class="admin-page-card">
        <div class="admin-section-heading">
            <div>
                <span class="admin-eyebrow">Historial</span>
                <h2>Listado de Reservas</h2>
            </div>
        </div>

        <div class="table-responsive admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Servicio</th>
                        <th>Cliente</th>
                        <th>Mascota</th>
                        <th>Vacuna</th>
                        <th>Enfermedad</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservas as $reserva)
                        <tr>
                            <td class="fw-bold">#{{ $reserva->id_reserva }}</td>
                            <td>{{ $reserva->fecha ?? 'N/A' }}</td>
                            <td>{{ $reserva->hora ?? 'N/A' }}</td>
                            <td>{{ $reserva->detalles->pluck('servicio.nombre_servicio')->filter()->implode(', ') ?: 'N/A' }}</td>
                            <td>{{ trim(($reserva->cliente->persona->nombres ?? '') . ' ' . ($reserva->cliente->persona->apellido_paterno ?? '')) ?: 'N/A' }}</td>
                            <td>{{ $reserva->mascota->nombre ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $reserva->vacuna ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $reserva->vacuna ? 'Sí' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $reserva->enfermedad ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                    {{ $reserva->enfermedad ? 'Sí' : 'No' }}
                                </span>
                            </td>
                            <td><span class="badge {{ $estadoClass($reserva->estado) }}">{{ $estadoLabel($reserva->estado) }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="admin-empty-state">
                                    <i class="fas fa-calendar-day"></i>
                                    <h3>No hay reservas registradas</h3>
                                    <p>Cuando los clientes agenden citas, aparecerán aquí.</p>
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

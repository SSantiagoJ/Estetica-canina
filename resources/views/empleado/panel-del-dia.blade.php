@extends('layouts.header')

@section('title', 'Panel del Día - Pet Grooming')

@section('header')
    @include('partials.admin_header')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_toolbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/empleado-panel.css') }}">
@endpush

@section('content')
<aside class="admin-toolbar bg-primary text-white shadow-sm d-flex flex-column pt-4">
    <ul class="nav flex-column px-2">
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.panel.del.dia') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect active">
                <i class="fas fa-tachometer-alt fs-5"></i>
                <span class="fw-semibold">Panel del Día</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.dashboard') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-chart-line fs-5"></i>
                <span class="fw-semibold">Dashboard</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.bandeja.reservas') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-calendar-check fs-5"></i>
                <span class="fw-semibold">Bandeja de Reservas</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.gestionar.turnos') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-clock fs-5"></i>
                <span class="fw-semibold">Gestionar Turnos</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.gestionar.novedades') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-bell fs-5"></i>
                <span class="fw-semibold">Gestionar Novedades</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.notificaciones') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-envelope fs-5"></i>
                <span class="fw-semibold">Notificaciones</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('home') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-home fs-5"></i>
                <span class="fw-semibold">Web Cliente</span>
            </a>
        </li>
    </ul>
</aside>

<main class="admin-content empleado-day-panel">
    <section class="day-hero">
        <div>
            <span class="day-eyebrow">Centro de cuidado</span>
            <h1>Panel del Día</h1>
            <p>
                {{ $empleadoActual ? 'Tus reservas asignadas para hoy.' : 'Vista general de las reservas del equipo para hoy.' }}
            </p>
        </div>
        <div class="day-hero-card">
            <i class="fas fa-calendar-day"></i>
            <div>
                <strong>{{ \Carbon\Carbon::parse($fechaHoy)->format('d/m/Y') }}</strong>
                <span>{{ $empleadoActual->persona->nombres ?? 'Equipo completo' }}</span>
            </div>
        </div>
    </section>

    <section class="day-stats-grid" aria-label="Resumen del día">
        <article class="day-stat">
            <span class="day-stat-icon day-stat-icon--rose"><i class="fas fa-list-check"></i></span>
            <div>
                <strong>{{ $stats['total_reservas'] }}</strong>
                <span>Total de reservas</span>
            </div>
        </article>
        <article class="day-stat">
            <span class="day-stat-icon day-stat-icon--honey"><i class="fas fa-hourglass-half"></i></span>
            <div>
                <strong>{{ $stats['reservas_pendientes'] }}</strong>
                <span>Pendientes</span>
            </div>
        </article>
        <article class="day-stat">
            <span class="day-stat-icon day-stat-icon--mint"><i class="fas fa-heart-circle-check"></i></span>
            <div>
                <strong>{{ $stats['reservas_atendidas'] }}</strong>
                <span>Atendidas</span>
            </div>
        </article>
        <article class="day-stat">
            <span class="day-stat-icon day-stat-icon--lavender"><i class="fas fa-users"></i></span>
            <div>
                <strong>{{ $statsGenerales['clientes_atendidos'] }}</strong>
                <span>Clientes atendidos</span>
            </div>
        </article>
    </section>

    <section class="day-layout">
        <div class="day-main-column">
            <section class="day-section">
                <div class="section-heading">
                    <div>
                        <span class="day-eyebrow">Agenda</span>
                        <h2>Reservas de hoy</h2>
                    </div>
                    <a href="{{ route('empleado.bandeja.reservas') }}" class="panel-link">
                        <i class="fas fa-arrow-right"></i>
                        <span>Ver bandeja</span>
                    </a>
                </div>

                @forelse($reservasDelDia as $reserva)
                    @php
                        $estado = $reserva->estado;
                        $pendiente = in_array($estado, ['P', 'N'], true);
                        $serviciosReserva = $reserva->detalles->pluck('servicio.nombre_servicio')->filter()->implode(', ');
                    @endphp
                    <article class="reservation-row">
                        <div class="reservation-time">
                            <strong>{{ \Carbon\Carbon::parse($reserva->hora)->format('H:i') }}</strong>
                            <span>{{ $pendiente ? 'Pendiente' : ($estado === 'A' ? 'Atendida' : 'Otro estado') }}</span>
                        </div>
                        <div class="reservation-body">
                            <div class="reservation-title-line">
                                <h3>{{ $reserva->mascota->nombre ?? 'Mascota sin nombre' }}</h3>
                                <span class="status-pill {{ $pendiente ? 'status-pill--pending' : 'status-pill--done' }}">
                                    {{ $pendiente ? 'Pendiente' : ($estado === 'A' ? 'Atendida' : $estado) }}
                                </span>
                            </div>
                            <p>
                                <i class="fas fa-user"></i>
                                {{ $reserva->cliente->persona->nombres ?? 'Cliente' }} {{ $reserva->cliente->persona->apellidos ?? '' }}
                            </p>
                            <p>
                                <i class="fas fa-scissors"></i>
                                {{ $serviciosReserva ?: 'Servicio no registrado' }}
                            </p>
                            @if(!$empleadoActual)
                                <p>
                                    <i class="fas fa-user-nurse"></i>
                                    {{ $reserva->empleado->persona->nombres ?? 'Sin empleado asignado' }}
                                </p>
                            @endif
                        </div>
                        <div class="reservation-actions">
                            @if($pendiente)
                                <form method="POST" action="{{ route('empleado.reservas.atender', $reserva->id_reserva) }}">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="panel-action-btn">
                                        <i class="fas fa-check"></i>
                                        <span>Atender</span>
                                    </button>
                                </form>
                            @else
                                <span class="soft-note">Finalizada</span>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="empty-panel">
                        <i class="fas fa-paw"></i>
                        <h3>No hay reservas para hoy</h3>
                        <p>Cuando se asignen citas para este día, aparecerán aquí con su hora, mascota y servicio.</p>
                    </div>
                @endforelse
            </section>
        </div>

        <aside class="day-side-column">
            <section class="day-section next-card">
                <span class="day-eyebrow">Siguiente cita</span>
                @if($stats['proxima_reserva'])
                    <h2>{{ \Carbon\Carbon::parse($stats['proxima_reserva']->hora)->format('H:i') }}</h2>
                    <p>{{ $stats['proxima_reserva']->mascota->nombre ?? 'Mascota' }} espera su atención.</p>
                @else
                    <h2>Sin pendientes</h2>
                    <p>No hay citas pendientes para este momento.</p>
                @endif
            </section>

            <section class="day-section">
                <span class="day-eyebrow">Este mes</span>
                <div class="mini-metric">
                    <strong>{{ $statsGenerales['reservas_mes'] }}</strong>
                    <span>Reservas programadas</span>
                </div>
            </section>

            <section class="day-section">
                <div class="section-heading compact">
                    <div>
                        <span class="day-eyebrow">Favoritos</span>
                        <h2>Servicios populares</h2>
                    </div>
                </div>
                @forelse($statsGenerales['servicios_populares'] as $servicio)
                    <div class="service-chip-row">
                        <span>{{ $servicio->nombre_servicio }}</span>
                        <strong>{{ $servicio->total }}</strong>
                    </div>
                @empty
                    <p class="muted-copy">Aún no hay datos suficientes.</p>
                @endforelse
            </section>

            <section class="day-section">
                <div class="section-heading compact">
                    <div>
                        <span class="day-eyebrow">Opiniones</span>
                        <h2>Comentarios 5 estrellas</h2>
                    </div>
                </div>
                @forelse($comentarios5Estrellas as $comentario)
                    <blockquote class="review-card">
                        <p>{{ $comentario->comentarios }}</p>
                        <footer>
                            {{ $comentario->nombres }} - {{ $comentario->mascota_nombre }}
                        </footer>
                    </blockquote>
                @empty
                    <p class="muted-copy">Los comentarios destacados aparecerán aquí.</p>
                @endforelse
            </section>
        </aside>
    </section>
</main>
@endsection

@extends('layouts.header')

@section('title', 'Ingresos - Pet Grooming')

@section('header')
    @include('partials.admin_header')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_toolbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/empleado-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('css/empleado-ingresos.css') }}">
@endpush

@section('content')
<aside class="admin-toolbar bg-primary text-white shadow-sm d-flex flex-column pt-4">
    <ul class="nav flex-column px-2">
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.panel.del.dia') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-tachometer-alt fs-5"></i>
                <span class="fw-semibold">Panel del Dia</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.dashboard') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-chart-line fs-5"></i>
                <span class="fw-semibold">Dashboard</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.ingresos') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect active">
                <i class="fas fa-sack-dollar fs-5"></i>
                <span class="fw-semibold">Ingresos</span>
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

<main class="admin-content ingresos-panel">
    @php
        $nombreEmpleado = $empleadoActual && $empleadoActual->persona
            ? trim(($empleadoActual->persona->nombres ?? '') . ' ' . ($empleadoActual->persona->apellidos ?? ''))
            : 'Equipo completo';
    @endphp

    <section class="income-hero">
        <div>
            <span class="income-eyebrow">Pagos confirmados</span>
            <h1>Control de ingresos</h1>
            <p>
                {{ $puedeVerDetalle
                    ? 'Supervisa pagos confirmados, comprobantes y recaudacion del mes.'
                    : 'Consulta tu monto recaudado del mes sin exponer datos de otros clientes.' }}
            </p>
        </div>
        <div class="income-hero-actions">
            <form method="GET" action="{{ route('empleado.ingresos') }}" class="income-month-filter">
                <label for="mes">Mes</label>
                <div>
                    <input type="month" id="mes" name="mes" value="{{ $mesSeleccionado }}">
                    <button type="submit">
                        <i class="fas fa-filter"></i>
                        <span>Filtrar</span>
                    </button>
                </div>
            </form>

            @if($puedeVerDetalle)
                <a href="{{ route('empleado.ingresos.excel', ['mes' => $mesSeleccionado]) }}" class="income-export-link">
                    <i class="fas fa-file-excel"></i>
                    <span>Descargar Excel</span>
                </a>
            @endif
        </div>
    </section>

    <section class="income-stats-grid {{ $puedeVerDetalle ? '' : 'income-stats-grid--single' }}" aria-label="Resumen de ingresos">
        <article class="income-stat-card income-stat-card--main">
            <span><i class="fas fa-coins"></i></span>
            <div>
                <small>Total del mes</small>
                <strong>S/ {{ number_format($totalMes, 2) }}</strong>
            </div>
        </article>
        @if($puedeVerDetalle)
            <article class="income-stat-card">
                <span><i class="fas fa-calendar-day"></i></span>
                <div>
                    <small>Ingresos de hoy</small>
                    <strong>S/ {{ number_format($totalHoy, 2) }}</strong>
                </div>
            </article>
            <article class="income-stat-card">
                <span><i class="fas fa-receipt"></i></span>
                <div>
                    <small>Pagos confirmados</small>
                    <strong>{{ $cantidadPagos }}</strong>
                </div>
            </article>
            <article class="income-stat-card">
                <span><i class="fas fa-chart-line"></i></span>
                <div>
                    <small>Ticket promedio</small>
                    <strong>S/ {{ number_format($ticketPromedio, 2) }}</strong>
                </div>
            </article>
        @endif
    </section>

    @if(!$puedeVerDetalle)
        <section class="income-employee-summary">
            <div class="income-employee-badge">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <span class="income-eyebrow">Vista de empleado</span>
                <h2>{{ $nombreEmpleado ?: 'Empleado' }}</h2>
                <p>
                    Por seguridad, este rol solo visualiza el monto mensual recaudado de sus propias reservas atendidas o pagadas.
                </p>
            </div>
        </section>
    @else
        <section class="income-layout">
            <div class="income-main-column">
                <section class="income-card">
                    <div class="income-section-heading">
                        <div>
                            <span class="income-eyebrow">Comprobantes</span>
                            <h2>Pagos confirmados</h2>
                        </div>
                        <strong>{{ $inicioMes->format('d/m/Y') }} - {{ $finMes->format('d/m/Y') }}</strong>
                    </div>

                    <div class="table-responsive income-table-wrap">
                        <table class="table income-table">
                            <thead>
                                <tr>
                                    <th>Boleta</th>
                                    <th>Cliente</th>
                                    <th>Mascota</th>
                                    <th>Empleado</th>
                                    <th>Metodo</th>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pagos as $pago)
                                    @php
                                        $reserva = $pago->reserva;
                                        $clientePersona = $reserva->cliente->persona ?? null;
                                        $empleadoPersona = $reserva->empleado->persona ?? null;
                                        $clienteNombre = $clientePersona ? trim(($clientePersona->nombres ?? '') . ' ' . ($clientePersona->apellidos ?? '')) : 'Cliente';
                                        $empleadoNombre = $empleadoPersona ? trim(($empleadoPersona->nombres ?? '') . ' ' . ($empleadoPersona->apellidos ?? '')) : 'Sin asignar';
                                    @endphp
                                    <tr>
                                        <td>{{ $pago->series ?? ('Pago #' . $pago->id_pago) }}</td>
                                        <td>{{ $clienteNombre }}</td>
                                        <td>{{ $reserva->mascota->nombre ?? 'Mascota' }}</td>
                                        <td>{{ $empleadoNombre }}</td>
                                        <td>
                                            <span class="income-method-pill">{{ $pago->metodo_pago === 'simulado' ? 'Pago confirmado' : ucfirst(str_replace('_', ' ', $pago->metodo_pago)) }}</span>
                                        </td>
                                        <td>{{ $pago->fecha }} {{ substr((string) $pago->hora, 0, 5) }}</td>
                                        <td><strong>S/ {{ number_format($pago->monto, 2) }}</strong></td>
                                        <td>
                                            <a href="{{ route('reservas.boleta.descargar', $pago->id_pago) }}" class="income-action-link">
                                                <i class="fas fa-file-arrow-down"></i>
                                                <span>Boleta</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="income-empty">
                                                <i class="fas fa-paw"></i>
                                                <strong>No hay pagos confirmados en este mes.</strong>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <aside class="income-side-column">
                <section class="income-card">
                    <div class="income-section-heading compact">
                        <div>
                            <span class="income-eyebrow">Equipo</span>
                            <h2>Por empleado</h2>
                        </div>
                    </div>

                    <div class="income-team-list">
                        @forelse($ingresosPorEmpleado as $item)
                            <article>
                                <div>
                                    <strong>{{ $item['empleado'] }}</strong>
                                    <small>{{ $item['pagos'] }} pagos</small>
                                </div>
                                <span>S/ {{ number_format($item['total'], 2) }}</span>
                            </article>
                        @empty
                            <p class="income-muted">Aun no hay recaudacion del equipo en este mes.</p>
                        @endforelse
                    </div>
                </section>

                <section class="income-card">
                    <div class="income-section-heading compact">
                        <div>
                            <span class="income-eyebrow">Alertas</span>
                            <h2>Notificaciones</h2>
                        </div>
                    </div>

                    <div class="income-notification-list">
                        @forelse($notificacionesPago as $notificacion)
                            <article>
                                <i class="fas fa-bell"></i>
                                <div>
                                    <strong>{{ $notificacion->titulo }}</strong>
                                    <p>{{ $notificacion->mensaje }}</p>
                                </div>
                            </article>
                        @empty
                            <p class="income-muted">Las notificaciones de pago apareceran aqui.</p>
                        @endforelse
                    </div>
                </section>
            </aside>
        </section>
    @endif
</main>
@endsection

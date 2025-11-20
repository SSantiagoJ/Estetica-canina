@extends('layouts.app')

@section('title', 'Panel del Día - Estética Canina')

@section('header')
    @include('partials.admin_header')
@endsection

@section('content')

<!-- Agregar los CSS del admin -->
<link rel="stylesheet" href="{{ asset('css/admin_toolbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">

<!-- Toolbar lateral para empleado -->
<aside class="admin-toolbar bg-primary text-white shadow-sm d-flex flex-column pt-4">
    <ul class="nav flex-column px-2">
        <!-- Panel del Día -->
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.panel.del.dia') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect active">
                <i class="fas fa-tachometer-alt fs-5"></i>
                <span class="fw-semibold">Panel del Día</span>
            </a>
        </li>

        <!-- Bandeja de Reservas -->
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.bandeja.reservas') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-calendar-check fs-5"></i>
                <span class="fw-semibold">Bandeja de Reservas</span>
            </a>
        </li>
        
        <!-- Gestión de turnos -->
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.gestionar.turnos') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-clock fs-5"></i>
                <span class="fw-semibold">Gestionar Turnos</span>
            </a>
        </li>
        
        <!-- Gestión de novedades -->
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.gestionar.novedades') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-bell fs-5"></i>
                <span class="fw-semibold">Gestionar Novedades</span>
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('empleado.notificaciones') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-bell fs-5"></i>
                <span class="fw-semibold">Gestionar Notificaciones</span>
            </a>
        </li>

        <!-- Enlace al web cliente -->
        <li class="nav-item mb-2">
            <a href="{{ route('home') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-home fs-5"></i>
                <span class="fw-semibold">Web Cliente</span>
            </a>
        </li>
    </ul>
</aside>

<!-- Contenedor principal -->
<main class="admin-content">
    <!-- Card principal -->
    <div class="card shadow-sm border-0">
        <!-- Título principal -->
        <h2 class="fw-bold text-dark text-center">
            <i class="fas fa-calendar-day me-2"></i> Panel del Día - {{ \Carbon\Carbon::today()->format('d/m/Y') }}
        </h2>
        
        <div class="card-body">
            <!-- Sección de estadísticas rápidas -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-calendar-alt fs-2"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $stats['total_reservas'] ?? 0 }}</h5>
                                    <small>Total Reservas Hoy</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-clock fs-2"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $stats['reservas_pendientes'] ?? 0 }}</h5>
                                    <small>Pendientes</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-check-circle fs-2"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $stats['reservas_atendidas'] ?? 0 }}</h5>
                                    <small>Atendidas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-chart-line fs-2"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $statsGenerales['reservas_mes'] ?? 0 }}</h5>
                                    <small>Este Mes</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de filtros -->
            <div class="filters-section mb-4">
                <h5 class="mb-3 text-secondary">
                    <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                </h5>
                
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" id="filtroEstado">
                            <option value="">Todos</option>
                            <option value="P">PENDIENTE</option>
                            <option value="A">ATENDIDO</option>
                            <option value="C">CANCELADO</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nombre cliente</label>
                        <input type="text" class="form-control" id="filtroCliente" placeholder="Ingrese nombre">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nombre mascota</label>
                        <input type="text" class="form-control" id="filtroMascota" placeholder="Escribe aquí">
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button class="btn btn-primary flex-grow-1" onclick="filtrarReservas()">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                        <button class="btn btn-secondary" onclick="limpiarFiltros()" title="Limpiar filtros">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabla de reservas del día -->
            <div class="table-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-secondary mb-0">
                        <i class="fas fa-list me-2"></i>Mis Reservas de Hoy
                    </h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Código</th>
                                <th>Cliente</th>
                                <th>Mascota</th>
                                <th>Servicio</th>
                                <th>Duración</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaReservas">
                            @forelse($reservasDelDia as $reserva)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ \Carbon\Carbon::parse($reserva->hora)->format('H:i') }}
                                    </span>
                                </td>
                                <td>{{ $reserva->id_reserva }}</td>
                                <td>
                                    {{ $reserva->cliente->persona->nombres ?? 'N/A' }} 
                                    {{ $reserva->cliente->persona->apellidos ?? '' }}
                                </td>
                                <td>
                                    <strong>{{ $reserva->mascota->nombre ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $reserva->mascota->especie ?? '' }}</small>
                                </td>
                                <td>
                                    @if($reserva->detalles->isNotEmpty())
                                        {{ $reserva->detalles->first()->servicio->nombre ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($reserva->detalles->isNotEmpty() && $reserva->detalles->first()->servicio)
                                        {{ $reserva->detalles->first()->servicio->duracion ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($reserva->estado == 'P')
                                        <span class="badge bg-warning text-dark">PENDIENTE</span>
                                    @elseif($reserva->estado == 'A')
                                        <span class="badge bg-success">ATENDIDO</span>
                                    @elseif($reserva->estado == 'C')
                                        <span class="badge bg-secondary">CANCELADO</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalVerReserva"
                                                onclick="cargarReserva({{ $reserva->id_reserva }})">
                                            <i class="fas fa-eye me-1"></i>Ver
                                        </button>
                                        
                                        @if($reserva->estado == 'P')
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-success"
                                                    onclick="marcarComoAtendido({{ $reserva->id_reserva }})">
                                                <i class="fas fa-check me-1"></i>Atender
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-calendar-times fs-2 mb-2"></i>
                                        <p>No tienes reservas asignadas para hoy</p>
                                        <small>¡Aprovecha para organizar tu día!</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Función para filtrar reservas
function filtrarReservas() {
    const estado = document.getElementById('filtroEstado').value;
    const cliente = document.getElementById('filtroCliente').value.toLowerCase();
    const mascota = document.getElementById('filtroMascota').value.toLowerCase();
    
    const filas = document.querySelectorAll('#tablaReservas tr');
    
    filas.forEach(fila => {
        if (fila.querySelector('td') === null) return; // Skip header row
        
        const estadoFila = fila.querySelector('.badge').textContent.trim();
        const clienteFila = fila.cells[2].textContent.toLowerCase();
        const mascotaFila = fila.cells[3].textContent.toLowerCase();
        
        let mostrar = true;
        
        // Filtro por estado
        if (estado && !estadoFila.includes(estado === 'P' ? 'PENDIENTE' : estado === 'A' ? 'ATENDIDO' : 'CANCELADO')) {
            mostrar = false;
        }
        
        // Filtro por cliente
        if (cliente && !clienteFila.includes(cliente)) {
            mostrar = false;
        }
        
        // Filtro por mascota
        if (mascota && !mascotaFila.includes(mascota)) {
            mostrar = false;
        }
        
        fila.style.display = mostrar ? '' : 'none';
    });
}

// Función para limpiar filtros
function limpiarFiltros() {
    document.getElementById('filtroEstado').value = '';
    document.getElementById('filtroCliente').value = '';
    document.getElementById('filtroMascota').value = '';
    
    // Mostrar todas las filas
    const filas = document.querySelectorAll('#tablaReservas tr');
    filas.forEach(fila => {
        fila.style.display = '';
    });
}

// Función para cargar información de una reserva (placeholder)
function cargarReserva(idReserva) {
    console.log('Cargar reserva:', idReserva);
    // Aquí iría la lógica para cargar los detalles en el modal
}

// Función para marcar como atendido
function marcarComoAtendido(idReserva) {
    if (confirm('¿Está seguro que desea marcar esta reserva como atendida?')) {
        // Aquí iría la lógica para actualizar el estado
        console.log('Marcar como atendido:', idReserva);
    }
}
</script>

@endsection
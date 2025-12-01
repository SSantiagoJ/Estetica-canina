@extends('layouts.header')

@section('title', 'Panel del Día - Estética Canina')

@section('header')
    @include('partials.admin_header')
@endsection

@section('content')

<!-- Agregar los CSS del admin -->
<link rel="stylesheet" href="{{ asset('css/admin_toolbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/pasarela.css') }}">
<!-- CSS de Swiper (Librería para el slider) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

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

            <!-- Pasarela horizontal de reservas -->
            <div class="pasarela-container px-3">
                <h5 class="text-secondary ms-2 mb-3">
                    <i class="fas fa-stream me-2"></i>Línea de Tiempo (Hoy)
                </h5>

                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">

                        @forelse($reservasDelDia as $reserva)
                            @php
                                // Lógica rápida de colores
                                $colorBarra = 'status-pendiente';
                                $textoEstado = 'Pendiente';
                                $colorBadge = 'bg-warning text-dark';

                                if($reserva->estado == 'A') {
                                    $colorBarra = 'status-confirmado';
                                    $textoEstado = 'Atendido';
                                    $colorBadge = 'bg-success';
                                } elseif($reserva->estado == 'C') {
                                    $colorBarra = 'status-finalizado';
                                    $textoEstado = 'Cancelado';
                                    $colorBadge = 'bg-secondary';
                                }
                            @endphp

                            <div class="swiper-slide">
                                <div class="ticket-card p-3">
                                    <!-- Barra de color lateral -->
                                    <div class="ticket-status-bar {{ $colorBarra }}"></div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-2 ps-2">
                                        <div class="ticket-time">
                                            {{ \Carbon\Carbon::parse($reserva->hora)->format('H:i') }}
                                        </div>
                                        <span class="badge {{ $colorBadge }} rounded-pill" style="font-size: 0.7rem;">
                                            {{ $textoEstado }}
                                        </span>
                                    </div>

                                    <div class="d-flex align-items-center ps-2 mb-2">
                                        <div class="ticket-pet-img me-2">
                                            <i class="fas fa-paw"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <h6 class="fw-bold mb-0 text-truncate">{{ $reserva->mascota->nombre ?? 'Mascota' }}</h6>
                                            <small class="text-muted d-block text-truncate">
                                                @if($reserva->detalles->isNotEmpty())
                                                    {{ $reserva->detalles->first()->servicio->nombre ?? 'Servicio General' }}
                                                @else
                                                    Servicio General
                                                @endif
                                            </small>
                                        </div>
                                    </div>

                                    <div class="ps-2 mb-2">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-user me-1"></i>
                                            {{ $reserva->cliente->persona->nombres ?? 'Cliente' }} {{ $reserva->cliente->persona->apellidos ?? '' }}
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="fas fa-hashtag me-1"></i>
                                            ID: {{ $reserva->id_reserva }}
                                        </small>
                                    </div>
                                    
                                    <!-- Botones de acción -->
                                    <div class="d-flex gap-1 mt-3 ps-2">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary flex-grow-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalVerReserva"
                                                data-reserva-id="{{ $reserva->id_reserva }}"
                                                onclick="cargarReserva(this.getAttribute('data-reserva-id'))"
                                                style="font-size: 0.75rem;">
                                            <i class="fas fa-eye me-1"></i>Ver
                                        </button>
                                        
                                        @if($reserva->estado == 'P')
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-success flex-grow-1"
                                                    data-reserva-id="{{ $reserva->id_reserva }}"
                                                    onclick="marcarComoAtendido(this.getAttribute('data-reserva-id'))"
                                                    style="font-size: 0.75rem;">
                                                <i class="fas fa-check me-1"></i>Atender
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="swiper-slide" style="width: 100% !important;">
                                <div class="alert alert-light border text-center">
                                    <i class="fas fa-calendar-times fs-2 mb-2 text-muted"></i>
                                    <p class="mb-1">No hay reservas para mostrar en la línea de tiempo.</p>
                                    <small class="text-muted">¡Aprovecha para organizar tu día!</small>
                                </div>
                            </div>
                        @endforelse

                    </div>
                    
                    <!-- Paginación (Puntitos abajo) -->
                    <div class="swiper-pagination"></div>
                </div>
            </div>

            <!-- Sección de Feedbacks de 5 Estrellas -->
            <div class="feedback-container">
                <div class="d-flex align-items-center justify-content-between px-4 mb-3">
                    <h5 class="text-dark mb-0 fw-bold">
                        <i class="fas fa-star text-warning me-2"></i>Lo que dicen de ti
                    </h5>
                    <span class="badge bg-warning text-dark rounded-pill">
                        <i class="fas fa-trophy me-1"></i> Top Rated
                    </span>
                </div>

                <!-- Slider específico para feedbacks -->
                <div class="swiper swiper-feedback">
                    <div class="swiper-wrapper">

                        @forelse($comentarios5Estrellas as $comentario)
                            <div class="swiper-slide swiper-slide-feedback">
                                <div class="review-card">
                                    <div class="quote-icon">"</div>
                                    
                                    <!-- Estrellas Estáticas -->
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>

                                    <!-- Comentario -->
                                    <p class="text-secondary fst-italic mb-4" style="font-size: 0.95rem; min-height: 45px;">
                                        "{{ Str::limit($comentario->comentarios, 110) }}"
                                    </p>

                                    <!-- Pie: Cliente y Mascota -->
                                    <div class="d-flex align-items-center border-top pt-3">
                                        <div class="client-avatar shadow-sm me-3">
                                            {{ substr($comentario->nombres ?? 'A', 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">
                                                {{ $comentario->nombres ?? 'Anónimo' }}
                                            </h6>
                                            <small class="text-muted" style="font-size: 0.8rem;">
                                                Dueño de <span class="text-primary">{{ $comentario->mascota_nombre ?? 'Mascota' }}</span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="swiper-slide swiper-slide-feedback w-100">
                                <div class="alert alert-light text-center border">
                                    <small>Aún no tienes reseñas de 5 estrellas. ¡Sigue así, pronto llegarán! 🌟</small>
                                </div>
                            </div>
                        @endforelse

                    </div>
                    
                    <!-- Paginación -->
                    <div class="swiper-pagination pagination-feedback"></div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Script de JS para activar el slider -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    // Inicializar Swiper para reservas
    var swiper = new Swiper(".mySwiper", {
        slidesPerView: "auto",
        spaceBetween: 20,
        freeMode: true,
        grabCursor: true,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
    });

    // Inicializar Swiper para feedbacks
    var swiperFeedback = new Swiper(".swiper-feedback", {
        slidesPerView: "auto",
        spaceBetween: 25,
        grabCursor: true,
        freeMode: true,
        pagination: {
            el: ".pagination-feedback",
            clickable: true,
            dynamicBullets: true,
        },
    });

    // ========================================
    // FUNCIONES SIMPLIFICADAS - BACKEND LOGIC
    // ========================================

    // Filtrar reservas (usa EmpleadoController::filtrarReservas)
    function filtrarReservas() {
        const formData = new FormData();
        formData.append('estado', document.getElementById('filtroEstado').value);
        formData.append('cliente', document.getElementById('filtroCliente').value);  
        formData.append('mascota', document.getElementById('filtroMascota').value);
        
        fetch('{{ route("empleado.filtrar.reservas") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => data.success ? location.reload() : alert('Error al filtrar'))
        .catch(() => alert('Error de conexión'));
    }

    // Limpiar filtros
    function limpiarFiltros() {
        document.querySelectorAll('#filtroEstado, #filtroCliente, #filtroMascota').forEach(el => el.value = '');
        location.reload();
    }

    // Cargar reserva (usa EmpleadoController::cargarReserva)
    function cargarReserva(id) {
        fetch(`{{ url('empleado/cargar-reserva') }}/${id}`)
        .then(response => response.json())
        .then(data => data.success ? alert(`Reserva #${data.reserva.id_reserva} - ${data.reserva.mascota.nombre}`) : alert('Error'))
        .catch(() => alert('Error de conexión'));
    }

    // Marcar como atendido (usa EmpleadoController::marcarComoAtendido)
    function marcarComoAtendido(id) {
        if(confirm('¿Marcar reserva como atendida?')) {
            fetch(`{{ url('empleado/marcar-atendido') }}/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message || (data.success ? 'Reserva atendida' : 'Error'));
                if(data.success) location.reload();
            })
            .catch(() => alert('Error de conexión'));
        }
    }
</script>

@endsection
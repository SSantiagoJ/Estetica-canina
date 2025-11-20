@extends('layouts.app')

@section('title', 'Gestionar Turnos - Estética Canina')

@section('header')
    @include('partials.admin_header')
@endsection

@section('content')

<!-- Agregar los CSS del admin -->
<link rel="stylesheet" href="{{ asset('css/admin_toolbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
<aside class="admin-toolbar bg-primary text-white shadow-sm d-flex flex-column pt-4">
    <ul class="nav flex-column px-2">

        <li class="nav-item mb-2">
            <a href="{{ route('empleado.bandeja.reservas') }}" 
               class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-calendar-check fs-5"></i>
                <span class="fw-semibold">Bandeja de Reservas</span>
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('empleado.gestionar.turnos') }}" 
               class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-clock fs-5"></i>
                <span class="fw-semibold">Gestionar Turnos</span>
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('empleado.gestionar.novedades') }}" 
               class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-bell fs-5"></i>
                <span class="fw-semibold">Gestionar Novedades</span>
            </a>
        </li>

        <!-- ESTA ES LA ACTIVA -->
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.notificaciones') }}" 
               class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect active">
                <i class="fas fa-envelope fs-5"></i>
                <span class="fw-semibold">Gestionar Notificaciones</span>
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('home') }}" 
               class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-home fs-5"></i>
                <span class="fw-semibold">Web Cliente</span>
            </a>
        </li>
    </ul>
</aside>

<main class="admin-content">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">

        <hr class="my-2"> 
        <h2 class="fw-bold text-dark text-center">
            <i class="fas fa-envelope me-2"></i> Gestionar Notificaciones
        </h2>

        <div class="card-body">
            <!-- ==========================================
     ENVIAR CORREO PERSONALIZADO
=========================================== -->
<div class="card shadow-sm border border-1 mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i> Enviar Correo Personalizado</h5>
    </div>

    <div class="card-body">

        <form action="{{ route('empleado.notificaciones.custom') }}" method="POST">
            @csrf

            <div class="row g-3">

                <!-- Seleccionar usuarios -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Seleccionar usuarios</label>
                    <select name="usuarios[]" class="form-select" multiple required>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->correo }}">
                                {{ $u->correo }} (ID: {{ $u->id_usuario }})
                            </option>
                        @endforeach
                    </select>

                    <small class="text-muted">Puedes seleccionar varios</small>
                </div>

                <!-- Asunto -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Asunto *</label>
                    <input type="text" name="asunto" class="form-control" placeholder="Escribe aquí..." required>
                </div>

                <!-- Mensaje -->
                <div class="col-12">
                    <label class="form-label fw-semibold">Mensaje personalizado *</label>
                    <textarea name="mensaje" class="form-control" rows="4" 
                        placeholder="Escribe el contenido del correo..." required></textarea>
                </div>
            </div>

            <div class="text-end mt-3">
                <button class="btn btn-success px-4">
                    <i class="fas fa-paper-plane me-2"></i>Enviar correo
                </button>
            </div>

        </form>
    </div>
</div>


            <div class="filters-section mb-4">
                <h5 class="mb-3 text-secondary"><i class="fas fa-filter me-2"></i>Filtros de Búsqueda</h5>

                <div class="row g-3 mb-3">

                    <div class="col-md-3">
                        <label class="form-label">Tipo</label>
                        <input type="text" id="filtroTipo" class="form-control" placeholder="vacunas, previas...">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select id="filtroEstado" class="form-select">
                            <option value="">Todos</option>
                            <option value="A">Activa</option>
                            <option value="I">Inactiva</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>
                        <button class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>

                </div>
            </div>

            <div class="table-section">
                <h5 class="text-secondary mb-3"><i class="fas fa-list me-2"></i>Listado de Notificaciones</h5>

                <div class="table-responsive">
                    <table class="table table-hover text-center">
                        <thead>
                            <tr>
                                <th>TIPO</th>
                                <th>MENSAJE</th>
                                <th>FRECUENCIA</th>
                                <th>ESTADO</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>

                        <tbody>
@forelse($notificaciones as $n)
    <tr class="align-middle">

        <td>
            @if($n->tipo == 'vacunas')
                <span class="fw-bold text-primary"><i class="fas fa-syringe me-1"></i> VACUNAS</span>
            @elseif($n->tipo == 'previas')
                <span class="fw-bold text-info"><i class="fas fa-clock me-1"></i> PREVIAS</span>
            @elseif($n->tipo == 'promos-nuevas')
                <span class="fw-bold text-warning"><i class="fas fa-gift me-1"></i> PROMOS NUEVAS</span>
            @else
                <span class="fw-bold text-secondary">{{ strtoupper($n->tipo) }}</span>
            @endif
        </td>

        <td class="text-start small">{{ $n->mensaje }}</td>

        <td>
            <span class="badge rounded-pill bg-dark text-white px-3 py-2">
                {{ strtoupper($n->fecha_envio) }}
            </span>
        </td>

        <td>
            <div class="form-check form-switch d-flex justify-content-center">
                <input class="form-check-input" type="checkbox"
                    onchange="cambiarEstado({{ $n->id_notificacion }})"
                    {{ $n->estado == 'A' ? 'checked' : '' }}>
            </div>
        </td>

        <td class="text-center">

            <button class="btn btn-outline-primary btn-sm me-1"
                onclick="editarNotificacion(
                    {{ $n->id_notificacion }},
                    '{{ $n->tipo }}',
                    '{{ addslashes($n->mensaje) }}',
                    '{{ $n->fecha_envio }}',
                    '{{ $n->estado }}'
                )">
                <i class="fas fa-edit"></i>
            </button>

            <form action="{{ route('empleado.notificaciones.ejecutar', $n->id_notificacion) }}" 
                method="POST" class="d-inline">
                @csrf
                <button class="btn btn-success btn-sm" title="Ejecutar comando Artisan">
                    <i class="fas fa-play"></i>
                </button>
            </form>

        </td>

    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center text-muted py-4">
            <i class="fas fa-inbox fa-2x d-block mb-2"></i>
            No hay notificaciones registradas
        </td>
    </tr>
@endforelse
</tbody>

                    </table>
                </div>

                <div class="mt-3">
                    <small class="badge bg-primary">Total: {{ $notificaciones->count() }} registros</small>
                </div>
            </div>

        </div>
    </div>
</main>

<!-- ==============================
    MODAL REGISTRAR NOTIFICACIÓN
================================ -->
<div class="modal fade" id="modalRegistrarNotificacion" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-body">

            <h4><i class="fas fa-plus-circle me-2"></i> Registrar Notificación</h4>

            <form action="{{ route('empleado.notificaciones.store') }}" method="POST">
                @csrf

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Tipo *</label>
                        <input type="text" name="tipo" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Frecuencia *</label>
                        <input type="text" name="fecha_envio" class="form-control" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Mensaje *</label>
                        <textarea class="form-control" name="mensaje" rows="3" required></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Estado *</label>
                        <select class="form-select" name="estado" required>
                            <option value="A">Activa</option>
                            <option value="I">Inactiva</option>
                        </select>
                    </div>

                </div>

                <div class="text-center mt-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-primary px-5">GUARDAR</button>
                </div>

            </form>
        </div>
    </div>
  </div>
</div>

<!-- ===========================
        MODAL EDITAR
=========================== -->
<div class="modal fade" id="modalEditarNotificacion" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-body">

            <h4><i class="fas fa-edit me-2"></i> Editar Notificación</h4>

            <form action="{{ route('empleado.notificaciones.update') }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" id="editId" name="id_notificacion">

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Tipo *</label>
                        <input type="text" id="editTipo" name="tipo" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Frecuencia *</label>
                        <input type="text" id="editFechaEnvio" name="fecha_envio" class="form-control" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Mensaje *</label>
                        <textarea id="editMensaje" name="mensaje" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Estado *</label>
                        <select id="editEstado" name="estado" class="form-select" required>
                            <option value="A">Activa</option>
                            <option value="I">Inactiva</option>
                        </select>
                    </div>

                </div>

                <div class="text-center mt-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-primary px-5">GUARDAR</button>
                </div>

            </form>
        </div>
    </div>
  </div>
</div>
<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<style>
.table td {
    white-space: nowrap;
}
.table td .btn {
    padding: 3px 8px;
    font-size: 12px;
}
.table-responsive {
    overflow-x: auto;
}
</style>



<script>
function editarNotificacion(id, tipo, mensaje, fecha, estado) {
    document.getElementById('editId').value = id;
    document.getElementById('editTipo').value = tipo;
    document.getElementById('editMensaje').value = mensaje;
    document.getElementById('editFechaEnvio').value = fecha;
    document.getElementById('editEstado').value = estado;

    let modal = new bootstrap.Modal(document.getElementById('modalEditarNotificacion'));
    modal.show();
}

</script>

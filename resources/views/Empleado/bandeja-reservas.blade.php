@extends('layouts.app')
@section('title', 'Gestionar Reserva - Estética Canina')
@section('header')
    @include('partials.empleado_header')
@endsection

@section('content')
<div class="container-fluid py-4" style="background-color: #2C3E50; min-height: 100vh;">
    
       <div class="card border-0 shadow-lg" style="border-radius: 20px;">
        <div class="card-body p-4">
                <!-- Título -->
                <h2 class="text-center mb-4 fw-bold" >Bandeja de reservas</h2>

                <!-- Mensajes de éxito/error -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Filtros - Fila 1 -->
                <div class="row g-3 mb-3">
                    
                    <div class="col-md-2">
                       <label class="form-label">Empleado</label>
                        <select class="form-select" id="filtroEmpleado">
                            <option value="">Seleccionar</option>
                             @foreach($empleados as $empleado)
                                <option value="{{ $empleado->id_empleado }}">
                                    {{ $empleado->persona->nombres ?? '' }} {{ $empleado->persona->apellidos ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tipo documento</label>
                        <select class="form-select" id="filtroTipoDoc">
                            <option value="">Seleccionar</option>
                            <option value="DNI">DNI</option>
                            <option value="CE">CE</option>
                            <option value="PASAPORTE">PASAPORTE</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">N° Documento</label>
                        <input type="text" class="form-control" id="filtroNumDoc" placeholder="Escribe aquí">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Nombre cliente</label>
                        <input type="text" class="form-control" id="filtroCliente" placeholder="Ingrese nombre">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select class="form-select" id="filtroEstado">
                            <option value="">Seleccionar</option>
                            <option value="P">PENDIENTE</option>
                            <option value="A">ATENDIDO</option>
                            <option value="C">CANCELADO</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button class="btn btn-primary flex-grow-1" onclick="buscarReservas()">Buscar</button>
                        <button class="btn btn-secondary" onclick="limpiarFiltros()" title="Limpiar filtros">
                            <i class="bi bi-arrow-clockwise">x</i>
                        </button>
                    </div>
                </div>

                <!-- Filtros - Fila 2 -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Nombre mascota</label>
                        <input type="text" class="form-control" id="filtroMascota" placeholder="Escribe aquí">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha inicio reserva</label>
                        <input type="date" class="form-control" id="filtroFechaInicio">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha fin reserva</label>
                        <input type="date" class="form-control" id="filtroFechaFin">
                    </div>
                </div>

                <!-- Tabla de reservas -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Fecha reserva</th>
                                <th>Hora</th>
                                <th>Servicio</th>
                                <th>Duración</th>
                                <th>N° Documento</th>
                                <th>Cliente</th>
                                <th>Especie</th>
                                <th>Enfermedad</th>
                                <th>Vacuna</th>
                                <th>Estado</th>
                                <th>Nombre mascota</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaReservas">
                            @forelse($reservas as $index => $reserva)
                            <tr data-empleado-id="{{ $reserva->id_empleado }}">
                                    <td>{{ $reserva->id_reserva }}</td>
                                    <td>{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($reserva->hora)->format('H:i') }}</td>
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
                                    <td>{{ $reserva->cliente->persona->nro_documento ?? 'N/A' }}</td>
                                    <td>
                                        {{ $reserva->cliente->persona->nombres ?? 'N/A' }} 
                                        {{ $reserva->cliente->persona->apellidos ?? '' }}
                                    </td>
                                    <td>{{ $reserva->mascota->especie ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        @if($reserva->enfermedad)
                                            <i class="bi bi-check-square-fill text-success"></i>
                                        @else
                                            <i class="bi bi-square text-muted"></i>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($reserva->vacuna)
                                            <i class="bi bi-check-square-fill text-success"></i>
                                        @else
                                            <i class="bi bi-square text-muted"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($reserva->estado == 'P')
                                            <span class="badge bg-warning text-dark">PENDIENTE</span>
                                        @elseif($reserva->estado == 'A')
                                            <span class="badge bg-info text-white">ATENDIDO</span>
                                        @elseif($reserva->estado == 'C')
                                            <span class="badge bg-secondary">CANCELADO</span>
                                        @endif
                                    </td>
                                    <td>{{ $reserva->mascota->nombre ?? 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('empleado.reservas.ver', $reserva->id_reserva) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                VER
                                            </a>
                                            @if($reserva->estado == 'P')
                                                <form action="{{ route('empleado.reservas.atender', $reserva->id_reserva) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('¿Está seguro de marcar esta reserva como atendida?')">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        ATENDER
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center text-muted">No hay reservas registradas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        # Registros: {{ $reservas->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-cerrar alertas después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});

// Función para buscar/filtrar reservas
function buscarReservas() {
    console.log('[v0] Iniciando búsqueda de reservas...');
    
    // Obtener valores de los filtros
    const filtroEmpleado = document.getElementById('filtroEmpleado').value;
    const filtroTipoDoc = document.getElementById('filtroTipoDoc').value.toUpperCase();
    const filtroNumDoc = document.getElementById('filtroNumDoc').value.trim();
    const filtroCliente = document.getElementById('filtroCliente').value.toLowerCase().trim();
    const filtroEstado = document.getElementById('filtroEstado').value;
    const filtroMascota = document.getElementById('filtroMascota').value.toLowerCase().trim();
    const filtroFechaInicio = document.getElementById('filtroFechaInicio').value;
    const filtroFechaFin = document.getElementById('filtroFechaFin').value;
    
    console.log('[v0] Filtros:', {
        empleado: filtroEmpleado,
        tipoDoc: filtroTipoDoc,
        numDoc: filtroNumDoc,
        cliente: filtroCliente,
        estado: filtroEstado,
        mascota: filtroMascota,
        fechaInicio: filtroFechaInicio,
        fechaFin: filtroFechaFin
    });
     // Obtener el texto del trabajador seleccionado
    let nombreTrabajadorSeleccionado = '';
    if (filtroEmpleado !== '') {
        const selectEmpleado = document.getElementById('filtroEmpleado');
        nombreEmpleadoSeleccionado = selectEmpleado.options
        [selectEmpleado.selectedIndex].text.trim().replace(/\s+/g, ' ');
    }
    

    // Obtener todas las filas de la tabla
    const filas = document.querySelectorAll('#tablaReservas tr');
    let filasVisibles = 0;
    
    filas.forEach(function(fila) {
        // Saltar si es la fila de "No hay reservas"
        if (fila.cells.length === 1) return;
        
        // Obtener datos de cada columna
        const empleadoId = fila.getAttribute('data-empleado-id');
        const fechaTexto = fila.cells[1].textContent.trim();
        const numDocTexto = fila.cells[5].textContent.trim();
        const clienteTexto = fila.cells[6].textContent.trim().toLowerCase();
        const estadoTexto = fila.cells[10].textContent.trim();
        const mascotaTexto = fila.cells[11].textContent.trim().toLowerCase();
        
        // Convertir fecha de dd/mm/yyyy a yyyy-mm-dd
        const partesFecha = fechaTexto.split('/');
        const fechaFormateada = `${partesFecha[2]}-${partesFecha[1]}-${partesFecha[0]}`;
        
        let coincide = true;
        
        // Filtro empleados
       if (filtroEmpleado !== '' && empleadoId !== filtroEmpleado) {
            coincide = false;
        }

        // Filtro por número de documento
        if (filtroNumDoc !== '' && !numDocTexto.includes(filtroNumDoc)) {
            coincide = false;
        }
        
        // Filtro por cliente
        if (filtroCliente !== '' && !clienteTexto.includes(filtroCliente)) {
            coincide = false;
        }
        
        // Filtro por estado
        if (filtroEstado !== '' && !estadoTexto.includes(filtroEstado === 'P' ? 'PENDIENTE' : filtroEstado === 'A' ? 'ATENDIDO' : 'CANCELADO')) {
            coincide = false;
        }
        
        // Filtro por mascota
        if (filtroMascota !== '' && !mascotaTexto.includes(filtroMascota)) {
            coincide = false;
        }
        
        // Filtro por rango de fechas
        if (filtroFechaInicio !== '' && fechaFormateada < filtroFechaInicio) {
            coincide = false;
        }
        
        if (filtroFechaFin !== '' && fechaFormateada > filtroFechaFin) {
            coincide = false;
        }
        
        // Mostrar u ocultar fila
        if (coincide) {
            fila.style.display = '';
            filasVisibles++;
        } else {
            fila.style.display = 'none';
        }
    });
    
    console.log('[v0] Filas visibles:', filasVisibles);
    
    if (filasVisibles === 0) {
        alert('No se encontraron reservas con los criterios de búsqueda');
    }
}

// Función para limpiar filtros
function limpiarFiltros() {
    document.getElementById('filtroEmpleado').value = '';
    document.getElementById('filtroTipoDoc').value = '';
    document.getElementById('filtroNumDoc').value = '';
    document.getElementById('filtroCliente').value = '';
    document.getElementById('filtroEstado').value = '';
    document.getElementById('filtroMascota').value = '';
    document.getElementById('filtroFechaInicio').value = '';
    document.getElementById('filtroFechaFin').value = '';
    
    // Mostrar todas las filas
    const filas = document.querySelectorAll('#tablaReservas tr');
    filas.forEach(function(fila) {
        fila.style.display = '';
    });
}
</script>

@section('title', 'Gestionar Reserva - Estética Canina')
@section('header')
    @include('partials.empleado_header')
@endsection

@section('content')
<div class="container-fluid py-4" style="background-color: #2C3E50; min-height: 100vh;">
    
       <div class="card border-0 shadow-lg" style="border-radius: 20px;">
        <div class="card-body p-4">
                <!-- Título -->
                <h2 class="text-center mb-4 fw-bold" >Bandeja de reservas</h2>

                <!-- Mensajes de éxito/error -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Filtros - Fila 1 -->
                <div class="row g-3 mb-3">
                    
                    <div class="col-md-2">
                       <label class="form-label">Empleado</label>
                        <select class="form-select" id="filtroEmpleado">
                            <option value="">Seleccionar</option>
                             @foreach($empleados as $empleado)
                                <option value="{{ $empleado->id_empleado }}">
                                    {{ $empleado->persona->nombres ?? '' }} {{ $empleado->persona->apellidos ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tipo documento</label>
                        <select class="form-select" id="filtroTipoDoc">
                            <option value="">Seleccionar</option>
                            <option value="DNI">DNI</option>
                            <option value="CE">CE</option>
                            <option value="PASAPORTE">PASAPORTE</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">N° Documento</label>
                        <input type="text" class="form-control" id="filtroNumDoc" placeholder="Escribe aquí">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Nombre cliente</label>
                        <input type="text" class="form-control" id="filtroCliente" placeholder="Ingrese nombre">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select class="form-select" id="filtroEstado">
                            <option value="">Seleccionar</option>
                            <option value="P">PENDIENTE</option>
                            <option value="A">ATENDIDO</option>
                            <option value="C">CANCELADO</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button class="btn btn-primary flex-grow-1" onclick="buscarReservas()">Buscar</button>
                        <button class="btn btn-secondary" onclick="limpiarFiltros()" title="Limpiar filtros">
                            <i class="bi bi-arrow-clockwise">x</i>
                        </button>
                    </div>
                </div>

                <!-- Filtros - Fila 2 -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Nombre mascota</label>
                        <input type="text" class="form-control" id="filtroMascota" placeholder="Escribe aquí">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha inicio reserva</label>
                        <input type="date" class="form-control" id="filtroFechaInicio">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha fin reserva</label>
                        <input type="date" class="form-control" id="filtroFechaFin">
                    </div>
                </div>

                <!-- Tabla de reservas -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Fecha reserva</th>
                                <th>Hora</th>
                                <th>Servicio</th>
                                <th>Duración</th>
                                <th>N° Documento</th>
                                <th>Cliente</th>
                                <th>Especie</th>
                                <th>Enfermedad</th>
                                <th>Vacuna</th>
                                <th>Estado</th>
                                <th>Nombre mascota</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaReservas">
                            @forelse($reservas as $index => $reserva)
                            <tr data-empleado-id="{{ $reserva->id_empleado }}">
                                    <td>{{ $reserva->id_reserva }}</td>
                                    <td>{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($reserva->hora)->format('H:i') }}</td>
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
                                    <td>{{ $reserva->cliente->persona->nro_documento ?? 'N/A' }}</td>
                                    <td>
                                        {{ $reserva->cliente->persona->nombres ?? 'N/A' }} 
                                        {{ $reserva->cliente->persona->apellidos ?? '' }}
                                    </td>
                                    <td>{{ $reserva->mascota->especie ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        @if($reserva->enfermedad)
                                            <i class="bi bi-check-square-fill text-success"></i>
                                        @else
                                            <i class="bi bi-square text-muted"></i>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($reserva->vacuna)
                                            <i class="bi bi-check-square-fill text-success"></i>
                                        @else
                                            <i class="bi bi-square text-muted"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($reserva->estado == 'P')
                                            <span class="badge bg-warning text-dark">PENDIENTE</span>
                                        @elseif($reserva->estado == 'A')
                                            <span class="badge bg-info text-white">ATENDIDO</span>
                                        @elseif($reserva->estado == 'C')
                                            <span class="badge bg-secondary">CANCELADO</span>
                                        @endif
                                    </td>
                                    <td>{{ $reserva->mascota->nombre ?? 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('empleado.reservas.ver', $reserva->id_reserva) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                VER
                                            </a>
                                            @if($reserva->estado == 'P')
                                                <form action="{{ route('empleado.reservas.atender', $reserva->id_reserva) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('¿Está seguro de marcar esta reserva como atendida?')">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        ATENDER
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center text-muted">No hay reservas registradas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        # Registros: {{ $reservas->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-cerrar alertas después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});

// Función para buscar/filtrar reservas
function buscarReservas() {
    console.log('[v0] Iniciando búsqueda de reservas...');
    
    // Obtener valores de los filtros
    const filtroEmpleado = document.getElementById('filtroEmpleado').value;
    const filtroTipoDoc = document.getElementById('filtroTipoDoc').value.toUpperCase();
    const filtroNumDoc = document.getElementById('filtroNumDoc').value.trim();
    const filtroCliente = document.getElementById('filtroCliente').value.toLowerCase().trim();
    const filtroEstado = document.getElementById('filtroEstado').value;
    const filtroMascota = document.getElementById('filtroMascota').value.toLowerCase().trim();
    const filtroFechaInicio = document.getElementById('filtroFechaInicio').value;
    const filtroFechaFin = document.getElementById('filtroFechaFin').value;
    
    console.log('[v0] Filtros:', {
        empleado: filtroEmpleado,
        tipoDoc: filtroTipoDoc,
        numDoc: filtroNumDoc,
        cliente: filtroCliente,
        estado: filtroEstado,
        mascota: filtroMascota,
        fechaInicio: filtroFechaInicio,
        fechaFin: filtroFechaFin
    });
     // Obtener el texto del trabajador seleccionado
    let nombreTrabajadorSeleccionado = '';
    if (filtroEmpleado !== '') {
        const selectEmpleado = document.getElementById('filtroEmpleado');
        nombreEmpleadoSeleccionado = selectEmpleado.options
        [selectEmpleado.selectedIndex].text.trim().replace(/\s+/g, ' ');
    }
    

    // Obtener todas las filas de la tabla
    const filas = document.querySelectorAll('#tablaReservas tr');
    let filasVisibles = 0;
    
    filas.forEach(function(fila) {
        // Saltar si es la fila de "No hay reservas"
        if (fila.cells.length === 1) return;
        
        // Obtener datos de cada columna
        const empleadoId = fila.getAttribute('data-empleado-id');
        const fechaTexto = fila.cells[1].textContent.trim();
        const numDocTexto = fila.cells[5].textContent.trim();
        const clienteTexto = fila.cells[6].textContent.trim().toLowerCase();
        const estadoTexto = fila.cells[10].textContent.trim();
        const mascotaTexto = fila.cells[11].textContent.trim().toLowerCase();
        
        // Convertir fecha de dd/mm/yyyy a yyyy-mm-dd
        const partesFecha = fechaTexto.split('/');
        const fechaFormateada = `${partesFecha[2]}-${partesFecha[1]}-${partesFecha[0]}`;
        
        let coincide = true;
        
        // Filtro empleados
       if (filtroEmpleado !== '' && empleadoId !== filtroEmpleado) {
            coincide = false;
        }

        // Filtro por número de documento
        if (filtroNumDoc !== '' && !numDocTexto.includes(filtroNumDoc)) {
            coincide = false;
        }
        
        // Filtro por cliente
        if (filtroCliente !== '' && !clienteTexto.includes(filtroCliente)) {
            coincide = false;
        }
        
        // Filtro por estado
        if (filtroEstado !== '' && !estadoTexto.includes(filtroEstado === 'P' ? 'PENDIENTE' : filtroEstado === 'A' ? 'ATENDIDO' : 'CANCELADO')) {
            coincide = false;
        }
        
        // Filtro por mascota
        if (filtroMascota !== '' && !mascotaTexto.includes(filtroMascota)) {
            coincide = false;
        }
        
        // Filtro por rango de fechas
        if (filtroFechaInicio !== '' && fechaFormateada < filtroFechaInicio) {
            coincide = false;
        }
        
        if (filtroFechaFin !== '' && fechaFormateada > filtroFechaFin) {
            coincide = false;
        }
        
        // Mostrar u ocultar fila
        if (coincide) {
            fila.style.display = '';
            filasVisibles++;
        } else {
            fila.style.display = 'none';
        }
    });
    
    console.log('[v0] Filas visibles:', filasVisibles);
    
    if (filasVisibles === 0) {
        alert('No se encontraron reservas con los criterios de búsqueda');
    }
}

// Función para limpiar filtros
function limpiarFiltros() {
    document.getElementById('filtroEmpleado').value = '';
    document.getElementById('filtroTipoDoc').value = '';
    document.getElementById('filtroNumDoc').value = '';
    document.getElementById('filtroCliente').value = '';
    document.getElementById('filtroEstado').value = '';
    document.getElementById('filtroMascota').value = '';
    document.getElementById('filtroFechaInicio').value = '';
    document.getElementById('filtroFechaFin').value = '';
    
    // Mostrar todas las filas
    const filas = document.querySelectorAll('#tablaReservas tr');
    filas.forEach(function(fila) {
        fila.style.display = '';
    });
}
</script>
@endpush
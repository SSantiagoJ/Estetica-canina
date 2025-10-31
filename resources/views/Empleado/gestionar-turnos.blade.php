@extends('layouts.app')

@section('title', 'Gestionar Turno - Estética Canina')

@section('header')
    @include('partials.empleado_header')
@endsection

@section('content')
<div class="container-fluid py-4" style="background-color: #2C3E50; min-height: 100vh;">
    
    {{-- Contenedor principal con fondo blanco --}}
    <div class="card border-0 shadow-lg" style="border-radius: 20px;">
        <div class="card-body p-4">
            
            {{-- Título --}}
            <h2 class="text-center mb-4 fw-bold">Gestionar Turno</h2>
            
            {{-- Mensajes de éxito/error --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>¡Éxito!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>¡Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>¡Error!</strong> Por favor corrige los siguientes errores:
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
         {{-- Botón NUEVO con atributos data de Bootstrap --}}
            <div class="mb-3">
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistrarTurno">
                    NUEVO
                </button>
            </div>
    {{-- Filtros de búsqueda --}}
            <div class="row g-3 mb-4">
                <div class="col-md-2">
                    <label class="form-label small">Tipo documento</label>
                    <select class="form-select form-select-sm" id="filtroTipoDoc">
                        <option value="">DNI</option>
                        <option value="CE">CE</option>
                        <option value="PASAPORTE">PASAPORTE</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label small">N° Documento</label>
                    <input type="text" class="form-control form-control-sm" id="filtroNumDoc" placeholder="">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label small">Trabajador</label>
                    <select class="form-select form-select-sm" id="filtroTrabajador">
                        <option value="">Seleccionar</option>
                        @foreach($empleados as $empleado)
                            <option value="{{ $empleado->id_empleado }}">
                                {{ $empleado->persona->nombres }} {{ $empleado->persona->apellidos }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Eliminado el filtro de Servicio --}}
            
                <div class="col-md-3">
                    <label class="form-label small">Fecha</label>
                    <input type="date" class="form-control form-control-sm" id="filtroFecha">
                </div>
                
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button class="btn btn-info text-white flex-grow-1" onclick="buscarTurnos()">Buscar</button>
                    <button class="btn btn-secondary" onclick="limpiarFiltros()" title="Limpiar filtros">
                       <i class="bi bi-x-circle">×</i> 
                    </button>
                </div>
            </div>

           {{-- Tabla de turnos --}}
<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>Nro</th>               
                <th>Tipo Doc</th>
                <th>N° Documento</th>
                <th>Trabajador</th>
                <th>Fecha</th>
                <th>Hora inicio</th>
                <th>Hora fin</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tablaTurnos">
            @forelse($turnos as $index => $turno)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        {{ $turno->empleado->persona->nombres ?? 'N/A' }} 
                        {{ $turno->empleado->persona->apellidos ?? '' }}
                    </td>
                     <td>{{ $turno->empleado->persona->tipo_doc ?? 'DNI' }}</td>
                    <td>{{ $turno->empleado->persona->nro_documento ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }}</td>
                    <td>{{ $turno->hora_inicio }}</td>
                    <td>{{ $turno->hora_fin }}</td>
                    <td>
                        @if($turno->estado == 'D')
                            <span class="badge bg-success">● DISPONIBLE</span>
                        @else
                            <span class="badge bg-warning text-dark">● OCUPADO</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-secondary btn-sm" 
                                onclick="editarTurno({{ $turno->id_turno }}, {{ $turno->id_empleado }}, '{{ $turno->fecha }}', '{{ $turno->hora_inicio }}', '{{ $turno->hora_fin }}', '{{ $turno->estado }}')">
                            EDITAR
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">No hay turnos registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
 </div>
  </div>
    </div>
    
</div>

{{-- Modal Registrar Turno --}}

 <div class="modal fade" id="modalRegistrarTurno" tabindex="-1" aria-labelledby="modalRegistrarTurnoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-body p-4">
            <h4 class="text-center mb-4" id="modalRegistrarTurnoLabel">Registrar turno</h4>
                
                <form id="formRegistrarTurno" action="{{ route('empleado.turnos.store') }}" method="POST" onsubmit="return validarFormularioRegistro()">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label text-primary">Trabajador <span class="text-danger">*</span></label>
                        <select class="form-select" name="id_empleado" id="registroEmpleado" required>
                            <option value="">Seleccione un trabajador</option>
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado->id_empleado }}">
                                    {{ $empleado->persona->nombres }} {{ $empleado->persona->apellidos }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Por favor seleccione un trabajador</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-primary">Fecha <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha" id="registroFecha" required>
                            <div class="invalid-feedback">Por favor ingrese una fecha</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-primary">Hora inicio <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="hora_inicio" id="registroHoraInicio" required>
                            <div class="invalid-feedback">Por favor ingrese hora de inicio</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-primary">Hora fin <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="hora_fin" id="registroHoraFin" required>
                            <div class="invalid-feedback">Por favor ingrese hora de fin</div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-primary">Estado <span class="text-danger">*</span></label>
                        <select class="form-select" name="estado" id="registroEstado" required>
                            <option value="">Seleccione un estado</option>
                            <option value="D">DISPONIBLE</option>
                            <option value="O">OCUPADO</option>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione un estado</div>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-secondary px-4 me-2" data-bs-dismiss="modal">CANCELAR</button>
                        <button type="submit" class="btn btn-primary px-5">GUARDAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Editar Turno --}}
<div class="modal fade" id="modalEditarTurno" tabindex="-1" aria-labelledby="modalEditarTurnoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-body p-4">
                <h4 class="text-center mb-4" id="modalEditarTurnoLabel">Editar turno</h4>
                
                <form id="formEditarTurno" method="POST" onsubmit="return validarFormularioEdicion()">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label text-primary">Trabajador <span class="text-danger">*</span></label>
                        <select class="form-select" name="id_empleado" id="editEmpleado" required>
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado->id_empleado }}">
                                    {{ $empleado->persona->nombres }} {{ $empleado->persona->apellidos }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Por favor seleccione un trabajador</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-primary">Fecha <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha" id="editFecha" required>
                            <div class="invalid-feedback">Por favor ingrese una fecha</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-primary">Hora inicio <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="hora_inicio" id="editHoraInicio" required>
                            <div class="invalid-feedback">Por favor ingrese hora de inicio</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-primary">Hora fin <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="hora_fin" id="editHoraFin" required>
                            <div class="invalid-feedback">Por favor ingrese hora de fin</div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-primary">Estado <span class="text-danger">*</span></label>
                        <select class="form-select" name="estado" id="editEstado" required>
                            <option value="D">DISPONIBLE</option>
                            <option value="O">OCUPADO</option>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione un estado</div>
                    </div>
                    
                    <div class="text-center">
                        <button type="button" class="btn btn-secondary px-4 me-2" data-bs-dismiss="modal">CANCELAR</button>
                        <button type="submit" class="btn btn-primary px-5">GUARDAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Función para editar turno usando atributos data de Bootstrap
function editarTurno(id, idEmpleado, fecha, horaInicio, horaFin, estado) {
    // Actualizar la acción del formulario
    document.getElementById('formEditarTurno').action = `/empleado/turnos/${id}`;
    
    // Llenar los campos del modal
    document.getElementById('editEmpleado').value = idEmpleado;
    document.getElementById('editFecha').value = fecha;
    document.getElementById('editHoraInicio').value = horaInicio;
    document.getElementById('editHoraFin').value = horaFin;
    document.getElementById('editEstado').value = estado;
 const modalElement = document.getElementById('modalEditarTurno');
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    modal.show();
}

function buscarTurnos() {
  // Obtener los valores de los filtros
    const filtroTipoDoc = document.getElementById('filtroTipoDoc').value.trim();
    const filtroNumDoc = document.getElementById('filtroNumDoc').value.trim();
   const filtroTrabajador = document.getElementById('filtroTrabajador').value;
    const filtroFecha = document.getElementById('filtroFecha').value;
    
    console.log('[v0] Iniciando búsqueda...');
     console.log('[v0] Filtro tipo doc:', filtroTipoDoc);
    console.log('[v0] Filtro num doc:', filtroNumDoc);
    console.log('[v0] Filtro trabajador ID:', filtroTrabajador);
    console.log('[v0] Filtro fecha:', filtroFecha);
    
    // Obtener el texto del trabajador seleccionado
    let nombreTrabajadorSeleccionado = '';
    if (filtroTrabajador !== '') {
        const selectTrabajador = document.getElementById('filtroTrabajador');
        nombreTrabajadorSeleccionado = selectTrabajador.options
        [selectTrabajador.selectedIndex].text.trim().replace(/\s+/g, ' ');
    }
    
    // Obtener todas las filas de la tabla
    const filas = document.querySelectorAll('#tablaTurnos tr');
    let filasVisibles = 0;
    
    // Recorrer cada fila
    filas.forEach(function(fila) {
        // Obtener datos de la fila
        const trabajadorFila = fila.cells[1].textContent.trim().replace(/\s+/g, ' ');
         const tipoDocFila = fila.cells[2].textContent.trim();
        const numDocFila = fila.cells[3].textContent.trim();
        const fechaTexto = fila.cells[4].textContent.trim();

        // Obtener la fecha de la fila y convertirla a formato YYYY-MM-DD
        const partesFecha = fechaTexto.split('/');
        const fechaFormateada = `${partesFecha[2]}-${partesFecha[1]}-${partesFecha[0]}`;
        
    
        // Variable para saber si la fila cumple con los filtros
        let coincide = true;
        
      // Filtro por tipo de documento
        if (filtroTipoDoc !== '' && tipoDocFila !== filtroTipoDoc) {
            coincide = false;
        }
        if (filtroNumDoc !== '' && !numDocFila.includes(filtroNumDoc)) {
                coincide = false;
            }
             // Filtro por trabajador
        if (filtroTrabajador !== '' && trabajadorFila !== nombreTrabajadorSeleccionado) {
            coincide = false;
        }
        
        // Filtro por fecha
        if (filtroFecha !== '' && fechaFormateada !== filtroFecha) {
            coincide = false;
        }
        
        // Mostrar u ocultar la fila
        if (coincide) {
            fila.style.display = '';
            filasVisibles++;
        } else {
            fila.style.display = 'none';
        }
    });
    
    console.log('[v0] Filas visibles:', filasVisibles);
    
    // Si no hay filas visibles, mostrar mensaje
    if (filasVisibles === 0) {
        alert('No se encontraron turnos con los criterios de búsqueda');
    }
}

function limpiarFiltros() {
    document.getElementById('filtroTipoDoc').value = '';
    document.getElementById('filtroNumDoc').value = '';
    document.getElementById('filtroTrabajador').value = '';
    document.getElementById('filtroFecha').value = '';
    
    const filas = document.querySelectorAll('#tablaTurnos tr');
    filas.forEach(function(fila) {
        fila.style.display = '';
    });
}

function validarFormularioRegistro() {
    console.log('[v0] Validando formulario de registro...');
    
    // Obtener valores del formulario
    const empleado = document.getElementById('registroEmpleado').value;
    const fecha = document.getElementById('registroFecha').value;
    const horaInicio = document.getElementById('registroHoraInicio').value;
    const horaFin = document.getElementById('registroHoraFin').value;
    const estado = document.getElementById('registroEstado').value;
    
    // Variable para controlar si hay errores
    let hayErrores = false;
    
    // Validar trabajador
    if (empleado === '') {
        mostrarError('registroEmpleado', 'Debe seleccionar un trabajador');
        hayErrores = true;
    } else {
        limpiarError('registroEmpleado');
    }
    
    // Validar fecha
    if (fecha === '') {
        mostrarError('registroFecha', 'Debe ingresar una fecha');
        hayErrores = true;
    } else {
        // Validar que la fecha no sea anterior a hoy
        const fechaSeleccionada = new Date(fecha);
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        
        if (fechaSeleccionada < hoy) {
            mostrarError('registroFecha', 'La fecha no puede ser anterior a hoy');
            hayErrores = true;
        } else {
            limpiarError('registroFecha');
        }
    }
    
    // Validar hora inicio
    if (horaInicio === '') {
        mostrarError('registroHoraInicio', 'Debe ingresar hora de inicio');
        hayErrores = true;
    } else {
        limpiarError('registroHoraInicio');
    }
    
    // Validar hora fin
    if (horaFin === '') {
        mostrarError('registroHoraFin', 'Debe ingresar hora de fin');
        hayErrores = true;
    } else {
        limpiarError('registroHoraFin');
    }
    
    // Validar que hora fin sea mayor que hora inicio
    if (horaInicio !== '' && horaFin !== '' && horaFin <= horaInicio) {
        mostrarError('registroHoraFin', 'La hora de fin debe ser mayor que la hora de inicio');
        hayErrores = true;
    }
    
    // Validar estado
    if (estado === '') {
        mostrarError('registroEstado', 'Debe seleccionar un estado');
        hayErrores = true;
    } else {
        limpiarError('registroEstado');
    }
    
    // Si hay errores, no enviar el formulario
    if (hayErrores) {
        console.log('[v0] Formulario con errores, no se enviará');
        return false;
    }
    
    console.log('[v0] Formulario válido, enviando...');
    return true;
}

function validarFormularioEdicion() {
    const empleado = document.getElementById('editEmpleado').value;
    const fecha = document.getElementById('editFecha').value;
    const horaInicio = document.getElementById('editHoraInicio').value;
    const horaFin = document.getElementById('editHoraFin').value;
    const estado = document.getElementById('editEstado').value;
    
    let hayErrores = false;
    
    if (empleado === '') {
        mostrarError('editEmpleado', 'Debe seleccionar un trabajador');
        hayErrores = true;
    } else {
        limpiarError('editEmpleado');
    }
    
    if (fecha === '') {
        mostrarError('editFecha', 'Debe ingresar una fecha');
        hayErrores = true;
    } else {
        limpiarError('editFecha');
    }
    
    if (horaInicio === '') {
        mostrarError('editHoraInicio', 'Debe ingresar hora de inicio');
        hayErrores = true;
    } else {
        limpiarError('editHoraInicio');
    }
    
    if (horaFin === '') {
        mostrarError('editHoraFin', 'Debe ingresar hora de fin');
        hayErrores = true;
    } else {
        limpiarError('editHoraFin');
    }
    
    if (horaInicio !== '' && horaFin !== '' && horaFin <= horaInicio) {
        mostrarError('editHoraFin', 'La hora de fin debe ser mayor que la hora de inicio');
        hayErrores = true;
    }
    
    if (estado === '') {
        mostrarError('editEstado', 'Debe seleccionar un estado');
        hayErrores = true;
    } else {
        limpiarError('editEstado');
    }
    
    if (hayErrores) {
        return false;
    }
    
    return true;
}
// Función para mostrar error en un campo
function mostrarError(idCampo, mensaje) {
    const campo = document.getElementById(idCampo);
    campo.classList.add('is-invalid');
    const feedback = campo.nextElementSibling;
    if (feedback && feedback.classList.contains('invalid-feedback')) {
        feedback.textContent = mensaje;
    }
}

// Función para limpiar error de un campo
function limpiarError(idCampo) {
    const campo = document.getElementById(idCampo);
    campo.classList.remove('is-invalid');
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-cerrar alertas después de 5 segundos
    

    setTimeout(function() {
  const alertas = document.querySelectorAll('.alert');
        alertas.forEach(function(alerta) {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alerta);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endpush
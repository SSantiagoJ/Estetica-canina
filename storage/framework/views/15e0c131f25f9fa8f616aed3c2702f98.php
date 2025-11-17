<?php $__env->startSection('title', 'Gestionar Turnos - Estética Canina'); ?>

<?php $__env->startSection('header'); ?>
    <?php echo $__env->make('partials.admin_header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<!-- Agregar los CSS del admin -->
<link rel="stylesheet" href="<?php echo e(asset('css/admin_toolbar.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('css/admin_dashboard.css')); ?>">

<!-- Toolbar lateral para empleado -->
<aside class="admin-toolbar bg-primary text-white shadow-sm d-flex flex-column pt-4">
    <ul class="nav flex-column px-2">
        <li class="nav-item mb-2">
            <a href="<?php echo e(route('empleado.bandeja.reservas')); ?>" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-calendar-check fs-5"></i>
                <span class="fw-semibold">Bandeja de Reservas</span>
            </a>
        </li>
        
        <!-- Corregido: marcar como activo solo una vez y con el ícono correcto -->
        <li class="nav-item mb-2">
            <a href="<?php echo e(route('empleado.gestionar.turnos')); ?>" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect active">
                <i class="fas fa-clock fs-5"></i>
                <span class="fw-semibold">Gestionar Turnos</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="<?php echo e(route('empleado.notificaciones')); ?>" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-bell fs-5"></i>
                <span class="fw-semibold">Gestionar Notificaciones</span>
            </a>
        </li>

        
        <li class="nav-item mb-2">
            <a href="<?php echo e(route('empleado.gestionar.novedades')); ?>" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-bell fs-5"></i>
                <span class="fw-semibold">Gestionar Novedades</span>
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="<?php echo e(route('dashboard')); ?>" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-home fs-5"></i>
                <span class="fw-semibold">Web Cliente</span>
            </a>
        </li>
    </ul>
</aside>

<!-- Contenido principal -->
<main class="admin-content">
    <!-- Alertas mejoradas con mejor estructura -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <span><?php echo e(session('error')); ?></span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Card principal -->
    <div class="card shadow-sm border-0">
    <!-- Título principal fuera del card -->
            <hr class="my-2"> 
            <h2 class="fw-bold text-dark mb-4 text-center">
                <i class="fas fa-calendar-alt me-2"></i> Gestionar Turnos
            </h2>
            <div class="card-body">
                <div class="mb-4">
                    <button type="button" class="btn btn-primary btn-lg" onclick="abrirModalNuevo()">
                        <i class="fas fa-plus me-2"></i> NUEVO TURNO
                    </button>
                </div>
                 <!-- Sección de filtros con fondo gris y mejor organización -->
                <div class="filters-section mb-4">
                        <h5 class="mb-3 text-secondary">
                            <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                        </h5>
                    <div class="row g-3 mb-3">
                        <div class="col-md-2">
                            <label class="form-label fw-medium">Tipo documento</label>
                            <select class="form-select" id="filtroTipoDoc">
                                <option value="">Todos</option>
                                <option value="DNI">DNI</option>
                                <option value="CE">CE</option>
                                <option value="PASAPORTE">PASAPORTE</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-medium">N° Documento</label>
                            <input type="text" class="form-control" id="filtroNumDoc" placeholder="Ingrese número">
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Trabajador</label>
                            <select class="form-select" id="filtroTrabajador">
                                <option value="">Seleccionar</option>
                                <?php $__currentLoopData = $empleados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empleado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($empleado->id_empleado); ?>">
                                        <?php echo e($empleado->persona->nombres); ?> <?php echo e($empleado->persona->apellidos); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Fecha</label>
                            <input type="date" class="form-control" id="filtroFecha">
                        </div>
                        
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button class="btn btn-primary flex-grow-1" onclick="buscarTurnos()">
                                <i class="fas fa-search me-1"></i> Buscar
                            </button>
                            <button class="btn btn-secondary" onclick="limpiarFiltros()" title="Limpiar filtros">
                                <i class="bi bi-arrow-clockwise"></i>x
                            </button>
                        </div>
                    </div>
                </div>

            <!-- Tabla con scroll y mejor diseño -->
            <div class="table-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-secondary mb-0">
                        <i class="fas fa-list me-2"></i>Listado de turnos
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover text-align: center">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Trabajador</th>               
                            <th>Tipo Doc</th>
                            <th>N° Documento</th>
                            <th>Fecha</th>
                            <th>Hora inicio</th>
                            <th>Hora fin</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaTurnos">
                        <?php $__empty_1 = true; $__currentLoopData = $turnos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $turno): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($index + 1); ?></td>
                                <td>
                                    <?php echo e($turno->empleado->persona->nombres ?? 'N/A'); ?> 
                                    <?php echo e($turno->empleado->persona->apellidos ?? ''); ?>

                                </td>
                                <td><?php echo e($turno->empleado->persona->tipo_doc ?? 'DNI'); ?></td>
                                <td><?php echo e($turno->empleado->persona->nro_documento ?? 'N/A'); ?></td>
                                <td><?php echo e(\Carbon\Carbon::parse($turno->fecha)->format('d/m/Y')); ?></td>
                                <td><?php echo e($turno->hora_inicio); ?></td>
                                <td><?php echo e($turno->hora_fin); ?></td>
                                <td>
                                    <?php if($turno->estado == 'D'): ?>
                                        <span class="badge bg-success">DISPONIBLE</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">OCUPADO</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-outline-primary btn-sm" 
                                            onclick="editarTurno(<?php echo e($turno->id_turno); ?>, <?php echo e($turno->id_empleado); ?>, '<?php echo e($turno->fecha); ?>', '<?php echo e($turno->hora_inicio); ?>', '<?php echo e($turno->hora_fin); ?>', '<?php echo e($turno->estado); ?>')">
                                        <i class="fas fa-edit me-1"></i> EDITAR
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    <p>No hay turnos registrados</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
            <!-- Contador de registros -->
            <div class="mt-3">
                <small class="badge bg-primary">
                   Total: <span id="totalRegistros"> <?php echo e($turnos->count()); ?></span> registros
                </small>
            </div>
        </div>
    </div>
</main>

<!-- Modal Registrar Turno con diseño mejorado y centrado -->
<div class="modal fade" id="modalRegistrarTurno" tabindex="-1" aria-labelledby="modalRegistrarTurnoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body">
                <h4  id="modalRegistrarTurnoLabel">
                    <i class="fas fa-plus-circle me-2"></i> Registrar Turno
                </h4>
                <form id="formRegistrarTurno" action="<?php echo e(route('empleado.turnos.store')); ?>" method="POST" onsubmit="return validarFormularioRegistro()">
                    <?php echo csrf_field(); ?>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Trabajador <span class="text-danger">*</span></label>
                        <select class="form-select" name="id_empleado" id="registroEmpleado" required>
                            <option value="">Seleccione un trabajador</option>
                            <?php $__currentLoopData = $empleados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empleado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($empleado->id_empleado); ?>">
                                    <?php echo e($empleado->persona->nombres); ?> <?php echo e($empleado->persona->apellidos); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione un trabajador</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-medium">Fecha <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha" id="registroFecha" required>
                            <div class="invalid-feedback">Por favor ingrese una fecha</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Hora inicio <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="hora_inicio" id="registroHoraInicio" required>
                            <div class="invalid-feedback">Por favor ingrese hora de inicio</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Hora fin <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="hora_fin" id="registroHoraFin" required>
                            <div class="invalid-feedback">Por favor ingrese hora de fin</div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-medium">Estado <span class="text-danger">*</span></label>
                        <select class="form-select" name="estado" id="registroEstado" required>
                            <option value="">Seleccione un estado</option>
                            <option value="D">DISPONIBLE</option>
                            <option value="O">OCUPADO</option>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione un estado</div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary px-4" onclick="cerrarModal('modalRegistrarTurno')">
                            <i class="fas fa-times me-1"></i> CANCELAR
                        </button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> GUARDAR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Turno con diseño mejorado y centrado -->
<div class="modal fade" id="modalEditarTurno" tabindex="-1" aria-labelledby="modalEditarTurnoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h4 id="modalEditarTurnoLabel">
                    <i class="fas fa-edit me-2"></i> Editar Turno
                </h4>
                <form id="formEditarTurno" action="<?php echo e(route('empleado.turnos.store')); ?>" method="POST" enctype="multipart/form-data" onsubmit="return validarFormularioEdicion()">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="row g-3">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Trabajador <span class="text-danger">*</span></label>
                            <select class="form-select" name="id_empleado" id="editEmpleado" required>
                                <?php $__currentLoopData = $empleados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empleado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($empleado->id_empleado); ?>">
                                    <?php echo e($empleado->persona->nombres); ?> <?php echo e($empleado->persona->apellidos); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                             </select>
                            <div class="invalid-feedback">Por favor seleccione un trabajador</div>
                        </div>
                        <div class="row mb-3">
                            <label class="form-label fw-medium">Fecha <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha" id="editFecha" required>
                            <div class="invalid-feedback">Por favor ingrese una fecha</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Hora inicio <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="hora_inicio" id="editHoraInicio" required>
                            <div class="invalid-feedback">Por favor ingrese hora de inicio</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Hora fin <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="hora_fin" id="editHoraFin" required>
                            <div class="invalid-feedback">Por favor ingrese hora de fin</div>
                        </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Estado <span class="text-danger">*</span></label>
                        <select class="form-select" name="estado" id="editEstado" required>
                            <option value="D">DISPONIBLE</option>
                            <option value="O">OCUPADO</option>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione un estado</div>
                    </div>
                </div> 
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-secondary px-4" onclick="cerrarModal('modalEditarTurno')">
                            <i class="fas fa-times me-1"></i> CANCELAR
                        </button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> GUARDAR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function abrirModalNuevo() {
    console.log('[v0] Abriendo modal de nuevo turno...');
    
    // Limpiar el formulario
    document.getElementById('formRegistrarTurno').reset();
    
    // Intentar con jQuery primero
    if (typeof $ !== 'undefined' && $.fn.modal) {
        $('#modalRegistrarTurno').modal('show');
        return;
    }
    
    // Luego con Bootstrap 5
    if (typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(document.getElementById('modalRegistrarTurno'));
        modal.show();
        return;
    }
    
    // Fallback: manipulación directa del DOM
    const modalElement = document.getElementById('modalRegistrarTurno');
    modalElement.classList.add('show');
    modalElement.style.display = 'block';
    document.body.classList.add('modal-open');
    
    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';
    document.body.appendChild(backdrop);
}

function cerrarModal(modalId) {
    console.log('[v0] Cerrando modal:', modalId);
    
    // Intentar con jQuery primero
    if (typeof $ !== 'undefined' && $.fn.modal) {
        $('#' + modalId).modal('hide');
        return;
    }
    
    // Luego con Bootstrap 5
    if (typeof bootstrap !== 'undefined') {
        const modalElement = document.getElementById(modalId);
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
        return;
    }
    
    // Fallback: manipulación directa del DOM
    const modalElement = document.getElementById(modalId);
    modalElement.classList.remove('show');
    modalElement.style.display = 'none';
    document.body.classList.remove('modal-open');
    
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) {
        backdrop.remove();
    }
    
    // Limpiar formulario
    if (modalId === 'modalRegistrarTurno') {
        document.getElementById('formRegistrarTurno').reset();
    }
}

// Función para editar turno
function editarTurno(id, idEmpleado, fecha, horaInicio, horaFin, estado) {
    console.log('[v0] Editando turno:', id);
    
    // Actualizar la acción del formulario
    document.getElementById('formEditarTurno').action = `/empleado/turnos/${id}`;
    
    // Llenar los campos del modal
    document.getElementById('editEmpleado').value = idEmpleado;
    document.getElementById('editFecha').value = fecha;
    document.getElementById('editHoraInicio').value = horaInicio;
    document.getElementById('editHoraFin').value = horaFin;
    document.getElementById('editEstado').value = estado;
    
     // Mostrar el modal usando jQuery o Bootstrap
    if (typeof $ !== 'undefined' && $.fn.modal) {
        $('#modalEditarTurno').modal('show');
    } 
    else if (typeof bootstrap !== 'undefined') {
    const modal = new bootstrap.Modal(document.getElementById('modalEditarTurno'));
    modal.show();
     }
    else {
        const modalElement = document.getElementById('modalEditarTurno');
        modalElement.classList.add('show');
        modalElement.style.display = 'block';
        modalElement.setAttribute('aria-modal', 'true');
        modalElement.removeAttribute('aria-hidden');
        
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(backdrop);
        document.body.classList.add('modal-open');
    }

}

function buscarTurnos() {
    console.log('[v0] Iniciando búsqueda de turnos...');
    
    const filtroTipoDoc = document.getElementById('filtroTipoDoc').value.trim();
    const filtroNumDoc = document.getElementById('filtroNumDoc').value.trim();
    const filtroTrabajador = document.getElementById('filtroTrabajador').value;
    const filtroFecha = document.getElementById('filtroFecha').value;
    
    let nombreTrabajadorSeleccionado = '';
    if (filtroTrabajador !== '') {
        const selectTrabajador = document.getElementById('filtroTrabajador');
        nombreTrabajadorSeleccionado = selectTrabajador.options[selectTrabajador.selectedIndex].text.trim().replace(/\s+/g, ' ');
    }
    
    const filas = document.querySelectorAll('#tablaTurnos tr');
    let filasVisibles = 0;
    
    filas.forEach(function(fila) {
        const trabajadorFila = fila.cells[1].textContent.trim().replace(/\s+/g, ' ');
        const tipoDocFila = fila.cells[2].textContent.trim();
        const numDocFila = fila.cells[3].textContent.trim();
        const fechaTexto = fila.cells[4].textContent.trim();
        
        const partesFecha = fechaTexto.split('/');
        const fechaFormateada = `${partesFecha[2]}-${partesFecha[1]}-${partesFecha[0]}`;
        
        let coincide = true;
        
        if (filtroTipoDoc !== '' && tipoDocFila !== filtroTipoDoc) {
            coincide = false;
        }
        if (filtroNumDoc !== '' && !numDocFila.includes(filtroNumDoc)) {
            coincide = false;
        }
        if (filtroTrabajador !== '' && trabajadorFila !== nombreTrabajadorSeleccionado) {
            coincide = false;
        }
        if (filtroFecha !== '' && fechaFormateada !== filtroFecha) {
            coincide = false;
        }
        
        if (coincide) {
            fila.style.display = '';
            filasVisibles++;
        } else {
            fila.style.display = 'none';
        }
    });
    
    console.log('[v0] Filas visibles:', filasVisibles);
    
    if (filasVisibles === 0) {
        alert('No se encontraron turnos con los criterios de búsqueda');
    }
}

function limpiarFiltros() {
    console.log('[v0] Limpiando filtros...');
    
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
    
    const empleado = document.getElementById('registroEmpleado').value;
    const fecha = document.getElementById('registroFecha').value;
    const horaInicio = document.getElementById('registroHoraInicio').value;
    const horaFin = document.getElementById('registroHoraFin').value;
    const estado = document.getElementById('registroEstado').value;
    
    let hayErrores = false;
    
    if (empleado === '') {
        mostrarError('registroEmpleado', 'Debe seleccionar un trabajador');
        hayErrores = true;
    } else {
        limpiarError('registroEmpleado');
    }
    
    if (fecha === '') {
        mostrarError('registroFecha', 'Debe ingresar una fecha');
        hayErrores = true;
    } else {
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
    
    if (horaInicio === '') {
        mostrarError('registroHoraInicio', 'Debe ingresar hora de inicio');
        hayErrores = true;
    } else {
        limpiarError('registroHoraInicio');
    }
    
    if (horaFin === '') {
        mostrarError('registroHoraFin', 'Debe ingresar hora de fin');
        hayErrores = true;
    } else {
        limpiarError('registroHoraFin');
    }
    
    if (horaInicio !== '' && horaFin !== '' && horaFin <= horaInicio) {
        mostrarError('registroHoraFin', 'La hora de fin debe ser mayor que la hora de inicio');
        hayErrores = true;
    }
    
    if (estado === '') {
        mostrarError('registroEstado', 'Debe seleccionar un estado');
        hayErrores = true;
    } else {
        limpiarError('registroEstado');
    }
    
    return !hayErrores;
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
    
    return !hayErrores;
}

function mostrarError(idCampo, mensaje) {
    const campo = document.getElementById(idCampo);
    campo.classList.add('is-invalid');
    const feedback = campo.nextElementSibling;
    if (feedback && feedback.classList.contains('invalid-feedback')) {
        feedback.textContent = mensaje;
    }
}

function limpiarError(idCampo) {
    const campo = document.getElementById(idCampo);
    campo.classList.remove('is-invalid');
}

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alertas = document.querySelectorAll('.alert');
        alertas.forEach(function(alerta) {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alerta);
            bsAlert.close();
        });
    }, 5000);
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ProyectoSpa\Estetica-canina\resources\views/empleado/gestionar-turnos.blade.php ENDPATH**/ ?>
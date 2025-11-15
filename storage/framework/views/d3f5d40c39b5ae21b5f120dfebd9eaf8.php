<?php $__env->startSection('title', 'Gestionar Reserva - Estética Canina'); ?>

<?php $__env->startSection('header'); ?>
    <?php echo $__env->make('partials.admin_header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<!-- <CHANGE> Agregar los CSS del admin -->
<link rel="stylesheet" href="<?php echo e(asset('css/admin/admin_toolbar.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('css/admin/admin_dashboard.css')); ?>">

<!-- Toolbar lateral para empleado -->
<aside class="admin-toolbar bg-primary text-white shadow-sm d-flex flex-column pt-4">
    <ul class="nav flex-column px-2">
        <!-- <CHANGE> Usar la ruta correcta: empleado.bandeja.reservas -->
        <li class="nav-item mb-2">
            <a href="<?php echo e(route('empleado.bandeja.reservas')); ?>" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect active">
                <i class="fas fa-calendar-check fs-5"></i>
                <span class="fw-semibold">Bandeja de Reservas</span>
            </a>
        </li>
        
        <!-- <CHANGE> Agregar gestión de turnos -->
        <li class="nav-item mb-2">
            <a href="<?php echo e(route('empleado.gestionar.turnos')); ?>" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-clock fs-5"></i>
                <span class="fw-semibold">Gestionar Turnos</span>
            </a>
        </li>
        
        <!-- <CHANGE> Agregar gestión de novedades -->
        <li class="nav-item mb-2">
            <a href="<?php echo e(route('empleado.gestionar.novedades')); ?>" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-bell fs-5"></i>
                <span class="fw-semibold">Gestionar Novedades</span>
            </a>
        </li>

        <!-- <CHANGE> Opcional: agregar enlace al dashboard general -->
        <li class="nav-item mb-2">
            <a href="<?php echo e(route('dashboard')); ?>" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-home fs-5"></i>
                <span class="fw-semibold">Web Cliente</span>
            </a>
        </li>
    </ul>
</aside>

<!-- Mejorar contenedor principal con mejor estructura -->
<main class="admin-content">
    <!-- Mensajes de éxito/error -->
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
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Card con estructura optimizada -->
    <div class="card shadow-sm border-0">
            <!-- Título principal fuera del card -->
        <h2 class="fw-bold text-dark text-center">
            <i class="fas fa-bell me-2"></i> Bandeja de reservas
        </h2>
        <div class="card-body">
            <!-- Sección de filtros con mejor organización -->
            <div class="filters-section mb-4">
                <h5 class="mb-3 text-secondary">
                    <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                </h5>
                
                <!-- Filtros - Fila 1 -->
                <div class="row g-3 mb-3">
                    <div class="col-md-2">
                        <label class="form-label">Empleado</label>
                        <select class="form-select" id="filtroEmpleado">
                            <option value="">Seleccionar</option>
                            <?php $__currentLoopData = $empleados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $empleado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($empleado->id_empleado); ?>">
                                    <?php echo e($empleado->persona->nombres ?? ''); ?> <?php echo e($empleado->persona->apellidos ?? ''); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                        <button class="btn btn-primary flex-grow-1" onclick="buscarReservas()">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>
                        <button class="btn btn-secondary" onclick="limpiarFiltros()" title="Limpiar filtros">
                            <i class="bi bi-arrow-clockwise"> X </i>
                        </button>
                    </div>
                </div>

                <!-- Filtros - Fila 2 -->
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nombre mascota</label>
                        <input type="text" class="form-control" id="filtroMascota" placeholder="Escribe aquí">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha inicio reserva</label>
                        <input type="date" class="form-control" id="filtroFechaInicio">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha fin reserva</label>
                        <input type="date" class="form-control" id="filtroFechaFin">
                    </div>
                </div>
            </div>

            <!-- Separador visual <hr class="my-4"> -->
            <!-- Tabla de reservas con estructura mejorada -->
            <div class="table-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-secondary mb-0">
                        <i class="fas fa-list me-2"></i>Listado de Reservas
                    </h5>
                    
                </div>

                <div class="table-responsive">
                    <table class="table table-hover text-align: center">
                        <thead>
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
                            <?php $__empty_1 = true; $__currentLoopData = $reservas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $reserva): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr data-empleado-id="<?php echo e($reserva->id_empleado); ?>">
                                <td><?php echo e($reserva->id_reserva); ?></td>
                                <td><?php echo e(\Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y')); ?></td>
                                <td><?php echo e(\Carbon\Carbon::parse($reserva->hora)->format('H:i')); ?></td>
                                <td>
                                    <?php if($reserva->detalles->isNotEmpty()): ?>
                                        <?php echo e($reserva->detalles->first()->servicio->nombre ?? 'N/A'); ?>

                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($reserva->detalles->isNotEmpty() && $reserva->detalles->first()->servicio): ?>
                                        <?php echo e($reserva->detalles->first()->servicio->duracion ?? 'N/A'); ?>

                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($reserva->cliente->persona->nro_documento ?? 'N/A'); ?></td>
                                <td>
                                    <?php echo e($reserva->cliente->persona->nombres ?? 'N/A'); ?> 
                                    <?php echo e($reserva->cliente->persona->apellidos ?? ''); ?>

                                </td>
                                <td><?php echo e($reserva->mascota->especie ?? 'N/A'); ?></td>
                                <td class="text-center">
                                    <?php if($reserva->enfermedad): ?>
                                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <?php else: ?>
                                        <i class="bi bi-dash-circle text-muted fs-5"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if($reserva->vacuna): ?>
                                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <?php else: ?>
                                        <i class="bi bi-dash-circle text-muted fs-5"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($reserva->estado == 'P'): ?>
                                        <span class="badge bg-warning text-dark">PENDIENTE</span>
                                    <?php elseif($reserva->estado == 'A'): ?>
                                        <span class="badge bg-info text-white">ATENDIDO</span>
                                    <?php elseif($reserva->estado == 'C'): ?>
                                        <span class="badge bg-secondary">CANCELADO</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($reserva->mascota->nombre ?? 'N/A'); ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                       <button type="button" 
                                                class="btn btn-sm btn-outline-primary"
                                                onclick="abrirModalVerReserva({
                                                    id_reserva: '<?php echo e($reserva->id_reserva); ?>',
                                                    fecha: '<?php echo e(\Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y')); ?>',
                                                    hora: '<?php echo e(\Carbon\Carbon::parse($reserva->hora)->format('H:i')); ?>',
                                                    nombres: '<?php echo e($reserva->cliente->persona->nombres ?? ''); ?>',
                                                    apellidos: '<?php echo e($reserva->cliente->persona->apellidos ?? ''); ?>',
                                                    nombre_mascota: '<?php echo e($reserva->mascota->nombre ?? ''); ?>',
                                                    especie: '<?php echo e($reserva->mascota->especie ?? ''); ?>',
                                                    vacuna: '<?php echo e($reserva->vacuna ? 'Sí' : 'No'); ?>',
                                                    enfermedad: '<?php echo e($reserva->enfermedad ? 'Sí' : 'No'); ?>',
                                                    alergia: '<?php echo e($reserva->mascota->alergia ?? 'Ninguna'); ?>',
                                                    descripcion_alergia: '<?php echo e($reserva->mascota->descripcion_alergia ?? 'Sin información'); ?>',
                                                    estado: '<?php echo e($reserva->estado == 'P' ? 'PENDIENTE' : ($reserva->estado == 'A' ? 'ATENDIDO' : 'CANCELADO')); ?>',
                                                    nombre_servicio: '<?php echo e($reserva->detalles->isNotEmpty() ? $reserva->detalles->first()->servicio->nombre : 'N/A'); ?>',
                                                   descripcion_atencion: '<?php echo e(($reserva->atencion && $reserva->atencion->descripcion) ? addslashes($reserva->atencion->descripcion) : ''); ?>',
                                                    comentarios_atencion: '<?php echo e(($reserva->atencion && $reserva->atencion->comentarios) ? addslashes($reserva->atencion->comentarios) : ''); ?>'
                                                })">
                                            <i class="fas fa-eye me-1"></i> VER
                                        </button>
                                        <?php if($reserva->estado == 'P'): ?>
                                            <!-- Cambiar el botón ATENDER para que abra un modal -->
                                            <button type="button" 
                                                    class="btn btn-sm btn-success"
                                                    onclick="abrirModalAtencion(
                                                        <?php echo e($reserva->id_reserva); ?>,
                                                        '<?php echo e($reserva->mascota->nombre ?? 'N/A'); ?>',
                                                        '<?php echo e(($reserva->cliente->persona->nombres ?? '')); ?> <?php echo e(($reserva->cliente->persona->apellidos ?? '')); ?>',
                                                        '<?php echo e($reserva->vacuna ? 'Sí' : 'No'); ?>',
                                                        '<?php echo e($reserva->enfermedad ? 'Sí' : 'No'); ?>',
                                                        '<?php echo e($reserva->mascota->descripcion_alergia ?? 'Sin información'); ?>',
                                                        '<?php echo e($reserva->mascota->alergia ?? 'Ninguna'); ?>'
                                                    )">
                                                    <i class="fas fa-check me-1"></i> ATENDER
                                                </button>
                                       <?php elseif($reserva->estado == 'A'): ?>
                                            <button type="button" class="btn btn-sm btn-secondary" disabled>
                                                <i class="fas fa-check me-1"></i> ATENDIDO
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="13" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block text-secondary"></i>
                                    No hay reservas registradas
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                <span class="badge bg-primary">
                        Total: <span id="totalRegistros"><?php echo e($reservas->count()); ?></span> registros
                    </span>
                    </div>
            </div>
        </div>
    </div>
</main>


<!-- Agregar modal para registrar atención -->
<div class="modal fade" id="modalAtencion" tabindex="-1" aria-labelledby="modalAtencionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="modal-title" id="modalAtencionLabel">
                    <i class="fas fa-notes-medical me-2"></i>Registrar Atención
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            <form action="<?php echo e(route('empleado.reservas.guardarAtencion')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                
                    <input type="hidden" id="atencion_id_reserva" name="id_reserva">
                    
                    <!-- Información de la reserva (solo lectura) -->
                    <div class="alert alert-info mb-4">
                        <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Información de la Reserva</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">ID Reserva:</label>
                                <p class="mb-0" id="info_id_reserva">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mascota:</label>
                                <p class="mb-0" id="info_mascota">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Cliente:</label>
                                <p class="mb-0" id="info_cliente">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Vacuna:</label>
                                <p class="mb-0" id="info_vacuna">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Enfermedad:</label>
                                <p class="mb-0" id="info_enfermedad">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Alergia:</label>
                                <p class="mb-0" id="info_alergia">-</p>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Descripción Alergia:</label>
                                <p class="mb-0" id="info_descripcion_alergia">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Campos editables -->
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" 
                                      id="atencion_descripcion" 
                                      name="descripcion" 
                                      rows="4" 
                                      placeholder="Describa el servicio realizado, condiciones de la mascota, etc."
                                      required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Comentarios</label>
                            <textarea class="form-control" 
                                      id="atencion_comentarios" 
                                      name="comentarios" 
                                      rows="3" 
                                      placeholder="Comentarios adicionales u observaciones"></textarea>
                        </div>
                        </div>
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModalAtencion()">
                        <i class="fas fa-times me-1"></i> CANCELAR
                    </button>
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save me-1"></i> GUARDAR
                    </button>
                    </div>
                    </div>
                 </form>
            </div>
        </div>
    </div>
</div>


<!-- Agregar modal para visualizar detalles de la reserva -->
<div class="modal fade" id="modalVerReserva" tabindex="-1" aria-labelledby="modalVerReservaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalVerReservaLabel">
                    <i class="fas fa-eye me-2"></i>Detalles de la Reserva
                </h5>
                 <button type="button" class="btn-close btn-close-white" onclick="cerrarModalVerReserva()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <!-- Información de la Reserva -->
                    <div class="col-12">
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-calendar-alt me-2"></i>Información de la Reserva
                        </h6>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted">ID Reserva:</label>
                        <p class="mb-0 fw-bold" id="ver_id_reserva">-</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted">Fecha:</label>
                        <p class="mb-0" id="ver_fecha">-</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted">Hora:</label>
                        <p class="mb-0" id="ver_hora">-</p>
                    </div>
                    
                    <!-- Información del Cliente -->
                    <div class="col-12">
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3 mt-2">
                            <i class="fas fa-user me-2"></i>Información del Cliente
                        </h6>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">Nombres:</label>
                        <p class="mb-0" id="ver_nombres">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">Apellidos:</label>
                        <p class="mb-0" id="ver_apellidos">-</p>
                    </div>
                    
                    <!-- Información de la Mascota -->
                    <div class="col-12">
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3 mt-2">
                            <i class="fas fa-paw me-2"></i>Información de la Mascota
                        </h6>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted">Nombre Mascota:</label>
                        <p class="mb-0" id="ver_nombre_mascota">-</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted">Especie:</label>
                        <p class="mb-0" id="ver_especie">-</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted">Vacuna:</label>
                        <p class="mb-0" id="ver_vacuna_detalle">-</p>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted">Enfermedad:</label>
                        <p class="mb-0" id="ver_enfermedad_detalle">-</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted">Alergia:</label>
                        <p class="mb-0" id="ver_alergia_detalle">-</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted">Estado:</label>
                        <p class="mb-0" id="ver_estado">-</p>
                    </div>
                    
                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-muted">Descripción Alergia:</label>
                        <p class="mb-0" id="ver_descripcion_alergia_detalle">-</p>
                    </div>
                    
                    <!-- Información del Servicio -->
                    <div class="col-12">
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3 mt-2">
                            <i class="fas fa-cut me-2"></i>Información del Servicio
                        </h6>
                    </div>
                    
                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-muted">Nombre del Servicio:</label>
                        <p class="mb-0" id="ver_nombre_servicio">-</p>
                    </div>
                     
                    <!-- Información de la Atención -->
                    <div class="col-12">
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3 mt-2">
                            <i class="fas fa-notes-medical me-2"></i>Información de la Atención
                        </h6>
                    </div>
                    
                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-muted">Descripción:</label>
                        <p class="mb-0" id="ver_descripcion_atencion">-</p>
                    </div>
                    
                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-muted">Comentarios:</label>
                        <p class="mb-0" id="ver_comentarios_atencion">-</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cerrarModalVerReserva()">
                    <i class="fas fa-times me-1"></i> CERRAR
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function abrirModalAtencion(idReserva, nombreMascota, nombreCliente, vacuna, enfermedad, descripcionAlergia, alergia) {
    console.log('[v0] Abriendo modal de atención para reserva:', idReserva);
    
    // Llenar campos ocultos y de información
    document.getElementById('atencion_id_reserva').value = idReserva;
    document.getElementById('info_id_reserva').textContent = idReserva;
    document.getElementById('info_mascota').textContent = nombreMascota;
    document.getElementById('info_cliente').textContent = nombreCliente;
    document.getElementById('info_vacuna').textContent = vacuna;
    document.getElementById('info_enfermedad').textContent = enfermedad;
    document.getElementById('info_alergia').textContent = alergia;
    document.getElementById('info_descripcion_alergia').textContent = descripcionAlergia;
    
    // Limpiar campos editables
    document.getElementById('atencion_descripcion').value = '';
    document.getElementById('atencion_comentarios').value = '';
    
    // Abrir modal
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery('#modalAtencion').modal('show');
    } else if (typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(document.getElementById('modalAtencion'));
        modal.show();
    } else {
        const modalElement = document.getElementById('modalAtencion');
        modalElement.classList.add('show');
        modalElement.style.display = 'block';
        document.body.classList.add('modal-open');
        
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'modalAtencionBackdrop';
        document.body.appendChild(backdrop);
    }
}

function cerrarModalAtencion() {
    console.log('[v0] Cerrando modal de atención...');
    
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery('#modalAtencion').modal('hide');
    } else if (typeof bootstrap !== 'undefined') {
        const modalElement = document.getElementById('modalAtencion');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    } else {
        const modalElement = document.getElementById('modalAtencion');
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        document.body.classList.remove('modal-open');
        
        const backdrop = document.getElementById('modalAtencionBackdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
    
    // Limpiar formulario
    document.getElementById('atencion_descripcion').value = '';
    document.getElementById('atencion_comentarios').value = '';
}


function abrirModalVerReserva(reserva) {
    console.log('[v0] Abriendo modal de visualización para reserva:', reserva);
    console.log('[v0] Descripción atención recibida:', reserva.descripcion_atencion);
    console.log('[v0] Comentarios atención recibidos:', reserva.comentarios_atencion);

    // Llenar campos de información
    document.getElementById('ver_id_reserva').textContent = reserva.id_reserva || '-';
    document.getElementById('ver_fecha').textContent = reserva.fecha || '-';
    document.getElementById('ver_hora').textContent = reserva.hora || '-';
    document.getElementById('ver_nombres').textContent = reserva.nombres || '-';
    document.getElementById('ver_apellidos').textContent = reserva.apellidos || '-';
    document.getElementById('ver_nombre_mascota').textContent = reserva.nombre_mascota || '-';
    document.getElementById('ver_especie').textContent = reserva.especie || '-';
    document.getElementById('ver_vacuna_detalle').textContent = reserva.vacuna || '-';
    document.getElementById('ver_enfermedad_detalle').textContent = reserva.enfermedad || '-';
    document.getElementById('ver_alergia_detalle').textContent = reserva.alergia || '-';
    document.getElementById('ver_descripcion_alergia_detalle').textContent = reserva.descripcion_alergia || '-';
    document.getElementById('ver_estado').textContent = reserva.estado || '-';
    document.getElementById('ver_nombre_servicio').textContent = reserva.nombre_servicio || '-';
    const descripcionElement = document.getElementById('ver_descripcion_atencion');
    const comentariosElement = document.getElementById('ver_comentarios_atencion');
   // Verificar si hay datos reales de descripción
    if (reserva.descripcion_atencion && 
        reserva.descripcion_atencion !== 'Sin registro' && 
        reserva.descripcion_atencion.trim() !== '') {
        descripcionElement.textContent = reserva.descripcion_atencion;
        descripcionElement.classList.remove('text-muted', 'fst-italic');
        console.log('[v0] Mostrando descripción:', reserva.descripcion_atencion);
    } else {
        descripcionElement.textContent = 'Sin registro de atención';
        descripcionElement.classList.add('text-muted', 'fst-italic');
        console.log('[v0] Sin descripción de atención');
    }
    // Verificar si hay datos reales de comentarios
    if (reserva.comentarios_atencion && 
        reserva.comentarios_atencion !== 'Sin comentarios' && 
        reserva.comentarios_atencion.trim() !== '') {
        comentariosElement.textContent = reserva.comentarios_atencion;
        comentariosElement.classList.remove('text-muted', 'fst-italic');
        console.log('[v0] Mostrando comentarios:', reserva.comentarios_atencion);
    } else {
        comentariosElement.textContent = 'Sin comentarios registrados';
        comentariosElement.classList.add('text-muted', 'fst-italic');
        console.log('[v0] Sin comentarios de atención');
    }
    
    // Abrir modal
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery('#modalVerReserva').modal('show');
    } else if (typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(document.getElementById('modalVerReserva'));
        modal.show();
    } else {
        const modalElement = document.getElementById('modalVerReserva');
        modalElement.classList.add('show');
        modalElement.style.display = 'block';
        document.body.classList.add('modal-open');
        
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'modalVerReservaBackdrop';
        document.body.appendChild(backdrop);
    }
}


function cerrarModalVerReserva() {
    console.log('[v0] Cerrando modal de visualización...');
    
    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
        jQuery('#modalVerReserva').modal('hide');
    } else if (typeof bootstrap !== 'undefined') {
        const modalElement = document.getElementById('modalVerReserva');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    } else {
        const modalElement = document.getElementById('modalVerReserva');
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        document.body.classList.remove('modal-open');
        
        const backdrop = document.getElementById('modalVerReservaBackdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('[v0] Configurando auto-cierre de alertas...');
    
   // Seleccionar SOLO las alertas de éxito y error, NO las alertas informativas
    const alertsToClose = document.querySelectorAll('.alert-success, .alert-danger');
    
   if (alertsToClose.length > 0) {
        setTimeout(function() {
        alertsToClose.forEach(function(alert) {
                // Método 1: Usar Bootstrap 5
                if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
                // Método 2: Usar jQuery + Bootstrap
                else if (typeof jQuery !== 'undefined' && jQuery.fn.alert) {
                    jQuery(alert).alert('close');
                }
                // Método 3: Remover manualmente con animación
                else {
                    alert.classList.remove('show');
                    setTimeout(function() {
                        alert.style.display = 'none';
                        alert.remove();
                    }, 150);
                }
            });
        }, 5000); // 5 segundos
    }

});

// Función para buscar/filtrar reservas
function buscarReservas() {
    console.log('[v0] Iniciando búsqueda de reservas...');
    
    const filtroEmpleado = document.getElementById('filtroEmpleado').value;
    const filtroTipoDoc = document.getElementById('filtroTipoDoc').value.toUpperCase();
    const filtroNumDoc = document.getElementById('filtroNumDoc').value.trim();
    const filtroCliente = document.getElementById('filtroCliente').value.toLowerCase().trim();
    const filtroEstado = document.getElementById('filtroEstado').value;
    const filtroMascota = document.getElementById('filtroMascota').value.toLowerCase().trim();
    const filtroFechaInicio = document.getElementById('filtroFechaInicio').value;
    const filtroFechaFin = document.getElementById('filtroFechaFin').value;
    
    const filas = document.querySelectorAll('#tablaReservas tr');
    let filasVisibles = 0;
    
    filas.forEach(function(fila) {
        if (fila.cells.length === 1) return;
        
        const empleadoId = fila.getAttribute('data-empleado-id');
        const fechaTexto = fila.cells[1].textContent.trim();
        const numDocTexto = fila.cells[5].textContent.trim();
        const clienteTexto = fila.cells[6].textContent.trim().toLowerCase();
        const estadoTexto = fila.cells[10].textContent.trim();
        const mascotaTexto = fila.cells[11].textContent.trim().toLowerCase();
        
        const partesFecha = fechaTexto.split('/');
        const fechaFormateada = `${partesFecha[2]}-${partesFecha[1]}-${partesFecha[0]}`;
        
        let coincide = true;
        
        if (filtroEmpleado !== '' && empleadoId !== filtroEmpleado) coincide = false;
        if (filtroNumDoc !== '' && !numDocTexto.includes(filtroNumDoc)) coincide = false;
        if (filtroCliente !== '' && !clienteTexto.includes(filtroCliente)) coincide = false;
        if (filtroEstado !== '' && !estadoTexto.includes(filtroEstado === 'P' ? 'PENDIENTE' : filtroEstado === 'A' ? 'ATENDIDO' : 'CANCELADO')) coincide = false;
        if (filtroMascota !== '' && !mascotaTexto.includes(filtroMascota)) coincide = false;
        if (filtroFechaInicio !== '' && fechaFormateada < filtroFechaInicio) coincide = false;
        if (filtroFechaFin !== '' && fechaFormateada > filtroFechaFin) coincide = false;
        
        if (coincide) {
            fila.style.display = '';
            filasVisibles++;
        } else {
            fila.style.display = 'none';
        }
    });
    
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
    
    const filas = document.querySelectorAll('#tablaReservas tr');
    filas.forEach(function(fila) {
        fila.style.display = '';
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ProyectoSpa\Estetica-canina\resources\views/empleado/bandeja-reservas.blade.php ENDPATH**/ ?>
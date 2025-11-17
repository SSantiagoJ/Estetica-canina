<?php $__env->startSection('header'); ?>
    <?php echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<link rel="stylesheet" href="<?php echo e(asset('css/reservas.css')); ?>">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container reserva-container">
    <!-- Progress bar -->
    <div class="progressbar mb-5">
        <ul class="steps">
            <li>Selección de Mascotas</li>
            <li class="active">Selección de Servicio</li>
            <li>Pago</li>
            <li>Confirmación</li>
        </ul>
    </div>

    <h2 class="titulo-seccion">Selección de Servicios</h2>

    <form action="<?php echo e(route('reservas.pago')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div class="row">
            <!-- Columna principal con los servicios -->
            <div class="col-md-8">
                <!-- Servicios Disponibles -->
                <div class="mb-4">
                    <h4 class="mb-3">Servicios Disponibles</h4>
                    <div class="row">
                        <?php $__currentLoopData = $servicios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $servicio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <label class="servicio-card">
                                    <input type="checkbox" name="servicios[]" value="<?php echo e($servicio->id_servicio); ?>">
                                    <div class="card-body text-center">
                                        <?php if($servicio->imagen_referencial): ?>
                                            <?php
                                                if (str_starts_with($servicio->imagen_referencial, 'servicios/')) {
                                                    $imagenUrl = asset('storage/' . $servicio->imagen_referencial);
                                                } else {
                                                    $imagenUrl = asset('images/servicios/' . $servicio->imagen_referencial);
                                                }
                                            ?>
                                            <img src="<?php echo e($imagenUrl); ?>"
                                                 alt="<?php echo e($servicio->nombre_servicio); ?>"
                                                 class="servicio-img">
                                        <?php else: ?>
                                            <img src="<?php echo e(asset('images/servicios/default.jpg')); ?>"
                                                 alt="<?php echo e($servicio->nombre_servicio); ?>"
                                                 class="servicio-img">
                                        <?php endif; ?>
                                        <h5 class="mt-2"><?php echo e($servicio->nombre_servicio); ?></h5>
                                        <p class="text-muted">S/ <?php echo e(number_format($servicio->costo, 2)); ?></p>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <!-- Servicios Adicionales -->
                <div class="mb-4">
                    <h4 class="mb-3">Servicios Adicionales</h4>
                    <div class="row">
                        <?php $__currentLoopData = $adicionales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $servicio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <label class="servicio-card">
                                    <input type="checkbox" name="adicionales[]" value="<?php echo e($servicio->id_servicio); ?>">
                                    <div class="card-body text-center">
                                        <?php if($servicio->imagen_referencial): ?>
                                            <?php
                                                if (str_starts_with($servicio->imagen_referencial, 'servicios/')) {
                                                    $imagenUrl = asset('storage/' . $servicio->imagen_referencial);
                                                } else {
                                                    $imagenUrl = asset('images/servicios/' . $servicio->imagen_referencial);
                                                }
                                            ?>
                                            <img src="<?php echo e($imagenUrl); ?>"
                                                 alt="<?php echo e($servicio->nombre_servicio); ?>"
                                                 class="servicio-img">
                                        <?php else: ?>
                                            <img src="<?php echo e(asset('images/servicios/default.jpg')); ?>"
                                                 alt="<?php echo e($servicio->nombre_servicio); ?>"
                                                 class="servicio-img">
                                        <?php endif; ?>
                                        <h5 class="mt-2"><?php echo e($servicio->nombre_servicio); ?></h5>
                                        <p class="text-muted">S/ <?php echo e(number_format($servicio->costo, 2)); ?></p>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <!-- Columna lateral con Detalles -->
            <div class="col-md-4">
                <div class="detalles-box">
                    <h5>Detalles</h5>

                    <!-- Fecha y hora -->
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" name="fecha" id="fecha" class="form-control" required>

                        <label for="hora" class="form-label mt-2">Hora</label>
                        <input type="time" name="hora" id="hora" class="form-control" required>
                    </div>

                    <!-- Información de salud -->
                    <div class="mb-3">
                        <label class="form-label">Información de salud</label><br>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="enfermedad" value="1" id="enfermedad">
                            <label class="form-check-label" for="enfermedad">¿Mascota con enfermedad?</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="vacuna" value="1" id="vacuna">
                            <label class="form-check-label" for="vacuna">¿Vacunas completas?</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="alergia" value="1" id="alergia">
                            <label class="form-check-label" for="alergia">¿Alergias?</label>
                        </div>
                        <textarea name="descripcion_alergia" class="form-control mt-2"
                                  placeholder="Si tu mascota tiene alergia, descríbela aquí..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="acciones mt-4 d-flex justify-content-between">
            <a href="<?php echo e(route('reservas.seleccionMascota')); ?>" class="btn-cancelar">Retroceder</a>
            <button type="submit" class="btn-siguiente">Ir a Pago</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ProyectoSpa\Estetica-canina\resources\views/reservas/seleccion-servicio.blade.php ENDPATH**/ ?>
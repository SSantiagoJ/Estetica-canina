<?php $__env->startSection('title', 'Inicio - Pet Grooming'); ?>
<?php $__env->startSection('header'); ?>
    <?php echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/core/estilo_menu.css')); ?>">
<section class="contenedor1">
    <div class="contenedor2">
        <h2>Belleza y bienestar para todos los amigos peludos</h2>
        <button class="boton1">RESERVAR</button>
    </div>
    <div class="contenedor-img">
        <img src="<?php echo e(asset('perrito1.png')); ?>" alt="Perrito">
    </div>
</section>

<section class="servicios">
    <h2>Descubre nuestros tratamientos, cortes y productos pensados para consentir a tu mascota</h2>

    <!-- Dropdown personalizado -->
    <div class="custom-select" role="combobox" aria-haspopup="listbox" aria-expanded="false" tabindex="0">
        <button type="button" class="select-btn" aria-label="Seleccionar servicio">
            <span class="selected-value">Selecciona un servicio</span>
            <span class="arrow-btn" aria-hidden="true">▾</span>
        </button>

        <ul class="select-options" role="listbox" tabindex="-1" aria-label="Opciones de servicios">
            <li role="option">Baño</li>
            <li role="option">Cortes</li>
            <li role="option">Tratamientos</li>
            <li role="option">Otros servicios</li>
        </ul>
    </div>

    <!-- Botón catálogo debajo -->
    <button class="btn-catalogo">Ver catálogo</button>
</section>

<section class="banner-section">
    <img src="<?php echo e(asset('banner.png')); ?>" alt="banner" class="banner-img">
</section>

<!-- Sección Servicios Adicionales -->
<section class="extra-services">
    <h2>Servicios adicionales</h2>
    <p>
        Junto al baño y la peluquería, tenemos servicios extra que harán que tu engreído
        se sienta sano, relajado y feliz. Pídelos al agendar tu cita.
    </p>

    <div class="services-grid">
        <div class="service-column">
            <img src="<?php echo e(asset('i_1.jpg')); ?>" alt="Servicio 1">
            <img src="<?php echo e(asset('i_3.jpg')); ?>" alt="Servicio 2">
            <img src="<?php echo e(asset('i_5.jpg')); ?>" alt="Servicio 3">
        </div>

        <div class="service-column">
            <img src="<?php echo e(asset('i_2.jpg')); ?>" alt="Servicio 4">
            <img src="<?php echo e(asset('i_4.jpg')); ?>" alt="Servicio 5">
            <img src="<?php echo e(asset('i_6.jpg')); ?>" alt="Servicio 6">
        </div>
    </div>
</section>

<!-- Opiniones -->
<section class="opiniones">
    <div class="opiniones-top">
        <img src="<?php echo e(asset('perrito2.png')); ?>" alt="Opinión">
        <p>
            Tu opinión nos ayuda a seguir consintiendo a los peluditos como se merecen.
            ¡Déjanos tus comentarios!
        </p>
    </div>

    <div class="opiniones-carrusel">
        <div class="container">
            <div class="row">
                <?php $__empty_1 = true; $__currentLoopData = $calificaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $calificacion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="opinion-card card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="mb-2">
                                    <?php for($i = 0; $i < 5; $i++): ?>
                                        <i class="fas fa-star text-warning"></i>
                                    <?php endfor; ?>
                                    <span class="fecha d-block text-muted mt-1">
                                        <?php echo e(\Carbon\Carbon::parse($calificacion->fecha_creacion)->format('d/m/Y')); ?>

                                    </span>
                                </div>
                                <p class="comentario card-text">
                                    <em>"<?php echo e($calificacion->comentarios); ?>"</em>
                                </p>
                                <footer class="blockquote-footer">
                                    <h3 class="usuario mb-1"><?php echo e($calificacion->nombres); ?></h3>
                                    <small class="text-muted">
                                        Mascota: <?php echo e($calificacion->mascota_nombre); ?>

                                    </small>
                                </footer>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-12">
                        <p class="text-center text-muted">
                            Aún no hay calificaciones destacadas
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/custom_select.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ProyectoSpa\Estetica-canina\resources\views/dashboard.blade.php ENDPATH**/ ?>
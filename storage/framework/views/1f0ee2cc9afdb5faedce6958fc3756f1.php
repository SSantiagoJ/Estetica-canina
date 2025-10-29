
<?php $__env->startSection('header'); ?>
    <?php echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<link rel="stylesheet" href="<?php echo e(asset('css/reservas.css')); ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container reserva-container">
    <!-- Barra de progreso -->
    <div class="progressbar mb-5">
        <ul class="steps">
            <li class="active">Selección de Mascotas</li>
            <li>Selección de Servicio</li>
            <li>Pago</li>
            <li>Confirmación</li>
        </ul>
    </div>

    <h2 class="titulo-seccion">Selección de Mascotas</h2>

    <form action="<?php echo e(route('reservas.seleccionServicio')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="row justify-content-center">
            <?php $__currentLoopData = $mascotas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mascota): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-3 col-sm-6 mb-4">
                    <label class="mascota-card">
                        <input type="checkbox" name="mascotas[]" value="<?php echo e($mascota->id_mascota); ?>">
                        <div class="card-body text-center">
                            <img src="<?php echo e(asset('images/razas/' . $mascota->raza . '.png')); ?>"
                                alt="<?php echo e($mascota->nombre); ?>"
                                class="mascota-img">
                            <h5 class="mascota-nombre"><?php echo e($mascota->nombre); ?></h5>
                        </div>
                    </label>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="acciones mt-4">
            <a href="<?php echo e(url('/dashboard')); ?>" class="btn-cancelar">Cancelar</a>
            <button type="submit" class="btn-siguiente">Siguiente</button>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ProyectoSpa\Estetica-canina\resources\views/reservas/seleccion-mascota.blade.php ENDPATH**/ ?>
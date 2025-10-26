
<?php $__env->startSection('header'); ?>
    <?php echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<link rel="stylesheet" href="<?php echo e(asset('css/finalizar.css')); ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="completada-box text-center">
    <img src="<?php echo e(asset('images/completada.png')); ?>" alt="Reserva Completada" class="mb-4" style="width: 150px;">
    <h2>✔ Reserva Completada</h2>
    <p>Tu reserva se confirmó con éxito. Te hemos enviado un correo con los detalles.</p>

    <?php if(isset($pago)): ?>
        <div class="mt-3 d-flex gap-2 justify-content-center flex-wrap">
            
            <a href="<?php echo e(route('reservas.boleta', ['id_pago' => $pago->id_pago])); ?>" class="btn btn-success" target="_blank">
                <i class="fa-solid fa-file-pdf"></i> Ver Boleta PDF
            </a>
            
            <a href="<?php echo e(asset('storage/boletas/'.$pago->series.'.pdf')); ?>" class="btn btn-outline-primary" download>
                <i class="fa-solid fa-download"></i> Descargar (<?php echo e($pago->series); ?>)
            </a>
        </div>
    <?php endif; ?>

    <br><br>
    <a href="<?php echo e(url('/dashboard')); ?>" class="btn btn-primary">Volver al inicio</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ProyectoSpa\Estetica-canina\resources\views/reservas/completada.blade.php ENDPATH**/ ?>
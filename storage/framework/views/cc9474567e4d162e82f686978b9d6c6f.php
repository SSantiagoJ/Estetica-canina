<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

<!-- Font Awesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'PetSpa'); ?></title>
<!-- Normalmente esto es una plantilla de las paginas -->
    <!-- CSS global -->
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
    
    <?php echo $__env->yieldContent('header'); ?>

    
    <div class="main-container">
        <?php echo $__env->yieldContent('content'); ?>
    </div>
     
    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>

</html>
<?php /**PATH C:\xampp\htdocs\ProyectoSpa\Estetica-canina\resources\views/layouts/app.blade.php ENDPATH**/ ?>
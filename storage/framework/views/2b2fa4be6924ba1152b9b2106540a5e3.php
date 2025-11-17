<link rel="stylesheet" href="<?php echo e(asset('css/admin_header.css')); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


<header class="admin-site-header">
    <nav class="admin-navbar">
        <div class="admin-nav-container">
            <a href="<?php echo e(route('dashboard')); ?>" class="admin-logo-link">
                <div class="admin-logo">
                    <i class="fas fa-paw"></i>
                </div>
                <span class="admin-brand-text">Pet Grooming</span>
                <span class="admin-badge">Admin</span>
                
            </a>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-logout">Cerrar Sesión</button>
            </form>
        </div>
    </nav>
</header>

<?php /**PATH C:\xampp\htdocs\ProyectoSpa\Estetica-canina\resources\views/partials/admin_header.blade.php ENDPATH**/ ?>
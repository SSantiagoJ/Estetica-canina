<header class="site-header">
    <div class="header-container">
        <div class="header-content">
            
            <link rel="stylesheet" href="<?php echo e(asset('css/core/header.css')); ?>">
            <a href="<?php echo e(url('/dashboard')); ?>" class="logo-link">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-paw"></i>
                    </div>
                    <span class="logo-text">Pet Grooming</span>
                </div>
            </a>

            
            <nav class="navbar-desktop">
                <a href="<?php echo e(url('/catalogo')); ?>" class="nav-link">
                    <i class="fas fa-book"></i>
                    <span>Catálogo</span>
                </a>

                <a href="<?php echo e(route('reservas.seleccionMascota')); ?>" class="nav-link nav-link-primary">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Genera tu Reserva</span>
                </a>

                <a href="<?php echo e(route('reservas.mis-reservas')); ?>" class="nav-link">
                    <i class="fas fa-history"></i>
                    <span>Mis Reservas</span>
                </a>

                <a href="<?php echo e(url('/perfil')); ?>" class="nav-link">
                    <i class="fas fa-user"></i>
                    <span>Perfil</span>
                </a>

                <?php if(auth()->guard()->check()): ?>
                    
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="user-details">
                            <span class="user-name"><?php echo e(auth()->user()->nombres); ?> <?php echo e(auth()->user()->apellidos); ?></span>
                            <span class="user-role"><?php echo e(auth()->user()->rol); ?></span>
                        </div>
                    </div>
                <?php else: ?>
                    
                    <a href="<?php echo e(route('login')); ?>" class="nav-link">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </a>
                    <a href="<?php echo e(route('register')); ?>" class="nav-link nav-link-secondary">
                        <i class="fas fa-user-plus"></i>
                        <span>Register</span>
                    </a>
                <?php endif; ?>
            </nav>

            
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="fas fa-bars" id="menu-icon"></i>
            </button>
        </div>

        
        <div class="navbar-mobile" id="mobile-menu">
            <?php if(auth()->guard()->check()): ?>
                
                <div class="user-info-mobile">
                    <div class="user-avatar-mobile">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="user-details-mobile">
                        <span class="user-name-mobile"><?php echo e(auth()->user()->nombres); ?> <?php echo e(auth()->user()->apellidos); ?></span>
                        <span class="user-role-mobile"><?php echo e(auth()->user()->rol); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <a href="<?php echo e(url('/catalogo')); ?>" class="nav-link-mobile">
                <i class="fas fa-book"></i>
                <span>Catálogo</span>
            </a>

            <a href="<?php echo e(route('reservas.seleccionMascota')); ?>" class="nav-link-mobile nav-link-mobile-primary">
                <i class="fas fa-calendar-plus"></i>
                <span>Genera tu Reserva</span>
            </a>

            <a href="<?php echo e(route('reservas.mis-reservas')); ?>" class="nav-link-mobile">
                <i class="fas fa-history"></i>
                <span>Mis Reservas</span>
            </a>

            <a href="<?php echo e(url('/perfil')); ?>" class="nav-link-mobile">
                <i class="fas fa-user"></i>
                <span>Perfil</span>
            </a>

            <?php if(auth()->guard()->guest()): ?>
                
                <div class="auth-links-mobile">
                    <a href="<?php echo e(route('login')); ?>" class="nav-link-mobile">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </a>
                    <a href="<?php echo e(route('register')); ?>" class="nav-link-mobile nav-link-mobile-secondary">
                        <i class="fas fa-user-plus"></i>
                        <span>Register</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    const icon = document.getElementById('menu-icon');
    
    menu.classList.toggle('active');
    
    if (menu.classList.contains('active')) {
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-times');
    } else {
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
    }
}
</script>
<?php /**PATH C:\xampp\htdocs\ProyectoSpa\Estetica-canina\resources\views/partials/header.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">


    <title>PetSpa - Iniciar Sesión</title>

    
    <link rel="stylesheet" href="<?php echo e(asset('css/login.css')); ?>">

    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="auth-wrapper">
            <!-- Lado izquierdo - Imagen/Branding -->
            <div class="auth-left">
                <div class="brand-content">
                    <div class="logo">
                        <i class="fas fa-paw"></i>
                        <h1>PetSpa</h1>
                    </div>
                    <h2>Bienvenido de vuelta</h2>
                    <p>El mejor cuidado para tu mascota te está esperando</p>
                    <div class="decorative-elements">
                        <div class="floating-paw paw-1"><i class="fas fa-paw"></i></div>
                        <div class="floating-paw paw-2"><i class="fas fa-paw"></i></div>
                        <div class="floating-paw paw-3"><i class="fas fa-paw"></i></div>
                    </div>
                </div>
            </div>

            <!-- Lado derecho - Formulario de Login -->
            <div class="auth-right">
                <div class="form-container">
                    <div class="form-wrapper active" id="loginForm">
                        <div class="form-header">
                            <h3>Iniciar Sesión</h3>
                            <p>Ingresa tus credenciales para acceder</p>
                        </div>

                        
                        <form class="auth-form" id="loginFormElement" method="POST" action="<?php echo e(route('login.process')); ?>">
                            <?php echo csrf_field(); ?> 

                            <div class="input-group">
                                <div class="input-wrapper">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input 
                                        type="email" 
                                        id="loginEmail" 
                                        name="correo" 
                                        placeholder="Correo electrónico" 
                                        required 
                                        autocomplete="email">
                                </div>
                            </div>

                            <div class="input-group">
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input 
                                        type="password" 
                                        id="loginPassword" 
                                        name="password" 
                                        placeholder="Contraseña" 
                                        required 
                                        autocomplete="current-password">
                                    <button type="button" class="toggle-password" onclick="togglePassword('loginPassword')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-options">
                                <label class="checkbox-wrapper">
                                    <input type="checkbox" name="remember">
                                    <span class="checkmark"></span>
                                    Recordarme
                                </label>
                                <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
                            </div>

                            <button type="submit" class="btn-primary">
                                <span>Iniciar Sesión</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </form>

                        <div class="form-footer">
                            <p>¿No tienes una cuenta? 
                                <a href="<?php echo e(route('register')); ?>" class="switch-form">Regístrate aquí</a>
                            </p>
                        </div>

                        <div class="divider">
                            <span>O continúa con</span>
                        </div>

                        <div class="social-login">
                            <button class="social-btn google">
                                <i class="fab fa-google"></i>
                                Google
                            </button>
                            <button class="social-btn facebook">
                                <i class="fab fa-facebook-f"></i>
                                Facebook
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div id="toast-container"></div>

    
    <script src="<?php echo e(asset('js/login.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\ProyectoSpa\Estetica-canina\resources\views/login.blade.php ENDPATH**/ ?>
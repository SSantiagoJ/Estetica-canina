<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetSpa - Registro</title>

    {{-- CSS principal --}}
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                    <h2>Únete a nuestra familia</h2>
                    <p>Crea tu cuenta y brinda el mejor cuidado a tu mascota</p>
                    <div class="decorative-elements">
                        <div class="floating-paw paw-1"><i class="fas fa-paw"></i></div>
                        <div class="floating-paw paw-2"><i class="fas fa-paw"></i></div>
                        <div class="floating-paw paw-3"><i class="fas fa-paw"></i></div>
                    </div>
                </div>
            </div>

            <!-- Lado derecho - Formulario de Registro -->
            <div class="auth-right">
                <div class="form-container">
                    <div class="form-wrapper active" id="registerForm">
                        <div class="form-header">
                            <h3>Crear Cuenta</h3>
                            <p>Únete a nuestra familia PetSpa</p>
                        </div>

                        {{-- Formulario AJAX --}}
                        <form class="auth-form" id="registerFormElement">
                            <div class="input-row">
                                <div class="input-group">
                                    <div class="input-wrapper">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" name="nombres" id="firstName" placeholder="Nombre" required autocomplete="given-name">
                                    </div>
                                </div>
                                <div class="input-group">
                                    <div class="input-wrapper">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" name="apellidos" id="lastName" placeholder="Apellido" required autocomplete="family-name">
                                    </div>
                                </div>
                            </div>

                            <div class="input-row">
                                <div class="input-group">
                                    <div class="input-wrapper">
                                        <i class="fas fa-id-card input-icon"></i>
                                        <select name="tipo_doc" id="documentType" required>
                                            <option value="">Tipo de Documento</option>
                                            <option value="DNI">DNI</option>
                                            <option value="Cedula">Cédula</option>
                                            <option value="Pasaporte">Pasaporte</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <div class="input-wrapper">
                                        <i class="fas fa-hashtag input-icon"></i>
                                        <input type="text" name="nro_documento" id="dni" placeholder="Número de documento" required>
                                    </div>
                                </div>
                            </div>

                            <div class="input-group">
                                <div class="input-wrapper">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" name="correo" id="registerEmail" placeholder="Correo electrónico" required autocomplete="email">
                                </div>
                            </div>

                            <div class="input-group">
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" name="password" id="registerPassword" placeholder="Contraseña" required autocomplete="new-password">
                                    <button type="button" class="toggle-password" onclick="togglePassword('registerPassword')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="input-group">
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" name="password_confirmation" id="confirmPassword" placeholder="Confirmar contraseña" required autocomplete="new-password">
                                    <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-options">
                                <label class="checkbox-wrapper">
                                    <input type="checkbox" id="acceptTerms" required>
                                    <span class="checkmark"></span>
                                    Acepto los <a href="#" class="terms-link">términos y condiciones</a>
                                </label>
                            </div>

                            <button type="submit" class="btn-primary">
                                <span>Crear Cuenta</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </form>

                        <div class="form-footer">
                            <p>¿Ya tienes una cuenta? 
                                <a href="{{ url('/login') }}" class="switch-form">Inicia sesión aquí</a>
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

    {{-- JS principal --}}
    <script src="{{ asset('js/register.js') }}"></script>
</body>
</html>

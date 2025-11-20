<header class="site-header">
    <div class="header-container">
        <div class="header-content">
            {{-- Logo clickeable que regresa al menú principal --}}
            <link rel="stylesheet" href="{{ asset('css/header.css') }}">
            <a href="{{ url('/') }}" class="logo-link">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-paw"></i>
                    </div>
                    <span class="logo-text">Pet Grooming</span>
                </div>
            </a>

            {{-- Navegación Desktop --}}
            <nav class="navbar-desktop">
                <a href="{{ url('/catalogo') }}" class="nav-link">
                    <i class="fas fa-book"></i>
                    <span>Catálogo</span>
                </a>

                <a href="{{ route('reservas.seleccionMascota') }}" class="nav-link nav-link-primary">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Genera tu Reserva</span>
                </a>

                <a href="{{ route('reservas.mis-reservas') }}" class="nav-link">
                    <i class="fas fa-history"></i>
                    <span>Mis Reservas</span>
                </a>

                @auth
                    <a href="{{ url('/perfil') }}" class="nav-link">
                        <i class="fas fa-user"></i>
                        <span>Perfil</span>
                    </a>
                    
                    {{-- Información del usuario con menú desplegable --}}
                    <div class="user-info dropdown-container">
                        <div class="user-avatar dropdown-trigger" onclick="toggleUserDropdown()">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="user-details dropdown-trigger" onclick="toggleUserDropdown()">
                            <span class="user-name">{{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}</span>
                            <span class="user-role">{{ auth()->user()->rol }}</span>
                        </div>
                        <div class="user-dropdown" id="user-dropdown">
                            <div class="dropdown-header">
                                <div class="dropdown-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="dropdown-user-info">
                                    <span class="dropdown-user-name">{{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}</span>
                                    <span class="dropdown-user-email">{{ auth()->user()->correo }}</span>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="{{ url('/perfil') }}" class="dropdown-item">
                                <i class="fas fa-user"></i>
                                <span>Mi Perfil</span>
                            </a>
                            <a href="{{ route('reservas.mis-reservas') }}" class="dropdown-item">
                                <i class="fas fa-history"></i>
                                <span>Mis Reservas</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" class="dropdown-logout-form">
                                @csrf
                                <button type="submit" class="dropdown-item logout-btn">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Cerrar Sesión</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    {{-- Dropdown de Perfil con Login/Register cuando no hay sesión --}}
                    <div class="profile-dropdown-container dropdown-container">
                        <div class="profile-dropdown-trigger nav-link" onclick="toggleProfileDropdown()">
                            <i class="fas fa-user"></i>
                            <span>Perfil</span>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </div>
                        <div class="profile-dropdown" id="profile-dropdown">
                            <button type="button" class="dropdown-item" onclick="openLoginModal()">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Iniciar Sesión</span>
                            </button>
                            <button type="button" class="dropdown-item" onclick="openRegisterModal()">
                                <i class="fas fa-user-plus"></i>
                                <span>Registrarse</span>
                            </button>
                        </div>
                    </div>
                @endauth
            </nav>

            {{-- Botón menú móvil --}}
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="fas fa-bars" id="menu-icon"></i>
            </button>
        </div>

        {{-- Navegación Móvil --}}
        <div class="navbar-mobile" id="mobile-menu">
            @auth
                {{-- Info usuario en móvil solo si está autenticado --}}
                <div class="user-info-mobile">
                    <div class="user-avatar-mobile">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="user-details-mobile">
                        <span class="user-name-mobile">{{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}</span>
                        <span class="user-role-mobile">{{ auth()->user()->rol }}</span>
                    </div>
                </div>
            @endauth

            <a href="{{ url('/catalogo') }}" class="nav-link-mobile">
                <i class="fas fa-book"></i>
                <span>Catálogo</span>
            </a>

            <a href="{{ route('reservas.seleccionMascota') }}" class="nav-link-mobile nav-link-mobile-primary">
                <i class="fas fa-calendar-plus"></i>
                <span>Genera tu Reserva</span>
            </a>

            <a href="{{ route('reservas.mis-reservas') }}" class="nav-link-mobile">
                <i class="fas fa-history"></i>
                <span>Mis Reservas</span>
            </a>

            <a href="{{ url('/perfil') }}" class="nav-link-mobile">
                <i class="fas fa-user"></i>
                <span>Perfil</span>
            </a>

            @auth
                {{-- Botón de logout en móvil --}}
                <form method="POST" action="{{ route('logout') }}" class="logout-form-mobile">
                    @csrf
                    <button type="submit" class="nav-link-mobile logout-mobile-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            @endauth

            @guest
                {{-- Botones de Login y Register en móvil si no hay sesión --}}
                <div class="auth-links-mobile">
                    <button type="button" class="nav-link-mobile" onclick="openLoginModal()">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </button>
                    <button type="button" class="nav-link-mobile nav-link-mobile-secondary" onclick="openRegisterModal()">
                        <i class="fas fa-user-plus"></i>
                        <span>Register</span>
                    </button>
                </div>
            @endguest
        </div>
    </div>
</header>

{{-- Font Awesome --}}
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

function toggleUserDropdown() {
    const dropdown = document.getElementById('user-dropdown');
    dropdown.classList.toggle('active');
}

function toggleProfileDropdown() {
    const dropdown = document.getElementById('profile-dropdown');
    const trigger = document.querySelector('.profile-dropdown-trigger');
    dropdown.classList.toggle('active');
    trigger.classList.toggle('active');
}

// Cerrar dropdown al hacer click fuera
document.addEventListener('click', function(event) {
    const userDropdown = document.getElementById('user-dropdown');
    const profileDropdown = document.getElementById('profile-dropdown');
    const container = event.target.closest('.dropdown-container');
    
    if (!container) {
        if (userDropdown) {
            userDropdown.classList.remove('active');
        }
        if (profileDropdown) {
            profileDropdown.classList.remove('active');
            const trigger = document.querySelector('.profile-dropdown-trigger');
            if (trigger) trigger.classList.remove('active');
        }
    }
});
</script>

<!-- Modales de Login y Register -->
<!-- Modal de Login -->
<div id="loginModal" class="auth-modal">
    <div class="modal-overlay" onclick="closeModal('loginModal')"></div>
    <div class="modal-content">
        <!-- Lado izquierdo - Branding -->
        <div class="modal-auth-left">
            <div class="modal-brand-content">
                <div class="modal-logo">
                    <i class="fas fa-paw"></i>
                    <h1>PetSpa</h1>
                </div>
                <h2>Bienvenido de vuelta</h2>
                <p>El mejor cuidado para tu mascota te está esperando</p>
                <div class="modal-decorative-elements">
                    <div class="modal-floating-paw paw-1"><i class="fas fa-paw"></i></div>
                    <div class="modal-floating-paw paw-2"><i class="fas fa-paw"></i></div>
                    <div class="modal-floating-paw paw-3"><i class="fas fa-paw"></i></div>
                </div>
            </div>
        </div>

        <!-- Lado derecho - Formulario de Login -->
        <div class="modal-auth-right">
            <button class="modal-close" onclick="closeModal('loginModal')">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-form-container">
                <div class="modal-form-header">
                    <h3>Iniciar Sesión</h3>
                    <p>Ingresa tus credenciales para acceder</p>
                </div>

                <form class="auth-form" id="loginModalForm" method="POST" action="{{ route('login.process') }}">
                    @csrf
                    <div class="input-group">
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input 
                                type="email" 
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
                                id="modalLoginPassword" 
                                name="password" 
                                placeholder="Contraseña" 
                                required 
                                autocomplete="current-password">
                            <button type="button" class="toggle-password" onclick="toggleModalPassword('modalLoginPassword')">
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
                        <button type="button" class="switch-modal" onclick="switchToRegister()">Regístrate aquí</button>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Register -->
<div id="registerModal" class="auth-modal">
    <div class="modal-overlay" onclick="closeModal('registerModal')"></div>
    <div class="modal-content">
        <!-- Lado izquierdo - Branding -->
        <div class="modal-auth-left">
            <div class="modal-brand-content">
                <div class="modal-logo">
                    <i class="fas fa-paw"></i>
                    <h1>PetSpa</h1>
                </div>
                <h2>Únete a nuestra familia</h2>
                <p>Crea tu cuenta y brinda el mejor cuidado a tu mascota</p>
                <div class="modal-decorative-elements">
                    <div class="modal-floating-paw paw-1"><i class="fas fa-paw"></i></div>
                    <div class="modal-floating-paw paw-2"><i class="fas fa-paw"></i></div>
                    <div class="modal-floating-paw paw-3"><i class="fas fa-paw"></i></div>
                </div>
            </div>
        </div>

        <!-- Lado derecho - Formulario de Register -->
        <div class="modal-auth-right">
            <button class="modal-close" onclick="closeModal('registerModal')">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-form-container">
                <div class="modal-form-header">
                    <h3>Crear Cuenta</h3>
                    <p>Únete a nuestra familia PetSpa</p>
                </div>

                <form class="auth-form" id="registerModalForm" method="POST" action="{{ route('register.process') }}">
                    @csrf
                    <div class="input-row">
                        <div class="input-group">
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" name="nombres" placeholder="Nombre" required autocomplete="given-name">
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" name="apellidos" placeholder="Apellido" required autocomplete="family-name">
                            </div>
                        </div>
                    </div>

                    <div class="input-row">
                        <div class="input-group">
                            <div class="input-wrapper">
                                <i class="fas fa-id-card input-icon"></i>
                                <select name="tipo_doc" required>
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
                                <input type="text" name="nro_documento" placeholder="Número de documento" required>
                            </div>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" name="correo" placeholder="Correo electrónico" required autocomplete="email">
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input 
                                type="password" 
                                id="modalRegisterPassword" 
                                name="password" 
                                placeholder="Contraseña" 
                                required 
                                autocomplete="new-password">
                            <button type="button" class="toggle-password" onclick="toggleModalPassword('modalRegisterPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input 
                                type="password" 
                                id="modalConfirmPassword" 
                                name="password_confirmation" 
                                placeholder="Confirmar contraseña" 
                                required 
                                autocomplete="new-password">
                            <button type="button" class="toggle-password" onclick="toggleModalPassword('modalConfirmPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" required>
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
                        <button type="button" class="switch-modal" onclick="switchToLogin()">Inicia sesión aquí</button>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Funciones para abrir/cerrar modales
function openLoginModal() {
    closeAllDropdowns();
    document.getElementById('loginModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function openRegisterModal() {
    closeAllDropdowns();
    document.getElementById('registerModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.style.overflow = 'auto';
}

function switchToRegister() {
    closeModal('loginModal');
    openRegisterModal();
}

function switchToLogin() {
    closeModal('registerModal');
    openLoginModal();
}

function closeAllDropdowns() {
    // Cerrar dropdown de usuario si está abierto
    const userDropdown = document.getElementById('user-dropdown');
    if (userDropdown) {
        userDropdown.classList.remove('active');
    }
    
    // Cerrar dropdown de perfil si está abierto
    const profileDropdown = document.getElementById('profile-dropdown');
    if (profileDropdown) {
        profileDropdown.classList.remove('active');
        const trigger = document.querySelector('.profile-dropdown-trigger');
        if (trigger) trigger.classList.remove('active');
    }
    
    // Cerrar menú móvil si está abierto
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileMenu && mobileMenu.classList.contains('active')) {
        toggleMobileMenu();
    }
}

function toggleModalPassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Cerrar modales al presionar ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal('loginModal');
        closeModal('registerModal');
    }
});

// Manejar formulario de login modal
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginModalForm');
    const registerForm = document.getElementById('registerModalForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<span>Iniciando...</span>';
            submitBtn.disabled = true;
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirigir según la respuesta del servidor
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Error en el login');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<span>Registrando...</span>';
            submitBtn.disabled = true;
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirigir según la respuesta del servidor
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Error en el registro');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});
</script>

<!-- Estilos para los modales -->
<style>
/* Variables del login.css */
:root {
  --primary-pink: #f4c2c2;
  --soft-pink: #f8e8e8;
  --navy-blue: #2c3e50;
  --light-blue: #a8d8ea;
  --sky-blue: #e3f2fd;
  --white: #ffffff;
  --gray-100: #f8f9fa;
  --gray-200: #e9ecef;
  --gray-300: #dee2e6;
  --gray-400: #ced4da;
  --gray-500: #adb5bd;
  --gray-600: #6c757d;
  --gray-700: #495057;
  --gray-800: #343a40;
  --gray-900: #212529;
  --success: #28a745;
  --danger: #dc3545;
  --warning: #ffc107;
  --info: #17a2b8;
  --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
  --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
  --shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.15);
  --transition: all 0.3s ease;
}

/* Estilos base para modales */
.auth-modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 10000;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, var(--soft-pink) 0%, var(--sky-blue) 100%);
  padding: 20px;
}

.modal-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.3);
  backdrop-filter: blur(5px);
}

.modal-content {
  position: relative;
  background: var(--white);
  border-radius: 20px;
  box-shadow: var(--shadow-xl);
  max-width: 900px;
  width: 90%;
  max-height: 90vh;
  overflow: hidden;
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 500px;
  animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* Lado izquierdo del modal - Branding (como en login.blade.php) */
.modal-auth-left {
  background: linear-gradient(135deg, var(--primary-pink) 0%, var(--light-blue) 100%);
  padding: 40px 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.modal-brand-content {
  text-align: center;
  color: var(--white);
  z-index: 2;
}

.modal-logo {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 15px;
  margin-bottom: 30px;
}

.modal-logo i {
  font-size: 2.5rem;
  color: var(--white);
}

.modal-logo h1 {
  font-size: 2rem;
  font-weight: 700;
  color: var(--white);
  font-family: 'Poppins', sans-serif;
}

.modal-brand-content h2 {
  font-size: 1.8rem;
  font-weight: 600;
  margin-bottom: 15px;
  font-family: 'Poppins', sans-serif;
}

.modal-brand-content p {
  font-size: 1rem;
  opacity: 0.9;
  margin-bottom: 30px;
  font-family: 'Poppins', sans-serif;
}

.modal-decorative-elements {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 1;
}

.modal-floating-paw {
  position: absolute;
  color: rgba(255, 255, 255, 0.1);
  font-size: 2rem;
  animation: modalFloatPaw 6s ease-in-out infinite;
}

.modal-floating-paw.paw-1 {
  top: 20%;
  left: 10%;
  animation-delay: 0s;
}

.modal-floating-paw.paw-2 {
  top: 60%;
  right: 15%;
  animation-delay: 2s;
}

.modal-floating-paw.paw-3 {
  bottom: 25%;
  left: 20%;
  animation-delay: 4s;
}

@keyframes modalFloatPaw {
  0%, 100% { transform: translateY(0) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(5deg); }
}

/* Lado derecho del modal - Formulario */
.modal-auth-right {
  padding: 40px 30px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  position: relative;
}

.modal-close {
  position: absolute;
  top: 20px;
  right: 20px;
  background: none;
  border: none;
  color: var(--gray-500);
  font-size: 1.5rem;
  cursor: pointer;
  padding: 8px;
  border-radius: 50%;
  transition: var(--transition);
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
}

.modal-close:hover {
  background: var(--gray-100);
  color: var(--gray-700);
}

.modal-form-container {
  width: 100%;
}

.modal-form-header {
  text-align: center;
  margin-bottom: 30px;
}

.modal-form-header h3 {
  font-size: 1.8rem;
  font-weight: 600;
  color: var(--navy-blue);
  margin-bottom: 10px;
  font-family: 'Poppins', sans-serif;
}

.modal-form-header p {
  color: var(--gray-600);
  font-size: 1rem;
  font-family: 'Poppins', sans-serif;
}

/* Formulario en modales (estilos idénticos a login.css) */
.auth-form {
  margin-bottom: 30px;
}

.auth-form .input-group {
  margin-bottom: 20px;
}

.auth-form .input-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
  margin-bottom: 20px;
}

.auth-form .input-row .input-group {
  margin-bottom: 0;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-wrapper .input-icon {
  position: absolute;
  left: 15px;
  color: var(--gray-500);
  font-size: 1rem;
  z-index: 2;
}

.input-wrapper input,
.input-wrapper select {
  width: 100%;
  padding: 15px 15px 15px 45px;
  border: 2px solid var(--gray-200);
  border-radius: 12px;
  font-size: 1rem;
  font-family: 'Poppins', sans-serif;
  transition: var(--transition);
  background: var(--white);
  color: var(--gray-700);
}

.input-wrapper select {
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 15px center;
  background-size: 16px;
}

.input-wrapper input:focus,
.input-wrapper select:focus {
  outline: none;
  border-color: var(--primary-pink);
  box-shadow: 0 0 0 3px rgba(244, 194, 194, 0.1);
}

.input-wrapper input::placeholder {
  color: var(--gray-500);
}

.input-wrapper select option {
  padding: 12px 15px;
  background: var(--white);
  color: var(--gray-700);
  font-size: 1rem;
  border: none;
}

.toggle-password {
  position: absolute;
  right: 15px;
  background: none;
  border: none;
  color: var(--gray-500);
  cursor: pointer;
  font-size: 1rem;
  transition: var(--transition);
  z-index: 2;
}

.toggle-password:hover {
  color: var(--primary-pink);
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  flex-wrap: wrap;
  gap: 10px;
}

.checkbox-wrapper {
  display: flex;
  align-items: center;
  cursor: pointer;
  font-size: 0.9rem;
  color: var(--gray-700);
  font-family: 'Poppins', sans-serif;
}

.checkbox-wrapper input[type="checkbox"] {
  display: none;
}

.checkmark {
  width: 18px;
  height: 18px;
  border: 2px solid var(--gray-300);
  border-radius: 4px;
  margin-right: 8px;
  position: relative;
  transition: var(--transition);
}

.checkbox-wrapper input[type="checkbox"]:checked + .checkmark {
  background: var(--primary-pink);
  border-color: var(--primary-pink);
}

.checkbox-wrapper input[type="checkbox"]:checked + .checkmark::after {
  content: "✓";
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: var(--white);
  font-size: 12px;
  font-weight: bold;
}

.forgot-password,
.terms-link {
  color: var(--primary-pink);
  text-decoration: none;
  font-size: 0.9rem;
  transition: var(--transition);
  font-family: 'Poppins', sans-serif;
}

.forgot-password:hover,
.terms-link:hover {
  color: var(--navy-blue);
  text-decoration: underline;
}

.btn-primary {
  width: 100%;
  padding: 15px 30px;
  background: linear-gradient(135deg, var(--primary-pink) 0%, var(--light-blue) 100%);
  color: var(--white);
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  position: relative;
  overflow: hidden;
  font-family: 'Poppins', sans-serif;
  margin-bottom: 20px;
}

.btn-primary::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: var(--transition);
}

.btn-primary:hover::before {
  left: 100%;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.form-footer {
  text-align: center;
  font-size: 0.9rem;
  color: var(--gray-600);
  font-family: 'Poppins', sans-serif;
}

.switch-modal {
  background: none;
  border: none;
  color: var(--primary-pink);
  cursor: pointer;
  text-decoration: underline;
  font-size: 0.9rem;
  transition: var(--transition);
  font-family: 'Poppins', sans-serif;
}

.switch-modal:hover {
  color: var(--navy-blue);
}

/* Responsive para móviles */
@media (max-width: 768px) {
  .modal-content {
    grid-template-columns: 1fr;
    max-width: 95%;
    min-height: auto;
  }
  
  .modal-auth-left {
    padding: 30px 20px;
  }
  
  .modal-logo h1 {
    font-size: 1.5rem;
  }
  
  .modal-brand-content h2 {
    font-size: 1.3rem;
  }
  
  .modal-auth-right {
    padding: 30px 20px;
  }
  
  .auth-form .input-row {
    grid-template-columns: 1fr;
    gap: 0;
  }
  
  .auth-form .input-row .input-group {
    margin-bottom: 20px;
  }
  
  .form-options {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
}

@media (max-width: 480px) {
  .modal-content {
    width: 98%;
    margin: 10px;
  }
  
  .modal-auth-left {
    padding: 20px 15px;
  }
  
  .modal-auth-right {
    padding: 20px 15px;
  }
}
</style>

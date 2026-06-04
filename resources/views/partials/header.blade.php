<header class="site-header">
    <div class="header-container">
        <div class="header-content">
            {{-- Logo clickeable que regresa al menú principal --}}
            <link rel="stylesheet" href="{{ asset('css/header.css') }}">
            <link rel="stylesheet" href="{{ asset('css/auth-modal.css') }}">
            <a href="{{ url('/') }}" class="logo-link">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-paw"></i>
                    </div>
                    <span class="logo-text">Pet Grooming</span>
                </div>
            </a>

            {{-- Navegación Desktop --}}
            @php
                $rolHeader = auth()->check() ? auth()->user()->rol : null;
                $esPersonalInterno = in_array($rolHeader, ['Empleado', 'Supervisor', 'Admin'], true);
                $esClientePortal = !$esPersonalInterno;
                $panelInternoUrl = match ($rolHeader) {
                    'Admin' => route('admin.dashboard'),
                    'Empleado', 'Supervisor' => route('empleado.panel.del.dia'),
                    default => route('intranet.login'),
                };
            @endphp

            <nav class="navbar-desktop">
                <a href="{{ url('/catalogo') }}" class="nav-link">
                    <i class="fas fa-book"></i>
                    <span>Catálogo</span>
                </a>

                @if($esClientePortal)
                    <a href="{{ route('reservas.seleccionMascota') }}" class="nav-link nav-link-primary">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Genera tu Reserva</span>
                    </a>

                    <a href="{{ route('reservas.mis-reservas') }}" class="nav-link">
                        <i class="fas fa-history"></i>
                        <span>Mis Reservas</span>
                    </a>
                @else
                    <a href="{{ $panelInternoUrl }}" class="nav-link nav-link-primary">
                        <i class="fas fa-briefcase"></i>
                        <span>Ir al panel</span>
                    </a>
                @endif

                @guest
                    <a href="{{ route('intranet.login') }}" class="nav-link nav-link-secondary">
                        <i class="fas fa-shield-halved"></i>
                        <span>Intranet</span>
                    </a>
                @endguest

                @auth
                    @php
                        $usuarioActual = auth()->user();
                        $personaActual = $usuarioActual->persona;
                        $nombreUsuario = trim(($personaActual->nombres ?? $usuarioActual->nombres ?? '') . ' ' . ($personaActual->apellidos ?? $usuarioActual->apellidos ?? ''));
                        $nombreUsuario = $nombreUsuario !== '' ? $nombreUsuario : 'Cliente Pet Grooming';
                        $partesNombre = preg_split('/\s+/', $nombreUsuario);
                        $inicialesUsuario = strtoupper(substr($partesNombre[0] ?? 'C', 0, 1) . substr($partesNombre[1] ?? '', 0, 1));
                    @endphp
                    @if(!$esPersonalInterno)
                        <a href="{{ url('/perfil') }}" class="nav-link">
                            <i class="fas fa-user"></i>
                            <span>Perfil</span>
                        </a>
                    @endif
                    
                    {{-- Información del usuario con menú desplegable --}}
                    <div class="user-info dropdown-container">
                        <div class="user-avatar dropdown-trigger" onclick="toggleUserDropdown()">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="user-details dropdown-trigger" onclick="toggleUserDropdown()">
                            <span class="user-name">{{ $nombreUsuario }}</span>
                            <span class="user-role">{{ $usuarioActual->rol }}</span>
                        </div>
                        <div class="user-dropdown" id="user-dropdown">
                            <div class="dropdown-header">
                                <div class="dropdown-avatar">
                                    <span class="dropdown-avatar-initials">{{ $inicialesUsuario }}</span>
                                </div>
                                <div class="dropdown-user-info">
                                    <span class="dropdown-user-name">{{ $nombreUsuario }}</span>
                                    <span class="dropdown-user-email">{{ $usuarioActual->correo }}</span>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            @if($esPersonalInterno)
                                <a href="{{ $panelInternoUrl }}" class="dropdown-item">
                                    <i class="fas fa-briefcase"></i>
                                    <span>Mi Panel</span>
                                </a>
                            @else
                                <a href="{{ url('/perfil') }}" class="dropdown-item">
                                    <i class="fas fa-user"></i>
                                    <span>Mi Perfil</span>
                                </a>
                                <a href="{{ route('reservas.mis-reservas') }}" class="dropdown-item">
                                    <i class="fas fa-history"></i>
                                    <span>Mis Reservas</span>
                                </a>
                            @endif
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
                        <span class="user-name-mobile">{{ $nombreUsuario }}</span>
                        <span class="user-role-mobile">{{ $usuarioActual->rol }}</span>
                    </div>
                </div>
            @endauth

            <a href="{{ url('/catalogo') }}" class="nav-link-mobile">
                <i class="fas fa-book"></i>
                <span>Catálogo</span>
            </a>

            @if($esClientePortal)
                <a href="{{ route('reservas.seleccionMascota') }}" class="nav-link-mobile nav-link-mobile-primary">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Genera tu Reserva</span>
                </a>

                <a href="{{ route('reservas.mis-reservas') }}" class="nav-link-mobile">
                    <i class="fas fa-history"></i>
                    <span>Mis Reservas</span>
                </a>
            @else
                <a href="{{ $panelInternoUrl }}" class="nav-link-mobile nav-link-mobile-primary">
                    <i class="fas fa-briefcase"></i>
                    <span>Ir al panel</span>
                </a>
            @endif

            @auth
                @if(!$esPersonalInterno)
                    <a href="{{ url('/perfil') }}" class="nav-link-mobile">
                        <i class="fas fa-user"></i>
                        <span>Perfil</span>
                    </a>
                @endif
            @endauth

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
                    <a href="{{ route('intranet.login') }}" class="nav-link-mobile nav-link-mobile-secondary">
                        <i class="fas fa-shield-halved"></i>
                        <span>Intranet</span>
                    </a>
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

                <form class="auth-form mfa-form" id="mfaModalForm" method="POST" action="{{ route('login.mfa') }}" hidden>
                    @csrf
                    <div class="mfa-message">
                        <i class="fas fa-shield-halved"></i>
                        <span id="mfaModalMessage">Ingresa el codigo de 6 digitos enviado a tu correo.</span>
                    </div>

                    <div class="input-group">
                        <div class="input-wrapper">
                            <i class="fas fa-key input-icon"></i>
                            <input
                                type="text"
                                name="code"
                                id="modalMfaCode"
                                placeholder="Codigo MFA"
                                required
                                inputmode="numeric"
                                pattern="[0-9]{6}"
                                maxlength="6"
                                autocomplete="one-time-code">
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">
                        <span>Verificar codigo</span>
                        <i class="fas fa-shield-check"></i>
                    </button>

                    <button type="button" class="switch-modal mfa-back-btn" onclick="resetModalMfaStep()">
                        Volver al login
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
                                minlength="9"
                                pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{9,}"
                                title="Debe tener mas de 8 caracteres, mayuscula, minuscula, numero y simbolo."
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
                                minlength="9"
                                pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{9,}"
                                title="Debe tener mas de 8 caracteres, mayuscula, minuscula, numero y simbolo."
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
                            <span class="checkbox-text">
                                Acepto los <a href="#" class="terms-link">términos y condiciones</a>
                            </span>
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
function openLoginModal(redirectTo = null) {
    closeAllDropdowns();
    resetModalMfaStep();
    if (redirectTo) {
        localStorage.setItem('pet_after_login', redirectTo);
    }
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
    if (modalId === 'loginModal') {
        resetModalMfaStep();
    }
}

function switchToRegister() {
    closeModal('loginModal');
    openRegisterModal();
}

function switchToLogin() {
    closeModal('registerModal');
    openLoginModal();
}

function resetModalMfaStep() {
    const loginForm = document.getElementById('loginModalForm');
    const mfaForm = document.getElementById('mfaModalForm');
    const mfaCode = document.getElementById('modalMfaCode');

    if (loginForm) loginForm.hidden = false;
    if (mfaForm) mfaForm.hidden = true;
    if (mfaCode) mfaCode.value = '';
}

function showModalMfaStep(message) {
    const loginForm = document.getElementById('loginModalForm');
    const mfaForm = document.getElementById('mfaModalForm');
    const mfaMessage = document.getElementById('mfaModalMessage');
    const mfaCode = document.getElementById('modalMfaCode');

    if (loginForm) loginForm.hidden = true;
    if (mfaForm) mfaForm.hidden = false;
    if (mfaMessage && message) mfaMessage.textContent = message;
    if (mfaCode) mfaCode.focus();
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
    const mfaForm = document.getElementById('mfaModalForm');
    const registerForm = document.getElementById('registerModalForm');
    const reservaUrl = "{{ route('reservas.seleccionMascota') }}";

    document.querySelectorAll('[data-reserva-login]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            openLoginModal(reservaUrl);
        });
    });
    
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
                    const redirectAfterLogin = localStorage.getItem('pet_after_login');
                    localStorage.removeItem('pet_after_login');
                    window.location.href = redirectAfterLogin || data.redirect;
                } else if (data.mfa_required) {
                    showModalMfaStep(data.message || 'Ingresa el codigo MFA enviado a tu correo.');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
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

    if (mfaForm) {
        mfaForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span>Verificando...</span>';
            submitBtn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const redirectAfterLogin = localStorage.getItem('pet_after_login');
                    localStorage.removeItem('pet_after_login');
                    window.location.href = redirectAfterLogin || data.redirect;
                } else {
                    alert(data.message || 'Codigo MFA incorrecto');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexion');
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
                    const redirectAfterLogin = localStorage.getItem('pet_after_login');
                    localStorage.removeItem('pet_after_login');
                    window.location.href = redirectAfterLogin || data.redirect;
                } else if (data.mfa_required) {
                    closeModal('registerModal');
                    openLoginModal();
                    showModalMfaStep(data.message || 'Ingresa el codigo MFA enviado a tu correo.');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
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

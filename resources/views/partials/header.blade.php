<header class="site-header">
    <div class="header-container">
        <div class="header-content">
            {{-- Logo clickeable que regresa al dashboard --}}
            <link rel="stylesheet" href="{{ asset('css/core/header.css') }}">
            <a href="{{ url('/dashboard') }}" class="logo-link">
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

                <a href="{{ url('/perfil') }}" class="nav-link">
                    <i class="fas fa-user"></i>
                    <span>Perfil</span>
                </a>

                @auth
                    {{-- Información del usuario con nombre visible solo si está autenticado --}}
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="user-details">
                            <span class="user-name">{{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}</span>
                            <span class="user-role">{{ auth()->user()->rol }}</span>
                        </div>
                    </div>
                @else
                    {{-- Botones de Login y Register si no hay sesión --}}
                    <a href="{{ route('login') }}" class="nav-link">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </a>
                    <a href="{{ route('register') }}" class="nav-link nav-link-secondary">
                        <i class="fas fa-user-plus"></i>
                        <span>Register</span>
                    </a>
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

            @guest
                {{-- Botones de Login y Register en móvil si no hay sesión --}}
                <div class="auth-links-mobile">
                    <a href="{{ route('login') }}" class="nav-link-mobile">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </a>
                    <a href="{{ route('register') }}" class="nav-link-mobile nav-link-mobile-secondary">
                        <i class="fas fa-user-plus"></i>
                        <span>Register</span>
                    </a>
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
</script>

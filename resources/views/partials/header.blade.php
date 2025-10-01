<header class="site-header">
    <div class="logo">
        <img src="{{ asset('assets/logo.png') }}" alt="Pet Grooming Logo">
        <span>Pet Grooming</span>
    </div>

    <nav class="navbar">
        <a href="{{ url('/') }}">Inicio</a>
        <a href="{{ url('/about') }}">Quiénes somos</a>
        <a href="{{ url('/contact') }}">Contacto</a>

        @auth
            <!-- Vista para usuarios logueados -->
            <div class="profile">
                <i class="fas fa-user-circle"></i>
                <span>{{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}</span>
                <small class="role">({{ auth()->user()->rol }})</small>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="btn-logout">Cerrar sesión</button>
            </form>
        @else
            <!-- Vista si no hay sesión -->
            <a href="{{ route('login') }}" class="btn">Login</a>
            <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
        @endauth
    </nav>
</header>

<!-- Íconos de FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Importa el CSS del header -->
<link rel="stylesheet" href="{{ asset('css/header.css') }}">

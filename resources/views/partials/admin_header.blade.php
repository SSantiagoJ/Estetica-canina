<header class="site-header">
    <div class="logo">
        <img src="{{ asset('assets/logo.png') }}" alt="Pet Grooming Logo">
        <span>Panel Admin</span>
    </div>

    <nav class="navbar">
        <a href="{{ url('/admin_dashboard') }}">Inicio</a>
        <a href="{{ url('/manage_users') }}">Usuarios</a>
        <a href="{{ url('/reports') }}">Reportes</a>
        <a href="{{ url('/settings') }}">Configuración</a>

        @auth
            <div class="profile">
                <i class="fas fa-user-shield"></i>
                <span>{{ auth()->user()->nombres }} ({{ auth()->user()->rol }})</span>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="btn-logout">Cerrar sesión</button>
            </form>
        @endauth
    </nav>
</header>

<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- CSS del header -->
<link rel="stylesheet" href="{{ asset('css/admin_header.css') }}">

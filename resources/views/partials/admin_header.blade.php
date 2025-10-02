<link rel="stylesheet" href="{{ asset('css/admin_header.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<header class="admin-site-header">
    <nav class="admin-navbar">
        <div class="admin-nav-container">
            <!-- Logo -->
            <a href="{{ route('dashboard') }}" class="admin-logo-link">
                <div class="admin-logo">
                    <i class="fas fa-paw"></i>
                </div>
                <div class="admin-brand-container">
                    <span class="admin-brand-text">Pet Grooming</span>
                    <span class="admin-badge">ADMIN</span>
                </div>
            </a>

            <!-- Menú principal -->
            <div class="admin-nav-menu">
                <a  class="admin-nav-link">Inicio</a>
                <a  class="admin-nav-link">Catálogo</a>
                <a  class="admin-nav-link">Historial</a>
                <a  class="admin-nav-link">Reserva</a>
            </div>

            <!-- Botón logout -->
            <form action="{{ route('logout') }}" method="POST" style="margin:0">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </button>
            </form>
        </div>
    </nav>

    <!-- Toolbar de administración -->
    <div class="admin-toolbar">
        <a><i class="fas fa-calendar-check"></i> Reservas</a>
        <a><i class="fas fa-tags"></i> Promociones</a>
        <a><i class="fas fa-clock"></i> Horarios</a>
        <a><i class="fas fa-cut"></i> Servicios</a>
        <a ><i class="fas fa-chart-line"></i> Reportes</a>
    </div>
</header>

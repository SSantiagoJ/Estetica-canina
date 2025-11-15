<link rel="stylesheet" href="{{ asset('css/admin/admin_header.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

{{-- Header --}}
<header class="admin-site-header">
    <nav class="admin-navbar">
        <div class="admin-nav-container">
            <a href="{{ route('dashboard') }}" class="admin-logo-link">
                <div class="admin-logo">
                    <i class="fas fa-paw"></i>
                </div>
                <span class="admin-brand-text">Pet Grooming</span>
                <span class="admin-badge">Admin</span>
                
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">Cerrar Sesión</button>
            </form>
        </div>
    </nav>
</header>


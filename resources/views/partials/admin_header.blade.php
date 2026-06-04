<link rel="stylesheet" href="{{ asset('css/admin_header.css') }}">
@if(request()->is('empleado*') || request()->is('admin*') || request()->is('admin_dashboard') || request()->is('intranet*'))
    <link rel="stylesheet" href="{{ asset('css/empleado-ui.css') }}">
@endif
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@php
    $rolActual = auth()->user()->rol ?? 'Admin';
@endphp

{{-- Header --}}
<header class="admin-site-header">
    <nav class="admin-navbar">
        <div class="admin-nav-container">
            <a href="{{ route('home') }}" class="admin-logo-link">
                <div class="admin-logo">
                    <i class="fas fa-paw"></i>
                </div>
                <span class="admin-brand-text">Pet Grooming</span>
                <span class="admin-badge">{{ $rolActual }}</span>
            </a>

            <div class="admin-header-actions">
                @if(in_array($rolActual, ['Admin', 'Empleado', 'Supervisor'], true))
                    <a href="{{ route('intranet.perfil') }}" class="btn-admin-profile">
                        <i class="fas fa-user-circle"></i>
                        <span>Mi Perfil</span>
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">Cerrar Sesion</button>
                </form>
            </div>
        </div>
    </nav>
</header>

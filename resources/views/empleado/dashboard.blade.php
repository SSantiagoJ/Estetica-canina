@extends('layouts.header')

@section('title', 'Gestionar Turnos - Estética Canina')

@section('header')
    @include('partials.admin_header')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_toolbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/empleado-ui.css') }}">
@endpush

@section('content')

<!-- Toolbar lateral para empleado -->
<aside class="admin-toolbar bg-primary text-white shadow-sm d-flex flex-column pt-4">
    <ul class="nav flex-column px-2">
        <!-- Panel del Día -->
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.panel.del.dia') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-tachometer-alt fs-5"></i>
                <span class="fw-semibold">Panel del Día</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.dashboard') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect active">
                <i class="fas fa-calendar-check fs-5"></i>
                <span class="fw-semibold">Dashboard</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.bandeja.reservas') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-calendar-check fs-5"></i>
                <span class="fw-semibold">Bandeja de Reservas</span>
            </a>
        </li>
        
        <!-- Corregido: marcar como activo solo una vez y con el ícono correcto -->
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.gestionar.turnos') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-clock fs-5"></i>
                <span class="fw-semibold">Gestionar Turnos</span>
            </a>
        </li>
             
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.gestionar.novedades') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-bell fs-5"></i>
                <span class="fw-semibold">Gestionar Novedades</span>
            </a>
        </li>
          <li class="nav-item mb-2">
            <a href="{{ route('empleado.notificaciones') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-bell fs-5"></i>
                <span class="fw-semibold">Gestionar Notificaciones</span>
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('home') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-home fs-5"></i>
                <span class="fw-semibold">Web Cliente</span>
            </a>
        </li>
    </ul>
</aside>

<main class="admin-content empleado-dashboard-panel">
<!-- Contenido principal -->
    <div class="card shadow-sm border-0 empleado-shell-card">
            <!-- Título principal fuera del card -->
        <h2 class="fw-bold text-dark text-center">
            <i class="fas fa-bell me-2"></i> Dashboard
        </h2>

    <!-- Dashboard 1: Análisis de Reservas -->
    <div class="card shadow-lg border-0 rounded-3 mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i> Análisis de Reservas</h5>
        </div>
        <div class="card-body p-0" style="min-height: 600px;">
            <!-- Contenedor para el iframe de Power BI Dashboard 1 -->
            <iframe 
                id="powerbi-dashboard-1"
                width="100%" 
                height="600" 
                src="" 
                frameborder="0" 
                allowFullScreen="true"
                style="border: none;">
            </iframe>
        </div>
    </div>

    <!-- Dashboard 2: Análisis de Servicios y Empleados -->
    <div class="card shadow-lg border-0 rounded-3 mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i> Análisis de Servicios y Empleados</h5>
        </div>
        <div class="card-body p-0" style="min-height: 600px;">
            <!-- Contenedor para el iframe de Power BI Dashboard 2 -->
            <iframe 
                id="powerbi-dashboard-2"
                width="100%" 
                height="600" 
                src="" 
                frameborder="0" 
                allowFullScreen="true"
                style="border: none;">
            </iframe>
        </div>
    </div>
    </div>
</main>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dashboard1Url = '{{ env("POWERBI_DASHBOARD_1_URL","https://app.powerbi.com/view?r=eyJrIjoiYmJiMTZjYWUtYzFkZC00MTdmLWIyMDAtMWQ2NDc4YWE1M2I5IiwidCI6ImM0YTY2YzM0LTJiYjctNDUxZi04YmUxLWIyYzI2YTQzMDE1OCIsImMiOjR9") }}';
        const dashboard2Url = '{{ env("POWERBI_DASHBOARD_2_URL", "https://app.powerbi.com/view?r=eyJrIjoiYmJiMTZjYWUtYzFkZC00MTdmLWIyMDAtMWQ2NDc4YWE1M2I5IiwidCI6ImM0YTY2YzM0LTJiYjctNDUxZi04YmUxLWIyYzI2YTQzMDE1OCIsImMiOjR9") }}';
        
        if (dashboard1Url) {
            document.getElementById('powerbi-dashboard-1').src = dashboard1Url;
        } else {
            document.getElementById('powerbi-dashboard-1').parentElement.innerHTML = 
                '<div class="alert alert-warning m-4">⚠️ Dashboard 1 no configurado. Agrega POWERBI_DASHBOARD_1_URL en el archivo .env</div>';
        }
        
        if (dashboard2Url) {
            document.getElementById('powerbi-dashboard-2').src = dashboard2Url;
        } else {
            document.getElementById('powerbi-dashboard-2').parentElement.innerHTML = 
                '<div class="alert alert-warning m-4">⚠️ Dashboard 2 no configurado. Agrega POWERBI_DASHBOARD_2_URL en el archivo .env</div>';
        }
    });
</script>
@endpush

@endsection

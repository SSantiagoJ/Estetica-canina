@extends('layouts.app')

@section('title', 'Inicio - Pet Grooming')
@section('header')
    @include('partials.header')
@endsection
@section('content')
<link rel="stylesheet" href="{{ asset('css/estilo_menu.css') }}">
<section class="contenedor1">
    <div class="contenedor2">
        <h2>Belleza y bienestar para todos los amigos peludos</h2>
        <button class="boton1">RESERVAR</button>
    </div>
    <div class="contenedor-img">
        <img src="{{ asset('perrito1.png') }}" alt="Perrito">
    </div>
</section>

<section class="servicios">
    <h2>Descubre nuestros tratamientos, cortes y productos pensados para consentir a tu mascota</h2>

    <!-- Dropdown personalizado -->
    <div class="custom-select" role="combobox" aria-haspopup="listbox" aria-expanded="false" tabindex="0">
        <button type="button" class="select-btn" aria-label="Seleccionar servicio">
            <span class="selected-value">Selecciona un servicio</span>
            <span class="arrow-btn" aria-hidden="true">▾</span>
        </button>

        <ul class="select-options" role="listbox" tabindex="-1" aria-label="Opciones de servicios">
            <li role="option">Baño</li>
            <li role="option">Cortes</li>
            <li role="option">Tratamientos</li>
            <li role="option">Otros servicios</li>
        </ul>
    </div>

    <!-- Botón catálogo debajo -->
    <button class="btn-catalogo">Ver catálogo</button>
</section>

<section class="banner-section">
    <img src="{{ asset('banner.png') }}" alt="banner" class="banner-img">
</section>

<!-- Sección Servicios Adicionales -->
<section class="extra-services">
    <h2>Servicios adicionales</h2>
    <p>
        Junto al baño y la peluquería, tenemos servicios extra que harán que tu engreído
        se sienta sano, relajado y feliz. Pídelos al agendar tu cita.
    </p>

    <div class="services-grid">
        <div class="service-column">
            <img src="{{ asset('i_1.jpg') }}" alt="Servicio 1">
            <img src="{{ asset('i_3.jpg') }}" alt="Servicio 2">
            <img src="{{ asset('i_5.jpg') }}" alt="Servicio 3">
        </div>

        <div class="service-column">
            <img src="{{ asset('i_2.jpg') }}" alt="Servicio 4">
            <img src="{{ asset('i_4.jpg') }}" alt="Servicio 5">
            <img src="{{ asset('i_6.jpg') }}" alt="Servicio 6">
        </div>
    </div>
</section>

<!-- Opiniones -->
<section class="opiniones">
    <div class="opiniones-top">
        <img src="{{ asset('perrito2.png') }}" alt="Opinión">
        <p>
            Tu opinión nos ayuda a seguir consintiendo a los peluditos como se merecen.
            ¡Déjanos tus comentarios!
        </p>
    </div>

    <div class="opiniones-carrusel">
        <div class="opinion-card">
            <span class="fecha">01/10/2025</span>
            <span class="estrellas">⭐⭐⭐⭐⭐</span>
            <h3 class="usuario">María López</h3>
            <p class="comentario">Excelente servicio, mi perrito quedó hermoso y feliz.</p>
        </div>

        <div class="opinion-card">
            <span class="fecha">25/09/2025</span>
            <span class="estrellas">⭐⭐⭐⭐</span>
            <h3 class="usuario">Carlos Fernández</h3>
            <p class="comentario">Muy buena atención, aunque demoraron un poco con la cita.</p>
        </div>

        <div class="opinion-card">
            <span class="fecha">15/09/2025</span>
            <span class="estrellas">⭐⭐⭐⭐⭐</span>
            <h3 class="usuario">Ana Ramírez</h3>
            <p class="comentario">Recomiendo totalmente, muy profesionales y amables.</p>
        </div>
        <div class="opinion-card">
            <span class="fecha">01/10/2025</span>
            <span class="estrellas">⭐⭐⭐⭐⭐</span>
            <h3 class="usuario">María López</h3>
            <p class="comentario">Excelente servicio, mi perrito quedó hermoso y feliz.</p>
        </div>

        <div class="opinion-card">
            <span class="fecha">25/09/2025</span>
            <span class="estrellas">⭐⭐⭐⭐</span>
            <h3 class="usuario">Carlos Fernández</h3>
            <p class="comentario">Muy buena atención, aunque demoraron un poco con la cita.</p>
        </div>

        <div class="opinion-card">
            <span class="fecha">15/09/2025</span>
            <span class="estrellas">⭐⭐⭐⭐⭐</span>
            <h3 class="usuario">Ana Ramírez</h3>
            <p class="comentario">Recomiendo totalmente, muy profesionales y amables.</p>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script src="{{ asset('js/custom_select.js') }}"></script>
@endpush

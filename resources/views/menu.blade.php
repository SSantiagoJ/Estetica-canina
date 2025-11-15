@extends('layouts.app')

@section('title', 'Inicio - Pet Grooming')
@section('header')
    @include('partials.admin_header')
@endsection
@section('content')
<link rel="stylesheet" href="{{ asset('css/cliente/estilo_menu.css') }}">
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
<section class="opiniones py-5">
    <div class="opiniones-top mb-4">
        <img src="{{ asset('perrito2.png') }}" alt="Opinión">
        <p>
            Tu opinión nos ayuda a seguir consintiendo a los peluditos como se merecen.
            ¡Déjanos tus comentarios!
        </p>
    </div>

    <div class="container">
        <div class="row">
            @forelse($calificaciones as $calificacion)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="opinion-card card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="mb-2">
                                @for($i = 0; $i < 5; $i++)
                                    <i class="fas fa-star text-warning"></i>
                                @endfor
                                <span class="fecha d-block text-muted mt-1">
                                    {{ \Carbon\Carbon::parse($calificacion->fecha_creacion)->format('d/m/Y') }}
                                </span>
                            </div>
                            <p class="comentario card-text">
                                <em>"{{ $calificacion->comentarios }}"</em>
                            </p>
                            <footer class="blockquote-footer">
                                <h3 class="usuario mb-1">{{ $calificacion->nombres }}</h3>
                                <small class="text-muted">
                                    Mascota: {{ $calificacion->mascota_nombre }}
                                </small>
                            </footer>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center text-muted">
                        Aún no hay calificaciones destacadas
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script src="{{ asset('js/custom_select.js') }}"></script>
@endpush

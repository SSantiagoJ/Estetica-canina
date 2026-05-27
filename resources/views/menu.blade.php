@extends('layouts.app')

@section('title', 'Inicio - Pet Grooming')
@section('header')
    @include('partials.header')
@endsection

@section('content')
<link rel="stylesheet" href="{{ asset('css/estilo_menu.css') }}">

<section class="contenedor1">
    <div class="contenedor2">
        <span class="hero-eyebrow">Estética canina y felina</span>
        <h2>Belleza y bienestar para todos los amigos peludos</h2>
        <p class="hero-copy">
            Agenda baños, cortes y tratamientos con una experiencia más clara, rápida y cómoda para tu mascota.
        </p>
        @auth
            <a href="{{ route('reservas.seleccionMascota') }}" class="boton1">Reservar ahora</a>
        @else
            <a href="{{ route('login') }}" class="boton1" data-reserva-login>Reservar ahora</a>
        @endauth
    </div>
    <div class="contenedor-img">
        <img src="{{ asset('perrito1.png') }}" alt="Perrito">
    </div>
</section>

<section class="servicios">
    <h2>Descubre nuestros tratamientos, cortes y productos pensados para consentir a tu mascota</h2>

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

    <a href="{{ route('catalogo') }}" class="btn-catalogo">Ver catálogo</a>
</section>

<section class="banner-section">
    <img src="{{ asset('banner.png') }}" alt="Banner de servicios" class="banner-img">
</section>

<section class="extra-services">
    <h2>Servicios adicionales</h2>
    <p>
        Junto al baño y la peluquería, tenemos servicios extra que harán que tu engreído
        se sienta sano, relajado y feliz. Pídelos al agendar tu cita.
    </p>

    <div class="services-grid">
        <div class="service-column">
            <img src="{{ asset('i_1.jpg') }}" alt="Servicio adicional 1">
            <img src="{{ asset('i_3.jpg') }}" alt="Servicio adicional 2">
            <img src="{{ asset('i_5.jpg') }}" alt="Servicio adicional 3">
        </div>

        <div class="service-column">
            <img src="{{ asset('i_2.jpg') }}" alt="Servicio adicional 4">
            <img src="{{ asset('i_4.jpg') }}" alt="Servicio adicional 5">
            <img src="{{ asset('i_6.jpg') }}" alt="Servicio adicional 6">
        </div>
    </div>
</section>

<section class="opiniones">
    <div class="opiniones-top">
        <img src="{{ asset('perrito2.png') }}" alt="Opinión de clientes">
        <p>
            Tu opinión nos ayuda a seguir consintiendo a los peluditos como se merecen.
            ¡Déjanos tus comentarios!
        </p>
    </div>

    <div class="opiniones-carrusel">
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
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/custom_select.js') }}"></script>
@endpush

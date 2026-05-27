{{-- resources/views/catalogo.blade.php --}}

@extends('layouts.app')

@section('title', 'Catálogo de Servicios')

@section('header')
    @include('partials.header')
@endsection

@section('content')
<link rel="stylesheet" href="{{ asset('css/catalogo.css') }}">

<main class="main-catalogo-page">
    <section class="catalogo-servicios">
        @php
            $descripcionesCategorias = [
                'BAÑOS' => 'Baños básicos, premium y terapéuticos con productos pensados para cada tipo de piel y pelaje.',
                'Baños' => 'Baños básicos, premium y terapéuticos con productos pensados para cada tipo de piel y pelaje.',
                'PELUQUERÍA' => 'Cortes profesionales adaptados a cada raza, estilo y necesidad de mantenimiento.',
                'Peluquería' => 'Cortes profesionales adaptados a cada raza, estilo y necesidad de mantenimiento.',
                'TRATAMIENTOS' => 'Cuidado especializado para piel, pelaje y bienestar general de tu mascota.',
                'Tratamientos' => 'Cuidado especializado para piel, pelaje y bienestar general de tu mascota.',
                'ADICIONALES' => 'Servicios complementarios para completar la experiencia de cuidado.',
                'Servicios Adicionales' => 'Servicios complementarios para completar la experiencia de cuidado.',
            ];

            $etiquetasCategorias = [
                'BAÑOS' => 'Baños',
                'PELUQUERÍA' => 'Peluquería',
                'TRATAMIENTOS' => 'Tratamientos',
                'ADICIONALES' => 'Servicios adicionales',
            ];
        @endphp

        @forelse($servicios as $categoria => $listaServicios)
            @php
                $categoriaNombre = $etiquetasCategorias[$categoria] ?? $categoria;
                $categoriaDescripcion = $descripcionesCategorias[$categoria] ?? 'Servicios de calidad para que tu mascota se vea y se sienta mejor.';
            @endphp

            <div class="categoria-item">
                <div class="categoria-row">
                    <div class="categoria-info">
                        <span class="categoria-eyebrow">{{ $listaServicios->count() }} servicios</span>
                        <h2 class="categoria-titulo">{{ $categoriaNombre }}</h2>
                        <p class="categoria-descripcion">{{ $categoriaDescripcion }}</p>
                    </div>

                    <div class="carousel-container">
                        <button class="carousel-btn carousel-prev" aria-label="Anterior">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                        </button>

                        <div class="servicios-carrusel">
                            @foreach($listaServicios as $servicio)
                                <article class="servicio-card">
                                    <div class="servicio-image-frame">
                                        @if($servicio->imagen_referencial)
                                            @php
                                                if (str_starts_with($servicio->imagen_referencial, 'servicios/')) {
                                                    $imagenUrl = asset('storage/' . $servicio->imagen_referencial);
                                                } else {
                                                    $imagenUrl = asset('images/servicios/' . $servicio->imagen_referencial);
                                                }
                                            @endphp
                                            <img src="{{ $imagenUrl }}" alt="{{ $servicio->nombre_servicio }}">
                                        @else
                                            <img src="{{ asset('images/servicios/default.jpg') }}" alt="{{ $servicio->nombre_servicio }}">
                                        @endif
                                    </div>

                                    <div class="servicio-content">
                                        <div class="servicio-meta">
                                            <span>S/ {{ number_format($servicio->costo, 2) }}</span>
                                            @if($servicio->duracion)
                                                <span>{{ $servicio->duracion }} min</span>
                                            @endif
                                        </div>
                                        <h3 class="servicio-nombre">{{ $servicio->nombre_servicio }}</h3>
                                        <p class="servicio-detalle">
                                            {{ $servicio->descripcion ?: 'Disponible para agenda según horario y trabajador.' }}
                                        </p>
                                        @auth
                                            <a href="{{ route('reservas.seleccionMascota') }}" class="servicio-reservar">Reservar</a>
                                        @else
                                            <a href="{{ route('login') }}" class="servicio-reservar" data-reserva-login>Reservar</a>
                                        @endauth
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <button class="carousel-btn carousel-next" aria-label="Siguiente">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            @if(!$loop->last)
                <hr class="categoria-divider">
            @endif
        @empty
            <div class="catalogo-empty">
                <h3>No hay servicios disponibles en este momento</h3>
            </div>
        @endforelse
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.categoria-item').forEach(categoria => {
            const track = categoria.querySelector('.servicios-carrusel');
            const prevBtn = categoria.querySelector('.carousel-prev');
            const nextBtn = categoria.querySelector('.carousel-next');

            if (!track) return;

            const cards = track.querySelectorAll('.servicio-card');
            if (cards.length === 0) return;

            const cardWidth = cards[0].offsetWidth + 18;
            let isDown = false;
            let startX;
            let scrollLeft;

            nextBtn.addEventListener('click', () => {
                track.scrollBy({ left: cardWidth * 2, behavior: 'smooth' });
            });

            prevBtn.addEventListener('click', () => {
                track.scrollBy({ left: -cardWidth * 2, behavior: 'smooth' });
            });

            track.addEventListener('mousedown', (e) => {
                isDown = true;
                track.classList.add('is-dragging');
                startX = e.pageX - track.offsetLeft;
                scrollLeft = track.scrollLeft;
            });

            track.addEventListener('mouseup', () => {
                isDown = false;
                track.classList.remove('is-dragging');
            });

            track.addEventListener('mouseleave', () => {
                isDown = false;
                track.classList.remove('is-dragging');
            });

            track.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - track.offsetLeft;
                const walk = (x - startX) * 1.5;
                track.scrollLeft = scrollLeft - walk;
            });
        });
    });
</script>
@endsection

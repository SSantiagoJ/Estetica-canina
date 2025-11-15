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
                    'Baños' => 'Desde baños básicos de limpieza hasta terapéuticos y relajantes con productos hipoalergénicos, diseñados para cada tipo de piel y pelaje, dejando a tu mascota fresca, suave y con un aroma encantador.',
                    'Peluquería' => 'Cortes profesionales adaptados a cada raza y estilo, desde un look clásico hasta uno moderno y divertido. Resaltamos la belleza natural de tu mascota, cuidando siempre su comodidad.',
                    'Tratamientos' => 'Tratamientos especializados para el cuidado integral de tu mascota, desde spa hasta tratamientos dermatológicos y de belleza avanzada.',
                    'Servicios Adicionales' => 'Servicios complementarios para el bienestar y cuidado completo de tu mascota.'
                ];
            @endphp

            @forelse($servicios as $categoria => $listaServicios)
            {{-- CATEGORÍA DINÁMICA --}}
            <div class="categoria-item">
                <div class="categoria-row">
                    <div class="categoria-info">
                        <h2 class="categoria-titulo">{{ $categoria }}</h2>
                        <p class="categoria-descripcion">
                            {{ $descripcionesCategorias[$categoria] ?? 'Servicios de calidad para tu mascota.' }}
                        </p>
                    </div>

                    <div class="carousel-container">
                        <button class="carousel-btn carousel-prev" aria-label="Anterior">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                        </button>

                        <div class="servicios-carrusel">
                            @foreach($listaServicios as $servicio)
                                @if($servicio->descripcion) {{-- Solo mostrar servicios con descripción --}}
                                <div class="servicio-card">
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
                                    <div class="servicio-overlay">
                                        <p class="servicio-nombre">{{ $servicio->nombre_servicio }}</p>
                                        <p class="servicio-detalle">{{ $servicio->descripcion }}</p>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>

                        <button class="carousel-btn carousel-next" aria-label="Siguiente">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
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
            <div class="text-center py-5">
                <h3>No hay servicios disponibles en este momento</h3>
            </div>
            @endforelse

        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Configurar carruseles para cada categoría
            document.querySelectorAll('.categoria-item').forEach(categoria => {
                const track = categoria.querySelector('.servicios-carrusel');
                const prevBtn = categoria.querySelector('.carousel-prev');
                const nextBtn = categoria.querySelector('.carousel-next');
                
                if (!track) return;

                const cards = track.querySelectorAll('.servicio-card');
                if (cards.length === 0) return;

                const cardWidth = cards[0].offsetWidth + 20; // 20px gap
                let isDown = false;
                let startX;
                let scrollLeft;

                // Botón siguiente
                nextBtn.addEventListener('click', () => {
                    track.scrollBy({
                        left: cardWidth * 2,
                        behavior: 'smooth'
                    });
                });

                // Botón anterior
                prevBtn.addEventListener('click', () => {
                    track.scrollBy({
                        left: -cardWidth * 2,
                        behavior: 'smooth'
                    });
                });

                // Drag to scroll
                track.style.cursor = 'grab';

                track.addEventListener('mousedown', (e) => {
                    isDown = true;
                    track.style.cursor = 'grabbing';
                    startX = e.pageX - track.offsetLeft;
                    scrollLeft = track.scrollLeft;
                });

                track.addEventListener('mouseup', () => {
                    isDown = false;
                    track.style.cursor = 'grab';
                });

                track.addEventListener('mouseleave', () => {
                    isDown = false;
                    track.style.cursor = 'grab';
                });

                track.addEventListener('mousemove', (e) => {
                    if (!isDown) return;
                    e.preventDefault();
                    const x = e.pageX - track.offsetLeft;
                    const walk = (x - startX) * 2;
                    track.scrollLeft = scrollLeft - walk;
                });
            });
        });
    </script>
@endsection

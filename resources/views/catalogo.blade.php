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

            {{-- BAÑOS --}}
            <div class="categoria-item">
                <div class="categoria-row">
                    <div class="categoria-info">
                        <h2 class="categoria-titulo">Baños</h2>
                        <p class="categoria-descripcion">Desde baños básicos de limpieza hasta terapéuticos y relajantes con
                            productos hipoalergénicos, diseñados para cada tipo de piel y pelaje, dejando a tu mascota
                            fresca, suave y con un aroma encantador.</p>
                    </div>

                    <div class="carousel-container">
                        <button class="carousel-btn carousel-prev" aria-label="Anterior">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                        </button>

                        <div class="servicios-carrusel">
                            <div class="servicio-card">
                                <img src="{{ asset('images/bano_basico.jpg') }}" alt="Baño Básico">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre">Baño Básico</p>
                                    <p class="servicio-detalle">Limpieza con shampoo neutro, secado y cepillado ligero.</p>
                                </div>
                            </div>

                            <div class="servicio-card">
                                <img src="{{ asset('images/bano_medicado.jpg') }}" alt="Baño Medicado">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre">Baño Medicado</p>
                                    <p class="servicio-detalle">Uso de shampoo especial para piel sensible, resequedad o
                                        alergias.</p>
                                </div>
                            </div>

                            <div class="servicio-card">
                                <img src="{{ asset('images/bano_antipulgas.jpg') }}" alt="Baño Antipulgas">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre">Baño Antipulgas</p>
                                    <p class="servicio-detalle">Baño con productos específicos contra pulgas y garrapatas.
                                    </p>
                                </div>
                            </div>

                            <div class="servicio-card">
                                <img src="{{ asset('images/bano_ozonoterapia.jpg') }}" alt="Baño Ozonoterapia">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre"> Baño Ozonoterapia</p>
                                    <p class="servicio-detalle">Agua ozonizada que ayuda a desinfectar y regenerar la piel.
                                    </p>
                                </div>
                            </div>
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

            <hr class="categoria-divider">

            {{-- PELUQUERÍA --}}
            <div class="categoria-item">
                <div class="categoria-row">
                    <div class="categoria-info">
                        <h2 class="categoria-titulo">Peluquería</h2>
                        <p class="categoria-descripcion">Cortes profesionales adaptados a cada raza y estilo, desde un look
                            clásico hasta uno moderno y divertido. Resaltamos la belleza natural de tu mascota, cuidando
                            siempre su comodidad.</p>
                    </div>

                    <div class="carousel-container">
                        <button class="carousel-btn carousel-prev" aria-label="Anterior">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                        </button>

                        <div class="servicios-carrusel">
                            <div class="servicio-card">
                                <img src="{{ asset('images/corte_puppy.jpg') }}" alt="Corte Puppy">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre">Corte Puppy</p>
                                    <p class="servicio-detalle">Estilo que mantiene un look tierno y juvenil, con pelo
                                        uniforme y redondeado.</p>
                                </div>
                            </div>

                            <div class="servicio-card">
                                <img src="{{ asset('images/corte_teddybear.jpg') }}" alt="Corte Teddy Bear">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre">Corte Teddy Bear</p>
                                    <p class="servicio-detalle">Patas y cara redondeadas con estilo tierno y elegante
                                        popular en razas pequeñas.
                                    </p>
                                </div>
                            </div>

                            <div class="servicio-card">
                                <img src="{{ asset('images/corte_exhibicion.jpg') }}" alt="Corte de Exhibición">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre">Corte de Exhibición</p>
                                    <p class="servicio-detalle">Estándares de raza para concursos y eventos.</p>
                                </div>
                            </div>

                            <div class="servicio-card">
                                <img src="{{ asset('images/corte_creativo.jpg') }}" alt="Corte Creativo">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre">Corte Creativo</p>
                                    <p class="servicio-detalle">Diseños y colores personalizados para ocasiones especiales.
                                    </p>
                                </div>
                            </div>

                            <div class="servicio-card">
                                <img src="{{ asset('images/corte_verano.jpg') }}" alt="Corte Verano">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre">Corte de Verano</p>
                                    <p class="servicio-detalle">Rebajado para mayor frescura en temporadas calurosas.
                                    </p>
                                </div>
                            </div>
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

            <hr class="categoria-divider">

            {{-- TRATAMIENTOS --}}
            <div class="categoria-item">
                <div class="categoria-row">
                    <div class="categoria-info">
                        <h2 class="categoria-titulo">Tratamientos</h2>
                        <p class="categoria-descripcion">Mascarillas nutritivas, hidratación profunda y cuidados especiales
                            para mantener un pelaje brillante, sedoso y saludable, ideales para consentir a tu mejor amigo.
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
                            <div class="servicio-card">
                                <img src="{{ asset('images/tratamiento_termal.png') }}" alt="Tratamiento Termal">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre">Tratamiento Termal</p>
                                    <p class="servicio-detalle">Mascarilla nutritiva que se aplica con calor húmedo. El
                                        calor abre la cutícula para una máxima absorción de nutrientes y reparación.</p>
                                </div>
                            </div>

                            <div class="servicio-card">
                                <img src="{{ asset('images/hidroterapia.png') }}" alt="Hidroterapia con Sales">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre">Hidroterapia con Sales Minerales</p>
                                    <p class="servicio-detalle">Bañera de hidromasaje que combina agua tibia y sales para
                                        aliviar dolores y desintoxicar la piel.
                                    </p>
                                </div>
                            </div>

                            <div class="servicio-card">
                                <img src="{{ asset('images/tratamiento_argan.png') }}" alt="Tratamiento Argan y Coco">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre">Tratamiento de Argán y Coco</p>
                                    <p class="servicio-detalle">Se usan aceites vegetales de alta calidad para un acabado de
                                        pelaje liso, sedoso e hidratado.</p>
                                </div>
                            </div>

                            <div class="servicio-card">
                                <img src="{{ asset('images/tratamiento_anticaida.png') }}" alt="Tratamiento Anti Caída">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre">Anti Caída y Mudanza</p>
                                    <p class="servicio-detalle">Uso de complejos vitamínicos y extractos naturales que
                                        fortalecen el folículo piloso y minimizan la caída excesiva de pelo.
                                    </p>
                                </div>
                            </div>

                            <div class="servicio-card">
                                <img src="{{ asset('images/barro_mineral.png') }}" alt="Envoltura de Barro Minera">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre">Envoltura de Barro Mineral</p>
                                    <p class="servicio-detalle">Aplicación de arcilla y barros terapéuticos naturales que
                                        actúan como exfoliante suave para la piel
                                    .</p>
                                </div>
                            </div>
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

            <hr class="categoria-divider">

            {{-- SERVICIOS ADICIONALES --}}
            <div class="categoria-item">
                <div class="categoria-row">
                    <div class="categoria-info">
                        <h2 class="categoria-titulo">Servicios Adicionales</h2>
                        <p class="categoria-descripcion">Corte y limado de uñas, limpieza de oídos, vaciado de glándulas y
                            más detalles que garantizan la higiene y bienestar integral de tu mascota. </p>
                    </div>

                    <div class="carousel-container">
                        <button class="carousel-btn carousel-prev" aria-label="Anterior">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                        </button>

                        <div class="servicios-carrusel">
                            <div class="servicio-card">
                                <img src="{{ asset('images/corte_unas.jpg') }}" alt="Corte y Limado de Uñas">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre">Corte y Limado de Uñas</p>
                                    <p class="servicio-detalle">Corte de uñas para prevenir dolor y problemas posturales; el
                                        limado suaviza los bordes.</p>
                                </div>
                            </div>

                            <div class="servicio-card">
                                <img src="{{ asset('images/limpieza_oidos.jpg') }}" alt="Limpieza de Oídos">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre">Limpieza de Oídos</p>
                                    <p class="servicio-detalle">Retiro de cera y vello del canal auditivo para prevenir
                                        infecciones y malos olores.</p>
                                </div>
                            </div>

                            <div class="servicio-card">
                                <img src="{{ asset('images/cepillado_dientes.jpg') }}" alt="Cepillado de Dientes">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre">Cepillado de Dientes</p>
                                    <p class="servicio-detalle">Limpieza bucal para reducir sarro superficial, prevenir el
                                        mal aliento y mantener la salud dental.</p>
                                </div>
                            </div>

                            <div class="servicio-card">
                                <img src="{{ asset('images/hidratacion_patas.jpg') }}" alt="Hidratación de Almohadillas">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre">Hidratación de Almohadillas</p>
                                    <p class="servicio-detalle">Aplicación de bálsamo especial para proteger y suavizar las
                                        almohadillas resecas o agrietadas.</p>
                                </div>
                            </div>

                            <div class="servicio-card">
                                <img src="{{ asset('images/despigmentacion.png') }}" alt="Despigmentacion de Lagrimal">
                                <div class="servicio-overlay">
                                    <p class="servicio-nombre">Despigmentacion de Lagrimal</p>
                                    <p class="servicio-detalle">Limpieza para reducir las manchas marrones causadas por las
                                        lágrimas alrededor de los ojos</p>
                                </div>
                            </div>
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

        </section>
    </main>

    {{-- Moved script inside @section('content') to fix duplicate @endsection error --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.categoria-item').forEach(function (categoria) {
                const container = categoria.querySelector('.carousel-container');
                const track = container.querySelector('.servicios-carrusel');
                const prevBtn = container.querySelector('.carousel-prev');
                const nextBtn = container.querySelector('.carousel-next');

                if (!track || !prevBtn || !nextBtn) return;

                const gap = 20;
                let scrollInterval = null;

                function getCardWidth() {
                    const card = track.querySelector('.servicio-card');
                    return card ? card.offsetWidth + gap : 260;
                }

                function updateButtons() {
                    const isAtStart = track.scrollLeft <= 10;
                    const isAtEnd = Math.ceil(track.scrollLeft + track.clientWidth) >= track.scrollWidth - 10;

                    prevBtn.style.opacity = isAtStart ? '0.3' : '1';
                    prevBtn.style.pointerEvents = isAtStart ? 'none' : 'auto';
                    nextBtn.style.opacity = isAtEnd ? '0.3' : '1';
                    nextBtn.style.pointerEvents = isAtEnd ? 'none' : 'auto';
                }

                prevBtn.addEventListener('click', () => {
                    track.scrollBy({ left: -getCardWidth(), behavior: 'smooth' });
                });

                nextBtn.addEventListener('click', () => {
                    track.scrollBy({ left: getCardWidth(), behavior: 'smooth' });
                });

                track.addEventListener('mousemove', (e) => {
                    const rect = track.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const edgeThreshold = 100; // pixels from edge to trigger scroll

                    // Clear any existing scroll interval
                    if (scrollInterval) {
                        clearInterval(scrollInterval);
                        scrollInterval = null;
                    }

                    // Check if near left edge
                    if (x < edgeThreshold && track.scrollLeft > 0) {
                        const scrollSpeed = Math.max(1, (edgeThreshold - x) / 10);
                        scrollInterval = setInterval(() => {
                            track.scrollLeft -= scrollSpeed;
                        }, 16);
                    }
                    // Check if near right edge
                    else if (x > rect.width - edgeThreshold &&
                        track.scrollLeft < track.scrollWidth - track.clientWidth) {
                        const scrollSpeed = Math.max(1, (x - (rect.width - edgeThreshold)) / 10);
                        scrollInterval = setInterval(() => {
                            track.scrollLeft += scrollSpeed;
                        }, 16);
                    }
                });

                track.addEventListener('mouseleave', () => {
                    if (scrollInterval) {
                        clearInterval(scrollInterval);
                        scrollInterval = null;
                    }
                });

                track.addEventListener('scroll', updateButtons);
                window.addEventListener('resize', updateButtons);
                updateButtons();

                // Drag functionality
                let isDown = false;
                let startX;
                let scrollLeft;

                track.addEventListener('mousedown', (e) => {
                    // Stop auto-scroll when dragging
                    if (scrollInterval) {
                        clearInterval(scrollInterval);
                        scrollInterval = null;
                    }
                    isDown = true;
                    track.style.cursor = 'grabbing';
                    startX = e.pageX - track.offsetLeft;
                    scrollLeft = track.scrollLeft;
                });

                track.addEventListener('mouseup', () => {
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
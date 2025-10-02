    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="{{ asset('css/estilo_menu.css') }}">
        <title>Document</title>
    </head>

    <body>
        <header>
            <div class="logo">
                <img src="{{ asset('logo.png') }}" alt="Pet Grooming Logo">
            </div>
            <nav>
                <ul>
                    <li><a href="#">Quiénes somos</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><button class="btn">Sign in</button></li>
                    <li><button class="btn">Register</button></li>
                </ul>
            </nav>
        </header>
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
                <!-- Columna 1 -->
                <div class="service-column">
                    <img src="{{ asset('i_1.jpg') }}" alt="Servicio 1">
                    <img src="{{ asset('i_3.jpg') }}" alt="Servicio 2">
                    <img src="{{ asset('i_5.jpg') }}" alt="Servicio 3">
                </div>

                <!-- Columna 2 -->
                <div class="service-column">
                    <img src="{{ asset('i_2.jpg') }}" alt="Servicio 4">
                    <img src="{{ asset('i_4.jpg') }}" alt="Servicio 5">
                    <img src="{{ asset('i_6.jpg') }}" alt="Servicio 6">
                </div>
            </div>
        </section>
        <!-- Sección Opiniones -->
        <section class="opiniones">
            <div class="opiniones-top">
                <img src="{{ asset('perrito2.png') }}" alt="Opinión">
                <p>
                    Tu opinión nos ayuda a seguir consintiendo a los peluditos como se merecen.
                    ¡Déjanos tus comentarios!
                </p>
            </div>

            <!-- Carrusel de Opiniones -->
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
            </div>
        </section>


        <!-- Footer -->
        <footer class="footer">
            <div class="footer-column">
                <h3>Pet Grooming</h3>
                <p>Amamos a los peluditos tanto como tú. 💕
                    Cada servicio está pensado para su bienestar, alegría y estilo.</p>
            </div>

            <div class="footer-column">
                <h3>Síguenos</h3>
                <div class="social-icons">
                    <a href="#"><img src="{{ asset('img/facebook.png') }}" alt="Facebook"></a>
                    <a href="#"><img src="{{ asset('img/instagram.png') }}" alt="Instagram"></a>
                    <a href="#"><img src="{{ asset('img/tiktok.png') }}" alt="TikTok"></a>
                </div>
            </div>
        </footer>



    </body>

    </html>
    <script>
        document.querySelectorAll('.custom-select').forEach(function(sel) {
            const btn = sel.querySelector('.select-btn');
            const list = sel.querySelector('.select-options');
            const options = sel.querySelectorAll('.select-options li');
            const value = sel.querySelector('.selected-value');

            // toggle al hacer click en el botón
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = sel.classList.toggle('open');
                sel.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            // seleccionar opción
            options.forEach(function(opt) {
                opt.addEventListener('click', function(e) {
                    e.stopPropagation();
                    value.textContent = this.textContent;
                    sel.classList.remove('open');
                    sel.setAttribute('aria-expanded', 'false');
                });
            });

            // click fuera -> cerrar
            document.addEventListener('click', function() {
                sel.classList.remove('open');
                sel.setAttribute('aria-expanded', 'false');
            });

            // accesibilidad teclado
            sel.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    sel.classList.remove('open');
                    sel.setAttribute('aria-expanded', 'false');
                }
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    const isOpen = sel.classList.toggle('open');
                    sel.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                }
            });
        });
    </script>
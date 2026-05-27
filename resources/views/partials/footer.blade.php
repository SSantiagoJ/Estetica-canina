<footer class="site-footer">
    <div class="site-footer__inner">
        <div class="site-footer__brand">
            <a href="{{ route('home') }}" class="site-footer__logo">
                <span class="site-footer__logo-icon"><i class="fas fa-paw"></i></span>
                <span>Pet Grooming</span>
            </a>
            <p>
                Cuidamos a cada mascota con atención veterinaria, cariño y servicios pensados para su bienestar diario.
            </p>
        </div>

        <nav class="site-footer__links" aria-label="Enlaces del pie de página">
            <h3>Explorar</h3>
            <a href="{{ route('home') }}">Inicio</a>
            <a href="{{ route('catalogo') }}">Servicios</a>
            <a href="{{ route('reservas.seleccionMascota') }}">Reservar cita</a>
        </nav>

        <div class="site-footer__contact">
            <h3>Estamos cerca</h3>
            <p><i class="fas fa-heart-pulse"></i> Atención para baños, cortes y cuidados especiales.</p>
            <div class="site-footer__social">
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>
    </div>

    <div class="site-footer__bottom">
        <span>Pet Grooming</span>
        <span>Bienestar y estética para mascotas</span>
    </div>
</footer>

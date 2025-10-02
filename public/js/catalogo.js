// resources/js/catalogo.js

$(document).ready(function() {
    // 1. Iterar sobre todos los contenedores de carrusel
    $('.servicios-carrusel').each(function() {
        const $carrusel = $(this);
        const scrollAmount = 10; // Cantidad de píxeles a desplazar por intervalo
        const scrollInterval = 15; // Milisegundos entre cada desplazamiento (velocidad)
        let scrollTimer;

        // Función para desplazar a la izquierda
        function scrollLeft() {
            $carrusel.scrollLeft($carrusel.scrollLeft() - scrollAmount);
        }

        // Función para desplazar a la derecha
        function scrollRight() {
            $carrusel.scrollLeft($carrusel.scrollLeft() + scrollAmount);
        }

        // 2. Definir las zonas de activación del hover (los bordes del carrusel)
        $carrusel.on('mousemove', function(e) {
            const containerWidth = $carrusel.width();
            const mouseX = e.pageX - $carrusel.offset().left;
            const threshold = 0.15; // 15% de cada lado es la zona de hover

            // Si el mouse está en la zona izquierda (primer 15%)
            if (mouseX < containerWidth * threshold) {
                if ($carrusel.scrollLeft() > 0) {
                    if (!scrollTimer) {
                        scrollTimer = setInterval(scrollLeft, scrollInterval);
                    }
                } else {
                    clearInterval(scrollTimer);
                    scrollTimer = null;
                }
            }
            // Si el mouse está en la zona derecha (último 15%)
            else if (mouseX > containerWidth * (1 - threshold)) {
                // Comprobar si hay más contenido para desplazar
                if ($carrusel.scrollLeft() + containerWidth < $carrusel[0].scrollWidth) {
                    if (!scrollTimer) {
                        scrollTimer = setInterval(scrollRight, scrollInterval);
                    }
                } else {
                    clearInterval(scrollTimer);
                    scrollTimer = null;
                }
            }
            // Si el mouse está en la zona central, detener el desplazamiento
            else {
                clearInterval(scrollTimer);
                scrollTimer = null;
            }
        });

        // 3. Detener el desplazamiento al salir del carrusel
        $carrusel.on('mouseleave', function() {
            clearInterval(scrollTimer);
            scrollTimer = null;
        });
    });
});
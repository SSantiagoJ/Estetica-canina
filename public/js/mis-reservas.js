document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');

            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // Add active class to clicked button and corresponding content
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });

    // Modal functionality
    const editButtons = document.querySelectorAll('.btn-editar');
    const modal = document.getElementById('editarReservaModal');
    const form = document.getElementById('formEditarReserva');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const reservaId = this.getAttribute('data-reserva-id');
            const mascotaNombre = this.getAttribute('data-mascota');
            const fecha = this.getAttribute('data-fecha');
            const hora = this.getAttribute('data-hora');
            const enfermedad = this.getAttribute('data-enfermedad');
            const vacuna = this.getAttribute('data-vacuna');
            const alergia = this.getAttribute('data-alergia');
            const descripcionAlergia = this.getAttribute('data-descripcion-alergia');
            
            // Formatear fecha
            const fechaFormateada = formatearFechaModal(fecha);
            
            // Actualizar contenido del modal
            document.getElementById('modalMascotaNombre').textContent = mascotaNombre.toUpperCase();
            document.getElementById('modalFecha').textContent = fechaFormateada;
            document.getElementById('modalHora').textContent = hora;
            
            // Establecer action del formulario
            form.action = `/reservas/${reservaId}`;
            
            // Establecer valores de los radio buttons
            document.querySelector(`input[name="enfermedad"][value="${enfermedad}"]`).checked = true;
            document.querySelector(`input[name="vacuna"][value="${vacuna}"]`).checked = true;
            document.querySelector(`input[name="alergia"][value="${alergia}"]`).checked = true;
            
            // Mostrar/ocultar campo de descripción de alergia
            const descripcionInput = document.getElementById('descripcionAlergia');
            if (alergia == '1') {
                descripcionInput.style.display = 'block';
                descripcionInput.value = descripcionAlergia || '';
            } else {
                descripcionInput.style.display = 'none';
            }
            
            // Mostrar modal
            modal.classList.add('active');
        });
    });

    // Manejo del campo de alergia
    const alergiaRadios = document.querySelectorAll('input[name="alergia"]');
    const descripcionInput = document.getElementById('descripcionAlergia');
    
    alergiaRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === '1') {
                descripcionInput.style.display = 'block';
            } else {
                descripcionInput.style.display = 'none';
                descripcionInput.value = '';
            }
        });
    });

    // Cerrar modal al hacer clic fuera
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModal();
        }
    });
});

function formatearFechaModal(fecha) {
    const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    const partes = fecha.split('-');
    const dia = partes[2];
    const mes = meses[parseInt(partes[1]) - 1];
    const año = partes[0];
    return `${dia}/${mes}/${año}`;
}

function cerrarModal() {
    const modal = document.getElementById('editarReservaModal');
    modal.classList.remove('active');
}

// Funciones para Modal de Detalles
function abrirModalDetalles(button) {
    const reservaId = button.getAttribute('data-reserva-id');
    
    // Buscar la reserva en el array de datos
    let reserva = null;
    for (let i = 0; i < reservasData.length; i++) {
        if (reservasData[i].id_reserva == reservaId) {
            reserva = reservasData[i];
            break;
        }
    }
    
    if (!reserva) {
        console.error('Reserva no encontrada:', reservaId);
        return;
    }
    
    const modal = document.getElementById('detallesReservaModal');
    
    // Actualizar información de la mascota
    document.getElementById('detallesMascotaNombre').textContent = reserva.mascota.nombre.toUpperCase();
    document.getElementById('detallesMascotaFoto').src = reserva.mascota.foto || '/images/default-pet.png';
    document.getElementById('detallesFecha').textContent = reserva.fecha_formateada;
    document.getElementById('detallesHora').textContent = reserva.hora;
    
    // Cargar servicios y calcular totales
    const serviciosContainer = document.getElementById('detallesServicios');
    serviciosContainer.innerHTML = '';
    let subtotal = 0;
    let totalIGV = 0;
    let total = 0;
    
    reserva.detalles.forEach(detalle => {
        const precioUnitario = parseFloat(detalle.precio_unitario);
        const igv = parseFloat(detalle.igv);
        const totalServicio = parseFloat(detalle.total);
        
        const servicioDiv = document.createElement('div');
        servicioDiv.className = 'servicio-item';
        servicioDiv.innerHTML = `
            <div class="servicio-info">
                <span class="servicio-nombre">${detalle.servicio.nombre_servicio}</span>
                <div class="servicio-detalles">
                    <div class="servicio-detalle-linea">
                        <span>Precio base:</span>
                        <span>S/ ${precioUnitario.toFixed(2)}</span>
                    </div>
                    <div class="servicio-detalle-linea">
                        <span>IGV (18%):</span>
                        <span>S/ ${igv.toFixed(2)}</span>
                    </div>
                </div>
            </div>
            <div class="servicio-precio-total">S/ ${totalServicio.toFixed(2)}</div>
        `;
        serviciosContainer.appendChild(servicioDiv);
        
        subtotal += precioUnitario;
        totalIGV += igv;
        total += totalServicio;
    });
    
    // Actualizar resumen de totales
    document.getElementById('detallesSubtotal').textContent = `S/ ${subtotal.toFixed(2)}`;
    document.getElementById('detallesIGV').textContent = `S/ ${totalIGV.toFixed(2)}`;
    document.getElementById('detallesTotal').textContent = `S/ ${total.toFixed(2)}`;
    
    // Generar recomendaciones usando la fecha original
    const recomendacionesContainer = document.getElementById('detallesRecomendaciones');
    recomendacionesContainer.innerHTML = '';
    
    const recomendaciones = obtenerRecomendaciones(reserva.detalles, reserva.fecha);
    
    recomendaciones.forEach(rec => {
        const recDiv = document.createElement('div');
        recDiv.className = 'recomendacion-item';
        recDiv.innerHTML = `
            <div class="recomendacion-servicio">
                <div class="recomendacion-nombre">${rec.servicio}</div>
                <div class="recomendacion-tiempo">Recomendado cada ${rec.frecuencia}</div>
            </div>
            <div class="recomendacion-fecha">
                <div class="recomendacion-fecha-label">Próxima visita sugerida:</div>
                <div class="recomendacion-fecha-valor">${rec.proximaFecha}</div>
            </div>
        `;
        recomendacionesContainer.appendChild(recDiv);
    });
    
    modal.classList.add('active');
}

function cerrarModalDetalles() {
    const modal = document.getElementById('detallesReservaModal');
    modal.classList.remove('active');
}

function obtenerRecomendaciones(detalles, fechaReserva) {
    // Definir frecuencias de servicio en semanas
    const frecuencias = {
        'baño': { semanas: 4, texto: '4 semanas' },
        'completo': { semanas: 4, texto: '4 semanas' },
        'básico': { semanas: 4, texto: '4 semanas' },
        'medicado': { semanas: 4, texto: '4 semanas' },
        'ozonoterapia': { semanas: 4, texto: '4 semanas' },
        'peluquería': { semanas: 8, texto: '8 semanas' },
        'canina': { semanas: 8, texto: '8 semanas' },
        'corte': { semanas: 4, texto: '4 semanas' },
        'uñas': { semanas: 4, texto: '4 semanas' },
        'vacuna': { semanas: 52, texto: '1 año' },
        'antirrábica': { semanas: 52, texto: '1 año' },
        'antirrabica': { semanas: 52, texto: '1 año' },
        'cepillo': { semanas: 12, texto: '12 semanas' },
        'cepillado': { semanas: 12, texto: '12 semanas' },
        'dentadura': { semanas: 12, texto: '12 semanas' },
        'dientes': { semanas: 12, texto: '12 semanas' },
        'limpieza': { semanas: 12, texto: '12 semanas' },
        'oídos': { semanas: 8, texto: '8 semanas' },
        'oidos': { semanas: 8, texto: '8 semanas' },
        'hidroterapia': { semanas: 2, texto: '2 semanas' },
        'tratamiento': { semanas: 6, texto: '6 semanas' },
        'argan': { semanas: 6, texto: '6 semanas' },
        'termal': { semanas: 6, texto: '6 semanas' },
        'anticaída': { semanas: 6, texto: '6 semanas' },
        'anticaida': { semanas: 6, texto: '6 semanas' }
    };
    
    const recomendaciones = [];
    
    // Parsear la fecha de la reserva correctamente
    // La fecha viene en formato YYYY-MM-DD desde la base de datos
    const partesFecha = fechaReserva.split('-');
    const fecha = new Date(parseInt(partesFecha[0]), parseInt(partesFecha[1]) - 1, parseInt(partesFecha[2]));
    
    detalles.forEach(detalle => {
        const nombreServicio = detalle.servicio.nombre_servicio.toLowerCase();
        let frecuencia = null;
        
        // Buscar coincidencia en el nombre del servicio
        for (const [key, value] of Object.entries(frecuencias)) {
            if (nombreServicio.includes(key)) {
                frecuencia = value;
                break;
            }
        }
        
        // Si no se encontró, usar frecuencia por defecto de 6 semanas
        if (!frecuencia) {
            frecuencia = { semanas: 6, texto: '6 semanas' };
        }
        
        // Calcular fecha próxima sumando exactamente las semanas
        const proximaFecha = new Date(fecha.getTime());
        proximaFecha.setDate(proximaFecha.getDate() + (frecuencia.semanas * 7));
        
        recomendaciones.push({
            servicio: detalle.servicio.nombre_servicio,
            frecuencia: frecuencia.texto,
            proximaFecha: formatearFechaRecomendacion(proximaFecha)
        });
    });
    
    return recomendaciones;
}

function formatearFechaRecomendacion(fecha) {
    const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 
                   'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    const dia = fecha.getDate();
    const mes = meses[fecha.getMonth()];
    const año = fecha.getFullYear();
    return `${dia} ${mes} ${año}`;
}
// Variables globales para reprogramación
let reservaActualId = null;
let trabajadorSeleccionado = null;
let fechaSeleccionada = null;

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
            const idEmpleado = this.getAttribute('data-id-empleado');
            const fechaCreacion = this.getAttribute('data-fecha-creacion');
            const enfermedad = this.getAttribute('data-enfermedad');
            const vacuna = this.getAttribute('data-vacuna');
            const alergia = this.getAttribute('data-alergia');
            const descripcionAlergia = this.getAttribute('data-descripcion-alergia');
            
            reservaActualId = reservaId;
            trabajadorSeleccionado = idEmpleado;
            fechaSeleccionada = fecha;
            
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
            
            // Verificar 48 horas para reprogramación
            verificarReprogramacion(fechaCreacion, fecha, hora);
            
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

    // Event listener para cambio de fecha en reprogramación
    const editFecha = document.getElementById('editFecha');
    if (editFecha) {
        editFecha.addEventListener('change', function() {
            fechaSeleccionada = this.value;
            document.getElementById('trabajador-section').style.display = 'block';
            document.getElementById('horarios-section').style.display = 'none';
        });
    }

    // Event listeners para botones de trabajadores en modal de edición
    const botonesTrabajaroresEdit = document.querySelectorAll('.btn-trabajador-modal');
    botonesTrabajaroresEdit.forEach(boton => {
        boton.addEventListener('click', function() {
            // Remover active de todos
            botonesTrabajaroresEdit.forEach(btn => btn.classList.remove('active'));
            // Agregar active al seleccionado
            this.classList.add('active');
            
            trabajadorSeleccionado = this.dataset.id;
            document.getElementById('editNuevoIdEmpleado').value = trabajadorSeleccionado;
            
            // Mostrar nombre del trabajador
            const nombreSpan = document.getElementById('nombre-trabajador-modal');
            if (nombreSpan) {
                nombreSpan.textContent = this.dataset.nombre;
            }
            
            // Cargar horarios
            if (fechaSeleccionada) {
                cargarHorariosEditar();
            } else {
                alert('Por favor selecciona primero una fecha');
            }
        });
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
    
    // Determinar la imagen de la mascota según su raza
    let mascotaFoto = reserva.mascota.foto;
    if (!mascotaFoto && reserva.mascota.raza) {
        const razaNormalizada = reserva.mascota.raza.toLowerCase().replace(/ /g, '-');
        mascotaFoto = `/images/razas/${razaNormalizada}.png`;
    }
    
    const imgElement = document.getElementById('detallesMascotaFoto');
    imgElement.src = mascotaFoto || '/images/default-avatar.png';
    imgElement.onerror = function() {
        this.src = '/images/default-avatar.png';
    };
    
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
                <div class="servicio-nombre">
                    <span>${detalle.servicio.nombre_servicio}</span>
                    <span class="servicio-precio-total-top">S/ ${totalServicio.toFixed(2)}</span>
                </div>
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
        `;
        serviciosContainer.appendChild(servicioDiv);
        
        subtotal += precioUnitario;
        totalIGV += igv;
        total += totalServicio;
    });
    
    // Agregar delivery si existe
    if (reserva.delivery) {
        const costoBase = parseFloat(reserva.delivery.costo_delivery);
        const igvDelivery = costoBase * 0.18;
        const totalDelivery = parseFloat(reserva.delivery.total_delivery);
        
        const deliveryDiv = document.createElement('div');
        deliveryDiv.className = 'servicio-item delivery-item';
        deliveryDiv.innerHTML = `
            <div class="servicio-info">
                <div class="servicio-nombre">
                    <span> Servicio de Delivery</span>
                    <span class="servicio-precio-total-top">S/ ${totalDelivery.toFixed(2)}</span>
                </div>
                <div class="servicio-detalles">
                    <div class="servicio-detalle-linea">
                        <small>📍 Recojo: ${reserva.delivery.direccion_recojo}</small>
                    </div>
                    <div class="servicio-detalle-linea">
                        <small>📍 Entrega: ${reserva.delivery.direccion_entrega}</small>
                    </div>
                    <div class="servicio-detalle-linea">
                        <span>Precio base:</span>
                        <span>S/ ${costoBase.toFixed(2)}</span>
                    </div>
                    <div class="servicio-detalle-linea">
                        <span>IGV (18%):</span>
                        <span>S/ ${igvDelivery.toFixed(2)}</span>
                    </div>
                </div>
            </div>
        `;
        serviciosContainer.appendChild(deliveryDiv);
        
        subtotal += costoBase;
        totalIGV += igvDelivery;
        total += totalDelivery;
    }
    
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

// Verificar si puede reprogramar (48 horas)
function verificarReprogramacion(fechaCreacion, fechaReserva, horaReserva) {
    const ahora = new Date();
    const creacion = new Date(fechaCreacion);
    const horasTranscurridas = (ahora - creacion) / (1000 * 60 * 60);
    
    const bloqueadoDiv = document.getElementById('reprogramacion-bloqueada');
    const disponibleDiv = document.getElementById('reprogramacion-disponible');
    const editFecha = document.getElementById('editFecha');
    
    if (horasTranscurridas > 48) {
        // Bloqueado
        bloqueadoDiv.style.display = 'block';
        disponibleDiv.style.display = 'none';
        editFecha.value = '';
        editFecha.disabled = true;
    } else {
        // Disponible
        bloqueadoDiv.style.display = 'none';
        disponibleDiv.style.display = 'block';
        editFecha.value = fechaReserva;
        editFecha.disabled = false;
    }
}

// Cargar horarios para edición
function cargarHorariosEditar() {
    if (!trabajadorSeleccionado || !fechaSeleccionada) {
        return;
    }
    
    const horariosGrid = document.getElementById('editHorariosGrid');
    horariosGrid.innerHTML = '<div class="text-center"><small>Cargando horarios...</small></div>';
    
    // Obtener duración total de servicios de la reserva actual
    // Por simplicidad, usamos 60 minutos por defecto
    const duracion = 60;
    
    fetch('/reservas/obtener-horarios', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            fecha: fechaSeleccionada,
            id_empleado: trabajadorSeleccionado,
            duracion: duracion,
            id_reserva_actual: reservaActualId
        })
    })
    .then(response => response.json())
    .then(data => {
        mostrarHorariosEditar(data);
        document.getElementById('horarios-section').style.display = 'block';
    })
    .catch(error => {
        console.error('Error:', error);
        horariosGrid.innerHTML = '<div class="text-danger"><small>Error al cargar horarios</small></div>';
    });
}

// Mostrar horarios en el modal de edición
function mostrarHorariosEditar(horarios) {
    const horariosGrid = document.getElementById('editHorariosGrid');
    horariosGrid.innerHTML = '';
    
    horarios.forEach(horario => {
        const slot = document.createElement('div');
        slot.className = 'horario-slot-modal';
        slot.textContent = horario.hora;
        
        if (horario.disponible) {
            slot.classList.add('disponible');
            slot.addEventListener('click', function() {
                seleccionarHorarioEditar(horario.hora, this);
            });
        } else {
            slot.classList.add('bloqueado');
        }
        
        horariosGrid.appendChild(slot);
    });
}

// Seleccionar horario en modal de edición
function seleccionarHorarioEditar(hora, elemento) {
    // Remover selección previa
    document.querySelectorAll('.horario-slot-modal.selected').forEach(slot => {
        slot.classList.remove('selected');
    });
    
    // Marcar como seleccionado
    elemento.classList.add('selected');
    
    // Guardar en input hidden
    document.getElementById('editNuevaHora').value = hora;
}

// ========================================
// FUNCIONES PARA CALIFICACIÓN DE SERVICIOS
// ========================================

function abrirModalCalificacion(button) {
    console.log('Abriendo modal de calificación');
    const reservaId = button.getAttribute('data-reserva-id');
    const mascotaNombre = button.getAttribute('data-mascota');
    const fecha = button.getAttribute('data-fecha');
    const servicios = button.getAttribute('data-servicios');
    
    console.log('Datos:', { reservaId, mascotaNombre, fecha, servicios });
    
    // Actualizar contenido del modal
    document.getElementById('calificacionMascotaNombre').textContent = mascotaNombre;
    document.getElementById('calificacionFecha').textContent = fecha;
    document.getElementById('calificacionServicios').textContent = servicios;
    document.getElementById('calificacion_id_reserva').value = reservaId;
    
    // Resetear formulario
    document.getElementById('formCalificacion').reset();
    document.getElementById('calificacion_rating').value = '0';
    document.querySelectorAll('.rating-stars .star').forEach(star => {
        star.classList.remove('active');
    });
    document.getElementById('ratingDescription').textContent = 'Selecciona una calificación';
    
    // Abrir modal con Bootstrap 5
    const modal = new bootstrap.Modal(document.getElementById('modalCalificacion'));
    modal.show();
}

// Event listeners para las estrellas de calificación
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.rating-stars .star');
    const ratingInput = document.getElementById('calificacion_rating');
    const ratingDescription = document.getElementById('ratingDescription');
    
    // Descripciones para cada calificación
    const descriptions = {
        1: '⭐ Muy malo',
        2: '⭐⭐ Malo', 
        3: '⭐⭐⭐ Regular',
        4: '⭐⭐⭐⭐ Bueno',
        5: '⭐⭐⭐⭐⭐ Excelente'
    };
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            ratingInput.value = value;
            
            // Actualizar estrellas visualmente
            stars.forEach(s => {
                s.classList.remove('active');
                if (s.getAttribute('data-value') <= value) {
                    s.classList.add('active');
                }
            });
            
            // Actualizar descripción
            ratingDescription.textContent = descriptions[value];
        });
        
        // Efecto hover
        star.addEventListener('mouseenter', function() {
            const value = this.getAttribute('data-value');
            stars.forEach(s => {
                s.classList.remove('hover');
                if (s.getAttribute('data-value') <= value) {
                    s.classList.add('hover');
                }
            });
        });
    });
    
    // Remover hover al salir del área de estrellas
    document.querySelector('.rating-stars').addEventListener('mouseleave', function() {
        stars.forEach(s => s.classList.remove('hover'));
    });
    
    // Guardar calificación
    document.getElementById('btnGuardarCalificacion').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('formCalificacion'));
        const calificacion = formData.get('calificacion');
        
        if (calificacion == '0') {
            alert('Por favor selecciona una calificación');
            return;
        }
        
        // Deshabilitar botón mientras se procesa
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        
        fetch('/calificacion/guardar', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('¡Gracias por tu calificación! Tu opinión nos ayuda a mejorar nuestros servicios.');
                bootstrap.Modal.getInstance(document.getElementById('modalCalificacion')).hide();
                location.reload(); // Recargar para mostrar el estado actualizado
            } else {
                alert(data.message || 'Error al guardar la calificación');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al conectar con el servidor');
        })
        .finally(() => {
            // Rehabilitar botón
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-save"></i> Guardar Calificación';
        });
    });
});

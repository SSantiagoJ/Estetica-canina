let trabajadorSeleccionado = null;
let fechaSeleccionada = null;
let duracionTotal = 0;
let calendarioActual = null;
let fechaMinima = null;

document.addEventListener('DOMContentLoaded', function () {
    inicializarDelivery();
    inicializarFormulario();
    inicializarTrabajadores();
    inicializarFecha();
    inicializarServicios();
    calcularDuracionTotal();
});

function inicializarDelivery() {
    const deliveryCheckbox = document.getElementById('requiere_delivery');

    if (!deliveryCheckbox) {
        return;
    }

    deliveryCheckbox.addEventListener('change', function () {
        const deliveryFields = document.getElementById('delivery-fields');
        const inputs = deliveryFields.querySelectorAll('input, textarea');
        const direccionRecojo = document.getElementById('direccion_recojo');

        if (this.checked) {
            deliveryFields.style.display = 'block';
            if (direccionRecojo) {
                direccionRecojo.required = true;
            }
            return;
        }

        deliveryFields.style.display = 'none';
        inputs.forEach(input => {
            input.required = false;
            input.value = '';
        });
    });
}

function inicializarFormulario() {
    const formulario = document.querySelector('form');

    if (!formulario) {
        return;
    }

    formulario.addEventListener('submit', function (e) {
        const fecha = document.getElementById('fecha')?.value;
        const idEmpleado = document.getElementById('id_empleado')?.value;
        const hora = document.getElementById('hora')?.value;

        if (!hayServiciosSeleccionados()) {
            e.preventDefault();
            alert('Por favor selecciona al menos un servicio');
            return false;
        }

        if (!fecha) {
            e.preventDefault();
            alert('Por favor selecciona un dia para la reserva');
            return false;
        }

        if (!idEmpleado) {
            e.preventDefault();
            alert('Por favor selecciona un trabajador');
            return false;
        }

        if (!hora) {
            e.preventDefault();
            alert('Por favor selecciona un rango de tiempo');
            return false;
        }

        const deliveryChecked = document.getElementById('requiere_delivery')?.checked;
        if (deliveryChecked) {
            const direccionRecojo = document.getElementById('direccion_recojo')?.value;
            const direccionEntrega = document.getElementById('direccion_entrega')?.value;

            if (!direccionEntrega && direccionRecojo) {
                document.getElementById('direccion_entrega').value = direccionRecojo;
            }
        }
    });
}

function inicializarFecha() {
    const fechaInput = document.getElementById('fecha');

    if (!fechaInput) {
        return;
    }

    const hoy = new Date();
    const fechaHoy = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate());
    fechaMinima = parsearFechaISO(fechaInput.dataset.minDate) || fechaHoy;
    fechaSeleccionada = fechaInput.value || null;

    const fechaBase = fechaSeleccionada ? parsearFechaISO(fechaSeleccionada) : fechaMinima;
    calendarioActual = new Date(fechaBase.getFullYear(), fechaBase.getMonth(), 1);

    document.getElementById('calendar-prev-month')?.addEventListener('click', function () {
        cambiarMes(-1);
    });

    document.getElementById('calendar-next-month')?.addEventListener('click', function () {
        cambiarMes(1);
    });

    renderizarCalendario();

    if (fechaSeleccionada) {
        actualizarResumenFecha();
    }
}

function inicializarServicios() {
    const serviciosCheckboxes = document.querySelectorAll('input[type="checkbox"][name="servicios[]"], input[type="checkbox"][name="adicionales[]"]');

    serviciosCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            calcularDuracionTotal();
            limpiarHorarioSeleccionado();
            actualizarResumenDuracion();

            if (fechaSeleccionada) {
                mostrarSeccionHorarios(true);
                if (asegurarTrabajadorSeleccionado()) {
                    cargarHorarios();
                }
            } else {
                mostrarSeccionHorarios(false);
            }
        });
    });
}

function inicializarTrabajadores() {
    const botonesTrabajadores = document.querySelectorAll('.btn-trabajador');

    botonesTrabajadores.forEach(boton => {
        boton.addEventListener('click', function () {
            seleccionarTrabajadorDesdeBoton(this, true);
        });
    });
}

function seleccionarTrabajadorDesdeBoton(boton, cargar = true) {
    if (!boton) {
        return false;
    }

    document.querySelectorAll('.btn-trabajador').forEach(btn => btn.classList.remove('active'));
    boton.classList.add('active');

    trabajadorSeleccionado = boton.dataset.id;
    document.getElementById('id_empleado').value = trabajadorSeleccionado;

    const horariosContainer = document.getElementById('horarios-container');
    const nombreTrabajador = document.getElementById('nombre-trabajador');
    if (horariosContainer && nombreTrabajador) {
        horariosContainer.style.display = 'block';
        nombreTrabajador.textContent = boton.dataset.nombre;
    }

    if (cargar && fechaSeleccionada) {
        cargarHorarios();
    }

    return true;
}

function asegurarTrabajadorSeleccionado() {
    if (trabajadorSeleccionado) {
        return true;
    }

    const primerTrabajador = document.querySelector('.btn-trabajador');
    return seleccionarTrabajadorDesdeBoton(primerTrabajador, false);
}

function seleccionarFecha(fecha) {
    if (!fecha || fecha < fechaMinima) {
        return;
    }

    fechaSeleccionada = formatearFechaISO(fecha);
    const fechaInput = document.getElementById('fecha');
    if (fechaInput) {
        fechaInput.value = fechaSeleccionada;
    }

    limpiarHorarioSeleccionado();
    actualizarResumenFecha();
    renderizarCalendario();

    mostrarSeccionHorarios(true);
    if (asegurarTrabajadorSeleccionado()) {
        cargarHorarios();
    }
}

function cambiarMes(offset) {
    calendarioActual = new Date(calendarioActual.getFullYear(), calendarioActual.getMonth() + offset, 1);
    renderizarCalendario();
}

function renderizarCalendario() {
    const grid = document.getElementById('pet-calendar-grid');
    const monthLabel = document.getElementById('calendar-month-label');
    const yearLabel = document.getElementById('calendar-year-label');
    const prevButton = document.getElementById('calendar-prev-month');

    if (!grid || !calendarioActual) {
        return;
    }

    const year = calendarioActual.getFullYear();
    const month = calendarioActual.getMonth();
    const monthName = new Intl.DateTimeFormat('es-PE', { month: 'long' }).format(calendarioActual);

    if (monthLabel) {
        monthLabel.textContent = capitalizar(monthName);
    }

    if (yearLabel) {
        yearLabel.textContent = year;
    }

    if (prevButton && fechaMinima) {
        const mesMinimo = new Date(fechaMinima.getFullYear(), fechaMinima.getMonth(), 1);
        prevButton.disabled = calendarioActual <= mesMinimo;
    }

    grid.innerHTML = '';

    const primerDiaSemana = new Date(year, month, 1).getDay();
    const diasDelMes = new Date(year, month + 1, 0).getDate();

    for (let i = 0; i < primerDiaSemana; i++) {
        const celdaVacia = document.createElement('span');
        celdaVacia.className = 'pet-calendar-empty';
        grid.appendChild(celdaVacia);
    }

    for (let dia = 1; dia <= diasDelMes; dia++) {
        const fecha = new Date(year, month, dia);
        const boton = document.createElement('button');
        boton.type = 'button';
        boton.className = 'pet-calendar-day';
        boton.innerHTML = `<span>${dia}</span>`;

        if (fecha < fechaMinima) {
            boton.disabled = true;
            boton.classList.add('is-disabled');
        }

        if (esMismaFecha(fecha, new Date())) {
            boton.classList.add('is-today');
        }

        if (fechaSeleccionada && formatearFechaISO(fecha) === fechaSeleccionada) {
            boton.classList.add('is-selected');
        }

        boton.addEventListener('click', function () {
            seleccionarFecha(fecha);
        });

        grid.appendChild(boton);
    }
}

function hayServiciosSeleccionados() {
    return document.querySelectorAll('input[type="checkbox"][name="servicios[]"]:checked, input[type="checkbox"][name="adicionales[]"]:checked').length > 0;
}

function mostrarSeccionHorarios(mostrar) {
    const section = document.getElementById('trabajador-horarios-section');
    const horariosContainer = document.getElementById('horarios-container');

    if (section) {
        section.style.display = mostrar ? 'block' : 'none';
    }

    if (!mostrar && horariosContainer) {
        horariosContainer.style.display = 'none';
    }
}

function calcularDuracionTotal() {
    duracionTotal = 0;
    const serviciosCheckboxes = document.querySelectorAll('input[type="checkbox"][name="servicios[]"]:checked, input[type="checkbox"][name="adicionales[]"]:checked');

    serviciosCheckboxes.forEach(checkbox => {
        duracionTotal += normalizarDuracion(checkbox.dataset.duracion);
    });

    return duracionTotal;
}

function normalizarDuracion(valor) {
    const numero = parseFloat(String(valor || '').replace(',', '.'));

    if (!Number.isFinite(numero) || numero <= 0) {
        return 60;
    }

    return Math.round(numero);
}

function obtenerDuracionReserva() {
    return Math.max(calcularDuracionTotal() || 60, 60);
}

function formatearDuracion(minutos) {
    const total = Math.max(parseInt(minutos, 10) || 60, 60);
    const horas = Math.floor(total / 60);
    const resto = total % 60;

    if (horas > 0 && resto > 0) {
        return `${horas} h ${resto} min`;
    }

    if (horas > 0) {
        return `${horas} h`;
    }

    return `${total} min`;
}

function sumarMinutos(hora, minutos) {
    const [horas, mins] = hora.split(':').map(Number);
    const fecha = new Date();
    fecha.setHours(horas, mins, 0, 0);
    fecha.setMinutes(fecha.getMinutes() + minutos);

    return `${String(fecha.getHours()).padStart(2, '0')}:${String(fecha.getMinutes()).padStart(2, '0')}`;
}

function obtenerPeriodo(hora) {
    const horaNumero = parseInt(hora.split(':')[0], 10);

    if (horaNumero < 12) {
        return 'Manana';
    }

    if (horaNumero < 17) {
        return 'Tarde';
    }

    return 'Noche';
}

function etiquetaPeriodo(periodo) {
    return periodo === 'Manana' ? 'Ma\u00f1ana' : periodo;
}

function parsearFechaISO(fechaISO) {
    if (!fechaISO) {
        return null;
    }

    const partes = fechaISO.split('-').map(Number);
    if (partes.length !== 3 || partes.some(Number.isNaN)) {
        return null;
    }

    return new Date(partes[0], partes[1] - 1, partes[2]);
}

function formatearFechaISO(fecha) {
    return `${fecha.getFullYear()}-${String(fecha.getMonth() + 1).padStart(2, '0')}-${String(fecha.getDate()).padStart(2, '0')}`;
}

function esMismaFecha(fechaA, fechaB) {
    return fechaA.getFullYear() === fechaB.getFullYear()
        && fechaA.getMonth() === fechaB.getMonth()
        && fechaA.getDate() === fechaB.getDate();
}

function capitalizar(texto) {
    return texto ? texto.charAt(0).toUpperCase() + texto.slice(1) : '';
}

function formatearFechaElegida(fechaISO) {
    if (!fechaISO) {
        return null;
    }

    const fecha = parsearFechaISO(fechaISO);
    const dia = new Intl.DateTimeFormat('es-PE', {
        weekday: 'long',
        day: 'numeric',
    }).format(fecha);
    const mes = new Intl.DateTimeFormat('es-PE', {
        month: 'long',
        year: 'numeric',
    }).format(fecha);

    return {
        dia: capitalizar(dia),
        mes: capitalizar(mes),
    };
}

function actualizarResumenFecha() {
    const summary = document.getElementById('fecha-summary-card');
    const dia = document.getElementById('fecha-summary-day');
    const mes = document.getElementById('fecha-summary-month');
    const displayText = document.getElementById('date-display-text');
    const displayHelper = document.getElementById('date-display-helper');

    if (!fechaSeleccionada) {
        return;
    }

    const fecha = formatearFechaElegida(fechaSeleccionada);

    if (summary && dia && mes) {
        summary.hidden = false;
        dia.textContent = fecha.dia;
        mes.textContent = `${fecha.mes} - ${formatearDuracion(obtenerDuracionReserva())} por cita`;
    }

    if (displayText && displayHelper) {
        displayText.textContent = fecha.dia;
        displayHelper.textContent = `${fecha.mes} - rangos disponibles segun trabajador`;
    }
}

function actualizarResumenDuracion() {
    actualizarResumenFecha();

    const helper = document.getElementById('duracion-helper');
    if (helper) {
        helper.textContent = hayServiciosSeleccionados()
            ? `Cada bloque se muestra como rango. Duracion estimada: ${formatearDuracion(obtenerDuracionReserva())}.`
            : 'Mostrando rangos de 1 h. Al elegir servicios, la duracion se ajusta automaticamente.';
    }
}

function limpiarHorarioSeleccionado() {
    const horaInput = document.getElementById('hora');
    const selectedCard = document.getElementById('selected-range-card');

    if (horaInput) {
        horaInput.value = '';
    }

    if (selectedCard) {
        selectedCard.hidden = true;
    }

    document.querySelectorAll('.horario-slot.selected').forEach(slot => {
        slot.classList.remove('selected');
    });
}

function cargarHorarios() {
    if (!trabajadorSeleccionado || !fechaSeleccionada) {
        return;
    }

    const duracion = obtenerDuracionReserva();

    actualizarResumenDuracion();

    const horariosGrid = document.getElementById('horarios-grid');
    if (!horariosGrid) {
        return;
    }

    horariosGrid.innerHTML = '<div class="slot-loader"><i class="fas fa-hourglass-half"></i><span>Buscando rangos disponibles...</span></div>';

    fetch('/reservas/obtener-horarios', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({
            fecha: fechaSeleccionada,
            id_empleado: trabajadorSeleccionado,
            duracion: duracion,
        }),
    })
        .then(response => response.json())
        .then(data => {
            mostrarHorarios(data);
        })
        .catch(error => {
            console.error('Error:', error);
            horariosGrid.innerHTML = '<div class="slot-empty"><i class="fas fa-triangle-exclamation"></i><span>Error al cargar rangos. Intenta nuevamente.</span></div>';
        });
}

function mostrarHorarios(horarios) {
    const horariosGrid = document.getElementById('horarios-grid');
    const duracion = obtenerDuracionReserva();

    if (!horariosGrid) {
        return;
    }

    horariosGrid.innerHTML = '';

    if (!Array.isArray(horarios) || horarios.length === 0) {
        horariosGrid.innerHTML = '<div class="slot-empty"><i class="fas fa-calendar-xmark"></i><span>No hay rangos disponibles para esta fecha.</span></div>';
        return;
    }

    const grupos = horarios.reduce((acc, horario) => {
        const periodo = obtenerPeriodo(horario.hora);
        acc[periodo] = acc[periodo] || [];
        acc[periodo].push(horario);
        return acc;
    }, {});

    ['Manana', 'Tarde', 'Noche'].forEach(periodo => {
        if (!grupos[periodo]) {
            return;
        }

        const grupo = document.createElement('section');
        grupo.className = 'horario-period-group';

        const titulo = document.createElement('h6');
        titulo.className = 'horario-period-title';
        titulo.textContent = etiquetaPeriodo(periodo);
        grupo.appendChild(titulo);

        const grid = document.createElement('div');
        grid.className = 'horario-period-grid';

        grupos[periodo].forEach(horario => {
            const horaFin = sumarMinutos(horario.hora, duracion);
            const slot = document.createElement('button');
            slot.type = 'button';
            slot.className = 'horario-slot';
            slot.innerHTML = `
                <span class="slot-range">${horario.hora} - ${horaFin}</span>
                <small>${horario.disponible ? 'Disponible' : 'Ocupado'}</small>
            `;

            if (horario.disponible) {
                slot.classList.add('disponible');
                slot.addEventListener('click', function () {
                    seleccionarHorario(horario.hora, horaFin, this);
                });
            } else {
                slot.classList.add('bloqueado');
                slot.disabled = true;
            }

            grid.appendChild(slot);
        });

        grupo.appendChild(grid);
        horariosGrid.appendChild(grupo);
    });

    if (!horarios.some(horario => horario.disponible)) {
        const aviso = document.createElement('div');
        aviso.className = 'slot-empty';
        aviso.innerHTML = '<i class="fas fa-calendar-xmark"></i><span>No quedan rangos libres para este trabajador en la fecha elegida.</span>';
        horariosGrid.appendChild(aviso);
    }
}

function seleccionarHorario(hora, horaFin, elemento) {
    document.querySelectorAll('.horario-slot.selected').forEach(slot => {
        slot.classList.remove('selected');
    });

    elemento.classList.add('selected');
    document.getElementById('hora').value = hora;

    const selectedCard = document.getElementById('selected-range-card');
    const title = document.getElementById('selected-range-title');
    const copy = document.getElementById('selected-range-copy');

    if (selectedCard && title && copy) {
        selectedCard.hidden = false;
        title.textContent = `${hora} - ${horaFin}`;
        copy.textContent = `Rango elegido para ${formatearDuracion(obtenerDuracionReserva())} de atencion.`;
    }
}

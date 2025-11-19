// JavaScript para el formulario de selección de servicios

// Variables globales
let trabajadorSeleccionado = null;
let fechaSeleccionada = null;
let duracionTotal = 0;

// Inicializar eventos cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    
    // Alternar visibilidad de los campos de entrega
    const deliveryCheckbox = document.getElementById('requiere_delivery');
    if (deliveryCheckbox) {
        deliveryCheckbox.addEventListener('change', function() {
            const deliveryFields = document.getElementById('delivery-fields');
            const inputs = deliveryFields.querySelectorAll('input, textarea');
            
            if (this.checked) {
                deliveryFields.style.display = 'block';
                // direccion_recojo sea obligatorio cuando se seleccione la entrega
                const direccionRecojo = document.getElementById('direccion_recojo');
                if (direccionRecojo) {
                    direccionRecojo.required = true;
                }
            } else {
                deliveryFields.style.display = 'none';
                // Eliminar atributo obligatorio cuando no se seleccione la entrega
                inputs.forEach(input => {
                    input.required = false;
                    input.value = ''; 
                });
            }
        });
    }

    // Autocompletar la dirección de entrega 
    const formulario = document.querySelector('form');
    if (formulario) {
        formulario.addEventListener('submit', function(e) {
            // Validar que se haya seleccionado trabajador y horario
            const idEmpleado = document.getElementById('id_empleado')?.value;
            const hora = document.getElementById('hora')?.value;
            
            if (!idEmpleado) {
                e.preventDefault();
                alert('Por favor selecciona un trabajador');
                return false;
            }
            
            if (!hora) {
                e.preventDefault();
                alert('Por favor selecciona un horario');
                return false;
            }
            
            // Delivery validación
            const deliveryChecked = document.getElementById('requiere_delivery')?.checked;
            if (deliveryChecked) {
                const direccionRecojo = document.getElementById('direccion_recojo')?.value;
                const direccionEntrega = document.getElementById('direccion_entrega')?.value;
                
                // Si la dirección de entrega está vacía, usar la dirección de recojo.
                if (!direccionEntrega && direccionRecojo) {
                    document.getElementById('direccion_entrega').value = direccionRecojo;
                }
            }
        });
    }

    // Gestión de trabajadores y horarios
    inicializarTrabajadores();
    
    // Event listener para cambios en la fecha
    const fechaInput = document.getElementById('fecha');
    if (fechaInput) {
        fechaInput.addEventListener('change', function() {
            fechaSeleccionada = this.value;
            
            // Mostrar sección de trabajadores si hay servicios seleccionados
            const serviciosSeleccionados = document.querySelectorAll('input[type="checkbox"][name="servicios[]"]:checked, input[type="checkbox"][name="adicionales[]"]:checked');
            if (serviciosSeleccionados.length > 0) {
                document.getElementById('trabajador-horarios-section').style.display = 'block';
            }
            
            if (trabajadorSeleccionado) {
                cargarHorarios();
            }
        });
    }

    // Event listener para cambios en servicios (recalcular duración)
    const serviciosCheckboxes = document.querySelectorAll('input[type="checkbox"][name="servicios[]"], input[type="checkbox"][name="adicionales[]"]');
    serviciosCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            calcularDuracionTotal();
            
            // Mostrar sección de trabajadores si hay al menos un servicio
            const hayServicios = document.querySelectorAll('input[type="checkbox"][name="servicios[]"]:checked, input[type="checkbox"][name="adicionales[]"]:checked').length > 0;
            if (hayServicios && fechaSeleccionada) {
                document.getElementById('trabajador-horarios-section').style.display = 'block';
            } else {
                document.getElementById('trabajador-horarios-section').style.display = 'none';
            }
            
            if (trabajadorSeleccionado && fechaSeleccionada) {
                cargarHorarios();
            }
        });
    });
});

// Inicializar botones de trabajadores
function inicializarTrabajadores() {
    const botonesTrabajadores = document.querySelectorAll('.btn-trabajador');
    
    botonesTrabajadores.forEach(boton => {
        boton.addEventListener('click', function() {
            // Remover clase active de todos
            botonesTrabajadores.forEach(btn => btn.classList.remove('active'));
            
            // Agregar clase active al seleccionado
            this.classList.add('active');
            
            // Guardar trabajador seleccionado
            trabajadorSeleccionado = this.dataset.id;
            document.getElementById('id_empleado').value = trabajadorSeleccionado;
            
            // Mostrar contenedor de horarios
            const horariosContainer = document.getElementById('horarios-container');
            const nombreTrabajador = document.getElementById('nombre-trabajador');
            if (horariosContainer && nombreTrabajador) {
                horariosContainer.style.display = 'block';
                nombreTrabajador.textContent = this.dataset.nombre;
            }
            
            // Cargar horarios si hay fecha
            if (fechaSeleccionada) {
                cargarHorarios();
            } else {
                alert('Por favor selecciona primero una fecha');
            }
        });
    });
}

// Calcular duración total de servicios seleccionados
function calcularDuracionTotal() {
    duracionTotal = 0;
    const serviciosCheckboxes = document.querySelectorAll('input[type="checkbox"][name="servicios[]"]:checked, input[type="checkbox"][name="adicionales[]"]:checked');
    
    serviciosCheckboxes.forEach(checkbox => {
        const duracion = parseInt(checkbox.dataset.duracion) || 0;
        duracionTotal += duracion;
    });
    
    return duracionTotal;
}

// Cargar horarios disponibles via AJAX
function cargarHorarios() {
    if (!trabajadorSeleccionado || !fechaSeleccionada) {
        return;
    }
    
    // Calcular duración total
    const duracion = calcularDuracionTotal();
    
    if (duracion === 0) {
        alert('Por favor selecciona al menos un servicio');
        return;
    }
    
    const horariosGrid = document.getElementById('horarios-grid');
    horariosGrid.innerHTML = '<div class="text-center"><i class="bi bi-hourglass-split"></i> Cargando horarios...</div>';
    
    // Realizar petición AJAX
    fetch('/reservas/obtener-horarios', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            fecha: fechaSeleccionada,
            id_empleado: trabajadorSeleccionado,
            duracion: duracion
        })
    })
    .then(response => response.json())
    .then(data => {
        mostrarHorarios(data);
    })
    .catch(error => {
        console.error('Error:', error);
        horariosGrid.innerHTML = '<div class="text-danger">Error al cargar horarios</div>';
    });
}

// Mostrar horarios en la interfaz
function mostrarHorarios(horarios) {
    const horariosGrid = document.getElementById('horarios-grid');
    horariosGrid.innerHTML = '';
    
    horarios.forEach(horario => {
        const slot = document.createElement('div');
        slot.className = 'horario-slot';
        slot.textContent = horario.hora;
        
        if (horario.disponible) {
            slot.classList.add('disponible');
            slot.addEventListener('click', function() {
                seleccionarHorario(horario.hora, this);
            });
        } else {
            slot.classList.add('bloqueado');
        }
        
        horariosGrid.appendChild(slot);
    });
}

// Seleccionar un horario
function seleccionarHorario(hora, elemento) {
    // Remover selección previa
    document.querySelectorAll('.horario-slot.selected').forEach(slot => {
        slot.classList.remove('selected');
    });
    
    // Marcar como seleccionado
    elemento.classList.add('selected');
    
    // Guardar en input hidden
    document.getElementById('hora').value = hora;
}

// JavaScript para el formulario de selección de servicios

// Inicializar eventos cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    
    // Toggle delivery fields visibility
    const deliveryCheckbox = document.getElementById('requiere_delivery');
    if (deliveryCheckbox) {
        deliveryCheckbox.addEventListener('change', function() {
            const deliveryFields = document.getElementById('delivery-fields');
            const inputs = deliveryFields.querySelectorAll('input, textarea');
            
            if (this.checked) {
                deliveryFields.style.display = 'block';
                // Make direccion_recojo required when delivery is selected
                const direccionRecojo = document.getElementById('direccion_recojo');
                if (direccionRecojo) {
                    direccionRecojo.required = true;
                }
            } else {
                deliveryFields.style.display = 'none';
                // Remove required attribute when delivery is not selected
                inputs.forEach(input => {
                    input.required = false;
                    input.value = ''; // Clear values
                });
            }
        });
    }

    // Auto-fill delivery address with recojo address if entrega is left empty
    const formulario = document.querySelector('form');
    if (formulario) {
        formulario.addEventListener('submit', function(e) {
            const deliveryChecked = document.getElementById('requiere_delivery')?.checked;
            if (deliveryChecked) {
                const direccionRecojo = document.getElementById('direccion_recojo')?.value;
                const direccionEntrega = document.getElementById('direccion_entrega')?.value;
                
                // If entrega address is empty, use recojo address
                if (!direccionEntrega && direccionRecojo) {
                    document.getElementById('direccion_entrega').value = direccionRecojo;
                }
            }
        });
    }
});

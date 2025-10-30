document.addEventListener('DOMContentLoaded', function() {
    const modal = new bootstrap.Modal(document.getElementById('modalSubirImagen'));
    const formSubirImagen = document.getElementById('formSubirImagen');
    const imagenInput = document.getElementById('imagenInput');
    let currentServicioId = null;

    // Abrir modal al hacer clic en "SUBIR IMAGEN"
    document.querySelectorAll('.btn-subir-imagen').forEach(btn => {
        btn.addEventListener('click', function() {
            currentServicioId = this.dataset.id;
            modal.show();
        });
    });

    // Subir imagen
    formSubirImagen.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append('imagen', imagenInput.files[0]);
        formData.append('id_servicio', currentServicioId);

        fetch('/admin/servicios/upload-image', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Imagen subida exitosamente');
                modal.hide();
                location.reload();
            } else {
                alert('Error al subir la imagen');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al subir la imagen');
        });
    });
});
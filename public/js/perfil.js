function openModal() {
    document.getElementById('modalMascota').style.display = 'flex';
}

function closeModal() {
    document.getElementById('modalMascota').style.display = 'none';
    document.getElementById('formMascota').reset();
}

function openEditModal(id, nombre, fecha, sexo, tamano, especie, raza, peso, descripcion) {
    document.getElementById('edit_id_mascota').value = id;
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_fecha_nacimiento').value = fecha;
    document.getElementById('edit_sexo').value = sexo;
    document.getElementById('edit_tamano').value = tamano || '';
    document.getElementById('edit_especie').value = especie;
    document.getElementById('edit_raza').value = raza || '';
    document.getElementById('edit_peso').value = peso || '';
    document.getElementById('edit_descripcion').value = descripcion || '';

    document.getElementById('modalEditarMascota').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('modalEditarMascota').style.display = 'none';
    document.getElementById('formEditarMascota').reset();
}

window.onclick = function(event) {
    const modal = document.getElementById('modalMascota');
    const editModal = document.getElementById('modalEditarMascota');
    const editPerfilModal = document.getElementById('modalEditarPerfil');
    
    if (event.target == modal) {
        closeModal();
    }
    if (event.target == editModal) {
        closeEditModal();
    }
    if (event.target == editPerfilModal) {
        closeEditPerfilModal();
    }
}

// Registrar nueva mascota
document.getElementById('formMascota').addEventListener('submit', async function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('/perfil/mascotas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok && result.success) {
            alert('Mascota registrada exitosamente');
            closeModal();
            location.reload();
        } else {
            const errorMsg = result.message || result.error || 'Error desconocido';
            alert('Error al registrar la mascota: ' + errorMsg);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error de conexión: ' + error.message);
    }
});

// Editar mascota existente
document.getElementById('formEditarMascota').addEventListener('submit', async function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    const idMascota = data.id_mascota;

    try {
        const response = await fetch(`/perfil/mascotas/${idMascota}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('#formEditarMascota input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok && result.success) {
            alert('Mascota actualizada exitosamente');
            closeEditModal();
            location.reload();
        } else {
            const errorMsg = result.message || result.error || 'Error desconocido';
            alert('Error al actualizar la mascota: ' + errorMsg);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error de conexión: ' + error.message);
    }
});

function openEditPerfilModal() {
    document.getElementById('modalEditarPerfil').style.display = 'flex';
}

function closeEditPerfilModal() {
    document.getElementById('modalEditarPerfil').style.display = 'none';
}

// Editar perfil
document.getElementById('formEditarPerfil').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    try {
        const response = await fetch('/perfil/actualizar', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('#formEditarPerfil input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            alert('Perfil actualizado exitosamente');
            closeEditPerfilModal();
            location.reload();
        } else {
            const errorMsg = result.message || result.error || 'Error desconocido';
            alert('Error al actualizar el perfil: ' + errorMsg);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error de conexion: ' + error.message);
    }
});
document.addEventListener("DOMContentLoaded", () => {
    const checkboxes = document.querySelectorAll('.select-row');
    const selectAll = document.getElementById('select-all');
    const btnEditar = document.getElementById('btn-editar');
    const btnGuardar = document.getElementById('btn-guardar');
    let editMode = false;

    // Selección masiva
    if (selectAll) {
        selectAll.addEventListener('change', () => {
            checkboxes.forEach(chk => chk.checked = selectAll.checked);
            toggleButtons();
        });
    }

    checkboxes.forEach(chk => chk.addEventListener('change', toggleButtons));

    function toggleButtons() {
        const checked = document.querySelectorAll('.select-row:checked').length;
        btnEditar.disabled = checked === 0;
        btnGuardar.disabled = true;
    }

    // Editar
    btnEditar.addEventListener('click', () => {
        editMode = true;
        document.querySelectorAll('.select-row:checked').forEach(chk => {
            const row = chk.closest('tr');

            // Fecha
            let fecha = row.querySelector('.editable.fecha');
            if (fecha) {
                let value = fecha.innerText.trim();
                fecha.innerHTML = `<input type="date" class="form-control form-control-sm" value="${value}">`;
            }

            // Hora
            let hora = row.querySelector('.editable.hora');
            if (hora) {
                let value = hora.innerText.trim();
                hora.innerHTML = `<input type="time" class="form-control form-control-sm" value="${value}">`;
            }

            // Mascota
            let mascota = row.querySelector('.editable.mascota');
            if (mascota) {
                let value = mascota.innerText.trim();
                mascota.innerHTML = `<input type="text" class="form-control form-control-sm" value="${value}">`;
            }

            // Estado (select)
            let estado = row.querySelector('.editable.estado');
            if (estado) {
                let raw = estado.dataset.value || estado.innerText.trim();
                estado.innerHTML = `
                    <select class="form-select form-select-sm">
                        <option value="N" ${raw === "N" || raw === "Pendiente" ? "selected" : ""}>Pendiente</option>
                        <option value="A" ${raw === "A" || raw === "Atendido" ? "selected" : ""}>Atendido</option>
                    </select>
                `;
            }
        });

        btnGuardar.disabled = false;
        btnEditar.disabled = true;
    });

    // Guardar
    btnGuardar.addEventListener('click', () => {
        let updates = [];

        document.querySelectorAll('.select-row:checked').forEach(chk => {
            const row = chk.closest('tr');
            const id = row.dataset.id;

            let data = {
                fecha: row.querySelector('.editable.fecha input')?.value,
                hora: row.querySelector('.editable.hora input')?.value,
                mascota: row.querySelector('.editable.mascota input')?.value,
                estado: row.querySelector('.editable.estado select')?.value
            };

            updates.push({ id, data });
        });

        // CSRF seguro
        let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

        fetch("/admin/reservas/update", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                ...(token ? { "X-CSRF-TOKEN": token } : {}) // solo si existe el meta
            },
            body: JSON.stringify({ updates })
        })
        .then(res => res.json())
        .then(resp => {
            if(resp.success){
                location.reload();
            } else {
                alert("Error al guardar cambios");
            }
        })
        .catch(err => {
            console.error("Error en fetch:", err);
            alert("Error en la petición");
        });
    });
});

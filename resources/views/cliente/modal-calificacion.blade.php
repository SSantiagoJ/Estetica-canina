<!-- resources/views/cliente/modal-calificacion.blade.php -->

<div class="modal fade" id="modalCalificacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-star"></i> Calificar Servicio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formCalificacion">
                    <input type="hidden" id="id_reserva" name="id_reserva">
                    
                    <div class="mb-3">
                        <label class="form-label">Calificación</label>
                        <div class="rating-stars">
                            <i class="fas fa-star star" data-value="1"></i>
                            <i class="fas fa-star star" data-value="2"></i>
                            <i class="fas fa-star star" data-value="3"></i>
                            <i class="fas fa-star star" data-value="4"></i>
                            <i class="fas fa-star star" data-value="5"></i>
                        </div>
                        <input type="hidden" id="calificacion" name="calificacion" value="0">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Comentarios (Opcional)</label>
                        <textarea class="form-control" name="comentarios" rows="3" 
                                  placeholder="Cuéntanos tu experiencia..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardarCalificacion">
                    Guardar Calificación
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.rating-stars {
    font-size: 2rem;
    cursor: pointer;
}

.rating-stars .star {
    color: #ddd;
    margin-right: 10px;
    transition: color 0.2s;
}

.rating-stars .star:hover,
.rating-stars .star.active {
    color: #ffc107;
}
</style>

<script>
document.querySelectorAll('.rating-stars .star').forEach(star => {
    star.addEventListener('click', function() {
        const value = this.dataset.value;
        document.getElementById('calificacion').value = value;
        
        document.querySelectorAll('.rating-stars .star').forEach(s => {
            s.classList.remove('active');
            if (s.dataset.value <= value) {
                s.classList.add('active');
            }
        });
    });
});

document.getElementById('btnGuardarCalificacion').addEventListener('click', function() {
    const formData = new FormData(document.getElementById('formCalificacion'));
    
    if (formData.get('calificacion') == 0) {
        alert('Por favor selecciona una calificación');
        return;
    }
    
    fetch('{{ route("calificacion.guardar") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById('modalCalificacion')).hide();
            location.reload();
        } else {
            alert(data.message);
        }
    });
});
</script>

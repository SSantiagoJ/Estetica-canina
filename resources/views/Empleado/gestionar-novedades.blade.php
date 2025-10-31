@extends('layouts.app')

@section('title', 'Gestionar Novedades - Estética Canina')

{{-- Agregando header de empleado igual que en gestionar turnos --}}
@section('header')
    @include('partials.empleado_header')
@endsection

@section('content')
<div class="container-fluid py-4" style="background-color: #2C3E50; min-height: 100vh;">
    <!-- Contenedor principal con fondo blanco -->
    <div class="card border-0 shadow-lg" style="border-radius: 20px;">
        <div class="card-body p-4">
            <!-- Título -->
            <h2 class="text-center mb-4 fw-bold">Gestión de novedades</h2>

    <!-- Mensajes de éxito/error -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>¡Éxito!</strong> 
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
           <strong>¡Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>¡Error!</strong> Por favor corrige los siguientes errores:
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

     <!-- Botón NUEVA NOVEDAD -->
            <div class="mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistrarNovedad">
                    NUEVA NOVEDAD
                </button>
            </div>

            <!-- Filtros de búsqueda -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label for="filtroTitulo" class="form-label small">Título</label>
                    <input type="text" class="form-control form-control-sm" id="filtroTitulo" placeholder="Escribe aquí">
                </div>
                <div class="col-md-2">
                    <label for="filtroFechapublicacion" class="form-label small">Fecha publicación</label>
                    <input type="date" class="form-control form-control-sm" id="filtroFechaPublicacion">
                </div>
                
                <div class="col-md-3">
                    <label for="filtroEstado" class="form-label small">Estado</label>
                    <select class="form-select form-select-sm" id="filtroEstado">
                        <option value="">Seleccionar</option>
                        <option value="B">BORRADOR</option>
                        <option value="P">PUBLICADO</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button class="btn btn-info text-white flex-grow-1" onclick="buscarNovedades()">Buscar</button>
                    <button class="btn btn-secondary" onclick="limpiarFiltros()" title="Limpiar filtros">
                        <i class="bi bi-arrow-clockwise">x</i>
                    </button>
        </div>
    </div>

    <!-- Tabla de novedades -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Título</th>
                            <th>Fecha publicación</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaNovedades">
                        @forelse($novedades as $novedad)
                            <tr>
                                <td>{{ $novedad->titulo }}</td>
                                <td>{{ \Carbon\Carbon::parse($novedad->fecha_publicacion)->format('d/m/Y') }}</td>
                                <td>{{ $novedad->categoria }}</td>
                                <td>
                                    @if($novedad->estado == 'B')
                                        <span class="badge bg-warning text-dark">● BORRADOR</span>
                                    @elseif($novedad->estado == 'P')
                                        <span class="badge bg-info text-white">● PUBLICADO</span>
                                    @endif
                                </td>
                                <td>
                               <button class="btn btn-secondary btn-sm" 
                                        onclick="editarNovedad(
                                            {{ $novedad->id_novedades }}, 
                                            '{{ addslashes($novedad->titulo) }}', 
                                            '{{ addslashes($novedad->resumen) }}', 
                                            '{{ addslashes($novedad->descripcion) }}', 
                                            '{{ $novedad->categoria }}', 
                                            '{{ $novedad->fecha_publicacion->format('Y-m-d') }}', 
                                            '{{ $novedad->estado }}'
                                        )">
                                        EDITAR
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No hay novedades registradas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <small class="text-muted"># Registros: {{ $novedades->count() }}</small>
            </div>
        </div>
    </div>

</div>

<!-- Modal Registrar Novedad -->
<div class="modal fade" id="modalRegistrarNovedad" tabindex="-1" aria-labelledby="modalRegistrarNovedadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-body p-4">
                <h4 class="text-center mb-4" id="modalRegistrarNovedadLabel">Registrar novedades</h4>
                
            <form action="{{ route('empleado.novedades.store') }}" method="POST" enctype="multipart/form-data" id="formRegistrarNovedad" onsubmit="return validarFormularioRegistrar()">
                @csrf
            
                    <div class="row g-3">
                        <div class="col-md-6">
                          <label for="titulo" class="form-label text-primary">Título <span class="text-danger">*</span></label>   
                            <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Escribe aquí" maxlength="200" required>
                            <div class="invalid-feedback">Por favor ingrese el título</div>
                        </div>
                        <div class="col-md-6">
                            <label for="resumen" class="form-label text-primary">Resumen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="resumen" name="resumen" placeholder="Escribe aquí" maxlength="500" required>
                            <div class="invalid-feedback">Por favor ingrese el resumen</div>
                        </div>
                        <div class="col-md-6">
                           <label for="fecha_publicacion" class="form-label text-primary">Fecha publicación <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha_publicacion" name="fecha_publicacion" required>
                            <div class="invalid-feedback">Por favor seleccione la fecha de publicación</div>
                        </div>
                        <div class="col-md-6">
                             <label for="categoria" class="form-label text-primary">Categoría <span class="text-danger">*</span></label>
                            <select class="form-select" id="categoria" name="categoria" required>
                                <option value="">Seleccionar</option>
                                <option value="Consejos">Consejos</option>
                                <option value="campaña">Campaña</option>
                                <option value="informativas">Informativas</option>
                                <option value="Ocio">Ocio</option>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione una categoría</div>
                        </div>
                        <div class="col-12">
                             <label for="descripcion" class="form-label text-primary">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" placeholder="Escribe aquí" maxlength="500" required></textarea>
                            <div class="invalid-feedback">Por favor ingrese la descripción</div>
                        </div>
                        <div class="col-md-6">
                           <label for="estado" class="form-label text-primary">Estado <span class="text-danger">*</span></label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="">Seleccionar</option>
                                <option value="B">BORRADOR</option>
                                <option value="P">PUBLICADO</option>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione el estado</div>
                        </div>
                        <div class="col-md-6">
                           <label for="imagen" class="form-label text-primary">Imagen</label>
                            <div class="input-group">
                                <button class="btn btn-outline-primary" type="button" onclick="document.getElementById('imagen').click()">
                                    <i class="bi bi-plus-circle me-2"></i>Adjuntar imagen
                                </button>
                                <input type="file" class="form-control d-none" id="imagen" name="imagen" accept="image/*" onchange="mostrarNombreArchivo(this)">
                                <span class="form-control" id="nombreArchivo">Ningún archivo seleccionado</span>
                            </div>
                            <small class="text-muted">Formatos: JPG, PNG, GIF (máx. 2MB)</small>
                        </div>
                    </div>
                  
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-secondary px-4 me-2" data-bs-dismiss="modal">CANCELAR</button>
                        <button type="submit" class="btn btn-primary px-5">GUARDAR</button>
                </div>
            </form>
             </div>
        </div>
    </div>
</div>

<!-- Modal Editar Novedad -->
<div class="modal fade" id="modalEditarNovedad" tabindex="-1" aria-labelledby="modalEditarNovedadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-body p-4">
                <h4 class="text-center mb-4" id="modalEditarNovedadLabel">Editar novedad</h4>
            <form action="" method="POST" enctype="multipart/form-data" id="formEditarNovedad" onsubmit="return validarFormularioEditar()">
                @csrf
                @method('PUT')
  
                    <div class="row g-3">
                        <div class="col-md-6">
                           <label for="editTitulo" class="form-label text-primary">Título <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editTitulo" name="titulo" placeholder="Escribe aquí" maxlength="200" required>
                            <div class="invalid-feedback">Por favor ingrese el título</div>
                        </div>
                        <div class="col-md-6">
                            <label for="editResumen" class="form-label text-primary">Resumen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editResumen" name="resumen" placeholder="Escribe aquí" maxlength="500" required>
                            <div class="invalid-feedback">Por favor ingrese el resumen</div>
                        </div>
                        <div class="col-md-6">
                            <label for="editFechaPublicacion" class="form-label text-primary">Fecha publicación <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="editFechaPublicacion" name="fecha_publicacion" required>
                            <div class="invalid-feedback">Por favor seleccione la fecha de publicación</div>
                        </div>
                        <div class="col-md-6">
                           <label for="editCategoria" class="form-label text-primary">Categoría <span class="text-danger">*</span></label>
                            <select class="form-select" id="editCategoria" name="categoria" required>
                                <option value="">Seleccionar</option>
                                <option value="Consejos">Consejos</option>
                                <option value="campaña">Campaña</option>
                                <option value="informativas">Informativas</option>
                                <option value="Ocio">Ocio</option>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione una categoría</div>
                        </div>
                        <div class="col-12">
                             <label for="editDescripcion" class="form-label text-primary">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="editDescripcion" name="descripcion" rows="4" placeholder="Escribe aquí" maxlength="500" required></textarea>
                            <div class="invalid-feedback">Por favor ingrese la descripción</div>
                        </div>
                        <div class="col-md-6">
                             <label for="editEstado" class="form-label text-primary">Estado <span class="text-danger">*</span></label>
                            <select class="form-select" id="editEstado" name="estado" required>
                                <option value="">Seleccionar</option>
                                <option value="B">BORRADOR</option>
                                <option value="P">PUBLICADO</option>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione el estado</div>
                        </div>
                        <div class="col-md-6">
                           <label for="editImagen" class="form-label text-primary">Imagen</label>
                            <div class="input-group">
                                <button class="btn btn-outline-primary" type="button" onclick="document.getElementById('editImagen').click()">
                                    <i class="bi bi-plus-circle me-2"></i>Adjuntar imagen
                                </button>
                                <input type="file" class="form-control d-none" id="editImagen" name="imagen" accept="image/*" onchange="mostrarNombreArchivoEditar(this)">
                                <span class="form-control" id="nombreArchivoEditar">Ningún archivo seleccionado</span>
                            </div>
                            <small class="text-muted">Formatos: JPG, PNG, GIF (máx. 2MB)</small>
                        </div>
                    </div>
                <div class="text-center mt-4">
                        <button type="button" class="btn btn-secondary px-4 me-2" data-bs-dismiss="modal">CANCELAR</button>
                        <button type="submit" class="btn btn-primary px-5">GUARDAR</button>
                </div>
            </form>
             </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Función para editar novedad
function editarNovedad(id, titulo, resumen, descripcion, categoria, fechaPublicacion, estado) {
    // Actualizar la acción del formulario
    document.getElementById('formEditarNovedad').action = `/empleado/novedades/${id}`;
    
    // Llenar los campos del modal
    document.getElementById('editTitulo').value = titulo;
    document.getElementById('editResumen').value = resumen;
    document.getElementById('editDescripcion').value = descripcion;
    document.getElementById('editCategoria').value = categoria;
    document.getElementById('editFechaPublicacion').value = fechaPublicacion;
    document.getElementById('editEstado').value = estado;
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('modalEditarNovedad'));
    modal.show();
}

// Función para buscar/filtrar novedades
function buscarNovedades() {
    const filtroTitulo = document.getElementById('filtroTitulo').value.toLowerCase().trim();
    const filtroFechaPublicacion = document.getElementById('filtroFechaPublicacion').value;
    const filtroEstado = document.getElementById('filtroEstado').value;
    
    const filas = document.querySelectorAll('#tablaNovedades tr');
    let filasVisibles = 0;
    
    filas.forEach(function(fila) {
        const titulo = fila.cells[0].textContent.toLowerCase().trim();
        const fechaTexto = fila.cells[1].textContent;
        const partesFecha = fechaTexto.split('/');
        const fechaFormateada = `${partesFecha[2]}-${partesFecha[1]}-${partesFecha[0]}`;
        const estadoTexto = fila.cells[3].textContent.trim();
        
        let coincide = true;
        
        // Filtro por título
        if (filtroTitulo !== '' && !titulo.includes(filtroTitulo)) {
            coincide = false;
        }
        
        // Filtro por fecha publicacion
        if (filtroFechaPublicacion !== '' && fechaFormateada < filtroFechaPublicacion) {
            coincide = false;
        }
        
        // Filtro por estado
        if (filtroEstado !== '') {
            const estadoFiltro = filtroEstado === 'B' ? 'BORRADOR' : 'PUBLICADO';
            if (!estadoTexto.includes(estadoFiltro)) {
                coincide = false;
            }
        }
        
        if (coincide) {
            fila.style.display = '';
            filasVisibles++;
        } else {
            fila.style.display = 'none';
        }
    });
    
    if (filasVisibles === 0) {
        alert('No se encontraron novedades con los criterios de búsqueda');
    }
}

// Función para limpiar filtros
function limpiarFiltros() {
    document.getElementById('filtroTitulo').value = '';
    document.getElementById('filtroFechaPublicacion').value = '';
    document.getElementById('filtroEstado').value = '';
    
    const filas = document.querySelectorAll('#tablaNovedades tr');
    filas.forEach(function(fila) {
        fila.style.display = '';
    });
}

// Función para mostrar nombre de archivo seleccionado (registrar)
function mostrarNombreArchivo(input) {
    const nombreArchivo = input.files[0] ? input.files[0].name : 'Ningún archivo seleccionado';
    document.getElementById('nombreArchivo').textContent = nombreArchivo;
}

// Función para mostrar nombre de archivo seleccionado (editar)
function mostrarNombreArchivoEditar(input) {
    const nombreArchivo = input.files[0] ? input.files[0].name : 'Ningún archivo seleccionado';
    document.getElementById('nombreArchivoEditar').textContent = nombreArchivo;
}

// Validación del formulario de registrar
function validarFormularioRegistrar() {
    const form = document.getElementById('formRegistrarNovedad');
    const titulo = document.getElementById('titulo');
    const resumen = document.getElementById('resumen');
    const descripcion = document.getElementById('descripcion');
    const categoria = document.getElementById('categoria');
    const fechaPublicacion = document.getElementById('fecha_publicacion');
    const estado = document.getElementById('estado');
    
    let valido = true;
    
    // Validar título
    if (titulo.value.trim() === '') {
        titulo.classList.add('is-invalid');
        valido = false;
    } else {
        titulo.classList.remove('is-invalid');
    }
    
    // Validar resumen
    if (resumen.value.trim() === '') {
        resumen.classList.add('is-invalid');
        valido = false;
    } else {
        resumen.classList.remove('is-invalid');
    }
    
    // Validar descripción
    if (descripcion.value.trim() === '') {
        descripcion.classList.add('is-invalid');
        valido = false;
    } else {
        descripcion.classList.remove('is-invalid');
    }
    
    // Validar categoría
    if (categoria.value === '') {
        categoria.classList.add('is-invalid');
        valido = false;
    } else {
        categoria.classList.remove('is-invalid');
    }
    
    // Validar fecha de publicación
    if (fechaPublicacion.value === '') {
        fechaPublicacion.classList.add('is-invalid');
        valido = false;
    } else {
        fechaPublicacion.classList.remove('is-invalid');
    }
    
    // Validar estado
    if (estado.value === '') {
        estado.classList.add('is-invalid');
        valido = false;
    } else {
        estado.classList.remove('is-invalid');
    }
    
    return valido;
}

// Validación del formulario de editar
function validarFormularioEditar() {
    const form = document.getElementById('formEditarNovedad');
    const titulo = document.getElementById('editTitulo');
    const resumen = document.getElementById('editResumen');
    const descripcion = document.getElementById('editDescripcion');
    const categoria = document.getElementById('editCategoria');
    const fechaPublicacion = document.getElementById('editFechaPublicacion');
    const estado = document.getElementById('editEstado');
    
    let valido = true;
    
    // Validar título
    if (titulo.value.trim() === '') {
        titulo.classList.add('is-invalid');
        valido = false;
    } else {
        titulo.classList.remove('is-invalid');
    }
    
    // Validar resumen
    if (resumen.value.trim() === '') {
        resumen.classList.add('is-invalid');
        valido = false;
    } else {
        resumen.classList.remove('is-invalid');
    }
    
    // Validar descripción
    if (descripcion.value.trim() === '') {
        descripcion.classList.add('is-invalid');
        valido = false;
    } else {
        descripcion.classList.remove('is-invalid');
    }
    
    // Validar categoría
    if (categoria.value === '') {
        categoria.classList.add('is-invalid');
        valido = false;
    } else {
        categoria.classList.remove('is-invalid');
    }
    
    // Validar fecha de publicación
    if (fechaPublicacion.value === '') {
        fechaPublicacion.classList.add('is-invalid');
        valido = false;
    } else {
        fechaPublicacion.classList.remove('is-invalid');
    }
    
    // Validar estado
    if (estado.value === '') {
        estado.classList.add('is-invalid');
        valido = false;
    } else {
        estado.classList.remove('is-invalid');
    }
    
    return valido;
}

// Auto-cerrar alertas después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const alertas = document.querySelectorAll('.alert');
    alertas.forEach(function(alerta) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alerta);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endpush
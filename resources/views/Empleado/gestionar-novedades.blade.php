@extends('layouts.app')

@section('title', 'Gestionar Novedades - Estética Canina')

@section('header')
    @include('partials.admin_header')
@endsection

@section('content')

<!-- Agregar los CSS del admin -->
<link rel="stylesheet" href="{{ asset('css/admin_toolbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">

<!-- Toolbar lateral para empleado -->
<aside class="admin-toolbar bg-primary text-white shadow-sm d-flex flex-column pt-4">
    <ul class="nav flex-column px-2">
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.bandeja.reservas') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-calendar-check fs-5"></i>
                <span class="fw-semibold">Bandeja de Reservas</span>
            </a>
        </li>
        
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.gestionar.turnos') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-clock fs-5"></i>
                <span class="fw-semibold">Gestionar Turnos</span>
            </a>
        </li>
        
        <!-- Marcar como activo -->
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.gestionar.novedades') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect active">
                <i class="fas fa-bell fs-5"></i>
                <span class="fw-semibold">Gestionar Novedades</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('empleado.notificaciones') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-bell fs-5"></i>
                <span class="fw-semibold">Gestionar Notificaciones</span>
            </a>
        </li>


        <li class="nav-item mb-2">
            <a href="{{ route('home') }}" class="nav-link text-white d-flex align-items-center gap-3 py-3 px-3 rounded hover-effect">
                <i class="fas fa-home fs-5"></i>
                <span class="fw-semibold">Web Cliente</span>
            </a>
        </li>
    </ul>
</aside>

<!-- Mejorar contenedor principal con mejor estructura -->
<main class="admin-content">
    <!-- Mensajes de éxito/error -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Card principal con estructura mejorada -->
    <div class="card shadow-sm border-0">
        <!-- Separador visual -->
         <hr class="my-2"> 
            <h2 class="fw-bold text-dark text-center">
                <i class="fas fa-bell me-2"></i> Gestionar Novedades
            </h2>
            <div class="card-body">
                    <div class="mb-4">
                        <!-- Agregar onclick como respaldo para abrir el modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistrarNovedad" onclick="abrirModalNuevo()">
                            <i class="fas fa-plus me-2">  </i> NUEVO  .
                        </button>
                    </div>
                    <!-- Sección de filtros con mejor organización -->
                        <div class="filters-section mb-4">
                            <h5 class="mb-3 text-secondary">
                                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                            </h5>
                            <!-- Sección de filtros con fondo -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <label for="filtroTitulo" class="form-label">Título</label>
                                    <input type="text" class="form-control" id="filtroTitulo" placeholder="Escribe aquí">
                                </div>
                                <div class="col-md-3">
                                    <label for="filtroFechapublicacion" class="form-label">Fecha publicación</label>
                                    <input type="date" class="form-control" id="filtroFechaPublicacion">
                                </div>
                                <div class="col-md-3">
                                    <label for="filtroEstado" class="form-label">Estado</label>
                                    <select class="form-select" id="filtroEstado">
                                        <option value="">Seleccionar</option>
                                        <option value="B">BORRADOR</option>
                                        <option value="P">PUBLICADO</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end gap-2">
                                        <button class="btn btn-primary flex-grow-1" onclick="buscarNovedades()">
                                            <i class="fas fa-search me-1"></i> Buscar
                                        </button>
                                        <button class="btn btn-secondary" onclick="limpiarFiltros()" title="Limpiar filtros">
                                            <i class="bi bi-arrow-clockwise"></i>x
                                        </button>
                                </div>
                            </div>
                        </div>
            <!-- Tabla con estructura mejorada y scroll -->
            <div class="table-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-secondary mb-0">
                        <i class="fas fa-list me-2"></i>Listado de novedades
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover text-align: center">
                        <thead>
                            <tr>
                                <th>TÍTULO</th>
                                <th>FECHA PUBLICACIÓN</th>
                                <th>CATEGORÍA</th>
                                <th>ESTADO</th>
                                <th>ACCIONES</th>
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
                                            <span class="badge bg-warning">BORRADOR</span>
                                        @elseif($novedad->estado == 'P')
                                            <span class="badge bg-info">PUBLICADO</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-primary btn-sm" 
                                            onclick="editarNovedad(
                                                {{ $novedad->id_novedades }}, 
                                                '{{ addslashes($novedad->titulo) }}', 
                                                '{{ addslashes($novedad->resumen) }}', 
                                                '{{ addslashes($novedad->descripcion) }}', 
                                                '{{ $novedad->categoria }}', 
                                                '{{ $novedad->fecha_publicacion->format('Y-m-d') }}', 
                                                '{{ $novedad->estado }}'
                                            )">
                                            <i class="fas fa-edit me-1"></i> EDITAR
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        No hay novedades registradas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Contador de registros con mejor espaciado -->
                        <div class="mt-3">
                             <small class="badge bg-primary">
                             Total: <span id="totalRegistros"> {{ $novedades->count() }}</span> registros
                             </small>
                        </div>
                    </div>
                 </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Registrar Novedad -->
<div class="modal fade" id="modalRegistrarNovedad" tabindex="-1" aria-labelledby="modalRegistrarNovedadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
            <div class="modal-body">
                <h4 id="modalRegistrarNovedadLabel">
                    <i class="fas fa-plus-circle me-2"></i> Registrar Novedad
                </h4>
                <form action="{{ route('empleado.novedades.store') }}" method="POST" enctype="multipart/form-data" id="formRegistrarNovedad" onsubmit="return validarFormularioRegistrar()">
                    @csrf
            
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="titulo" class="form-label">Título <span class="text-danger">*</span></label>   
                            <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Escribe aquí" maxlength="200" required>
                            <div class="invalid-feedback">Por favor ingrese el título</div>
                        </div>
                        <div class="col-md-6">
                            <label for="resumen" class="form-label">Resumen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="resumen" name="resumen" placeholder="Escribe aquí" maxlength="500" required>
                            <div class="invalid-feedback">Por favor ingrese el resumen</div>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_publicacion" class="form-label">Fecha publicación <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha_publicacion" name="fecha_publicacion" required>
                            <div class="invalid-feedback">Por favor seleccione la fecha de publicación</div>
                        </div>
                        <div class="col-md-6">
                            <label for="categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
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
                            <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" placeholder="Escribe aquí" maxlength="500" required></textarea>
                            <div class="invalid-feedback">Por favor ingrese la descripción</div>
                        </div>
                        <div class="col-md-6">
                            <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="">Seleccionar</option>
                                <option value="B">BORRADOR</option>
                                <option value="P">PUBLICADO</option>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione el estado</div>
                        </div>
                        <div class="col-md-6">
                            <label for="imagen" class="form-label">Imagen</label>
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
                       <button type="button" class="btn btn-secondary px-4 me-2" data-bs-dismiss="modal" onclick="cerrarModal('modalRegistrarNovedad')">
                            <i class="fas fa-times me-2"></i> CANCELAR
                        </button>
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-save me-2"></i> GUARDAR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Novedad -->
<div class="modal fade" id="modalEditarNovedad" tabindex="-1" aria-labelledby="modalEditarNovedadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
       <div class="modal-content">
            <div class="modal-body">
                <h4 id="modalEditarNovedadLabel">
                    <i class="fas fa-edit me-2"></i> Editar Novedad
                </h4>
                
                <form  action="{{ route('empleado.novedades.store') }}" method="POST" enctype="multipart/form-data" id="formEditarNovedad" onsubmit="return validarFormularioEditar()">
                    @csrf
                    @method('PUT')
  
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editTitulo" class="form-label">Título <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editTitulo" name="titulo" placeholder="Escribe aquí" maxlength="200" required>
                            <div class="invalid-feedback">Por favor ingrese el título</div>
                        </div>
                        <div class="col-md-6">
                            <label for="editResumen" class="form-label">Resumen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editResumen" name="resumen" placeholder="Escribe aquí" maxlength="500" required>
                            <div class="invalid-feedback">Por favor ingrese el resumen</div>
                        </div>
                        <div class="col-md-6">
                            <label for="editFechaPublicacion" class="form-label">Fecha publicación <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="editFechaPublicacion" name="fecha_publicacion" required>
                            <div class="invalid-feedback">Por favor seleccione la fecha de publicación</div>
                        </div>
                        <div class="col-md-6">
                            <label for="editCategoria" class="form-label">Categoría <span class="text-danger">*</span></label>
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
                            <label for="editDescripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="editDescripcion" name="descripcion" rows="4" placeholder="Escribe aquí" maxlength="500" required></textarea>
                            <div class="invalid-feedback">Por favor ingrese la descripción</div>
                        </div>
                        <div class="col-md-6">
                            <label for="editEstado" class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-select" id="editEstado" name="estado" required>
                                <option value="">Seleccionar</option>
                                <option value="B">BORRADOR</option>
                                <option value="P">PUBLICADO</option>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione el estado</div>
                        </div>
                        <div class="col-md-6">
                            <label for="editImagen" class="form-label">Imagen</label>
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
                       <button type="button" class="btn btn-secondary px-4 me-2" data-bs-dismiss="modal" onclick="cerrarModal('modalEditarNovedad')">
                            <i class="fas fa-times me-2"></i> CANCELAR
                        </button>
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-save me-2"></i> GUARDAR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')
<script>
  function abrirModalNuevo() {
    console.log('[v0] Abriendo modal de nueva novedad...');
    // Intentar con jQuery primero (método más común en Laravel)
    if (typeof $ !== 'undefined' && $.fn.modal) {
        $('#modalRegistrarNovedad').modal('show');
    } 
    // Si Bootstrap 5 está disponible como objeto global
    else if (typeof bootstrap !== 'undefined') {
    const modal = new bootstrap.Modal(document.getElementById('modalRegistrarNovedad'));
    modal.show();
}

// Fallback: manipulación directa del DOM
    else {
        const modalElement = document.getElementById('modalRegistrarNovedad');
        modalElement.classList.add('show');
        modalElement.style.display = 'block';
        modalElement.setAttribute('aria-modal', 'true');
        modalElement.removeAttribute('aria-hidden');
        
        // Agregar backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(backdrop);
        document.body.classList.add('modal-open');
    }
}

function editarNovedad(id, titulo, resumen, descripcion, categoria, fechaPublicacion, estado) {
    console.log('[v0] Editando novedad con ID:', id);
    
    // Actualizar la acción del formulario
    document.getElementById('formEditarNovedad').action = `/empleado/novedades/${id}`;
    
    // Llenar los campos del modal
    document.getElementById('editTitulo').value = titulo;
    document.getElementById('editResumen').value = resumen;
    document.getElementById('editDescripcion').value = descripcion;
    document.getElementById('editCategoria').value = categoria;
    document.getElementById('editFechaPublicacion').value = fechaPublicacion;
    document.getElementById('editEstado').value = estado;
    
    document.getElementById('nombreArchivoEditar').textContent = 'Ningún archivo seleccionado';

  // Mostrar el modal usando jQuery o Bootstrap
    if (typeof $ !== 'undefined' && $.fn.modal) {
        $('#modalEditarNovedad').modal('show');
    } 
    else if (typeof bootstrap !== 'undefined') {
    const modal = new bootstrap.Modal(document.getElementById('modalEditarNovedad'));
    modal.show();
     }
    else {
        const modalElement = document.getElementById('modalEditarNovedad');
        modalElement.classList.add('show');
        modalElement.style.display = 'block';
        modalElement.setAttribute('aria-modal', 'true');
        modalElement.removeAttribute('aria-hidden');
        
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(backdrop);
        document.body.classList.add('modal-open');
    }
}

// Función para buscar/filtrar novedades
function buscarNovedades() {
    console.log('[v0] Buscando novedades...');
    
    const filtroTitulo = document.getElementById('filtroTitulo').value.toLowerCase().trim();
    const filtroFechaPublicacion = document.getElementById('filtroFechaPublicacion').value;
    const filtroEstado = document.getElementById('filtroEstado').value;
    
    const filas = document.querySelectorAll('#tablaNovedades tr');
    let filasVisibles = 0;
    
    filas.forEach(function(fila) {
        // Verificar que la fila tenga celdas (evitar el mensaje "No hay novedades")
        if (fila.cells.length < 5) {
            return;
        }
        
        const titulo = fila.cells[0].textContent.toLowerCase().trim();
        const fechaTexto = fila.cells[1].textContent;
        
        // Convertir fecha de dd/mm/yyyy a yyyy-mm-dd
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
    
    console.log('[v0] Filas visibles:', filasVisibles);
    
    if (filasVisibles === 0) {
        alert('No se encontraron novedades con los criterios de búsqueda');
    }
}

// Función para limpiar filtros
function limpiarFiltros() {
    console.log('[v0] Limpiando filtros...');
    
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

// Función para cerrar modales
function cerrarModal(modalId) {
    console.log('[v0] Cerrando modal:', modalId);
    
    // Intentar con jQuery primero
    if (typeof $ !== 'undefined' && $.fn.modal) {
        $('#' + modalId).modal('hide');
    } 
    // Si Bootstrap 5 está disponible
    else if (typeof bootstrap !== 'undefined') {
        const modalElement = document.getElementById(modalId);
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    }
    // Fallback: manipulación directa del DOM
    else {
        const modalElement = document.getElementById(modalId);
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        modalElement.setAttribute('aria-hidden', 'true');
        modalElement.removeAttribute('aria-modal');
        
        // Remover backdrop
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
        document.body.classList.remove('modal-open');
    }
    
    // Limpiar formulario
    if (modalId === 'modalRegistrarNovedad') {
        document.getElementById('formRegistrarNovedad').reset();
    } else if (modalId === 'modalEditarNovedad') {
        document.getElementById('formEditarNovedad').reset();
    }
}

// Auto-cerrar alertas después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    console.log('[v0] Página de novedades cargada correctamente');
    
 if (typeof $ !== 'undefined') {
        console.log('[v0] jQuery está disponible');
    }
    
    if (typeof bootstrap !== 'undefined') {
        console.log('[v0] Bootstrap (objeto global) está disponible');
    }
    
    if (typeof $ === 'undefined' && typeof bootstrap === 'undefined') {
        console.warn('[v0] Ni jQuery ni Bootstrap están disponibles. Los modales usarán manipulación DOM directa.');
    }
    
    const alertas = document.querySelectorAll('.alert');
    alertas.forEach(function(alerta) {
        setTimeout(function() {
            if (typeof bootstrap !== 'undefined') {
                const bsAlert = new bootstrap.Alert(alerta);
                bsAlert.close();
            } else if (typeof $ !== 'undefined') {
                $(alerta).alert('close');
            } else {
                alerta.style.display = 'none';
            }
        }, 5000);
    });
});
</script>
@endpush
@extends('layouts.app')

@section('header')
    @include('partials.header')
@endsection

@section('title', 'Mis Reservas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mis-reservas.css') }}">
@endpush

@section('content')
<div class="mis-reservas-container">
    <header class="reservas-header">
        <h1>Mis Reservas</h1>
    </header>

    <div class="tabs-container">
        <button class="tab-button active" data-tab="proximas">Reservas Programadas</button>
        <button class="tab-button" data-tab="historial">Historial de Servicios</button>
    </div>

    <!-- Reservas Programadas -->
    <div class="tab-content active" id="proximas">
        <div class="reservas-list">
            @forelse($proximasReservas as $reserva)
            <div class="reserva-card">
                <div class="reserva-info">
                    <img src="{{ $reserva->mascota->foto ?? '/images/default-pet.png' }}" 
                         alt="{{ $reserva->mascota->nombre }}" 
                         class="pet-avatar">
                    <div class="reserva-details">
                        <h3 class="pet-name">{{ strtoupper($reserva->mascota->nombre) }}</h3>
                        <p class="reserva-fecha">{{ $reserva->fecha_formateada }}</p>
                        <p class="reserva-servicios">{{ $reserva->hora }} - {{ $reserva->servicios_texto }}</p>
                    </div>
                </div>
                <button class="btn-action btn-editar" data-reserva-id="{{ $reserva->id_reserva }}" 
                        data-mascota="{{ $reserva->mascota->nombre }}" 
                        data-fecha="{{ $reserva->fecha }}" 
                        data-hora="{{ $reserva->hora }}"
                        data-id-empleado="{{ $reserva->id_empleado }}"
                        data-fecha-creacion="{{ $reserva->fecha_creacion }}"
                        data-enfermedad="{{ $reserva->enfermedad }}"
                        data-vacuna="{{ $reserva->vacuna }}"
                        data-alergia="{{ $reserva->alergia }}"
                        data-descripcion-alergia="{{ $reserva->descripcion_alergia }}">
                    Editar
                </button>
            </div>
            @empty
            <div class="empty-state">
                <p>No tienes reservas próximas</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Historial de Servicios -->
    <div class="tab-content" id="historial">
        {{-- Botón comentado temporalmente - ruta no definida
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('tratamientos.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-history"></i> Ver Historial de Tratamientos
            </a>
        </div>
        --}}

        <div class="reservas-list">
            @forelse($historialReservas as $reserva)
            <div class="reserva-card">
                <div class="reserva-info">
                    <img src="{{ $reserva->mascota->foto ?? '/images/default-pet.png' }}" 
                         alt="{{ $reserva->mascota->nombre }}" 
                         class="pet-avatar">
                    <div class="reserva-details">
                        <h3 class="pet-name">{{ strtoupper($reserva->mascota->nombre) }}</h3>
                        <p class="reserva-fecha">{{ $reserva->fecha_formateada }}</p>
                        <p class="reserva-servicios">{{ $reserva->hora }} - {{ $reserva->servicios_texto }}</p>
                        
                        <!-- Estado de calificación -->
                        @php
                            $feedback = DB::table('feedbacks')->where('id_reserva', $reserva->id_reserva)->first();
                        @endphp
                        
                        @if($feedback)
                            <p class="calificacion-existente">
                                <i class="fas fa-star text-warning"></i>
                                <span>Ya calificaste este servicio ({{ $feedback->calificacion }}/5 estrellas)</span>
                            </p>
                        @else
                            <p class="sin-calificacion">
                                <i class="far fa-star text-muted"></i>
                                <span class="text-muted">Servicio sin calificar</span>
                            </p>
                        @endif
                    </div>
                </div>
                
                <div class="reserva-actions">
                    @if($feedback)
                        <!-- Si ya fue calificado, solo mostrar el botón de ver detalles -->
                        <a href="#" class="btn-action btn-detalles" 
                           data-reserva-id="{{ $reserva->id_reserva }}"
                           data-mascota="{{ $reserva->mascota->nombre }}"
                           data-fecha="{{ $reserva->fecha_formateada }}"
                           data-hora="{{ $reserva->hora }}"
                           onclick="event.preventDefault(); abrirModalDetalles(this);">
                            Ver Detalles
                        </a>
                        <span class="calificado-badge">✓ Calificado</span>
                    @else
                        <!-- Si no fue calificado, mostrar ambos botones -->
                        <a href="#" class="btn-action btn-detalles" 
                           data-reserva-id="{{ $reserva->id_reserva }}"
                           data-mascota="{{ $reserva->mascota->nombre }}"
                           data-fecha="{{ $reserva->fecha_formateada }}"
                           data-hora="{{ $reserva->hora }}"
                           onclick="event.preventDefault(); abrirModalDetalles(this);">
                            Ver Detalles
                        </a>
                        <button class="btn-action btn-calificar" 
                                data-reserva-id="{{ $reserva->id_reserva }}"
                                data-mascota="{{ $reserva->mascota->nombre }}"
                                data-fecha="{{ $reserva->fecha_formateada }}"
                                data-servicios="{{ $reserva->servicios_texto }}"
                                onclick="abrirModalCalificacion(this)">
                            <i class="fas fa-star"></i> Calificar
                        </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="empty-state">
                <p>No tienes historial de servicios</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal de Edición -->
<div id="editarReservaModal" class="modal-overlay">
    <div class="modal-content">
        <h2 class="modal-title">Editar Detalles de la Reserva - <span id="modalMascotaNombre"></span></h2>
        <p class="modal-subtitle">Fecha: <span id="modalFecha"></span> | Hora: <span id="modalHora"></span></p>
        
        <form id="formEditarReserva" method="POST">
            @csrf
            @method('PUT')
            
            <div class="modal-body">
                <div class="modal-section">
                    <h3 class="section-title">Información de salud</h3>
                    
                    <div class="health-check">
                        <i class="fas fa-thermometer-half health-icon"></i>
                        <label>¿Su mascota presenta alguna enfermedad?</label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="enfermedad" value="1"> Sí
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="enfermedad" value="0" checked> No
                            </label>
                        </div>
                    </div>

                    <div class="health-check">
                        <i class="fas fa-syringe health-icon"></i>
                        <label>¿Su mascota se encuentra vacunada?</label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="vacuna" value="1"> Sí
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="vacuna" value="0" checked> No
                            </label>
                        </div>
                    </div>

                    <div class="health-check-alergia">
                        <div class="health-check">
                            <i class="fas fa-exclamation-triangle health-icon"></i>
                            <label>¿Su mascota presenta alguna alergia?</label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="alergia" value="1"> Sí
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="alergia" value="0" checked> No
                                </label>
                            </div>
                        </div>
                        <input type="text" name="descripcion_alergia" id="descripcionAlergia" 
                               class="form-control alergia-input" 
                               placeholder="Ejemplo: alergia al polen, problemas cardiacos..." 
                               style="display: none;">
                    </div>
                </div>

                <!-- Reprogramación de Reserva -->
                <div class="modal-section" id="reprogramacion-section">
                    <h3 class="section-title">Reprogramar Fecha y Hora</h3>
                    <div id="reprogramacion-bloqueada" style="display: none;">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No es posible reprogramar esta reserva porque han pasado más de 48 horas desde que fue creada.
                        </div>
                    </div>
                    <div id="reprogramacion-disponible">
                        <div class="mb-3">
                            <label for="editFecha" class="form-label">Nueva Fecha</label>
                            <input type="date" name="nueva_fecha" id="editFecha" class="form-control" min="{{ date('Y-m-d') }}">
                        </div>

                        <div class="mb-3" id="trabajador-section" style="display: none;">
                            <label class="form-label">Trabajador Disponible</label>
                            <div class="trabajadores-tabs-modal" id="editTrabajadoresTabs">
                                @foreach($empleados ?? [] as $empleado)
                                    <button type="button" 
                                            class="btn-trabajador-modal" 
                                            data-id="{{ $empleado->id_empleado }}"
                                            data-nombre="{{ $empleado->persona->nombres }}">
                                        {{ $empleado->persona->nombres }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3" id="horarios-section" style="display: none;">
                            <label class="form-label">Horario Disponible para <span id="nombre-trabajador-modal"></span></label>
                            <div class="horarios-grid-modal" id="editHorariosGrid">
                                <!-- Horarios se cargarán dinámicamente -->
                            </div>
                        </div>

                        <input type="hidden" name="nueva_hora" id="editNuevaHora">
                        <input type="hidden" name="nuevo_id_empleado" id="editNuevoIdEmpleado">
                    </div>
                </div>

                <div class="modal-section">
                    <h3 class="section-title">Comentarios Adicionales:</h3>
                    <textarea name="comentarios" id="comentariosAdicionales" class="form-control" rows="3" 
                              placeholder="Agrega un comentario"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn-actualizar">Actualizar cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Detalles de Reserva -->
<div id="detallesReservaModal" class="modal-overlay">
    <div class="modal-content modal-detalles">
        <div class="modal-header-detalles">
            <h2 class="modal-title-detalles">Resumen de Reserva</h2>
            <button type="button" class="btn-close-modal" onclick="cerrarModalDetalles()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body-detalles">
            <!-- Información General -->
            <div class="detalle-section">
                <div class="mascota-info-header">
                    <img id="detallesMascotaFoto" src="/images/default-pet.png" alt="Mascota" class="mascota-avatar-grande">
                    <div>
                        <h3 id="detallesMascotaNombre" class="mascota-nombre-grande"></h3>
                        <p class="detalle-fecha-hora">
                            <i class="far fa-calendar-alt"></i> <span id="detallesFecha"></span>
                        </p>
                        <p class="detalle-fecha-hora">
                            <i class="far fa-clock"></i> <span id="detallesHora"></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Servicios Realizados -->
            <div class="detalle-section">
                <h4 class="section-subtitle">
                    <i class="fas fa-cut"></i> Servicios Realizados
                </h4>
                <div id="detallesServicios" class="servicios-lista">
                </div>
                <div class="total-resumen">
                    <div class="total-row">
                        <span class="total-label">Subtotal:</span>
                        <span id="detallesSubtotal" class="total-valor">S/ 0.00</span>
                    </div>
                    <div class="total-row">
                        <span class="total-label">IGV (18%):</span>
                        <span id="detallesIGV" class="total-valor">S/ 0.00</span>
                    </div>
                    <div class="total-row total-final">
                        <span class="total-label-final">Total Pagado:</span>
                        <span id="detallesTotal" class="total-monto-final">S/ 0.00</span>
                    </div>
                </div>
            </div>

            <!-- Próximas Recomendaciones -->
            <div class="detalle-section recomendaciones-section">
                <h4 class="section-subtitle">
                    <i class="fas fa-lightbulb"></i> Próximas Visitas Recomendadas
                </h4>
                <p class="recomendacion-intro">Basado en los servicios realizados, te recomendamos agendar:</p>
                <div id="detallesRecomendaciones" class="recomendaciones-lista">
                </div>
            </div>
        </div>

        <div class="modal-footer-detalles">
            <a href="{{ route('reservas.seleccionMascota') }}" class="btn-nueva-reserva">
                <i class="fas fa-calendar-plus"></i> Agendar Nueva Cita
            </a>
        </div>
    </div>
</div>

<!-- Modal de Calificación -->
<div class="modal fade" id="modalCalificacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-star text-warning"></i> Calificar Servicio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <h6 id="calificacionMascotaNombre" class="fw-bold"></h6>
                    <p class="text-muted mb-1" id="calificacionFecha"></p>
                    <p class="text-muted" id="calificacionServicios"></p>
                </div>
                
                <form id="formCalificacion">
                    @csrf
                    <input type="hidden" id="calificacion_id_reserva" name="id_reserva">
                    
                    <div class="mb-3 text-center">
                        <label class="form-label fw-semibold">¿Cómo fue tu experiencia?</label>
                        <div class="rating-stars">
                            <i class="fas fa-star star" data-value="1"></i>
                            <i class="fas fa-star star" data-value="2"></i>
                            <i class="fas fa-star star" data-value="3"></i>
                            <i class="fas fa-star star" data-value="4"></i>
                            <i class="fas fa-star star" data-value="5"></i>
                        </div>
                        <input type="hidden" id="calificacion_rating" name="calificacion" value="0">
                        <div class="rating-text mt-2">
                            <small class="text-muted" id="ratingDescription">Selecciona una calificación</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Comentarios (Opcional)</label>
                        <textarea class="form-control" name="comentarios" rows="3" 
                                  placeholder="Cuéntanos tu experiencia con el servicio..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardarCalificacion">
                    <i class="fas fa-save"></i> Guardar Calificación
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/mis-reservas.js') }}"></script>
<script>
// Datos de reservas para el modal de detalles
const reservasData = {!! json_encode($historialReservas->map(function($reserva) {
    $delivery = DB::table('deliveries')->where('id_reserva', $reserva->id_reserva)->first();
    return [
        'id_reserva' => $reserva->id_reserva,
        'fecha' => $reserva->fecha, // Fecha original en formato YYYY-MM-DD
        'fecha_formateada' => $reserva->fecha_formateada,
        'hora' => $reserva->hora,
        'mascota' => [
            'nombre' => $reserva->mascota->nombre,
            'foto' => $reserva->mascota->foto
        ],
        'detalles' => $reserva->detalles->map(function($detalle) {
            return [
                'servicio' => [
                    'nombre_servicio' => $detalle->servicio->nombre_servicio,
                    'costo' => $detalle->servicio->costo
                ],
                'precio_unitario' => $detalle->precio_unitario,
                'igv' => $detalle->igv,
                'total' => $detalle->total
            ];
        }),
        'delivery' => $delivery ? [
            'direccion_recojo' => $delivery->direccion_recojo,
            'direccion_entrega' => $delivery->direccion_entrega,
            'costo_delivery' => $delivery->costo_delivery,
            'total_delivery' => $delivery->costo_delivery * 1.18
        ] : null
    ];
})) !!};
</script>
@endpush
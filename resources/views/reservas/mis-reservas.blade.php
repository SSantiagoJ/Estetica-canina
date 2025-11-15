@extends('layouts.app')

@section('header')
    @include('partials.header')
@endsection

@section('title', 'Mis Reservas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cliente/mis-reservas.css') }}">
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
                    </div>
                </div>
                <a href="#" class="btn-action btn-detalles" 
                   data-reserva-id="{{ $reserva->id_reserva }}"
                   data-mascota="{{ $reserva->mascota->nombre }}"
                   data-fecha="{{ $reserva->fecha_formateada }}"
                   data-hora="{{ $reserva->hora }}"
                   onclick="event.preventDefault(); abrirModalDetalles(this);">
                    Ver Detalles
                </a>
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

@endsection

@push('scripts')
<script src="{{ asset('js/mis-reservas.js') }}"></script>
<script>
// Datos de reservas para el modal de detalles
const reservasData = {!! json_encode($historialReservas->map(function($reserva) {
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
        })
    ];
})) !!};
</script>
@endpush
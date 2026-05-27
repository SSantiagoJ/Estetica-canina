@extends('layouts.app')

@section('header')
    @include('partials.header')
@endsection

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/reservas.css') }}">

<div class="container reserva-container">
    <div class="progressbar mb-5">
        <ul class="steps">
            <li>Selección de mascotas</li>
            <li class="active">Selección de servicio</li>
            <li>Pago</li>
            <li>Confirmación</li>
        </ul>
    </div>

    <div class="reserva-heading">
        <span class="reserva-eyebrow">Agenda tu cita</span>
        <h2 class="titulo-seccion">Elige los servicios y horario</h2>
    </div>

    <form action="{{ route('reservas.pago') }}" method="POST">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8">
                <section class="servicios-section mb-4">
                    <div class="section-heading">
                        <h4>Servicios disponibles</h4>
                        <p>Selecciona uno o más servicios principales para tu mascota.</p>
                    </div>

                    <div class="row g-3">
                        @foreach($servicios as $servicio)
                            <div class="col-md-6 col-xl-4">
                                <label class="servicio-card">
                                    <input type="checkbox"
                                           name="servicios[]"
                                           value="{{ $servicio->id_servicio }}"
                                           data-duracion="{{ $servicio->duracion }}"
                                           class="servicio-checkbox">
                                    <div class="card-body text-center">
                                        <span class="selected-indicator">Seleccionado</span>
                                        @if($servicio->imagen_referencial)
                                            @php
                                                if (str_starts_with($servicio->imagen_referencial, 'servicios/')) {
                                                    $imagenUrl = asset('storage/' . $servicio->imagen_referencial);
                                                } else {
                                                    $imagenUrl = asset('images/servicios/' . $servicio->imagen_referencial);
                                                }
                                            @endphp
                                            <img src="{{ $imagenUrl }}" alt="{{ $servicio->nombre_servicio }}" class="servicio-img">
                                        @else
                                            <img src="{{ asset('images/servicios/default.jpg') }}" alt="{{ $servicio->nombre_servicio }}" class="servicio-img">
                                        @endif
                                        <h5>{{ $servicio->nombre_servicio }}</h5>
                                        <div class="servicio-meta-row">
                                            <span>S/ {{ number_format($servicio->costo, 2) }}</span>
                                            @if($servicio->duracion)
                                                <span>{{ $servicio->duracion }} min</span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="servicios-section mb-4">
                    <div class="section-heading">
                        <h4>Servicios adicionales</h4>
                        <p>Complementa la atención con extras opcionales.</p>
                    </div>

                    <div class="row g-3">
                        @foreach($adicionales as $servicio)
                            <div class="col-md-6 col-xl-4">
                                <label class="servicio-card">
                                    <input type="checkbox"
                                           name="adicionales[]"
                                           value="{{ $servicio->id_servicio }}"
                                           data-duracion="{{ $servicio->duracion }}"
                                           class="servicio-checkbox">
                                    <div class="card-body text-center">
                                        <span class="selected-indicator">Seleccionado</span>
                                        @if($servicio->imagen_referencial)
                                            @php
                                                if (str_starts_with($servicio->imagen_referencial, 'servicios/')) {
                                                    $imagenUrl = asset('storage/' . $servicio->imagen_referencial);
                                                } else {
                                                    $imagenUrl = asset('images/servicios/' . $servicio->imagen_referencial);
                                                }
                                            @endphp
                                            <img src="{{ $imagenUrl }}" alt="{{ $servicio->nombre_servicio }}" class="servicio-img">
                                        @else
                                            <img src="{{ asset('images/servicios/default.jpg') }}" alt="{{ $servicio->nombre_servicio }}" class="servicio-img">
                                        @endif
                                        <h5>{{ $servicio->nombre_servicio }}</h5>
                                        <div class="servicio-meta-row">
                                            <span>S/ {{ number_format($servicio->costo, 2) }}</span>
                                            @if($servicio->duracion)
                                                <span>{{ $servicio->duracion }} min</span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>

            <div class="col-lg-4">
                <aside class="reserva-side">
                    <div class="detalles-box">
                        <h5>Detalles de la cita</h5>

                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" required>
                        </div>

                        <div class="mb-3" id="trabajador-horarios-section" style="display: none;">
                            <label class="form-label">Trabajadores disponibles</label>
                            <div class="trabajadores-tabs">
                                @foreach($empleados as $empleado)
                                    <button type="button"
                                            class="btn-trabajador"
                                            data-id="{{ $empleado->id_empleado }}"
                                            data-nombre="{{ $empleado->persona->nombres }}">
                                        {{ $empleado->persona->nombres }}
                                    </button>
                                @endforeach
                            </div>

                            <div class="horarios-container mt-3" id="horarios-container" style="display: none;">
                                <label class="form-label">Horarios para <span id="nombre-trabajador"></span></label>
                                <div class="horarios-grid" id="horarios-grid"></div>
                            </div>

                            <input type="hidden" name="hora" id="hora" required>
                            <input type="hidden" name="id_empleado" id="id_empleado" required>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Información de salud</label>
                            <div class="health-options">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="enfermedad" value="1" id="enfermedad">
                                    <label class="form-check-label" for="enfermedad">¿Mascota con enfermedad?</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="vacuna" value="1" id="vacuna">
                                    <label class="form-check-label" for="vacuna">¿Vacunas completas?</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="alergia" value="1" id="alergia">
                                    <label class="form-check-label" for="alergia">¿Alergias?</label>
                                </div>
                            </div>
                            <textarea name="descripcion_alergia" class="form-control mt-2"
                                      placeholder="Si tu mascota tiene alergia, descríbela aquí..."></textarea>
                        </div>
                    </div>

                    <div class="detalles-box mt-3">
                        <h5>Servicio de delivery</h5>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="requiere_delivery" value="1" id="requiere_delivery">
                                <label class="form-check-label" for="requiere_delivery">
                                    <strong>Solicitar recojo y entrega</strong>
                                </label>
                                <small class="d-block text-muted mt-1">
                                    Recogeremos a tu mascota en tu domicilio y la devolveremos cuando el servicio esté completo.
                                </small>
                            </div>
                        </div>

                        <div id="delivery-fields" style="display: none;">
                            <div class="mb-3">
                                <label for="direccion_recojo" class="form-label">Dirección de recojo</label>
                                <input type="text" name="direccion_recojo" id="direccion_recojo"
                                       class="form-control" placeholder="Ingrese su dirección completa">
                                <small class="text-muted">Incluya referencias si es necesario</small>
                            </div>

                            <div class="mb-3">
                                <label for="direccion_entrega" class="form-label">Dirección de entrega</label>
                                <input type="text" name="direccion_entrega" id="direccion_entrega"
                                       class="form-control" placeholder="Ingrese dirección de entrega si es diferente">
                                <small class="text-muted">Déjelo en blanco si es la misma dirección.</small>
                            </div>

                            <div class="delivery-note" role="alert">
                                <strong>S/ 20.00</strong> IGV incluido. El recojo se coordinará según la hora de reserva.
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        <div class="acciones mt-4">
            <a href="{{ route('reservas.seleccionMascota') }}" class="btn-cancelar">Retroceder</a>
            <button type="submit" class="btn-siguiente">Ir a pago</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/seleccion-servicio.js') }}"></script>
@endpush

@extends('layouts.app')
@section('header')
    @include('partials.header')
@endsection

@section('content')

<link rel="stylesheet" href="{{ asset('css/reservas.css') }}">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container reserva-container">
    <!-- Progress bar -->
    <div class="progressbar mb-5">
        <ul class="steps">
            <li>Selección de Mascotas</li>
            <li class="active">Selección de Servicio</li>
            <li>Pago</li>
            <li>Confirmación</li>
        </ul>
    </div>

    <h2 class="titulo-seccion">Selección de Servicios</h2>

    <form action="{{ route('reservas.pago') }}" method="POST">
        @csrf

        <div class="row">
            <!-- Columna principal con los servicios -->
            <div class="col-md-8">
                <!-- Servicios Disponibles -->
                <div class="mb-4">
                    <h4 class="mb-3">Servicios Disponibles</h4>
                    <div class="row">
                        @foreach($servicios as $servicio)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <label class="servicio-card">
                                    <input type="checkbox" 
                                           name="servicios[]" 
                                           value="{{ $servicio->id_servicio }}"
                                           data-duracion="{{ $servicio->duracion }}"
                                           class="servicio-checkbox">
                                    <div class="card-body text-center">
                                        @if($servicio->imagen_referencial)
                                            @php
                                                if (str_starts_with($servicio->imagen_referencial, 'servicios/')) {
                                                    $imagenUrl = asset('storage/' . $servicio->imagen_referencial);
                                                } else {
                                                    $imagenUrl = asset('images/servicios/' . $servicio->imagen_referencial);
                                                }
                                            @endphp
                                            <img src="{{ $imagenUrl }}"
                                                 alt="{{ $servicio->nombre_servicio }}"
                                                 class="servicio-img">
                                        @else
                                            <img src="{{ asset('images/servicios/default.jpg') }}"
                                                 alt="{{ $servicio->nombre_servicio }}"
                                                 class="servicio-img">
                                        @endif
                                        <h5 class="mt-2">{{ $servicio->nombre_servicio }}</h5>
                                        <p class="text-muted">S/ {{ number_format($servicio->costo, 2) }}</p>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Servicios Adicionales -->
                <div class="mb-4">
                    <h4 class="mb-3">Servicios Adicionales</h4>
                    <div class="row">
                        @foreach($adicionales as $servicio)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <label class="servicio-card">
                                    <input type="checkbox" 
                                           name="adicionales[]" 
                                           value="{{ $servicio->id_servicio }}"
                                           data-duracion="{{ $servicio->duracion }}"
                                           class="servicio-checkbox">
                                    <div class="card-body text-center">
                                        @if($servicio->imagen_referencial)
                                            @php
                                                if (str_starts_with($servicio->imagen_referencial, 'servicios/')) {
                                                    $imagenUrl = asset('storage/' . $servicio->imagen_referencial);
                                                } else {
                                                    $imagenUrl = asset('images/servicios/' . $servicio->imagen_referencial);
                                                }
                                            @endphp
                                            <img src="{{ $imagenUrl }}"
                                                 alt="{{ $servicio->nombre_servicio }}"
                                                 class="servicio-img">
                                        @else
                                            <img src="{{ asset('images/servicios/default.jpg') }}"
                                                 alt="{{ $servicio->nombre_servicio }}"
                                                 class="servicio-img">
                                        @endif
                                        <h5 class="mt-2">{{ $servicio->nombre_servicio }}</h5>
                                        <p class="text-muted">S/ {{ number_format($servicio->costo, 2) }}</p>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Columna lateral con Detalles -->
            <div class="col-md-4">
                <div class="detalles-box">
                    <h5>Detalles</h5>

                    <!-- Fecha y hora -->
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" name="fecha" id="fecha" class="form-control" required>

                        <label for="hora" class="form-label mt-2">Hora</label>
                        <input type="time" name="hora" id="hora" class="form-control" required>
                    </div>

                    <!-- Información de salud -->
                    <div class="mb-3">
                        <label class="form-label">Información de salud</label><br>
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
                        <textarea name="descripcion_alergia" class="form-control mt-2"
                                  placeholder="Si tu mascota tiene alergia, descríbela aquí..."></textarea>
                    </div>
                </div>

                <!-- Delivery Section -->
                <div class="detalles-box mt-3">
                    <h5>🚗 Servicio de Delivery</h5>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="requiere_delivery" value="1" id="requiere_delivery">
                            <label class="form-check-label" for="requiere_delivery">
                                <strong>Solicitar servicio de recojo y entrega</strong>
                            </label>
                            <small class="d-block text-muted mt-1">
                                Recogeremos a tu mascota en tu domicilio y la devolveremos cuando el servicio esté completo.
                            </small>
                        </div>
                    </div>

                    <div id="delivery-fields" style="display: none;">
                        <div class="mb-3">
                            <label for="direccion_recojo" class="form-label">Dirección de Recojo</label>
                            <input type="text" name="direccion_recojo" id="direccion_recojo" 
                                   class="form-control" placeholder="Ingrese su dirección completa">
                            <small class="text-muted">Incluya referencias si es necesario</small>
                        </div>

                        <div class="mb-3">
                            <label for="direccion_entrega" class="form-label">Dirección de Entrega</label>
                            <input type="text" name="direccion_entrega" id="direccion_entrega" 
                                   class="form-control" placeholder="Ingrese dirección de entrega (si es diferente)">
                            <small class="text-muted">Deje en blanco si es la misma dirección de recojo</small>
                        </div>

                        <div class="alert alert-info" role="alert">
                            <small>
                                <i class="bi bi-info-circle"></i> El servicio de delivery tiene un costo fijo de <strong>S/ 20.00</strong> (IGV incluido).<br>
                                • Recojo: Se coordinará según la hora de su reserva<br>
                                • Entrega: Al finalizar el servicio
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
                <!-- Botones de acción -->
        <div class="acciones mt-4 d-flex justify-content-between">
            <a href="{{ route('reservas.seleccionMascota') }}" class="btn-cancelar">Retroceder</a>
            <button type="submit" class="btn-siguiente">Ir a Pago</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/seleccion-servicio.js') }}"></script>
@endpush

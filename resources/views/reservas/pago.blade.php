@extends('layouts.app')

@section('header')
    @include('partials.header')
@endsection

@section('content')
<link rel="stylesheet" href="{{ asset('css/pago.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container reserva-container mt-4">
    <div class="progressbar mb-5">
        <ul class="steps">
            <li>Selección de Mascotas</li>
            <li>Selección de Servicio</li>
            <li class="active">Pago</li>
            <li>Confirmación</li>
        </ul>
    </div>

    <h2 class="titulo-seccion text-center mb-4">Resumen y Pago</h2>

    <div class="row">
        <!-- Columna izquierda -->
        <div class="col-md-8">
            <div class="card shadow-sm p-3 mb-4">
                <h5 class="mb-3">👤 Datos del Cliente</h5>
                <p><strong>Nombre:</strong> {{ Auth::user()->persona->nombres }} {{ Auth::user()->persona->apellidos }}</p>
                <p><strong>Correo:</strong> {{ Auth::user()->correo }}</p>
            </div>

            <div class="card shadow-sm p-3 mb-4">
                <h5 class="mb-3">🐾 Mascotas Seleccionadas</h5>
                @foreach($mascotas as $m)
                    <div class="border rounded p-2 mb-2 bg-light">
                        <p><strong>Nombre:</strong> {{ $m->nombre }}</p>
                        <p><strong>Especie:</strong> {{ $m->especie }}</p>
                        <p><strong>Raza:</strong> {{ $m->raza }}</p>
                    </div>
                @endforeach
            </div>

            <div class="card shadow-sm p-3">
                <h5 class="mb-3">🧴 Servicios Seleccionados</h5>
                <div class="row">
                    @foreach($servicios as $s)
                        <div class="col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <img src="{{ asset('img/servicios/'.$s->imagen_referencial) }}" class="card-img-top" alt="{{ $s->nombre_servicio }}">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $s->nombre_servicio }}</h6>
                                    <p class="card-text">S/ {{ number_format($s->costo, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @foreach($adicionales as $a)
                        <div class="col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <img src="{{ asset('img/servicios/'.$a->imagen_referencial) }}" class="card-img-top" alt="{{ $a->nombre_servicio }}">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $a->nombre_servicio }}</h6>
                                    <p class="card-text">S/ {{ number_format($a->costo, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Columna derecha -->
        <div class="col-md-4">
            <div class="card shadow-sm p-3 text-center">
                <h5>💰 Total a Pagar</h5>
                @php
                    $total = $servicios->sum('costo') + $adicionales->sum('costo');
                @endphp
                <h3 class="text-success mt-3">S/ {{ number_format($total, 2) }}</h3>
                <p class="text-muted">Monto total</p>

                <!-- 🟡 CONTENEDOR DONDE IRÁ EL BOTÓN -->
                <div id="paypal-button-container" class="mt-4"></div>

                <a href="{{ route('reservas.seleccionServicio') }}" class="btn btn-outline-secondary mt-3">⬅ Volver</a>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<!-- SDK de PayPal -->
<script src="https://www.paypal.com/sdk/js?client-id=ARTxzEbR-GgKPnQdy64P9D3zeGlcj9zRJgCTy8ewKh3ZSyhr-lsh20yrYCfP2j-Jr8rAc9ysyLyRB3Xc&currency=USD"></script>

<script>
window.addEventListener('load', function() {
    if (typeof paypal === 'undefined') {
        console.error("⚠️ El SDK de PayPal no se cargó correctamente.");
        alert("El SDK de PayPal no se cargó. Revisa tu conexión o el Client ID.");
        return;
    }

    paypal.Buttons({
        style: {
            layout: 'vertical',
            color: 'gold',
            shape: 'rect',
            label: 'paypal'
        },
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    description: 'Pago PetSpa',
                    amount:{
                        currency_code:'USD',
                        // convierte tus soles a dólares solo para la prueba
                        value:"{{ number_format($total / 3.80, 2, '.', '') }}"
                        }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                alert('✅ Pago completado por ' + details.payer.name.given_name);
                window.location.href = "{{ route('reservas.guardarPago') }}";
            });
        },
        onError: function(err) {
            console.error("Error en PayPal:", err);
            alert("❌ Ocurrió un error al procesar el pago.");
        }
    }).render('#paypal-button-container');
});
</script>
@endpush

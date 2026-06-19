@extends('layouts.app')

@section('header')
    @include('partials.header')
@endsection

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/pago.css') }}">

@php
    $subtotalBaseServicios = $servicios->sum('costo') + $adicionales->sum('costo');
    $igvServicios = $subtotalBaseServicios * 0.18;
    $subtotalServicios = $subtotalBaseServicios + $igvServicios;
    $costoDelivery = session('requiere_delivery', 0) == 1 ? 20.00 : 0;
    $total = $subtotalServicios + $costoDelivery;
@endphp

<div class="container reserva-container pago-container mt-4">
    <div class="progressbar mb-5">
        <ul class="steps">
            <li>Selección de mascotas</li>
            <li>Selección de servicio</li>
            <li class="active">Pago</li>
            <li>Confirmación</li>
        </ul>
    </div>

    <div class="pago-heading">
        <span class="pago-eyebrow">Resumen final</span>
        <h2 class="titulo-seccion text-center mb-2">Confirma y paga tu reserva</h2>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <section class="pago-card mb-4">
                <h5>Datos del cliente</h5>
                <div class="pago-client-grid">
                    <p><strong>Nombre:</strong> {{ Auth::user()->persona->nombres }} {{ Auth::user()->persona->apellidos }}</p>
                    <p><strong>Correo:</strong> {{ Auth::user()->correo }}</p>
                </div>
            </section>

            <section class="pago-card mb-4">
                <h5>Mascotas seleccionadas</h5>
                <div class="mascotas-resumen">
                    @foreach($mascotas as $m)
                        <div class="mascota-resumen-card">
                            <strong>{{ $m->nombre }}</strong>
                            <span>{{ $m->especie }} · {{ $m->raza }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="pago-card">
                <div class="pago-section-title">
                    <h5>Servicios seleccionados</h5>
                    <span>{{ $servicios->count() + $adicionales->count() }} ítems</span>
                </div>

                <div class="servicios-pago-grid">
                    @foreach($servicios as $s)
                        <article class="servicio-pago-card">
                            <img src="{{ $s->imagen_url }}"
                                 class="pago-servicio-img"
                                 alt="{{ $s->nombre_servicio }}"
                                 onerror="this.src='{{ asset('images/servicios/default.jpg') }}'">
                            <div>
                                <h6>{{ $s->nombre_servicio }}</h6>
                                <p>S/ {{ number_format($s->costo, 2) }}</p>
                            </div>
                        </article>
                    @endforeach

                    @foreach($adicionales as $a)
                        <article class="servicio-pago-card">
                            <img src="{{ $a->imagen_url }}"
                                 class="pago-servicio-img"
                                 alt="{{ $a->nombre_servicio }}"
                                 onerror="this.src='{{ asset('images/servicios/default.jpg') }}'">
                            <div>
                                <h6>{{ $a->nombre_servicio }}</h6>
                                <p>S/ {{ number_format($a->costo, 2) }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if(session('requiere_delivery', 0) == 1)
                    <div class="delivery-summary mt-3">
                        <h6>Servicio de delivery incluido</h6>
                        <p><strong>Recojo:</strong> {{ session('direccion_recojo') }}</p>
                        <p><strong>Entrega:</strong> {{ session('direccion_entrega') ?: session('direccion_recojo') }}</p>
                        <p class="mb-0"><strong>Costo:</strong> S/ 20.00</p>
                    </div>
                @endif
            </section>
        </div>

        <div class="col-lg-4">
            <aside class="pago-summary-card">
                <h5>Total a pagar</h5>

                <div class="pago-total-list">
                    <p><strong>Servicios</strong> <span>S/ {{ number_format($subtotalBaseServicios, 2) }}</span></p>
                    <p><strong>IGV servicios</strong> <span>S/ {{ number_format($igvServicios, 2) }}</span></p>
                    @if($costoDelivery > 0)
                        <p><strong>Delivery</strong> <span>S/ {{ number_format($costoDelivery, 2) }}</span></p>
                    @endif
                    <hr>
                    <h4><strong>Total</strong> <span>S/ {{ number_format($total, 2) }}</span></h4>
                </div>

                <div class="internal-payment-panel">
                    <div>
                        <span class="payment-pill">Pago seguro</span>
                        <h6>Confirma tu pago</h6>
                        <p>Al confirmar, generaremos tu boleta electronica y enviaremos los detalles a tu correo.</p>
                    </div>
                    <form method="POST" action="{{ route('reservas.pagoSimulado') }}">
                        @csrf
                        <button type="submit" class="btn-internal-payment">
                            <i class="fas fa-shield-heart"></i>
                            Confirmar pago
                        </button>
                    </form>
                </div>

                <div class="paypal-panel">
                    <span class="payment-pill payment-pill--paypal">Pago externo</span>
                    <div id="paypal-button-container"></div>
                </div>

                <button type="button" class="btn-volver" onclick="window.history.back()">Volver</button>
            </aside>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://www.paypal.com/sdk/js?client-id=ARTxzEbR-GgKPnQdy64P9D3zeGlcj9zRJgCTy8ewKh3ZSyhr-lsh20yrYCfP2j-Jr8rAc9ysyLyRB3Xc&currency=USD"></script>

<script>
window.addEventListener('load', function() {
    if (typeof paypal === 'undefined') {
        console.error("El SDK de PayPal no se cargó correctamente.");
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
                        value:"{{ number_format($total / 3.80, 2, '.', '') }}"
                    }
                }]
            });
        },

        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                fetch("{{ route('reservas.finalizar') }}", {
                    method: "POST",
                    headers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ metodo_pago: "paypal" })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert("Ocurrió un error al guardar la reserva.");
                        return;
                    }

                    alert('Pago completado correctamente por ' + details.payer.name.given_name);

                    setTimeout(() => {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = "{{ route('reservas.guardarPago') }}";

                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = '_token';
                        tokenInput.value = "{{ csrf_token() }}";
                        form.appendChild(tokenInput);

                        if (data.reserva_id) {
                            const reservaInput = document.createElement('input');
                            reservaInput.type = 'hidden';
                            reservaInput.name = 'reserva_id';
                            reservaInput.value = data.reserva_id;
                            form.appendChild(reservaInput);
                        }

                        document.body.appendChild(form);
                        form.submit();
                    }, 800);
                })
                .catch(err => {
                    console.error("Error al crear la reserva:", err);
                    alert("Ocurrió un error al crear la reserva en el servidor.");
                });
            });
        },

        onCancel: function() {
            alert("El pago fue cancelado por el usuario.");
        },
        onError: function(err) {
            console.error("Error en PayPal:", err);
            alert("Ocurrió un error al procesar el pago.");
        }
    }).render('#paypal-button-container');
});
</script>
@endpush

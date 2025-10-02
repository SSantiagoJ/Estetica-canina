@extends('layouts.app')
@section('header')
    @include('partials.header')
@endsection
@section('content')
<link rel="stylesheet" href="{{ asset('css/pago.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container reserva-container">
    <!-- Progress bar -->
    <div class="progressbar mb-5">
        <ul class="steps">
            <li>Selección de Mascotas</li>
            <li>Selección de Servicio</li>
            <li class="active">Pago</li>
            <li>Confirmación</li>
        </ul>
    </div>

    <h2 class="titulo-seccion">Métodos de Pago</h2>

    <form id="form-pago" action="{{ route('reservas.finalizar') }}" method="POST">
        @csrf
        <input type="hidden" name="metodo_pago" id="metodo_pago">

        <div class="row">
            <!-- Columna izquierda -->
            <div class="col-md-8">
                <!-- Tarjeta -->
                <div class="metodo-card" onclick="selectPago('tarjeta')">
                    <h5>💳 Tarjeta de Crédito/Débito</h5>
                    <div id="form-tarjeta" class="d-none mt-3">
                        <div class="mb-2">
                            <label class="form-label">Número de tarjeta</label>
                            <input type="text" class="form-control" name="num_tarjeta" placeholder="XXXX-XXXX-XXXX-XXXX">
                        </div>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <label class="form-label">F. Vencimiento</label>
                                <input type="text" class="form-control" name="fecha_venc" placeholder="MM/AA">
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label">CVV</label>
                                <input type="password" class="form-control" name="cvv" maxlength="3" placeholder="***">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Nombre del propietario</label>
                            <input type="text" class="form-control" name="titular" placeholder="Juan Pérez">
                        </div>
                    </div>
                </div>

                <!-- Yape -->
                <div class="metodo-card" onclick="selectPago('yape')">
                    <h5>📱 Yape</h5>
                    <p>Escanee el QR para pagar con Yape</p>
                    <img src="{{ asset('images/qr-yape.png') }}" alt="QR Yape" class="qr-yape d-none" width="200">
                </div>
            </div>

            
            <!-- Columna derecha (Resumen tipo boleta) -->
                <div class="col-md-4">
                    <div class="detalles-box">
                        <h5>Resumen de Reserva</h5>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Concepto</th>
                                    <th class="text-end">Precio (S/)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($servicios as $s)
                                    <tr>
                                        <td>{{ $s->nombre_servicio }}</td>
                                        <td class="text-end">{{ number_format($s->costo, 2) }}</td>
                                    </tr>
                                @endforeach
                                @foreach($adicionales as $a)
                                    <tr>
                                        <td>{{ $a->nombre_servicio }}</td>
                                        <td class="text-end">{{ number_format($a->costo, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                @php
                                    $total = $servicios->sum('costo') + $adicionales->sum('costo');
                                    $reserva = $total / 2;
                                @endphp
                                <tr>
                                    <th>Total</th>
                                    <th class="text-end">S/ {{ number_format($total, 2) }}</th>
                                </tr>
                                <tr class="table-primary">
                                    <th>Pagar ahora (50%)</th>
                                    <th class="text-end">S/ {{ number_format($reserva, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

        </div>

        <!-- Botones -->
        <div class="acciones mt-4 d-flex justify-content-between">
            <a href="{{ route('reservas.seleccionServicio') }}" class="btn-cancelar">Retroceder</a>
            <button type="button" class="btn-siguiente" onclick="procesarPago()">Pagar</button>
        </div>
    </form>
</div>

<!-- Modal Confirmación Tarjeta -->
<div class="modal fade" id="modalTarjeta" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4 text-center">
      <h5>¿Está seguro de pagar con tarjeta?</h5>
      <div class="mt-3">
        <button class="btn btn-secondary" data-bs-dismiss="modal">No</button>
        <button class="btn btn-primary" onclick="confirmarPago('tarjeta')">Sí</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Confirmación Yape -->
<div class="modal fade" id="modalYape" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4 text-center">
      <h5>¿Ha realizado el pago en Yape?</h5>
      <div class="mt-3">
        <button class="btn btn-secondary" data-bs-dismiss="modal">No</button>
        <button class="btn btn-primary" onclick="confirmarPago('yape')">Sí, Confirmar</button>
      </div>
    </div>
  </div>
</div>
<script>
    let metodo = '';

    function selectPago(tipo) {
        metodo = tipo;
        document.getElementById('metodo_pago').value = tipo;

        if (tipo === 'tarjeta') {
            document.getElementById('form-tarjeta').classList.remove('d-none');
            document.querySelector('.qr-yape').classList.add('d-none');
        } else if (tipo === 'yape') {
            document.querySelector('.qr-yape').classList.remove('d-none');
            document.getElementById('form-tarjeta').classList.add('d-none');
        }
    }

    function procesarPago() {
        if (metodo === '') {
            alert('Seleccione un método de pago');
            return;
        }
        if (metodo === 'tarjeta') {
            new bootstrap.Modal(document.getElementById('modalTarjeta')).show();
        } else if (metodo === 'yape') {
            new bootstrap.Modal(document.getElementById('modalYape')).show();
        }
    }

    function confirmarPago(tipo) {
        document.getElementById('form-pago').submit();
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection

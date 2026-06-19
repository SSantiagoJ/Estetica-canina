<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta Electronica {{ $pago->series }}</title>
    <link rel="stylesheet" href="{{ public_path('css/boleta.css') }}" type="text/css">
</head>
<body>
@php
    $personaCliente = $cliente->persona ?? null;
    $clienteNombre = trim(($personaCliente->nombres ?? '') . ' ' . ($personaCliente->apellidos ?? ''));
    $mascota = $reserva->mascota ?? null;
    $empleadoPersona = optional($reserva->empleado)->persona;
    $empleadoNombre = $empleadoPersona ? trim(($empleadoPersona->nombres ?? '') . ' ' . ($empleadoPersona->apellidos ?? '')) : 'Equipo Pet Grooming';
    $subtotalServicios = collect($servicios)->sum('precio_unitario');
    $igvServicios = collect($servicios)->sum('igv');
    $deliveryBase = $delivery ? (float) $delivery->costo_delivery : 0;
    $deliveryIgv = $deliveryBase * 0.18;
    $deliveryTotal = $deliveryBase + $deliveryIgv;
    $subtotalBase = $subtotalServicios + $deliveryBase;
    $igvTotal = $igvServicios + $deliveryIgv;
    $codigoVerificacion = $pago->codigo_operacion ?: ($pago->provider_payment_id ?: $pago->series);
@endphp

<main class="invoice">
    <table class="brand-table">
        <tr>
            <td class="brand-cell">
                <div class="brand-mark">PG</div>
                <div>
                    <h1>Pet Grooming</h1>
                    <p>Estetica, bienestar y cuidado veterinario para mascotas</p>
                </div>
            </td>
            <td class="document-cell">
                <span>BOLETA ELECTR&Oacute;NICA</span>
                <strong>{{ $pago->series }}</strong>
                <small>Comprobante de pago</small>
            </td>
        </tr>
    </table>

    <table class="info-grid">
        <tr>
            <td>
                <h2>Datos del cliente</h2>
                <p><strong>Cliente:</strong> {{ $clienteNombre ?: 'Cliente Pet Grooming' }}</p>
                <p><strong>Correo:</strong> {{ $reserva->usuario->correo ?? $pago->usuario_creacion }}</p>
                <p><strong>Mascota:</strong> {{ $mascota->nombre ?? 'Mascota' }} @if(!empty($mascota->especie))({{ $mascota->especie }})@endif</p>
            </td>
            <td>
                <h2>Datos de la cita</h2>
                <p><strong>Fecha de reserva:</strong> {{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</p>
                <p><strong>Hora:</strong> {{ substr((string) $reserva->hora, 0, 5) }}</p>
                <p><strong>Atiende:</strong> {{ $empleadoNombre ?: 'Equipo Pet Grooming' }}</p>
            </td>
        </tr>
    </table>

    <table class="payment-strip">
        <tr>
            <td>
                <span>Metodo de pago</span>
                <strong>{{ $pago->metodo_pago === 'simulado' ? 'Pago confirmado' : ucfirst(str_replace('_', ' ', $pago->metodo_pago)) }}</strong>
            </td>
            <td>
                <span>Estado</span>
                <strong>{{ $pago->estado_gateway ?: 'APROBADO' }}</strong>
            </td>
            <td>
                <span>Fecha de emision</span>
                <strong>{{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y') }} {{ substr((string) $pago->hora, 0, 5) }}</strong>
            </td>
            <td>
                <span>Operacion</span>
                <strong>{{ $codigoVerificacion }}</strong>
            </td>
        </tr>
    </table>

    <h2 class="section-title">Detalle de servicios</h2>
    <table class="items-table">
        <thead>
            <tr>
                <th>Servicio</th>
                <th class="text-right">Base</th>
                <th class="text-right">IGV 18%</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($servicios as $det)
                <tr>
                    <td>
                        <strong>{{ $det->servicio->nombre_servicio ?? 'Servicio Pet Grooming' }}</strong>
                        <span>{{ $det->servicio->descripcion ?? 'Cuidado personalizado para tu mascota.' }}</span>
                    </td>
                    <td class="text-right">S/ {{ number_format($det->precio_unitario, 2) }}</td>
                    <td class="text-right">S/ {{ number_format($det->igv, 2) }}</td>
                    <td class="text-right strong">S/ {{ number_format($det->total, 2) }}</td>
                </tr>
            @endforeach

            @if($delivery)
                <tr>
                    <td>
                        <strong>Recojo y entrega a domicilio</strong>
                        <span>Recojo: {{ $delivery->direccion_recojo }} | Entrega: {{ $delivery->direccion_entrega }}</span>
                    </td>
                    <td class="text-right">S/ {{ number_format($deliveryBase, 2) }}</td>
                    <td class="text-right">S/ {{ number_format($deliveryIgv, 2) }}</td>
                    <td class="text-right strong">S/ {{ number_format($deliveryTotal, 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td class="note-cell">
                <strong>Codigo de verificacion</strong>
                <p>{{ $codigoVerificacion }}</p>
                <small>Este comprobante fue generado electronicamente por Pet Grooming.</small>
            </td>
            <td class="amounts-cell">
                <table>
                    <tr>
                        <td>Subtotal</td>
                        <td>S/ {{ number_format($subtotalBase, 2) }}</td>
                    </tr>
                    <tr>
                        <td>IGV</td>
                        <td>S/ {{ number_format($igvTotal, 2) }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td>Total pagado</td>
                        <td>S/ {{ number_format($pago->monto, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <footer class="invoice-footer">
        <p>Gracias por confiar en Pet Grooming. Cuidamos a tu mascota con carino, orden y responsabilidad.</p>
        <p>Direccion: Calle Principal 123, Ica, Peru | Telefono: (056) 123456 | pet.grooming.2025.grupo2@gmail.com</p>
    </footer>
</main>
</body>
</html>

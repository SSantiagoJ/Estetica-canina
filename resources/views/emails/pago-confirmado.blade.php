<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta electronica enviada</title>
</head>
<body style="margin:0; padding:0; background:#fff7fb; font-family:Arial, Helvetica, sans-serif; color:#493441;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%; background:#fff7fb; padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:660px; width:100%; overflow:hidden; border-radius:20px; background:#ffffff; border:1px solid #f1d7e2;">
                    <tr>
                        <td style="padding:28px 30px; background:#e87ba2; color:#ffffff;">
                            <p style="margin:0 0 8px; font-size:12px; letter-spacing:1px; text-transform:uppercase; font-weight:bold;">Pet Grooming</p>
                            <h1 style="margin:0; font-size:28px; line-height:1.2;">Tu boleta electronica esta lista</h1>
                            <p style="margin:10px 0 0; font-size:15px; line-height:1.5;">Adjuntamos el comprobante PDF de tu reserva y dejamos registrado el pago en nuestro sistema.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;">
                            <p style="margin:0 0 18px; font-size:16px;">
                                Hola {{ $cliente->persona->nombres ?? 'cliente' }},
                            </p>

                            <p style="margin:0 0 22px; font-size:15px; line-height:1.6; color:#6f5c68;">
                                Gracias por confiar en Pet Grooming. Tu pago fue confirmado y tu boleta electronica se encuentra adjunta a este correo.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse; margin:0 0 22px;">
                                <tr>
                                    <td style="padding:12px; background:#fff1f6; border:1px solid #f3d4df; font-weight:bold;">Boleta</td>
                                    <td style="padding:12px; border:1px solid #f3d4df;">{{ $pago->series }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px; background:#fff1f6; border:1px solid #f3d4df; font-weight:bold;">Monto pagado</td>
                                    <td style="padding:12px; border:1px solid #f3d4df;">S/ {{ number_format($pago->monto, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px; background:#fff1f6; border:1px solid #f3d4df; font-weight:bold;">Metodo</td>
                                    <td style="padding:12px; border:1px solid #f3d4df;">{{ $pago->metodo_pago === 'simulado' ? 'Pago confirmado' : ucfirst(str_replace('_', ' ', $pago->metodo_pago)) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px; background:#fff1f6; border:1px solid #f3d4df; font-weight:bold;">Operacion</td>
                                    <td style="padding:12px; border:1px solid #f3d4df;">{{ $pago->codigo_operacion ?? $pago->provider_payment_id }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px; background:#fff1f6; border:1px solid #f3d4df; font-weight:bold;">Reserva</td>
                                    <td style="padding:12px; border:1px solid #f3d4df;">{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }} a las {{ substr((string) $reserva->hora, 0, 5) }}</td>
                                </tr>
                            </table>

                            <p style="margin:0 0 8px; font-size:15px; font-weight:bold;">Servicios incluidos</p>
                            <ul style="margin:0 0 24px; padding-left:20px; font-size:14px; line-height:1.7; color:#6f5c68;">
                                @foreach($reserva->detalles as $detalle)
                                    <li>{{ $detalle->servicio->nombre_servicio ?? 'Servicio Pet Grooming' }} - S/ {{ number_format($detalle->total, 2) }}</li>
                                @endforeach
                            </ul>

                            <div style="padding:16px; border-radius:14px; background:#fff7fb; border:1px solid #f3d4df;">
                                <strong style="display:block; margin-bottom:6px;">Recordatorio bonito</strong>
                                <span style="font-size:13px; color:#7a6571; line-height:1.5;">Llega unos minutos antes de tu cita y trae cualquier indicacion especial de salud de tu mascota.</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

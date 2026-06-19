<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reserva confirmada</title>
</head>
<body style="margin:0; padding:0; background:#f8f3ff; font-family:Arial, Helvetica, sans-serif; color:#493441;">
    @php
        $personaCliente = $cliente->persona ?? null;
        $clienteNombre = trim(($personaCliente->nombres ?? '') . ' ' . ($personaCliente->apellidos ?? ''));
        $empleadoPersona = optional($reserva->empleado)->persona;
        $empleadoNombre = $empleadoPersona ? trim(($empleadoPersona->nombres ?? '') . ' ' . ($empleadoPersona->apellidos ?? '')) : 'Equipo Pet Grooming';
    @endphp
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%; background:#f8f3ff; padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:660px; width:100%; overflow:hidden; border-radius:20px; background:#ffffff; border:1px solid #eadcf6;">
                    <tr>
                        <td style="padding:28px 30px; background:#493441; color:#ffffff;">
                            <p style="margin:0 0 8px; font-size:12px; letter-spacing:1px; text-transform:uppercase; font-weight:bold;">Pet Grooming</p>
                            <h1 style="margin:0; font-size:28px; line-height:1.2;">Reserva confirmada</h1>
                            <p style="margin:10px 0 0; font-size:15px; line-height:1.5;">Ya tenemos registrada la cita de tu mascota.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;">
                            <p style="margin:0 0 18px; font-size:16px;">
                                Hola {{ $clienteNombre ?: 'cliente' }},
                            </p>

                            <p style="margin:0 0 22px; font-size:15px; line-height:1.6; color:#6f5c68;">
                                Tu reserva fue registrada correctamente. Te esperamos para brindar una atencion tranquila, ordenada y carinosa.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse; margin:0 0 22px;">
                                <tr>
                                    <td style="padding:12px; background:#fff1f6; border:1px solid #f3d4df; font-weight:bold;">Mascota</td>
                                    <td style="padding:12px; border:1px solid #f3d4df;">{{ $reserva->mascota->nombre ?? 'Mascota' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px; background:#fff1f6; border:1px solid #f3d4df; font-weight:bold;">Fecha</td>
                                    <td style="padding:12px; border:1px solid #f3d4df;">{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px; background:#fff1f6; border:1px solid #f3d4df; font-weight:bold;">Hora</td>
                                    <td style="padding:12px; border:1px solid #f3d4df;">{{ substr((string) $reserva->hora, 0, 5) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px; background:#fff1f6; border:1px solid #f3d4df; font-weight:bold;">Atiende</td>
                                    <td style="padding:12px; border:1px solid #f3d4df;">{{ $empleadoNombre ?: 'Equipo Pet Grooming' }}</td>
                                </tr>
                                @if($pago)
                                    <tr>
                                        <td style="padding:12px; background:#fff1f6; border:1px solid #f3d4df; font-weight:bold;">Boleta</td>
                                        <td style="padding:12px; border:1px solid #f3d4df;">{{ $pago->series }} | S/ {{ number_format($pago->monto, 2) }}</td>
                                    </tr>
                                @endif
                            </table>

                            <p style="margin:0 0 8px; font-size:15px; font-weight:bold;">Servicios reservados</p>
                            <ul style="margin:0 0 24px; padding-left:20px; font-size:14px; line-height:1.7; color:#6f5c68;">
                                @forelse($reserva->detalles as $detalle)
                                    <li>{{ $detalle->servicio->nombre_servicio ?? 'Servicio Pet Grooming' }}</li>
                                @empty
                                    <li>Servicio Pet Grooming</li>
                                @endforelse
                            </ul>

                            <div style="padding:16px; border-radius:14px; background:#f8f3ff; border:1px solid #eadcf6;">
                                <strong style="display:block; margin-bottom:6px;">Importante</strong>
                                <span style="font-size:13px; color:#7a6571; line-height:1.5;">Si tu mascota tiene alergias, enfermedad o alguna indicacion especial, avisanos antes de iniciar la atencion.</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

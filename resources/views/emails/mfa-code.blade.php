@php
    $brandName = 'Pet Grooming';
    $title = $setupRequired ? 'Crea tu MFA' : 'Verifica tu acceso';
    $subtitle = $setupRequired
        ? 'Activa una capa extra de seguridad para proteger tus reservas y datos.'
        : 'Confirma que eres tu antes de continuar.';
    $intro = $setupRequired
        ? 'Tu cuenta aun no tiene MFA activo. Usa este codigo para crearlo y mantener tu acceso protegido.'
        : 'Usa este codigo para completar tu inicio de sesion de forma segura.';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ $brandName }}</title>
</head>
<body style="margin:0; padding:0; background:#fff7f1; font-family:Arial, Helvetica, sans-serif; color:#513b4a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%; background:#fff7f1; padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px; width:100%; overflow:hidden; border-radius:18px; background:#ffffff; border:1px solid #f1d7df; box-shadow:0 18px 40px rgba(81,59,74,0.12);">
                    <tr>
                        <td style="padding:0;">
                            <div style="height:10px; background:linear-gradient(90deg,#e8839f 0%,#ffdbe2 45%,#9ddfc3 100%);"></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:34px 34px 22px; text-align:center; background:linear-gradient(135deg,#fff0f5 0%,#ffffff 58%,#ecfff6 100%);">
                            <div style="display:inline-block; width:68px; height:68px; line-height:68px; border-radius:50%; background:#ffffff; color:#e8839f; font-size:32px; box-shadow:0 10px 24px rgba(232,131,159,0.18);">
                                &#128062;
                            </div>
                            <h1 style="margin:18px 0 8px; font-size:30px; line-height:1.15; color:#513b4a; font-weight:800;">
                                {{ $title }}
                            </h1>
                            <p style="margin:0; font-size:16px; line-height:1.55; color:#7a6672;">
                                {{ $subtitle }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 34px 8px;">
                            <p style="margin:0 0 18px; font-size:16px; line-height:1.65; color:#5f4b59;">
                                Hola, {{ $nombre ?? 'familia Pet Grooming' }}.
                            </p>
                            <p style="margin:0 0 22px; font-size:16px; line-height:1.65; color:#5f4b59;">
                                {{ $intro }}
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 auto 22px;">
                                <tr>
                                    <td align="center" style="padding:22px; border-radius:16px; background:#fff7fb; border:1px dashed #e8839f;">
                                        <div style="font-size:13px; font-weight:700; color:#b65c75; text-transform:uppercase; letter-spacing:1.2px; margin-bottom:10px;">
                                            Codigo de seguridad
                                        </div>
                                        <div style="font-size:38px; line-height:1; letter-spacing:8px; color:#513b4a; font-weight:800;">
                                            {{ $code }}
                                        </div>
                                        <div style="font-size:13px; line-height:1.45; color:#7a6672; margin-top:12px;">
                                            Este codigo vence en {{ $expiresMinutes }} minutos.
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 22px;">
                                <tr>
                                    <td style="padding:16px 18px; border-radius:14px; background:#ecfff6; border:1px solid #ccebdd; color:#4b6b5c; font-size:14px; line-height:1.55;">
                                        <strong>Consejo de seguridad:</strong>
                                        nunca compartas este codigo. Nuestro equipo no te lo pedira por telefono, WhatsApp ni redes sociales.
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 8px; font-size:14px; line-height:1.6; color:#7a6672;">
                                Si no solicitaste este acceso, puedes ignorar este mensaje. Tu cuenta seguira protegida.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:22px 34px 30px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-top:1px solid #f1d7df;">
                                <tr>
                                    <td style="padding-top:20px; text-align:center;">
                                        <div style="font-size:16px; font-weight:800; color:#513b4a;">{{ $brandName }}</div>
                                        <div style="font-size:13px; color:#9a7d8b; margin-top:5px;">Bienestar, estetica y cuidado para mascotas.</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

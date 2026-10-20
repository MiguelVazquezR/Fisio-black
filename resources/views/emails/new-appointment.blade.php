<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva cita por confirmar</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f1;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1c1d1a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f3f4f1;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background-color:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e4e6e0;">
                    <tr>
                        <td style="background-color:#1c1d1a;padding:24px 28px;">
                            <p style="margin:0;font-size:12px;letter-spacing:2px;text-transform:uppercase;color:#c9f269;font-weight:700;">
                                {{ config('fisio.clinic.name') }}
                            </p>
                            <h1 style="margin:8px 0 0;font-size:22px;line-height:1.3;color:#ffffff;">
                                Nueva cita por confirmar
                            </h1>
                            <p style="margin:8px 0 0;font-size:13px;line-height:1.6;color:#b9bbb4;">
                                Un paciente acaba de agendar en línea y está esperando respuesta.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:24px 28px 8px;">
                            <p style="margin:0;font-size:12px;letter-spacing:1.5px;text-transform:uppercase;color:#6d8a1f;font-weight:700;">
                                Folio {{ $folio }}
                            </p>
                            <h2 style="margin:6px 0 0;font-size:18px;line-height:1.4;">
                                {{ $patientName }}
                            </h2>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:8px 28px 0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;line-height:1.6;">
                                <tr>
                                    <td style="padding:8px 0;color:#6f7268;width:120px;">Servicio</td>
                                    <td style="padding:8px 0;font-weight:600;">{{ $serviceName }} ({{ $serviceDuration }} min)</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6f7268;">Fecha</td>
                                    <td style="padding:8px 0;font-weight:600;">{{ $dateLabel }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6f7268;">Hora</td>
                                    <td style="padding:8px 0;font-weight:600;">{{ $timeLabel }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6f7268;">Modalidad</td>
                                    <td style="padding:8px 0;font-weight:600;">{{ $modalityLabel }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6f7268;vertical-align:top;">Dirección</td>
                                    <td style="padding:8px 0;">{{ $addressLabel }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6f7268;">Teléfono</td>
                                    <td style="padding:8px 0;">
                                        <a href="tel:{{ $patientPhone }}" style="color:#1c1d1a;font-weight:600;text-decoration:none;">
                                            {{ $patientPhone }}
                                        </a>
                                    </td>
                                </tr>
                                @if ($patientEmail)
                                    <tr>
                                        <td style="padding:8px 0;color:#6f7268;">Correo</td>
                                        <td style="padding:8px 0;">
                                            <a href="mailto:{{ $patientEmail }}" style="color:#1c1d1a;font-weight:600;text-decoration:none;">
                                                {{ $patientEmail }}
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                                @if ($notes)
                                    <tr>
                                        <td style="padding:8px 0;color:#6f7268;vertical-align:top;">Notas</td>
                                        <td style="padding:8px 0;">{{ $notes }}</td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:16px 28px 0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#fdf6e3;border:1px solid #f2dfa8;border-radius:12px;">
                                <tr>
                                    <td style="padding:14px 16px;font-size:13px;line-height:1.6;color:#7a5b12;">
                                        <strong>Estatus: Pendiente.</strong>
                                        Revisa la agenda y confirma la cita lo antes posible para liberar
                                        el horario o responderle al paciente por WhatsApp.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 28px 28px;">
                            <a href="{{ $dashboardUrl }}" style="display:inline-block;background-color:#1c1d1a;color:#ffffff;font-size:14px;font-weight:700;text-decoration:none;padding:12px 22px;border-radius:10px;">
                                Abrir agenda y confirmar
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="border-top:1px solid #e4e6e0;padding:16px 28px;font-size:11px;line-height:1.6;color:#8b8d85;">
                            Este correo se generó automáticamente cuando un paciente agendó desde
                            {{ config('app.url') }}.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>


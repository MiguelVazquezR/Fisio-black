<?php

namespace App\Support;

use App\Models\Appointment;

final class WhatsAppMessage
{
    public static function confirmation(Appointment $appointment): string
    {
        return implode("\n", [
            sprintf('Hola %s, tu cita en %s está *CONFIRMADA*.', $appointment->patient_name, self::clinicName()),
            '',
            'Servicio: '.$appointment->service->name,
            'Fecha: '.self::dateLabel($appointment),
            'Hora: '.$appointment->timeLabel(),
            'Modalidad: '.$appointment->modalityLabel(),
            'Dirección: '.$appointment->addressLabel(),
            '',
            'Te esperamos 10 minutos antes. Si necesitas reagendar, escríbenos por este medio.',
            '¡Gracias por confiar en nosotros!',
        ]);
    }

    public static function reschedule(Appointment $appointment): string
    {
        return implode("\n", [
            sprintf('Hola %s, tu cita en %s fue *REAGENDADA*.', $appointment->patient_name, self::clinicName()),
            '',
            'Nueva fecha: '.self::dateLabel($appointment),
            'Nueva hora: '.$appointment->timeLabel(),
            'Servicio: '.$appointment->service->name,
            'Modalidad: '.$appointment->modalityLabel(),
            'Dirección: '.$appointment->addressLabel(),
            '',
            'Si la nueva fecha no te funciona, respóndenos por aquí y la ajustamos.',
        ]);
    }

    public static function reminder(Appointment $appointment): string
    {
        return implode("\n", [
            sprintf('Hola %s, te recordamos tu cita en %s.', $appointment->patient_name, self::clinicName()),
            '',
            'Servicio: '.$appointment->service->name,
            'Fecha: '.self::dateLabel($appointment),
            'Hora: '.$appointment->timeLabel(),
            'Modalidad: '.$appointment->modalityLabel(),
            'Dirección: '.$appointment->addressLabel(),
            '',
            'Si necesitas cambiarla, escríbenos por este medio. ¡Te esperamos!',
        ]);
    }

    public static function url(string $phone, string $message): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        return 'https://wa.me/'.$digits.'?text='.rawurlencode($message);
    }

    public static function clinicUrl(string $message): string
    {
        return self::url((string) config('fisio.clinic.whatsapp'), $message);
    }

    private static function clinicName(): string
    {
        return (string) config('fisio.clinic.name');
    }

    private static function dateLabel(Appointment $appointment): string
    {
        return DateLabel::long($appointment->appointment_date);
    }
}

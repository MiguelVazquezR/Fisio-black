<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Support\DateLabel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewAppointmentReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva cita por confirmar · '.$this->appointment->folio(),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $appointment = $this->appointment;

        return new Content(
            view: 'emails.new-appointment',
            with: [
                'folio' => $appointment->folio(),
                'patientName' => $appointment->patient_name,
                'patientPhone' => $appointment->patient_phone,
                'patientEmail' => $appointment->patient_email,
                'serviceName' => $appointment->service->name,
                'serviceDuration' => $appointment->service->duration_minutes,
                'dateLabel' => DateLabel::long($appointment->appointment_date),
                'timeLabel' => $appointment->timeLabel(),
                'modalityLabel' => $appointment->modalityLabel(),
                'addressLabel' => $appointment->addressLabel(),
                'notes' => $appointment->notes,
                'dashboardUrl' => route('admin.dashboard'),
            ],
        );
    }
}

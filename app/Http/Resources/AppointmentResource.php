<?php

namespace App\Http\Resources;

use App\Models\Appointment;
use App\Support\DateLabel;
use App\Support\WhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Appointment */
class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->resource->loadMissing('service');

        return [
            'id' => $this->id,
            'folio' => 'FB-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT),
            'patient_name' => $this->patient_name,
            'patient_phone' => $this->patient_phone,
            'patient_email' => $this->patient_email,
            'is_home_service' => $this->is_home_service,
            'address_notes' => $this->address_notes,
            'address_label' => $this->addressLabel(),
            'modality_label' => $this->modalityLabel(),
            'appointment_date' => $this->appointment_date->toDateString(),
            'appointment_time' => $this->timeLabel(),
            'date_label' => DateLabel::long($this->appointment_date),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'notes' => $this->notes,
            'service' => new ServiceResource($this->service),
            'whatsapp' => [
                'confirmation' => WhatsAppMessage::url($this->patient_phone, WhatsAppMessage::confirmation($this->resource)),
                'reschedule' => WhatsAppMessage::url($this->patient_phone, WhatsAppMessage::reschedule($this->resource)),
                'reminder' => WhatsAppMessage::url($this->patient_phone, WhatsAppMessage::reminder($this->resource)),
            ],
        ];
    }
}

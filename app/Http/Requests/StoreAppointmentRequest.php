<?php

namespace App\Http\Requests;

use App\Models\Service;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => [
                'required',
                'integer',
                Rule::exists(Service::class, 'id')->where('is_active', true),
            ],
            'is_home_service' => ['required', 'boolean'],
            'appointment_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'patient_name' => ['required', 'string', 'min:3', 'max:120'],
            'patient_phone' => ['required', 'string', 'regex:/^\+[1-9]\d{9,14}$/'],
            'patient_email' => ['nullable', 'email', 'max:255'],
            'address_notes' => ['required_if:is_home_service,true', 'nullable', 'string', 'min:6', 'max:300'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'service_id.required' => 'Selecciona un servicio.',
            'appointment_date.required' => 'Selecciona una fecha.',
            'appointment_date.after_or_equal' => 'Selecciona una fecha a partir de hoy.',
            'appointment_time.required' => 'Selecciona un horario.',
            'patient_name.required' => 'Escribe tu nombre completo.',
            'patient_phone.regex' => 'Escribe un teléfono con lada, por ejemplo +52 33 1234 5678.',
            'address_notes.required_if' => 'Escribe la dirección donde te atenderemos.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $raw = (string) $this->input('patient_phone', '');
        $digits = preg_replace('/\D+/', '', $raw) ?? '';

        $phone = match (true) {
            str_starts_with(trim($raw), '+') => '+'.$digits,
            strlen($digits) === 10 => '+52'.$digits,
            strlen($digits) === 12 && str_starts_with($digits, '52') => '+'.$digits,
            default => $raw,
        };

        $this->merge([
            'patient_name' => trim((string) $this->input('patient_name', '')),
            'patient_phone' => $phone,
            'is_home_service' => $this->boolean('is_home_service'),
        ]);
    }
}

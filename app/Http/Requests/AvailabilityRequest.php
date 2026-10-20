<?php

namespace App\Http\Requests;

use App\Models\Service;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AvailabilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
            'service_id' => [
                'required',
                'integer',
                Rule::exists(Service::class, 'id')->where('is_active', true),
            ],
            'modality' => ['required', 'string', 'in:clinic,home'],
            'ignore' => ['nullable', 'integer', Rule::exists('appointments', 'id')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date.required' => 'Selecciona una fecha.',
            'service_id.required' => 'Selecciona un servicio.',
            'modality.in' => 'Selecciona una modalidad válida.',
        ];
    }
}

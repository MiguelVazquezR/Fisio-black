<?php

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_name' => fake()->name(),
            'patient_phone' => '+5233'.fake()->numerify('########'),
            'patient_email' => fake()->safeEmail(),
            'service_id' => Service::factory(),
            'is_home_service' => false,
            'address_notes' => null,
            'appointment_date' => now()->addDays(3)->toDateString(),
            'appointment_time' => '10:00:00',
            'status' => AppointmentStatus::Pending,
            'notes' => null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn () => ['status' => AppointmentStatus::Confirmed]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => AppointmentStatus::Cancelled]);
    }

    public function rescheduled(): static
    {
        return $this->state(fn () => ['status' => AppointmentStatus::Rescheduled]);
    }

    public function homeService(): static
    {
        return $this->state(fn () => [
            'is_home_service' => true,
            'address_notes' => fake()->address(),
        ]);
    }
}

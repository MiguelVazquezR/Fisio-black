<?php

use App\Enums\AppointmentStatus;
use App\Enums\ServiceCategory;
use App\Mail\NewAppointmentReceived;
use App\Models\Appointment;
use App\Models\BlockedDate;
use App\Models\Service;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;

function fisioNextOpenDate(): string
{
    $date = CarbonImmutable::today()->addDays(2);

    while ($date->isSunday()) {
        $date = $date->addDay();
    }

    return $date->toDateString();
}

test('the landing page lists active services and booking configuration', function () {
    Service::factory()->create([
        'name' => 'Rehabilitación física',
        'slug' => 'rehabilitacion-fisica',
        'category' => ServiceCategory::Rehabilitacion,
    ]);
    Service::factory()->quoteOnly()->create([
        'name' => 'Masaje terapéutico',
        'slug' => 'masaje-terapeutico',
    ]);
    Service::factory()->inactive()->create();

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('services', 2)
            ->has('categories', 4)
            ->where('booking.clinic_hours', ['08:00', '20:00'])
            ->where('booking.home_hours', ['08:00', '14:00'])
            ->has('blockedDates')
            ->where('recentAppointment', null)
        );
});

test('services without a price are returned without a price', function () {
    Service::factory()->quoteOnly()->create(['name' => 'Punción seca']);

    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('services.0.price', null)
            ->where('services.0.name', 'Punción seca')
        );
});

test('availability returns twelve daily slots in the clinic', function () {
    $service = Service::factory()->create(['duration_minutes' => 60]);
    $date = fisioNextOpenDate();

    $this->getJson(route('booking.availability', [
        'date' => $date,
        'service_id' => $service->id,
        'modality' => 'clinic',
    ]))
        ->assertOk()
        ->assertJsonPath('is_open', true)
        ->assertJsonPath('slots.0.value', '08:00')
        ->assertJsonCount(12, 'slots');
});

test('home service availability uses the morning window', function () {
    $service = Service::factory()->create(['duration_minutes' => 60]);
    $date = fisioNextOpenDate();

    $this->getJson(route('booking.availability', [
        'date' => $date,
        'service_id' => $service->id,
        'modality' => 'home',
    ]))
        ->assertOk()
        ->assertJsonPath('slots.0.value', '08:00')
        ->assertJsonPath('slots.5.value', '13:00')
        ->assertJsonCount(6, 'slots');
});

test('availability marks booked and pending slots with twelve hour labels', function () {
    $service = Service::factory()->create(['duration_minutes' => 60]);
    $date = fisioNextOpenDate();

    Appointment::factory()->confirmed()->create([
        'service_id' => $service->id,
        'appointment_date' => $date,
        'appointment_time' => '09:00:00',
    ]);
    Appointment::factory()->create([
        'service_id' => $service->id,
        'appointment_date' => $date,
        'appointment_time' => '11:00:00',
        'status' => AppointmentStatus::Pending,
    ]);

    $slots = collect($this->getJson(route('booking.availability', [
        'date' => $date,
        'service_id' => $service->id,
        'modality' => 'clinic',
    ]))
        ->assertOk()
        ->json('slots'))
        ->keyBy('value');

    expect($slots['08:00']['state'])->toBe('available')
        ->and($slots['08:00']['available'])->toBeTrue()
        ->and($slots['09:00']['state'])->toBe('booked')
        ->and($slots['09:00']['available'])->toBeFalse()
        ->and($slots['11:00']['state'])->toBe('pending')
        ->and($slots['11:00']['available'])->toBeFalse()
        ->and($slots['11:00']['label'])->toBe('11:00 a.m.')
        ->and($slots['17:00']['label'])->toBe('5:00 p.m.');
});

test('a pending appointment blocks its slot', function () {
    $service = Service::factory()->create(['duration_minutes' => 60]);
    $date = fisioNextOpenDate();

    Appointment::factory()->create([
        'service_id' => $service->id,
        'appointment_date' => $date,
        'appointment_time' => '09:00:00',
        'status' => AppointmentStatus::Pending,
    ]);

    $response = $this->post(route('appointments.store'), [
        'service_id' => $service->id,
        'is_home_service' => false,
        'appointment_date' => $date,
        'appointment_time' => '09:00',
        'patient_name' => 'Carlos Ruiz',
        'patient_phone' => '+523311112233',
    ]);

    $response->assertSessionHasErrors('appointment_time');
    expect(Appointment::query()->count())->toBe(1);
});

test('admins receive an email when an appointment is booked', function () {
    Mail::fake();

    $admin = User::factory()->create(['email' => 'agenda@fisioblack.com']);
    $service = Service::factory()->create(['duration_minutes' => 60]);
    $date = fisioNextOpenDate();

    $this->post(route('appointments.store'), [
        'service_id' => $service->id,
        'is_home_service' => false,
        'appointment_date' => $date,
        'appointment_time' => '09:00',
        'patient_name' => 'María López',
        'patient_phone' => '3312345678',
    ])->assertSessionHasNoErrors();

    Mail::assertSent(
        NewAppointmentReceived::class,
        fn (NewAppointmentReceived $mail): bool => $mail->hasTo($admin->email)
            && $mail->appointment->patient_name === 'María López',
    );
});

test('the new appointment email renders the appointment details', function () {
    $service = Service::factory()->create(['name' => 'Terapia neural']);
    $appointment = Appointment::factory()->create([
        'service_id' => $service->id,
        'patient_name' => 'María López',
        'patient_phone' => '+523312345678',
        'appointment_date' => '2026-11-05',
        'appointment_time' => '16:30:00',
        'is_home_service' => false,
    ])->load('service');

    (new NewAppointmentReceived($appointment))
        ->assertSeeInHtml('María López')
        ->assertSeeInHtml('Terapia neural')
        ->assertSeeInHtml('4:30 p.m.')
        ->assertSeeInHtml('FB-');
});

test('a patient can book an available slot', function () {
    $service = Service::factory()->create(['duration_minutes' => 60]);
    $date = fisioNextOpenDate();

    $response = $this->post(route('appointments.store'), [
        'service_id' => $service->id,
        'is_home_service' => false,
        'appointment_date' => $date,
        'appointment_time' => '09:00',
        'patient_name' => 'María López',
        'patient_phone' => '3312345678',
        'patient_email' => 'maria@example.com',
        'notes' => 'Dolor lumbar de tres semanas.',
    ]);

    $response->assertRedirect(route('home').'#agendar');
    $response->assertSessionHas('appointment');

    $summary = session('appointment');

    expect($summary['appointment_time'])->toBe('9:00 a.m.')
        ->and($summary['folio'])->toStartWith('FB-');

    $this->assertDatabaseHas('appointments', [
        'service_id' => $service->id,
        'patient_name' => 'María López',
        'patient_phone' => '+523312345678',
        'appointment_date' => $date,
        'appointment_time' => '09:00:00',
        'status' => AppointmentStatus::Pending->value,
    ]);
});

test('an occupied slot cannot be booked twice', function () {
    $service = Service::factory()->create(['duration_minutes' => 60]);
    $date = fisioNextOpenDate();

    Appointment::factory()->confirmed()->create([
        'service_id' => $service->id,
        'appointment_date' => $date,
        'appointment_time' => '09:00:00',
    ]);

    $response = $this->from(route('home'))->post(route('appointments.store'), [
        'service_id' => $service->id,
        'is_home_service' => false,
        'appointment_date' => $date,
        'appointment_time' => '09:00',
        'patient_name' => 'Carlos Ruiz',
        'patient_phone' => '+523311112233',
    ]);

    $response->assertSessionHasErrors('appointment_time');
    expect(Appointment::query()->count())->toBe(1);
});

test('cancelled appointments free their slot', function () {
    $service = Service::factory()->create(['duration_minutes' => 60]);
    $date = fisioNextOpenDate();

    Appointment::factory()->cancelled()->create([
        'service_id' => $service->id,
        'appointment_date' => $date,
        'appointment_time' => '09:00:00',
    ]);

    $response = $this->post(route('appointments.store'), [
        'service_id' => $service->id,
        'is_home_service' => false,
        'appointment_date' => $date,
        'appointment_time' => '09:00',
        'patient_name' => 'Carlos Ruiz',
        'patient_phone' => '+523311112233',
    ]);

    $response->assertSessionHasNoErrors();
    expect(Appointment::query()->count())->toBe(2);
});

test('sunday appointments are rejected', function () {
    $service = Service::factory()->create();
    $sunday = CarbonImmutable::today()->next(CarbonInterface::SUNDAY);

    $response = $this->post(route('appointments.store'), [
        'service_id' => $service->id,
        'is_home_service' => false,
        'appointment_date' => $sunday->toDateString(),
        'appointment_time' => '10:00',
        'patient_name' => 'Carlos Ruiz',
        'patient_phone' => '+523311112233',
    ]);

    $response->assertSessionHasErrors([
        'appointment_date' => 'El domingo no hay atención. Nuestro horario es de lunes a sábado.',
    ]);
    expect(Appointment::query()->count())->toBe(0);
});

test('blocked dates cannot be booked', function () {
    $service = Service::factory()->create();
    $date = fisioNextOpenDate();
    BlockedDate::factory()->create(['date' => $date]);

    $response = $this->post(route('appointments.store'), [
        'service_id' => $service->id,
        'is_home_service' => false,
        'appointment_date' => $date,
        'appointment_time' => '10:00',
        'patient_name' => 'Carlos Ruiz',
        'patient_phone' => '+523311112233',
    ]);

    $response->assertSessionHasErrors([
        'appointment_date' => 'Esa fecha no está disponible. Elige otro día.',
    ]);
});

test('home service bookings require an address', function () {
    $service = Service::factory()->create();
    $date = fisioNextOpenDate();

    $response = $this->post(route('appointments.store'), [
        'service_id' => $service->id,
        'is_home_service' => true,
        'appointment_date' => $date,
        'appointment_time' => '10:00',
        'patient_name' => 'Carlos Ruiz',
        'patient_phone' => '+523311112233',
    ]);

    $response->assertSessionHasErrors('address_notes');
});

test('home service bookings are limited to the morning window', function () {
    $service = Service::factory()->create(['duration_minutes' => 60]);
    $date = fisioNextOpenDate();

    $response = $this->post(route('appointments.store'), [
        'service_id' => $service->id,
        'is_home_service' => true,
        'appointment_date' => $date,
        'appointment_time' => '15:00',
        'patient_name' => 'Carlos Ruiz',
        'patient_phone' => '+523311112233',
        'address_notes' => 'Avenida Siempre Viva 123, Zapopan',
    ]);

    $response->assertSessionHasErrors('appointment_time');
});

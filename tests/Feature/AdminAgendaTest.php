<?php

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\BlockedDate;
use App\Models\Service;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Inertia\Testing\AssertableInertia as Assert;

function agendaOpenDate(): string
{
    $date = CarbonImmutable::today()->addDays(3);

    while ($date->isSunday()) {
        $date = $date->addDay();
    }

    return $date->toDateString();
}

test('guests cannot access the agenda', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
});

test('the agenda opens in the weekly view with totals for the week', function () {
    $service = Service::factory()->create(['name' => 'Fisioterapia deportiva']);
    $weekStart = CarbonImmutable::today()->startOfWeek(CarbonInterface::MONDAY);

    Appointment::factory()->create([
        'service_id' => $service->id,
        'appointment_date' => $weekStart->toDateString(),
        'appointment_time' => '10:00:00',
        'patient_name' => 'Ana Torres',
    ]);
    Appointment::factory()->create([
        'service_id' => $service->id,
        'appointment_date' => $weekStart->addDays(2)->toDateString(),
        'appointment_time' => '11:00:00',
        'patient_name' => 'Luis Pérez',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('admin.dashboard', ['date' => $weekStart->toDateString()]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Dashboard')
            ->has('appointments', 2)
            ->where('filters.date', $weekStart->toDateString())
            ->where('filters.view', 'week')
            ->where('counts.total', 2)
            ->where('appointments.0.patient_name', 'Ana Torres')
            ->where('appointments.1.patient_name', 'Luis Pérez')
            ->where('appointments.0.service.name', 'Fisioterapia deportiva')
            ->where('appointments.0.service.id', $service->id)
            ->missing('appointments.0.service.data')
            ->has('week', 7)
            ->has('statusOptions', 4)
        );
});

test('the agenda can be narrowed to a single day', function () {
    $service = Service::factory()->create();
    $weekStart = CarbonImmutable::today()->startOfWeek(CarbonInterface::MONDAY);

    Appointment::factory()->create([
        'service_id' => $service->id,
        'appointment_date' => $weekStart->toDateString(),
        'appointment_time' => '10:00:00',
        'patient_name' => 'Ana Torres',
    ]);
    Appointment::factory()->create([
        'service_id' => $service->id,
        'appointment_date' => $weekStart->addDays(2)->toDateString(),
        'appointment_time' => '11:00:00',
        'patient_name' => 'Luis Pérez',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('admin.dashboard', [
            'date' => $weekStart->toDateString(),
            'view' => 'day',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Dashboard')
            ->has('appointments', 1)
            ->where('appointments.0.patient_name', 'Ana Torres')
            ->where('filters.view', 'day')
            ->where('counts.total', 1)
        );
});

test('the agenda can filter by status', function () {
    $service = Service::factory()->create();
    $date = CarbonImmutable::today();

    Appointment::factory()->create([
        'service_id' => $service->id,
        'appointment_date' => $date->toDateString(),
        'appointment_time' => '09:00:00',
        'status' => AppointmentStatus::Pending,
    ]);
    Appointment::factory()->confirmed()->create([
        'service_id' => $service->id,
        'appointment_date' => $date->toDateString(),
        'appointment_time' => '10:00:00',
        'patient_name' => 'Paciente Confirmado',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('admin.dashboard', [
            'date' => $date->toDateString(),
            'status' => AppointmentStatus::Confirmed->value,
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('appointments', 1)
            ->where('appointments.0.patient_name', 'Paciente Confirmado')
            ->where('filters.status', AppointmentStatus::Confirmed->value)
        );
});

test('the week view includes appointments from the whole week', function () {
    $service = Service::factory()->create();
    $weekStart = CarbonImmutable::today()->startOfWeek(CarbonInterface::MONDAY);

    Appointment::factory()->create([
        'service_id' => $service->id,
        'appointment_date' => $weekStart->toDateString(),
        'appointment_time' => '09:00:00',
    ]);
    Appointment::factory()->create([
        'service_id' => $service->id,
        'appointment_date' => $weekStart->addDays(2)->toDateString(),
        'appointment_time' => '09:00:00',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('admin.dashboard', [
            'date' => $weekStart->toDateString(),
            'view' => 'week',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('appointments', 2)
            ->where('filters.view', 'week')
        );
});

test('an appointment can be confirmed', function () {
    $appointment = Appointment::factory()->create([
        'appointment_date' => agendaOpenDate(),
        'appointment_time' => '10:00:00',
    ]);

    $this->actingAs(User::factory()->create())
        ->from(route('admin.dashboard'))
        ->patch(route('admin.appointments.confirm', $appointment))
        ->assertRedirect(route('admin.dashboard'));

    expect($appointment->fresh()->status)->toBe(AppointmentStatus::Confirmed);
});

test('cancelled appointments cannot be confirmed', function () {
    $appointment = Appointment::factory()->cancelled()->create([
        'appointment_date' => agendaOpenDate(),
        'appointment_time' => '10:00:00',
    ]);

    $this->actingAs(User::factory()->create())
        ->from(route('admin.dashboard'))
        ->patch(route('admin.appointments.confirm', $appointment))
        ->assertSessionHasErrors('status');

    expect($appointment->fresh()->status)->toBe(AppointmentStatus::Cancelled);
});

test('an appointment can be rescheduled', function () {
    $service = Service::factory()->create(['duration_minutes' => 60]);
    $date = agendaOpenDate();
    $appointment = Appointment::factory()->create([
        'service_id' => $service->id,
        'appointment_date' => $date,
        'appointment_time' => '09:00:00',
    ]);

    $this->actingAs(User::factory()->create())
        ->from(route('admin.dashboard'))
        ->patch(route('admin.appointments.reschedule', $appointment), [
            'appointment_date' => $date,
            'appointment_time' => '11:00',
        ])
        ->assertRedirect(route('admin.dashboard'));

    $appointment->refresh();

    expect($appointment->status)->toBe(AppointmentStatus::Rescheduled);
    expect($appointment->appointment_date->toDateString())->toBe($date);
    expect($appointment->timeLabel())->toBe('11:00 a.m.');
});

test('an appointment cannot be rescheduled onto a taken slot', function () {
    $service = Service::factory()->create(['duration_minutes' => 60]);
    $date = agendaOpenDate();

    Appointment::factory()->confirmed()->create([
        'service_id' => $service->id,
        'appointment_date' => $date,
        'appointment_time' => '09:00:00',
    ]);

    $appointment = Appointment::factory()->create([
        'service_id' => $service->id,
        'appointment_date' => $date,
        'appointment_time' => '11:00:00',
    ]);

    $this->actingAs(User::factory()->create())
        ->from(route('admin.dashboard'))
        ->patch(route('admin.appointments.reschedule', $appointment), [
            'appointment_date' => $date,
            'appointment_time' => '09:00',
        ])
        ->assertSessionHasErrors('appointment_time');

    $appointment->refresh();

    expect($appointment->status)->toBe(AppointmentStatus::Pending);
    expect($appointment->timeLabel())->toBe('11:00 a.m.');
});

test('an appointment can be cancelled', function () {
    $appointment = Appointment::factory()->create([
        'appointment_date' => agendaOpenDate(),
        'appointment_time' => '10:00:00',
    ]);

    $this->actingAs(User::factory()->create())
        ->from(route('admin.dashboard'))
        ->patch(route('admin.appointments.cancel', $appointment))
        ->assertRedirect(route('admin.dashboard'));

    expect($appointment->fresh()->status)->toBe(AppointmentStatus::Cancelled);
});

test('blocked dates can be created and removed', function () {
    $user = User::factory()->create();
    $date = agendaOpenDate();

    $this->actingAs($user)
        ->from(route('admin.dashboard'))
        ->post(route('admin.blocked-dates.store'), [
            'date' => $date,
            'reason' => 'Capacitación',
        ])
        ->assertRedirect(route('admin.dashboard'));

    $this->assertDatabaseHas('blocked_dates', [
        'date' => $date,
        'reason' => 'Capacitación',
    ]);

    $blockedDate = BlockedDate::query()->firstOrFail();

    $this->actingAs($user)
        ->from(route('admin.dashboard'))
        ->delete(route('admin.blocked-dates.destroy', $blockedDate))
        ->assertRedirect(route('admin.dashboard'));

    $this->assertDatabaseMissing('blocked_dates', ['id' => $blockedDate->id]);
});

test('the same date cannot be blocked twice', function () {
    $date = agendaOpenDate();
    BlockedDate::factory()->create(['date' => $date]);

    $this->actingAs(User::factory()->create())
        ->from(route('admin.dashboard'))
        ->post(route('admin.blocked-dates.store'), ['date' => $date])
        ->assertSessionHasErrors('date');
});

test('admin availability ignores the appointment being rescheduled', function () {
    $service = Service::factory()->create(['duration_minutes' => 60]);
    $date = agendaOpenDate();
    $appointment = Appointment::factory()->create([
        'service_id' => $service->id,
        'appointment_date' => $date,
        'appointment_time' => '10:00:00',
    ]);

    $response = $this->actingAs(User::factory()->create())
        ->getJson(route('admin.availability', [
            'date' => $date,
            'service_id' => $service->id,
            'modality' => 'clinic',
            'ignore' => $appointment->id,
        ]))
        ->assertOk();

    $slot = collect($response->json('slots'))->firstWhere('value', '10:00');

    expect($slot['available'])->toBeTrue();
});

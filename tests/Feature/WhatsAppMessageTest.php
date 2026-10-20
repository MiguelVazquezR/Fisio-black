<?php

use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Service;
use App\Support\WhatsAppMessage;

test('the confirmation message includes the appointment details', function () {
    $service = Service::factory()->create(['name' => 'Punción seca']);
    $appointment = Appointment::factory()->create([
        'service_id' => $service->id,
        'patient_name' => 'María López',
        'patient_phone' => '+523312345678',
        'appointment_date' => '2026-11-05',
        'appointment_time' => '10:00:00',
        'is_home_service' => false,
    ]);
    $appointment->load('service');

    $message = WhatsAppMessage::confirmation($appointment);

    expect($message)
        ->toContain('María López')
        ->toContain('Punción seca')
        ->toContain('10:00')
        ->toContain('CONFIRMADA')
        ->toContain('FisioBlack')
        ->toContain('Avenida Marina Vallarta 636');
});

test('the reminder and reschedule messages are available', function () {
    $service = Service::factory()->create(['name' => 'Terapia neural']);
    $appointment = Appointment::factory()->create([
        'service_id' => $service->id,
        'appointment_date' => '2026-11-06',
        'appointment_time' => '12:00:00',
    ]);
    $appointment->load('service');

    expect(WhatsAppMessage::reminder($appointment))
        ->toContain('te recordamos tu cita')
        ->toContain('12:00');

    expect(WhatsAppMessage::reschedule($appointment))
        ->toContain('REAGENDADA')
        ->toContain('Nueva hora: 12:00');
});

test('whatsapp urls use the international phone number', function () {
    $url = WhatsAppMessage::url('+52 33 1234 5678', 'Hola, confirmo mi cita.');

    expect($url)
        ->toStartWith('https://wa.me/523312345678?text=')
        ->toContain(rawurlencode('Hola, confirmo mi cita.'));
});

test('home visits use the patient address in the message', function () {
    $service = Service::factory()->create(['name' => 'Terapia física a domicilio']);
    $appointment = Appointment::factory()->homeService()->create([
        'service_id' => $service->id,
        'appointment_date' => '2026-11-07',
        'appointment_time' => '09:00:00',
        'address_notes' => 'Calle Hidalgo 45, Zapopan',
    ]);
    $appointment->load('service');

    $message = WhatsAppMessage::confirmation($appointment);

    expect($message)
        ->toContain('A domicilio')
        ->toContain('Calle Hidalgo 45, Zapopan');
});

test('the appointment resource exposes whatsapp actions', function () {
    $service = Service::factory()->create(['name' => 'Kinesiology taping']);
    $appointment = Appointment::factory()->create([
        'service_id' => $service->id,
        'patient_phone' => '+523312345678',
        'appointment_date' => '2026-11-08',
        'appointment_time' => '13:00:00',
    ]);

    $payload = (new AppointmentResource($appointment->load('service')))->resolve();

    expect($payload['folio'])->toStartWith('FB-');
    expect($payload['whatsapp']['confirmation'])->toStartWith('https://wa.me/523312345678');
    expect($payload['whatsapp']['reschedule'])->toStartWith('https://wa.me/523312345678');
    expect($payload['whatsapp']['reminder'])->toStartWith('https://wa.me/523312345678');
});

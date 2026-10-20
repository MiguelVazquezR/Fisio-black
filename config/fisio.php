<?php

return [
    'clinic' => [
        'name' => 'FisioBlack',
        'tagline' => 'Terapia física, deportiva y quiropraxia',
        'address' => 'Avenida Marina Vallarta 636, Santa Margarita Residencial, 45138 Zapopan, Jal.',
        'phone' => '+52 33 2070 5157',
        'whatsapp' => env('FISIO_WHATSAPP', '+523320705157'),
        'email' => 'carlos95.cfyd@gmail.com',
        'maps_url' => 'https://maps.google.com/?q=Avenida+Marina+Vallarta+636,+Santa+Margarita+Residencial,+45138+Zapopan,+Jal.',
    ],

    'booking' => [
        'open_weekdays' => [1, 2, 3, 4, 5, 6],
        'clinic_hours' => ['08:00', '20:00'],
        'home_hours' => ['08:00', '14:00'],
        'slot_interval' => 60,
        'max_advance_days' => 60,
        'min_lead_minutes' => 60,
        'currency' => 'MXN',
        'timezone' => 'America/Mexico_City',
    ],
];

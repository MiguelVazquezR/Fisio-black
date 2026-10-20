<?php

namespace App\Http\Controllers\Booking;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AvailabilityRequest;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Mail\NewAppointmentReceived;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use App\Support\AppointmentSchedule;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AppointmentController extends Controller
{
    public function __construct(private readonly AppointmentSchedule $schedule) {}

    public function availability(AvailabilityRequest $request): JsonResponse
    {
        $service = Service::query()
            ->whereKey($request->integer('service_id'))
            ->where('is_active', true)
            ->firstOrFail();
        $date = CarbonImmutable::createFromFormat('Y-m-d', (string) $request->input('date'))->startOfDay();

        return response()->json($this->schedule->availability(
            $date,
            $service,
            $request->input('modality') === 'home',
        ));
    }

    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $service = Service::query()
            ->whereKey($data['service_id'])
            ->where('is_active', true)
            ->firstOrFail();
        $date = CarbonImmutable::parse($data['appointment_date'])->startOfDay();
        $isHomeService = (bool) $data['is_home_service'];

        $appointment = DB::transaction(function () use ($data, $service, $date, $isHomeService): Appointment {
            $this->schedule->ensureBookable(
                $date,
                $data['appointment_time'],
                $service,
                $isHomeService,
            );

            return Appointment::create([
                'patient_name' => $data['patient_name'],
                'patient_phone' => $data['patient_phone'],
                'patient_email' => $data['patient_email'] ?? null,
                'service_id' => $service->id,
                'is_home_service' => $isHomeService,
                'address_notes' => $data['address_notes'] ?? null,
                'appointment_date' => $date->toDateString(),
                'appointment_time' => $data['appointment_time'].':00',
                'status' => AppointmentStatus::Pending,
                'notes' => $data['notes'] ?? null,
            ]);
        });

        $appointment->load('service');

        $admins = User::query()->pluck('email')->filter()->all();

        if ($admins !== []) {
            try {
                Mail::to($admins)->send(new NewAppointmentReceived($appointment));
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        return redirect()
            ->to(route('home').'#agendar')
            ->with('appointment', (new AppointmentResource($appointment))->resolve());
    }
}

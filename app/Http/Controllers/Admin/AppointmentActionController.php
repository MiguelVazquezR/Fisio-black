<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\RescheduleAppointmentRequest;
use App\Models\Appointment;
use App\Support\AppointmentSchedule;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AppointmentActionController extends Controller
{
    public function __construct(private readonly AppointmentSchedule $schedule) {}

    public function confirm(Appointment $appointment): RedirectResponse
    {
        $this->ensureTransition($appointment, AppointmentStatus::Confirmed);

        $appointment->update(['status' => AppointmentStatus::Confirmed]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Cita confirmada. Ya puedes enviar el mensaje de WhatsApp.',
        ]);

        return back();
    }

    public function cancel(Appointment $appointment): RedirectResponse
    {
        if ($appointment->status === AppointmentStatus::Cancelled) {
            return back();
        }

        $appointment->update(['status' => AppointmentStatus::Cancelled]);

        Inertia::flash('toast', [
            'type' => 'warning',
            'message' => 'Cita cancelada. El horario quedó libre.',
        ]);

        return back();
    }

    public function reschedule(RescheduleAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $this->ensureTransition($appointment, AppointmentStatus::Rescheduled);

        $data = $request->validated();
        $date = CarbonImmutable::parse($data['appointment_date'])->startOfDay();
        $appointment->loadMissing('service');

        DB::transaction(function () use ($appointment, $date, $data): void {
            $this->schedule->ensureBookable(
                $date,
                $data['appointment_time'],
                $appointment->service,
                $appointment->is_home_service,
                $appointment,
                false,
            );

            $appointment->update([
                'appointment_date' => $date->toDateString(),
                'appointment_time' => $data['appointment_time'].':00',
                'status' => AppointmentStatus::Rescheduled,
            ]);
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Cita reagendada correctamente.',
        ]);

        return back();
    }

    private function ensureTransition(Appointment $appointment, AppointmentStatus $status): void
    {
        if (! $appointment->status->canTransitionTo($status)) {
            throw ValidationException::withMessages([
                'status' => 'No se puede modificar una cita cancelada.',
            ]);
        }
    }
}

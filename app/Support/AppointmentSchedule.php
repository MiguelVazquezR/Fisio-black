<?php

namespace App\Support;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\BlockedDate;
use App\Models\Service;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class AppointmentSchedule
{
    /**
     * @return array{date: string, is_open: bool, message: string|null, slots: array<int, array{value: string, label: string, available: bool, state: string}>}
     */
    public function availability(
        CarbonImmutable $date,
        Service $service,
        bool $isHomeService,
        ?Appointment $ignoring = null,
        bool $enforceLeadTime = true,
    ): array {
        $date = $date->startOfDay();
        $closedReason = $this->closedReason($date);

        if ($closedReason !== null) {
            return [
                'date' => $date->toDateString(),
                'is_open' => false,
                'message' => $closedReason,
                'slots' => [],
            ];
        }

        $appointments = $this->appointmentsOn($date, $ignoring);
        $earliest = CarbonImmutable::now()->addMinutes(
            (int) config('fisio.booking.min_lead_minutes')
        );

        $slots = [];

        foreach ($this->startTimes($date, $service, $isHomeService) as $time) {
            $start = $date->setTimeFromTimeString($time);
            $end = $start->addMinutes($service->duration_minutes);
            $overlapping = $this->overlapping($start, $end, $appointments);

            $state = match (true) {
                $overlapping->contains(fn (Appointment $appointment): bool => $appointment->status !== AppointmentStatus::Pending) => 'booked',
                $overlapping->isNotEmpty() => 'pending',
                $enforceLeadTime && $start->lessThan($earliest) => 'unavailable',
                default => 'available',
            };

            $slots[] = [
                'value' => $time,
                'label' => TimeLabel::twelveHour($time),
                'available' => $state === 'available',
                'state' => $state,
            ];
        }

        return [
            'date' => $date->toDateString(),
            'is_open' => true,
            'message' => null,
            'slots' => $slots,
        ];
    }

    public function ensureBookable(
        CarbonImmutable $date,
        string $time,
        Service $service,
        bool $isHomeService,
        ?Appointment $ignoring = null,
        bool $enforceLeadTime = true,
    ): void {
        $availability = $this->availability($date, $service, $isHomeService, $ignoring, $enforceLeadTime);

        if (! $availability['is_open']) {
            throw ValidationException::withMessages([
                'appointment_date' => $availability['message'] ?? 'La fecha seleccionada no está disponible.',
            ]);
        }

        $slot = collect($availability['slots'])->firstWhere('value', $time);

        if ($slot === null) {
            throw ValidationException::withMessages([
                'appointment_time' => 'Elige un horario dentro del horario de atención.',
            ]);
        }

        if (! $slot['available']) {
            throw ValidationException::withMessages([
                'appointment_time' => 'Ese horario ya no está disponible. Elige otro.',
            ]);
        }
    }

    private function closedReason(CarbonImmutable $date): ?string
    {
        $today = CarbonImmutable::today();

        if ($date->lessThan($today)) {
            return 'No es posible agendar en una fecha pasada.';
        }

        if ($date->greaterThan($today->addDays((int) config('fisio.booking.max_advance_days')))) {
            return 'Solo puedes agendar con hasta '.(int) config('fisio.booking.max_advance_days').' días de anticipación.';
        }

        if (! in_array($date->dayOfWeek, config('fisio.booking.open_weekdays'), true)) {
            return 'El domingo no hay atención. Nuestro horario es de lunes a sábado.';
        }

        $isBlocked = BlockedDate::query()
            ->whereDate('date', $date->toDateString())
            ->exists();

        if ($isBlocked) {
            return 'Esa fecha no está disponible. Elige otro día.';
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private function startTimes(CarbonImmutable $date, Service $service, bool $isHomeService): array
    {
        [$opensAt, $closesAt] = $isHomeService
            ? config('fisio.booking.home_hours')
            : config('fisio.booking.clinic_hours');

        $interval = (int) config('fisio.booking.slot_interval');
        $cursor = $date->setTimeFromTimeString($opensAt);
        $limit = $date->setTimeFromTimeString($closesAt);
        $times = [];

        while ($cursor->addMinutes($service->duration_minutes)->lessThanOrEqualTo($limit)) {
            $times[] = $cursor->format('H:i');
            $cursor = $cursor->addMinutes($interval);
        }

        return $times;
    }

    /**
     * @return Collection<int, Appointment>
     */
    private function appointmentsOn(CarbonImmutable $date, ?Appointment $ignoring): Collection
    {
        return Appointment::query()
            ->with('service')
            ->whereDate('appointment_date', $date->toDateString())
            ->whereIn('status', AppointmentStatus::activeValues())
            ->when($ignoring, fn ($query) => $query->whereKeyNot($ignoring->getKey()))
            ->get();
    }

    /**
     * @param  Collection<int, Appointment>  $appointments
     * @return Collection<int, Appointment>
     */
    private function overlapping(CarbonImmutable $start, CarbonImmutable $end, Collection $appointments): Collection
    {
        return $appointments
            ->filter(function (Appointment $appointment) use ($start, $end): bool {
                $appointmentStart = $appointment->startsAt();
                $appointmentEnd = $appointmentStart->addMinutes($appointment->service->duration_minutes);

                return $start->lessThan($appointmentEnd) && $end->greaterThan($appointmentStart);
            })
            ->values();
    }
}

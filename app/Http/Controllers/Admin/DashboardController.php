<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AvailabilityRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\BlockedDate;
use App\Models\Service;
use App\Support\AppointmentSchedule;
use App\Support\DateLabel;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private readonly AppointmentSchedule $schedule) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
            'status' => ['nullable', Rule::enum(AppointmentStatus::class)],
            'view' => ['nullable', 'in:day,week'],
        ]);

        $date = filled($validated['date'] ?? null)
            ? CarbonImmutable::createFromFormat('Y-m-d', $validated['date'])->startOfDay()
            : CarbonImmutable::today();

        $status = filled($validated['status'] ?? null)
            ? AppointmentStatus::from($validated['status'])
            : null;

        $view = $validated['view'] ?? 'day';

        $weekStart = $date->startOfWeek(CarbonInterface::MONDAY);
        $rangeStart = $view === 'week' ? $weekStart : $date;
        $rangeEnd = $view === 'week' ? $weekStart->addDays(6) : $date;

        $appointments = Appointment::query()
            ->with('service')
            ->whereBetween('appointment_date', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
            ->when($status, fn (Builder $query) => $query->where('status', $status->value))
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        $statusCounts = Appointment::query()
            ->whereDate('appointment_date', $date->toDateString())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $weekCounts = Appointment::query()
            ->whereBetween('appointment_date', [$weekStart->toDateString(), $weekStart->addDays(6)->toDateString()])
            ->active()
            ->selectRaw('appointment_date, count(*) as total')
            ->groupBy('appointment_date')
            ->pluck('total', 'appointment_date')
            ->mapWithKeys(fn ($total, $day) => [
                CarbonImmutable::parse((string) $day)->toDateString() => (int) $total,
            ]);

        $dayAppointments = Appointment::query()
            ->whereDate('appointment_date', $date->toDateString());

        return Inertia::render('admin/Dashboard', [
            'appointments' => AppointmentResource::collection($appointments)->toArray($request),
            'filters' => [
                'date' => $date->toDateString(),
                'status' => $status?->value,
                'view' => $view,
            ],
            'dateLabel' => DateLabel::long($date),
            'counts' => [
                'total' => (int) $statusCounts->sum(),
                'pending' => (int) ($statusCounts[AppointmentStatus::Pending->value] ?? 0),
                'confirmed' => (int) ($statusCounts[AppointmentStatus::Confirmed->value] ?? 0),
                'home' => (clone $dayAppointments)->active()->where('is_home_service', true)->count(),
            ],
            'week' => $this->weekDays($weekStart, $date, $weekCounts),
            'statusOptions' => collect(AppointmentStatus::cases())
                ->map(fn (AppointmentStatus $appointmentStatus) => [
                    'value' => $appointmentStatus->value,
                    'label' => $appointmentStatus->label(),
                ])
                ->values(),
            'blockedDates' => BlockedDate::query()
                ->whereDate('date', '>=', today()->toDateString())
                ->orderBy('date')
                ->get()
                ->map(fn (BlockedDate $blockedDate) => [
                    'id' => $blockedDate->id,
                    'date' => $blockedDate->date->toDateString(),
                    'label' => DateLabel::dayMonth($blockedDate->date),
                    'reason' => $blockedDate->reason,
                ])
                ->values(),
        ]);
    }

    public function availability(AvailabilityRequest $request): JsonResponse
    {
        $service = Service::query()->whereKey($request->integer('service_id'))->firstOrFail();

        $ignoring = $request->filled('ignore')
            ? Appointment::query()->find($request->integer('ignore'))
            : null;

        $date = CarbonImmutable::createFromFormat('Y-m-d', (string) $request->input('date'))->startOfDay();

        return response()->json($this->schedule->availability(
            $date,
            $service,
            $request->input('modality') === 'home',
            $ignoring,
            false,
        ));
    }

    /**
     * @param  Collection<string, int>  $weekCounts
     * @return array<int, array<string, mixed>>
     */
    private function weekDays(CarbonImmutable $weekStart, CarbonImmutable $selected, Collection $weekCounts): array
    {
        return collect(range(0, 6))
            ->map(function (int $offset) use ($weekStart, $selected, $weekCounts): array {
                $day = $weekStart->addDays($offset);

                return [
                    'date' => $day->toDateString(),
                    'weekday' => DateLabel::weekday($day),
                    'day' => $day->day,
                    'is_selected' => $day->isSameDay($selected),
                    'is_today' => $day->isToday(),
                    'count' => $weekCounts->get($day->toDateString(), 0),
                ];
            })
            ->all();
    }
}

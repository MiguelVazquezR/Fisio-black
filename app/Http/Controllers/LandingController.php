<?php

namespace App\Http\Controllers;

use App\Enums\ServiceCategory;
use App\Http\Resources\ServiceResource;
use App\Models\BlockedDate;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $services = Service::query()
            ->active()
            ->orderBy('name')
            ->get();

        return Inertia::render('Welcome', [
            'services' => ServiceResource::collection($services)->toArray($request),
            'categories' => collect(ServiceCategory::cases())
                ->map(fn (ServiceCategory $category) => [
                    'value' => $category->value,
                    'label' => $category->label(),
                ])
                ->values(),
            'blockedDates' => BlockedDate::query()
                ->whereDate('date', '>=', today()->toDateString())
                ->orderBy('date')
                ->get()
                ->map(fn (BlockedDate $blockedDate) => $blockedDate->date->toDateString())
                ->values(),
            'selectedService' => $request->query('servicio'),
            'clinic' => config('fisio.clinic'),
            'booking' => [
                'clinic_hours' => config('fisio.booking.clinic_hours'),
                'home_hours' => config('fisio.booking.home_hours'),
                'max_advance_days' => config('fisio.booking.max_advance_days'),
                'min_lead_minutes' => config('fisio.booking.min_lead_minutes'),
                'currency' => config('fisio.booking.currency'),
            ],
        ]);
    }
}

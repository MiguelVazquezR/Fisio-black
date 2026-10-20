<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedDate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlockedDateController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today', 'unique:blocked_dates,date'],
            'reason' => ['nullable', 'string', 'max:120'],
        ]);

        BlockedDate::create($validated);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Fecha bloqueada. Ya no aparecerá disponible para agendar.',
        ]);

        return back();
    }

    public function destroy(BlockedDate $blockedDate): RedirectResponse
    {
        $blockedDate->delete();

        Inertia::flash('toast', [
            'type' => 'info',
            'message' => 'La fecha volvió a estar disponible.',
        ]);

        return back();
    }
}

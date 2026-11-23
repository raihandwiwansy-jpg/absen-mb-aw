<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\OrganizationalPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display organization and system settings.
     */
    public function index(): View
    {
        $periods = OrganizationalPeriod::orderByDesc('start_date')->get();
        $activePeriod = OrganizationalPeriod::getActive();

        return view('settings.index', compact('periods', 'activePeriod'));
    }

    /**
     * Store new organizational period.
     */
    public function storePeriod(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['boolean'],
        ]);

        if (! empty($validated['is_active'])) {
            OrganizationalPeriod::query()->update(['is_active' => false]);
        }

        $period = OrganizationalPeriod::create([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $request->boolean('is_active'),
        ]);

        ActivityLog::log([
            'action' => 'create_period',
            'description' => "Menambahkan periode organisasi baru: {$period->name}",
            'subject_type' => OrganizationalPeriod::class,
            'subject_id' => $period->id,
        ]);

        return redirect()->route('settings.index')->with('success', "Periode {$period->name} berhasil ditambahkan.");
    }

    /**
     * Set a period as active.
     */
    public function setActivePeriod(OrganizationalPeriod $period): RedirectResponse
    {
        OrganizationalPeriod::query()->update(['is_active' => false]);
        $period->update(['is_active' => true]);

        ActivityLog::log([
            'action' => 'set_active_period',
            'description' => "Mengaktifkan periode organisasi: {$period->name}",
            'subject_type' => OrganizationalPeriod::class,
            'subject_id' => $period->id,
        ]);

        return redirect()->route('settings.index')->with('success', "Periode {$period->name} sekarang aktif.");
    }
}

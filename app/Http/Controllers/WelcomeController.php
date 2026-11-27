<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use App\Models\Division;
use App\Models\Instrument;
use App\Models\Member;
use App\Models\OrganizationalPeriod;
use App\Models\PracticeSchedule;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /**
     * Display the public landing and schedule showcase page.
     */
    public function index(): View
    {
        $today = today();
        $tomorrow = today()->addDay();

        // 1. Prioritize: Tomorrow's practice first (as requested by user),
        // or Today's practice, or closest upcoming schedule
        $featuredSchedule = PracticeSchedule::with(['officer', 'activeSession'])
            ->whereDate('date', $tomorrow)
            ->first();

        if (! $featuredSchedule) {
            $featuredSchedule = PracticeSchedule::with(['officer', 'activeSession'])
                ->whereDate('date', $today)
                ->first();
        }

        if (! $featuredSchedule) {
            $featuredSchedule = PracticeSchedule::with(['officer', 'activeSession'])
                ->where('date', '>=', $today)
                ->orderBy('date')
                ->orderBy('start_time')
                ->first();
        }

        // If no upcoming schedule exists at all, get the most recent one for showcase
        if (! $featuredSchedule) {
            $featuredSchedule = PracticeSchedule::with(['officer', 'activeSession'])
                ->orderByDesc('date')
                ->first();
        }

        // 2. Other upcoming schedules (excluding the featured one)
        $upcomingSchedules = PracticeSchedule::with(['officer'])
            ->where('date', '>=', $today)
            ->when($featuredSchedule, fn ($q) => $q->where('id', '!=', $featuredSchedule->id))
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(3)
            ->get();

        // 3. Active Attendance Session — only show banner if still within practice end_time.
        // Auto-close stale sessions whose schedule end time has already passed.
        AttendanceSession::closeStaleSessions();

        $activeSession = AttendanceSession::where('is_active', true)
            ->with(['practiceSchedule'])
            ->first();

        // 4. Master Data Statistics
        $totalMembers = Member::where('status', 'active')->count();
        $totalDivisions = Division::count();
        $totalInstruments = Instrument::count();
        $activePeriod = OrganizationalPeriod::where('is_active', true)->first();

        // 5. Divisions showcase
        $divisions = Division::withCount('members')->get();

        return view('welcome', compact(
            'featuredSchedule',
            'upcomingSchedules',
            'activeSession',
            'totalMembers',
            'totalDivisions',
            'totalInstruments',
            'activePeriod',
            'divisions'
        ));
    }
}

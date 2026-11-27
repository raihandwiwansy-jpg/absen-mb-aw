<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\OrganizationalPeriod;
use App\Models\PracticeSchedule;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PracticeScheduleController extends Controller
{
    /**
     * Display a listing of practice schedules.
     */
    public function index(Request $request): View
    {
        $query = PracticeSchedule::with(['officer', 'organizationalPeriod', 'attendanceSessions'])
            ->orderByDesc('date')
            ->orderBy('start_time');

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->month) {
            $query->whereMonth('date', date('m', strtotime($request->month)))
                ->whereYear('date', date('Y', strtotime($request->month)));
        }

        $schedules = $query->paginate(10)->withQueryString();
        $upcomingSchedules = PracticeSchedule::where('date', '>=', today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        return view('schedules.index', compact('schedules', 'upcomingSchedules'));
    }

    /**
     * Show form for creating a new practice schedule.
     */
    public function create(): View
    {
        $periods = OrganizationalPeriod::where('is_active', true)->get();
        $officers = User::role(['super_admin', 'administrator', 'attendance_officer', 'coach'])->get();

        return view('schedules.create', compact('periods', 'officers'));
    }

    /**
     * Store newly created schedule.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:regular,additional,competition'],
            'date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required', 'after:start_time'],
            'location' => ['required', 'string', 'max:255'],
            'dress_code' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'officer_id' => ['nullable', 'exists:users,id'],
            'organizational_period_id' => ['nullable', 'exists:organizational_periods,id'],
        ], [
            'end_time.after' => 'Jam selesai harus lebih akhir dari jam mulai.',
        ]);

        $schedule = PracticeSchedule::create($validated);

        ActivityLog::log([
            'action' => 'create_schedule',
            'description' => "Membuat jadwal latihan: {$schedule->title} pada {$schedule->date->format('d/m/Y')}",
            'subject_type' => PracticeSchedule::class,
            'subject_id' => $schedule->id,
        ]);

        return redirect()->route('schedules.show', $schedule)->with('success', 'Jadwal latihan berhasil dibuat.');
    }

    /**
     * Display the specified schedule.
     */
    public function show(PracticeSchedule $schedule): View
    {
        $schedule->load([
            'officer',
            'organizationalPeriod',
            'attendanceSessions' => fn ($q) => $q->with('openedBy', 'attendanceToken')
                ->withCount([
                    'attendances as present_count' => fn ($a) => $a->where('status', 'present'),
                    'attendances as late_count' => fn ($a) => $a->where('status', 'late'),
                    'attendances as absent_count' => fn ($a) => $a->where('status', 'absent'),
                ]),
            'permissionRequests.member',
        ]);

        return view('schedules.show', compact('schedule'));
    }

    /**
     * Show form for editing schedule.
     */
    public function edit(PracticeSchedule $schedule): View
    {
        $periods = OrganizationalPeriod::where('is_active', true)->get();
        $officers = User::role(['super_admin', 'administrator', 'attendance_officer', 'coach'])->get();

        return view('schedules.edit', compact('schedule', 'periods', 'officers'));
    }

    /**
     * Update schedule.
     */
    public function update(Request $request, PracticeSchedule $schedule): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:regular,additional,competition'],
            'date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required', 'after:start_time'],
            'location' => ['required', 'string', 'max:255'],
            'dress_code' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'officer_id' => ['nullable', 'exists:users,id'],
            'organizational_period_id' => ['nullable', 'exists:organizational_periods,id'],
        ], [
            'end_time.after' => 'Jam selesai harus lebih akhir dari jam mulai.',
        ]);

        $schedule->update($validated);

        ActivityLog::log([
            'action' => 'update_schedule',
            'description' => "Memperbarui jadwal latihan: {$schedule->title}",
            'subject_type' => PracticeSchedule::class,
            'subject_id' => $schedule->id,
        ]);

        return redirect()->route('schedules.show', $schedule)->with('success', 'Jadwal latihan berhasil diperbarui.');
    }

    /**
     * Remove the specified schedule.
     */
    public function destroy(PracticeSchedule $schedule): RedirectResponse
    {
        $title = $schedule->title;
        $schedule->delete();

        ActivityLog::log([
            'action' => 'delete_schedule',
            'description' => "Menghapus jadwal latihan: {$title}",
        ]);

        return redirect()->route('schedules.index')->with('success', "Jadwal latihan {$title} berhasil dihapus.");
    }

    /**
     * Return JSON for calendar view.
     */
    public function calendar(): JsonResponse
    {
        $schedules = PracticeSchedule::all()->map(function (PracticeSchedule $schedule) {
            $colors = [
                'regular' => '#101D3B',
                'additional' => '#C6A044',
                'competition' => '#dc2626',
            ];

            return [
                'id' => $schedule->id,
                'title' => $schedule->title,
                'start' => $schedule->date->format('Y-m-d').'T'.$schedule->start_time,
                'end' => $schedule->date->format('Y-m-d').'T'.$schedule->end_time,
                'backgroundColor' => $colors[$schedule->type] ?? '#101D3B',
                'borderColor' => '#C6A044',
                'url' => route('schedules.show', $schedule),
                'location' => $schedule->location,
                'dress_code' => $schedule->dress_code,
                'type' => $schedule->type_label,
            ];
        });

        return response()->json($schedules);
    }
}

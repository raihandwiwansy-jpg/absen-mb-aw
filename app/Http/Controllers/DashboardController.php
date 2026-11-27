<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Division;
use App\Models\Member;
use App\Models\PermissionRequest;
use App\Models\PracticeSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main administrative dashboard.
     */
    public function index(): View
    {
        $memberStatusCounts = Member::query()
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $totalMembers = (int) $memberStatusCounts->sum();
        $activeMembers = (int) ($memberStatusCounts['active'] ?? 0);

        $today = Carbon::today();

        // Today's attendance counts (single grouped query)
        $todayCounts = DB::table('attendances')
            ->join('attendance_sessions', 'attendances.attendance_session_id', '=', 'attendance_sessions.id')
            ->join('practice_schedules', 'attendance_sessions.practice_schedule_id', '=', 'practice_schedules.id')
            ->whereNull('practice_schedules.deleted_at')
            ->whereDate('practice_schedules.date', $today->toDateString())
            ->select('attendances.status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('attendances.status')
            ->pluck('aggregate', 'status');

        $presentToday = (int) ($todayCounts['present'] ?? 0);
        $lateToday = (int) ($todayCounts['late'] ?? 0);
        $permissionToday = (int) ($todayCounts['permission'] ?? 0);
        $sickToday = (int) ($todayCounts['sick'] ?? 0);
        $absentToday = (int) ($todayCounts['absent'] ?? 0);

        // Overall attendance percentage (single grouped query)
        $overallCounts = Attendance::query()
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $totalRecorded = (int) $overallCounts->sum();
        $attendedCount = (int) (($overallCounts['present'] ?? 0) + ($overallCounts['late'] ?? 0));
        $overallPercentage = $totalRecorded > 0 ? round(($attendedCount / $totalRecorded) * 100, 1) : 0;

        // Fetch last 6 months attendance aggregates in one query for both weekly & monthly charts
        $sixMonthsStart = $today->copy()->subMonths(5)->startOfMonth();
        $chartRows = DB::table('attendances')
            ->join('attendance_sessions', 'attendances.attendance_session_id', '=', 'attendance_sessions.id')
            ->join('practice_schedules', 'attendance_sessions.practice_schedule_id', '=', 'practice_schedules.id')
            ->whereNull('practice_schedules.deleted_at')
            ->whereDate('practice_schedules.date', '>=', $sixMonthsStart->toDateString())
            ->select('practice_schedules.date', 'attendances.status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('practice_schedules.date', 'attendances.status')
            ->get();

        $dailyMap = [];
        $monthlyMap = [];
        foreach ($chartRows as $row) {
            $dateStr = substr((string) $row->date, 0, 10);
            $monthKey = substr($dateStr, 0, 7);
            $count = (int) $row->aggregate;

            $dailyMap[$dateStr][$row->status] = ($dailyMap[$dateStr][$row->status] ?? 0) + $count;
            $monthlyMap[$monthKey][$row->status] = ($monthlyMap[$monthKey][$row->status] ?? 0) + $count;
        }

        // Weekly attendance chart data (last 7 days)
        $weeklyLabels = [];
        $weeklyPresent = [];
        $weeklyLate = [];
        $weeklyPermission = [];
        $weeklySick = [];
        $weeklyAbsent = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $dateKey = $date->toDateString();
            $bucket = $dailyMap[$dateKey] ?? [];

            $weeklyLabels[] = $date->translatedFormat('d M');
            $weeklyPresent[] = (int) ($bucket['present'] ?? 0);
            $weeklyLate[] = (int) ($bucket['late'] ?? 0);
            $weeklyPermission[] = (int) ($bucket['permission'] ?? 0);
            $weeklySick[] = (int) ($bucket['sick'] ?? 0);
            $weeklyAbsent[] = (int) ($bucket['absent'] ?? 0);
        }

        // Monthly attendance chart data (last 6 months)
        $monthlyLabels = [];
        $monthlyPresent = [];
        $monthlyAbsent = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = $today->copy()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $bucket = $monthlyMap[$monthKey] ?? [];

            $monthlyLabels[] = $month->translatedFormat('F Y');
            $monthlyPresent[] = (int) (($bucket['present'] ?? 0) + ($bucket['late'] ?? 0));
            $monthlyAbsent[] = (int) (($bucket['absent'] ?? 0) + ($bucket['permission'] ?? 0) + ($bucket['sick'] ?? 0));
        }

        // Attendance statistics by division (2 queries instead of N*3)
        $divisions = Division::withCount(['members' => function ($q) {
            $q->where('status', 'active');
        }])->get();

        $divisionAttendanceRows = DB::table('attendances')
            ->join('members', 'attendances.member_id', '=', 'members.id')
            ->whereNull('members.deleted_at')
            ->whereNotNull('members.division_id')
            ->select('members.division_id', 'attendances.status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('members.division_id', 'attendances.status')
            ->get();

        $divisionAttendanceMap = [];
        foreach ($divisionAttendanceRows as $row) {
            $divId = (int) $row->division_id;
            $count = (int) $row->aggregate;
            $divisionAttendanceMap[$divId]['total'] = ($divisionAttendanceMap[$divId]['total'] ?? 0) + $count;
            if (in_array($row->status, ['present', 'late'], true)) {
                $divisionAttendanceMap[$divId]['present'] = ($divisionAttendanceMap[$divId]['present'] ?? 0) + $count;
            }
        }

        $divisionStats = $divisions->map(function (Division $division) use ($divisionAttendanceMap) {
            $totals = $divisionAttendanceMap[$division->id] ?? ['total' => 0, 'present' => 0];
            $percentage = $totals['total'] > 0 ? round(($totals['present'] / $totals['total']) * 100, 1) : 0;

            return [
                'name' => $division->name,
                'color' => $division->color ?? '#101D3B',
                'active_members' => $division->members_count,
                'percentage' => $percentage,
            ];
        });

        // Most disciplined members (single query using withCount instead of N*2 queries)
        $topMembers = Member::with(['division', 'instrument'])
            ->where('status', 'active')
            ->withCount([
                'attendances as total_attendances_count',
                'attendances as present_attendances_count' => fn ($q) => $q->whereIn('status', ['present', 'late']),
            ])
            ->get()
            ->map(function (Member $member) {
                $total = (int) $member->total_attendances_count;
                $present = (int) $member->present_attendances_count;
                $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

                return [
                    'member' => $member,
                    'total' => $total,
                    'present' => $present,
                    'percentage' => $percentage,
                ];
            })
            ->sortByDesc('percentage')
            ->take(5);

        // Recent organizational activities
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->limit(8)
            ->get();

        // Upcoming practice schedules
        $upcomingSchedules = PracticeSchedule::with(['officer', 'activeSession'])
            ->where('date', '>=', $today)
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        // Pending permission requests count
        $pendingPermissionsCount = PermissionRequest::where('status', 'pending')->count();

        return view('dashboard', compact(
            'totalMembers',
            'activeMembers',
            'presentToday',
            'lateToday',
            'absentToday',
            'permissionToday',
            'sickToday',
            'overallPercentage',
            'weeklyLabels',
            'weeklyPresent',
            'weeklyLate',
            'weeklyPermission',
            'weeklySick',
            'weeklyAbsent',
            'monthlyLabels',
            'monthlyPresent',
            'monthlyAbsent',
            'divisionStats',
            'topMembers',
            'recentActivities',
            'upcomingSchedules',
            'pendingPermissionsCount'
        ));
    }
}

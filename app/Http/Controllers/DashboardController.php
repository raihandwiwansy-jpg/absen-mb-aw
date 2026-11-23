<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Division;
use App\Models\Member;
use App\Models\PermissionRequest;
use App\Models\PracticeSchedule;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main administrative dashboard.
     */
    public function index(): View
    {
        $totalMembers = Member::count();
        $activeMembers = Member::where('status', 'active')->count();

        // Today's attendance session
        $today = Carbon::today();
        $todaySessions = AttendanceSession::whereHas('practiceSchedule', function ($query) use ($today) {
            $query->whereDate('date', $today);
        })->pluck('id');

        $presentToday = Attendance::whereIn('attendance_session_id', $todaySessions)->where('status', 'present')->count();
        $lateToday = Attendance::whereIn('attendance_session_id', $todaySessions)->where('status', 'late')->count();
        $permissionToday = Attendance::whereIn('attendance_session_id', $todaySessions)->where('status', 'permission')->count();
        $sickToday = Attendance::whereIn('attendance_session_id', $todaySessions)->where('status', 'sick')->count();
        $absentToday = Attendance::whereIn('attendance_session_id', $todaySessions)->where('status', 'absent')->count();

        // Overall attendance percentage
        $totalRecorded = Attendance::count();
        $attendedCount = Attendance::whereIn('status', ['present', 'late'])->count();
        $overallPercentage = $totalRecorded > 0 ? round(($attendedCount / $totalRecorded) * 100, 1) : 0;

        // Weekly attendance chart data (last 7 days)
        $weeklyLabels = [];
        $weeklyPresent = [];
        $weeklyLate = [];
        $weeklyPermission = [];
        $weeklySick = [];
        $weeklyAbsent = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weeklyLabels[] = $date->translatedFormat('d M');

            $sessionIds = AttendanceSession::whereHas('practiceSchedule', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })->pluck('id');

            $weeklyPresent[] = Attendance::whereIn('attendance_session_id', $sessionIds)->where('status', 'present')->count();
            $weeklyLate[] = Attendance::whereIn('attendance_session_id', $sessionIds)->where('status', 'late')->count();
            $weeklyPermission[] = Attendance::whereIn('attendance_session_id', $sessionIds)->where('status', 'permission')->count();
            $weeklySick[] = Attendance::whereIn('attendance_session_id', $sessionIds)->where('status', 'sick')->count();
            $weeklyAbsent[] = Attendance::whereIn('attendance_session_id', $sessionIds)->where('status', 'absent')->count();
        }

        // Monthly attendance chart data (last 6 months)
        $monthlyLabels = [];
        $monthlyPresent = [];
        $monthlyAbsent = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::today()->subMonths($i);
            $monthlyLabels[] = $month->translatedFormat('F Y');

            $monthSessionIds = AttendanceSession::whereHas('practiceSchedule', function ($query) use ($month) {
                $query->whereYear('date', $month->year)->whereMonth('date', $month->month);
            })->pluck('id');

            $monthlyPresent[] = Attendance::whereIn('attendance_session_id', $monthSessionIds)->whereIn('status', ['present', 'late'])->count();
            $monthlyAbsent[] = Attendance::whereIn('attendance_session_id', $monthSessionIds)->whereIn('status', ['absent', 'permission', 'sick'])->count();
        }

        // Attendance statistics by division
        $divisions = Division::withCount(['members' => function ($q) {
            $q->where('status', 'active');
        }])->get();

        $divisionStats = $divisions->map(function (Division $division) {
            $memberIds = $division->members()->pluck('id');
            $totalAttendances = Attendance::whereIn('member_id', $memberIds)->count();
            $presentCount = Attendance::whereIn('member_id', $memberIds)->whereIn('status', ['present', 'late'])->count();
            $percentage = $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 1) : 0;

            return [
                'name' => $division->name,
                'color' => $division->color ?? '#101D3B',
                'active_members' => $division->members_count,
                'percentage' => $percentage,
            ];
        });

        // Most disciplined members (highest attendance rate, minimum 1 attendance)
        $topMembers = Member::with(['division', 'instrument'])
            ->where('status', 'active')
            ->get()
            ->map(function (Member $member) {
                $total = $member->attendances()->count();
                $present = $member->attendances()->whereIn('status', ['present', 'late'])->count();
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
            ->where('date', '>=', Carbon::today())
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

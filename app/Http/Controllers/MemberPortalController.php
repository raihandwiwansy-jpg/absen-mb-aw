<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use App\Models\Member;
use App\Models\PracticeSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MemberPortalController extends Controller
{
    /**
     * Dedicated member dashboard.
     */
    public function dashboard(): View
    {
        $user = auth()->user();
        $member = $user->member;

        if (! $member) {
            return view('portal.no-member');
        }

        $member->load(['division', 'instrument', 'position', 'organizationalPeriod']);

        // Personal stats in one grouped query
        $statusCounts = $member->attendances()
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $totalSessions = (int) $statusCounts->sum();
        $lateCount = (int) ($statusCounts['late'] ?? 0);
        $presentCount = (int) (($statusCounts['present'] ?? 0) + $lateCount);
        $permissionCount = (int) ($statusCounts['permission'] ?? 0);
        $sickCount = (int) ($statusCounts['sick'] ?? 0);
        $absentCount = (int) ($statusCounts['absent'] ?? 0);
        $attendancePercentage = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100, 1) : 0.0;

        // Recent personal attendances
        $recentAttendances = $member->attendances()
            ->with(['attendanceSession.practiceSchedule', 'recordedBy'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Auto-close stale sessions before checking active session banner
        AttendanceSession::closeStaleSessions();

        // Active Attendance Session (if opened by officer/coach/admin)
        $activeSession = AttendanceSession::where('is_active', true)
            ->with(['practiceSchedule'])
            ->first();

        // Upcoming schedules (prioritize future schedules, fallback to most recent so newly created schedules are always visible)
        $upcomingSchedules = PracticeSchedule::with(['officer', 'attendanceSessions'])
            ->where('date', '>=', today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        if ($upcomingSchedules->isEmpty()) {
            $upcomingSchedules = PracticeSchedule::with(['officer', 'attendanceSessions'])
                ->orderByDesc('date')
                ->limit(5)
                ->get();
        }

        // Recent permission requests
        $recentRequests = $member->permissionRequests()
            ->with('practiceSchedule')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('portal.dashboard', compact(
            'member',
            'totalSessions',
            'presentCount',
            'lateCount',
            'permissionCount',
            'sickCount',
            'absentCount',
            'attendancePercentage',
            'recentAttendances',
            'upcomingSchedules',
            'recentRequests',
            'activeSession'
        ));
    }

    /**
     * View all practice schedules and calendar for members.
     */
    public function schedules(Request $request): View
    {
        $user = auth()->user();
        $member = $user->member;

        if (! $member) {
            return view('portal.no-member');
        }

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

        AttendanceSession::closeStaleSessions();

        $activeSession = AttendanceSession::where('is_active', true)
            ->with(['practiceSchedule'])
            ->first();

        return view('portal.schedules', compact('member', 'schedules', 'activeSession'));
    }

    /**
     * View personal member profile.
     */
    public function profile(): View
    {
        $user = auth()->user();
        $member = $user->member;

        if (! $member) {
            return view('portal.no-member');
        }

        $member->load(['division', 'instrument', 'position', 'organizationalPeriod']);

        return view('portal.profile', compact('member'));
    }

    /**
     * View complete personal attendance history.
     */
    public function attendance(Request $request): View
    {
        $user = auth()->user();
        $member = $user->member;

        if (! $member) {
            return view('portal.no-member');
        }

        $query = $member->attendances()
            ->with(['attendanceSession.practiceSchedule', 'recordedBy'])
            ->orderByDesc('created_at');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $attendances = $query->paginate(15)->withQueryString();

        return view('portal.attendance', compact('member', 'attendances'));
    }
}

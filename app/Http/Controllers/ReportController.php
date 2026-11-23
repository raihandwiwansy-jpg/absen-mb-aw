<?php

namespace App\Http\Controllers;

use App\Exports\MembersExport;
use App\Models\Attendance;
use App\Models\Division;
use App\Models\Member;
use App\Models\OrganizationalPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Show main reports center.
     */
    public function index(): View
    {
        $periods = OrganizationalPeriod::all();
        $divisions = Division::all();

        return view('reports.index', compact('periods', 'divisions'));
    }

    /**
     * Comprehensive attendance report with filters.
     */
    public function attendance(Request $request): View
    {
        $query = Attendance::with(['member.division', 'member.instrument', 'attendanceSession.practiceSchedule', 'recordedBy'])
            ->orderByDesc('created_at');

        if ($request->division_id) {
            $query->whereHas('member', function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->start_date) {
            $query->whereHas('attendanceSession.practiceSchedule', function ($q) use ($request) {
                $q->whereDate('date', '>=', $request->start_date);
            });
        }

        if ($request->end_date) {
            $query->whereHas('attendanceSession.practiceSchedule', function ($q) use ($request) {
                $q->whereDate('date', '<=', $request->end_date);
            });
        }

        $attendances = $query->paginate(20)->withQueryString();
        $divisions = Division::all();

        // Calculate summary counts
        $totalPresent = (clone $query)->where('status', 'present')->count();
        $totalLate = (clone $query)->where('status', 'late')->count();
        $totalPermission = (clone $query)->where('status', 'permission')->count();
        $totalSick = (clone $query)->where('status', 'sick')->count();
        $totalAbsent = (clone $query)->where('status', 'absent')->count();

        return view('reports.attendance', compact(
            'attendances',
            'divisions',
            'totalPresent',
            'totalLate',
            'totalPermission',
            'totalSick',
            'totalAbsent'
        ));
    }

    /**
     * Member discipline rankings report.
     */
    public function discipline(Request $request): View
    {
        $query = Member::with(['division', 'instrument', 'position'])
            ->where('status', 'active');

        if ($request->division_id) {
            $query->where('division_id', $request->division_id);
        }

        $members = $query->get()->map(function (Member $member) {
            $total = $member->attendances()->count();
            $present = $member->attendances()->whereIn('status', ['present', 'late'])->count();
            $late = $member->attendances()->where('status', 'late')->count();
            $permission = $member->attendances()->where('status', 'permission')->count();
            $sick = $member->attendances()->where('status', 'sick')->count();
            $absent = $member->attendances()->where('status', 'absent')->count();
            $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

            return (object) [
                'member' => $member,
                'total_sessions' => $total,
                'present' => $present,
                'late' => $late,
                'permission' => $permission,
                'sick' => $sick,
                'absent' => $absent,
                'percentage' => $percentage,
            ];
        })->sortByDesc('percentage')->values();

        $divisions = Division::all();

        return view('reports.discipline', compact('members', 'divisions'));
    }

    /**
     * Division performance and attendance report.
     */
    public function division(Request $request): View
    {
        $divisions = Division::with(['instruments', 'members'])->get()->map(function (Division $division) {
            $memberIds = $division->members()->pluck('id');
            $totalAttendances = Attendance::whereIn('member_id', $memberIds)->count();
            $presentCount = Attendance::whereIn('member_id', $memberIds)->whereIn('status', ['present', 'late'])->count();
            $lateCount = Attendance::whereIn('member_id', $memberIds)->where('status', 'late')->count();
            $permissionCount = Attendance::whereIn('member_id', $memberIds)->where('status', 'permission')->count();
            $sickCount = Attendance::whereIn('member_id', $memberIds)->where('status', 'sick')->count();
            $absentCount = Attendance::whereIn('member_id', $memberIds)->where('status', 'absent')->count();
            $percentage = $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 1) : 0;

            return (object) [
                'division' => $division,
                'total_members' => $division->members()->count(),
                'active_members' => $division->members()->where('status', 'active')->count(),
                'total_attendances' => $totalAttendances,
                'present' => $presentCount,
                'late' => $lateCount,
                'permission' => $permissionCount,
                'sick' => $sickCount,
                'absent' => $absentCount,
                'percentage' => $percentage,
            ];
        });

        return view('reports.division', compact('divisions'));
    }

    /**
     * Export attendance report to PDF with Symphony letterhead.
     */
    public function exportPdf(Request $request): Response
    {
        $query = Attendance::with(['member.division', 'member.instrument', 'attendanceSession.practiceSchedule'])
            ->orderByDesc('created_at');

        if ($request->division_id) {
            $query->whereHas('member', function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->start_date) {
            $query->whereHas('attendanceSession.practiceSchedule', function ($q) use ($request) {
                $q->whereDate('date', '>=', $request->start_date);
            });
        }

        if ($request->end_date) {
            $query->whereHas('attendanceSession.practiceSchedule', function ($q) use ($request) {
                $q->whereDate('date', '<=', $request->end_date);
            });
        }

        $attendances = $query->limit(200)->get();
        $dateGenerated = Carbon::now()->translatedFormat('d F Y, H:i');

        $pdf = Pdf::loadView('reports.pdf.attendance', compact('attendances', 'dateGenerated'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-absensi-symphony-'.date('Ymd-His').'.pdf');
    }

    /**
     * Export members to Excel.
     */
    public function exportExcel()
    {
        return Excel::download(new MembersExport, 'data-anggota-symphony-'.date('Ymd').'.xlsx');
    }
}

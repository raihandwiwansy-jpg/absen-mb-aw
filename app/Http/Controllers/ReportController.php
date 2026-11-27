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
use Illuminate\Support\Facades\DB;
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

        // Calculate summary counts in a single grouped query
        $statusCounts = (clone $query)
            ->reorder()
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $attendances = $query->paginate(20)->withQueryString();
        $divisions = Division::all();

        $totalPresent = (int) ($statusCounts['present'] ?? 0);
        $totalLate = (int) ($statusCounts['late'] ?? 0);
        $totalPermission = (int) ($statusCounts['permission'] ?? 0);
        $totalSick = (int) ($statusCounts['sick'] ?? 0);
        $totalAbsent = (int) ($statusCounts['absent'] ?? 0);

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

        $members = $query->withCount([
            'attendances as total_sessions_count',
            'attendances as present_count' => fn ($q) => $q->whereIn('status', ['present', 'late']),
            'attendances as late_count' => fn ($q) => $q->where('status', 'late'),
            'attendances as permission_count' => fn ($q) => $q->where('status', 'permission'),
            'attendances as sick_count' => fn ($q) => $q->where('status', 'sick'),
            'attendances as absent_count' => fn ($q) => $q->where('status', 'absent'),
        ])->get()->map(function (Member $member) {
            $total = (int) $member->total_sessions_count;
            $present = (int) $member->present_count;
            $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

            return (object) [
                'member' => $member,
                'total_sessions' => $total,
                'present' => $present,
                'late' => (int) $member->late_count,
                'permission' => (int) $member->permission_count,
                'sick' => (int) $member->sick_count,
                'absent' => (int) $member->absent_count,
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
        $attendanceRows = DB::table('attendances')
            ->join('members', 'attendances.member_id', '=', 'members.id')
            ->whereNull('members.deleted_at')
            ->whereNotNull('members.division_id')
            ->select('members.division_id', 'attendances.status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('members.division_id', 'attendances.status')
            ->get();

        $attendanceMap = [];
        foreach ($attendanceRows as $row) {
            $divId = (int) $row->division_id;
            $attendanceMap[$divId][$row->status] = (int) $row->aggregate;
        }

        $divisions = Division::withCount([
            'members as total_members_count',
            'members as active_members_count' => fn ($q) => $q->where('status', 'active'),
        ])->get()->map(function (Division $division) use ($attendanceMap) {
            $bucket = $attendanceMap[$division->id] ?? [];
            $lateCount = (int) ($bucket['late'] ?? 0);
            $presentCount = (int) (($bucket['present'] ?? 0) + $lateCount);
            $permissionCount = (int) ($bucket['permission'] ?? 0);
            $sickCount = (int) ($bucket['sick'] ?? 0);
            $absentCount = (int) ($bucket['absent'] ?? 0);
            $totalAttendances = (int) array_sum($bucket);
            $percentage = $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 1) : 0;

            return (object) [
                'division' => $division,
                'total_members' => (int) $division->total_members_count,
                'active_members' => (int) $division->active_members_count,
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

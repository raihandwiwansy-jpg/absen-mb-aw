<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Division;
use App\Models\Member;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    /**
     * Show general attendance overview or redirect to sessions.
     */
    public function index(): View|RedirectResponse
    {
        return redirect()->route('attendance.sessions.index');
    }

    /**
     * Show QR Code scanner page for members.
     */
    public function scan(Request $request): View
    {
        $token = $request->query('token');

        return view('attendance.scan', compact('token'));
    }

    /**
     * Process scanned QR code token via AJAX.
     */
    public function processScan(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $token = $this->attendanceService->validateToken($request->token);

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid, sesi sudah ditutup, atau token telah kedaluwarsa.',
            ], 422);
        }

        $user = auth()->user();
        $member = $user->member;

        if (! $member) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda belum terhubung dengan data anggota Symphony Marching Band.',
            ], 422);
        }

        if ($member->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya anggota berstatus aktif yang dapat melakukan absensi.',
            ], 403);
        }

        $result = $this->attendanceService->recordAttendance($token, $member);

        if ($result['success']) {
            ActivityLog::log([
                'action' => 'qr_attendance',
                'description' => "Member {$member->full_name} berhasil absen (Status: {$result['status']}) via QR Code",
                'subject_type' => Attendance::class,
            ]);
        }

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Manually record or update an individual member's attendance.
     */
    public function manualRecord(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'attendance_session_id' => ['required', 'exists:attendance_sessions,id'],
            'member_id' => ['required', 'exists:members,id'],
            'status' => ['required', 'in:present,late,permission,sick,absent'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $checkInTime = in_array($validated['status'], ['present', 'late']) ? now() : null;

        $attendance = Attendance::updateOrCreate(
            [
                'attendance_session_id' => $validated['attendance_session_id'],
                'member_id' => $validated['member_id'],
            ],
            [
                'status' => $validated['status'],
                'check_in_time' => $checkInTime,
                'notes' => $validated['notes'] ?? null,
                'recorded_by' => auth()->id(),
            ]
        );

        $member = Member::find($validated['member_id']);

        ActivityLog::log([
            'action' => 'manual_attendance',
            'description' => "Memperbarui absensi {$member->full_name} menjadi {$attendance->status_label}",
            'subject_type' => Attendance::class,
            'subject_id' => $attendance->id,
        ]);

        if ($request->wantsJson()) {
            $stats = $this->attendanceService->getSessionStatistics($attendance->attendanceSession);

            return response()->json([
                'success' => true,
                'message' => "Absensi {$member->full_name} berhasil diperbarui ({$attendance->status_label}).",
                'attendance' => $attendance,
                'status_label' => $attendance->status_label,
                'check_in_time' => $attendance->check_in_time ? $attendance->check_in_time->format('H:i:s') : '-',
                'recorded_by' => auth()->user()->name,
                'stats' => $stats,
            ]);
        }

        return back()->with('success', "Absensi {$member->full_name} berhasil diperbarui.");
    }

    /**
     * Display attendance history with filtering.
     */
    public function history(Request $request): View
    {
        $query = Attendance::with(['member.division', 'member.instrument', 'attendanceSession.practiceSchedule', 'recordedBy'])
            ->orderByDesc('created_at');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->division_id) {
            $query->whereHas('member', function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
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

        if ($request->search) {
            $query->whereHas('member', function ($q) use ($request) {
                $q->where('full_name', 'like', '%'.$request->search.'%')
                    ->orWhere('member_code', 'like', '%'.$request->search.'%');
            });
        }

        $attendances = $query->paginate(20)->withQueryString();
        $divisions = Division::all();

        return view('attendance.history', compact('attendances', 'divisions'));
    }
}

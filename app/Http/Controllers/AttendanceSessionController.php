<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\PracticeSchedule;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceSessionController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    /**
     * Display a listing of attendance sessions.
     */
    public function index(): View
    {
        AttendanceSession::closeStaleSessions();

        $sessions = AttendanceSession::with(['practiceSchedule', 'openedBy', 'attendanceToken'])
            ->withCount([
                'attendances',
                'attendances as present_count' => fn ($q) => $q->where('status', 'present'),
                'attendances as late_count' => fn ($q) => $q->where('status', 'late'),
                'attendances as permission_count' => fn ($q) => $q->where('status', 'permission'),
                'attendances as sick_count' => fn ($q) => $q->where('status', 'sick'),
                'attendances as absent_count' => fn ($q) => $q->where('status', 'absent'),
            ])
            ->orderByDesc('opened_at')
            ->paginate(10);

        return view('attendance.sessions.index', compact('sessions'));
    }

    /**
     * Show form for creating a new session.
     */
    public function create(): View
    {
        // Get practice schedules that don't have an active session right now
        $schedules = PracticeSchedule::where('date', '>=', today()->subDays(1))
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('attendance.sessions.create', compact('schedules'));
    }

    /**
     * Store new attendance session.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'practice_schedule_id' => ['required', 'exists:practice_schedules,id'],
        ]);

        return DB::transaction(function () use ($validated) {
            // Check if there is already an active session for this schedule
            $existing = AttendanceSession::where('practice_schedule_id', $validated['practice_schedule_id'])
                ->where('is_active', true)
                ->first();

            if ($existing) {
                return redirect()->route('attendance.sessions.show', $existing)
                    ->with('warning', 'Sesi absensi untuk jadwal ini sudah aktif.');
            }

            $session = AttendanceSession::create([
                'practice_schedule_id' => $validated['practice_schedule_id'],
                'opened_by' => auth()->id(),
                'opened_at' => now(),
                'is_active' => true,
            ]);

            // Generate expiring token
            $this->attendanceService->generateToken($session);

            // Populate all active members as absent initially
            $this->attendanceService->initializeSessionAttendances($session);

            ActivityLog::log([
                'action' => 'open_attendance_session',
                'description' => "Membuka sesi absensi untuk {$session->practiceSchedule->title}",
                'subject_type' => AttendanceSession::class,
                'subject_id' => $session->id,
            ]);

            return redirect()->route('attendance.sessions.show', $session)
                ->with('success', 'Sesi absensi berhasil dibuka dan QR Code telah dibuat.');
        });
    }

    /**
     * Display the attendance session sheet.
     */
    public function show(AttendanceSession $session): View
    {
        $session->load([
            'practiceSchedule',
            'openedBy',
            'attendanceToken',
            'attendances.member.division',
            'attendances.member.instrument',
            'attendances.recordedBy',
        ]);

        $stats = $this->attendanceService->getSessionStatistics($session);
        $attendances = $session->attendances()->with(['member.division', 'member.instrument'])->get();

        return view('attendance.sessions.show', compact('session', 'stats', 'attendances'));
    }

    /**
     * Close attendance session.
     */
    public function close(AttendanceSession $session): RedirectResponse
    {
        $session->update([
            'is_active' => false,
            'closed_at' => now(),
        ]);

        ActivityLog::log([
            'action' => 'close_attendance_session',
            'description' => "Menutup sesi absensi #{$session->id} ({$session->practiceSchedule->title})",
            'subject_type' => AttendanceSession::class,
            'subject_id' => $session->id,
        ]);

        return redirect()->route('attendance.sessions.show', $session)
            ->with('success', 'Sesi absensi berhasil ditutup.');
    }

    /**
     * Regenerate QR token for this session.
     */
    public function refreshToken(AttendanceSession $session): RedirectResponse
    {
        $this->attendanceService->generateToken($session);

        return back()->with('success', 'Token QR Code baru berhasil dibuat.');
    }

    /**
     * Generate and return QR code as SVG.
     */
    public function qrCode(AttendanceSession $session): Response
    {
        $token = $session->attendanceToken;

        if (! $token || $token->expires_at->isPast()) {
            $token = $this->attendanceService->generateToken($session);
        }

        $qrData = route('attendance.scan', ['token' => $token->token]);

        $svg = QrCode::size(280)
            ->color(16, 29, 59)
            ->backgroundColor(247, 245, 239)
            ->margin(1)
            ->generate($qrData);

        return response($svg)->header('Content-Type', 'image/svg+xml');
    }

    /**
     * Return real-time session stats and recent check-ins for projector / live view.
     */
    public function liveData(AttendanceSession $session): JsonResponse
    {
        $stats = $this->attendanceService->getSessionStatistics($session);

        $recentCheckIns = $session->attendances()
            ->whereIn('status', ['present', 'late'])
            ->whereNotNull('check_in_time')
            ->with(['member.division', 'member.instrument'])
            ->orderByDesc('check_in_time')
            ->limit(10)
            ->get()
            ->map(function ($att) {
                return [
                    'member_name' => $att->member?->full_name ?? 'Anggota',
                    'member_code' => $att->member?->member_code ?? '-',
                    'division' => $att->member?->division?->name ?? '-',
                    'instrument' => $att->member?->instrument?->name ?? '-',
                    'status' => $att->status,
                    'status_label' => $att->status_label,
                    'check_in_time' => $att->check_in_time->format('H:i:s'),
                    'time_ago' => $att->check_in_time->diffForHumans(),
                ];
            });

        $allAttendances = $session->attendances()
            ->with(['member', 'recordedBy'])
            ->get()
            ->map(function ($att) {
                return [
                    'member_id' => $att->member_id,
                    'member_name' => $att->member?->full_name ?? 'Anggota',
                    'status' => $att->status,
                    'status_label' => $att->status_label,
                    'check_in_time' => $att->check_in_time ? $att->check_in_time->format('H:i:s') : '-',
                    'recorded_by' => $att->recordedBy?->name ?? 'Sistem',
                ];
            });

        $token = $session->attendanceToken;

        return response()->json([
            'is_active' => (bool) $session->is_active,
            'stats' => $stats,
            'recent_check_ins' => $recentCheckIns,
            'all_attendances' => $allAttendances,
            'token_expires_at' => $token ? $token->expires_at->toIso8601String() : null,
        ]);
    }

    /**
     * Bulk update attendances in a session.
     */
    public function bulkMark(Request $request, AttendanceSession $session): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:present,late,permission,sick,absent'],
            'target' => ['required', 'in:all_absent,all'],
        ]);

        $query = $session->attendances();
        if ($validated['target'] === 'all_absent') {
            $query->where('status', 'absent');
        }

        $now = in_array($validated['status'], ['present', 'late']) ? now() : null;
        $count = $query->update([
            'status' => $validated['status'],
            'check_in_time' => $now,
            'recorded_by' => auth()->id(),
            'updated_at' => now(),
        ]);

        $stats = $this->attendanceService->getSessionStatistics($session);

        ActivityLog::log([
            'action' => 'bulk_attendance',
            'description' => "Memperbarui {$count} anggota menjadi {$validated['status']} pada sesi #{$session->id}",
            'subject_type' => AttendanceSession::class,
            'subject_id' => $session->id,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Berhasil memperbarui {$count} anggota menjadi ".ucfirst($validated['status']),
                'updated_count' => $count,
                'stats' => $stats,
            ]);
        }

        return back()->with('success', "Berhasil memperbarui {$count} anggota menjadi ".ucfirst($validated['status']));
    }
}

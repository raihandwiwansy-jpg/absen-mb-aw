<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\PermissionRequest;
use App\Models\PracticeSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PermissionRequestController extends Controller
{
    /**
     * Display a listing of permission requests.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = PermissionRequest::with(['member.division', 'practiceSchedule', 'reviewedBy'])
            ->orderByDesc('created_at');

        // If regular member, only show own requests
        if ($user->hasRole('member') && ! $user->hasAnyRole(['super_admin', 'administrator', 'attendance_officer', 'coach'])) {
            if ($user->member) {
                $query->where('member_id', $user->member->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $requests = $query->paginate(15)->withQueryString();

        return view('permission-requests.index', compact('requests'));
    }

    /**
     * Show form for creating a new permission request.
     */
    public function create(): View
    {
        $user = auth()->user();
        $member = $user->member;

        $schedules = PracticeSchedule::where('date', '>=', today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('permission-requests.create', compact('schedules', 'member'));
    }

    /**
     * Store newly submitted permission request.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $member = $user->member;

        if (! $member) {
            return back()->with('error', 'Akun Anda belum terhubung dengan data anggota.');
        }

        $validated = $request->validate([
            'practice_schedule_id' => ['required', 'exists:practice_schedules,id'],
            'type' => ['required', 'in:permission,sick'],
            'reason' => ['required', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif,pdf', 'max:10240'],
        ], [
            'practice_schedule_id.required' => 'Jadwal latihan wajib dipilih.',
            'type.required' => 'Jenis pengajuan wajib dipilih.',
            'reason.required' => 'Alasan ketidakhadiran wajib diisi.',
            'attachment.mimes' => 'Lampiran harus berupa gambar (JPG, PNG, WEBP, GIF) atau PDF.',
            'attachment.max' => 'Ukuran file lampiran maksimal 10MB.',
        ]);

        // Check if already requested for this schedule
        $existing = PermissionRequest::where('member_id', $member->id)
            ->where('practice_schedule_id', $validated['practice_schedule_id'])
            ->first();

        if ($existing) {
            return back()->with('warning', 'Anda sudah pernah mengajukan izin/sakit untuk jadwal latihan ini.');
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        $permissionRequest = PermissionRequest::create([
            'member_id' => $member->id,
            'practice_schedule_id' => $validated['practice_schedule_id'],
            'type' => $validated['type'],
            'reason' => $validated['reason'],
            'attachment' => $attachmentPath,
            'status' => 'pending',
        ]);

        ActivityLog::log([
            'action' => 'submit_permission',
            'description' => "Pengajuan {$permissionRequest->type_label} oleh {$member->full_name}",
            'subject_type' => PermissionRequest::class,
            'subject_id' => $permissionRequest->id,
        ]);

        return redirect()->route('permission-requests.index')
            ->with('success', 'Pengajuan izin/sakit berhasil dikirim dan menunggu persetujuan.');
    }

    /**
     * Display the specified permission request.
     */
    public function show(PermissionRequest $permissionRequest): View
    {
        $user = auth()->user();
        $isStaff = $user->hasAnyRole(['super_admin', 'administrator', 'attendance_officer', 'coach']);

        if (! $isStaff && $permissionRequest->member_id !== $user->member?->id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pengajuan izin milik anggota lain.');
        }

        $permissionRequest->load(['member.division', 'member.instrument', 'practiceSchedule', 'reviewedBy']);

        return view('permission-requests.show', compact('permissionRequest'));
    }

    /**
     * Approve a permission request.
     */
    public function approve(Request $request, PermissionRequest $permissionRequest): RedirectResponse|JsonResponse
    {
        return DB::transaction(function () use ($request, $permissionRequest) {
            $permissionRequest->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            // If there's an attendance session for this schedule, reflect in attendance records!
            $session = AttendanceSession::where('practice_schedule_id', $permissionRequest->practice_schedule_id)
                ->latest()
                ->first();

            if ($session) {
                Attendance::updateOrCreate(
                    [
                        'attendance_session_id' => $session->id,
                        'member_id' => $permissionRequest->member_id,
                    ],
                    [
                        'status' => $permissionRequest->type,
                        'notes' => 'Izin disetujui: '.$permissionRequest->reason,
                        'recorded_by' => auth()->id(),
                    ]
                );
            }

            ActivityLog::log([
                'action' => 'approve_permission',
                'description' => "Menyetujui pengajuan {$permissionRequest->type_label} #{$permissionRequest->id} untuk {$permissionRequest->member->full_name}",
                'subject_type' => PermissionRequest::class,
                'subject_id' => $permissionRequest->id,
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Pengajuan {$permissionRequest->type_label} telah disetujui.",
                    'status' => 'approved',
                ]);
            }

            return back()->with('success', "Pengajuan {$permissionRequest->type_label} telah disetujui.");
        });
    }

    /**
     * Reject a permission request.
     */
    public function reject(Request $request, PermissionRequest $permissionRequest): RedirectResponse|JsonResponse
    {
        $permissionRequest->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        ActivityLog::log([
            'action' => 'reject_permission',
            'description' => "Menolak pengajuan {$permissionRequest->type_label} #{$permissionRequest->id} untuk {$permissionRequest->member->full_name}",
            'subject_type' => PermissionRequest::class,
            'subject_id' => $permissionRequest->id,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Pengajuan {$permissionRequest->type_label} telah ditolak.",
                'status' => 'rejected',
            ]);
        }

        return back()->with('success', "Pengajuan {$permissionRequest->type_label} telah ditolak.");
    }
}

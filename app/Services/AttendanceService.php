<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\AttendanceToken;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AttendanceService
{
    /**
     * Generate or regenerate an expiring token for the attendance session.
     */
    public function generateToken(AttendanceSession $session): AttendanceToken
    {
        $session->attendanceToken()->delete();

        $schedule = $session->practiceSchedule;
        $scheduleDate = $schedule->date instanceof Carbon ? $schedule->date->format('Y-m-d') : (string) $schedule->date;
        $expiresAt = Carbon::parse($scheduleDate.' '.$schedule->end_time)->addHours(2);

        // Ensure expires_at is in the future
        if ($expiresAt->isPast()) {
            $expiresAt = now()->addHours(3);
        }

        return AttendanceToken::create([
            'attendance_session_id' => $session->id,
            'token' => Str::random(48),
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Validate a token string and return the token model if valid.
     */
    public function validateToken(string $token): ?AttendanceToken
    {
        $attendanceToken = AttendanceToken::with(['attendanceSession.practiceSchedule'])
            ->where('token', $token)
            ->first();

        if (! $attendanceToken) {
            return null;
        }

        if ($attendanceToken->expires_at->isPast()) {
            return null;
        }

        if (! $attendanceToken->attendanceSession || ! $attendanceToken->attendanceSession->is_active) {
            return null;
        }

        return $attendanceToken;
    }

    /**
     * Record attendance from QR code scan.
     *
     * @return array{success: bool, status?: string, message: string}
     */
    public function recordAttendance(AttendanceToken $token, Member $member): array
    {
        $session = $token->attendanceSession;
        $schedule = $session->practiceSchedule;

        // Check if attendance already recorded and not 'absent'
        $existing = Attendance::where('attendance_session_id', $session->id)
            ->where('member_id', $member->id)
            ->first();

        if ($existing && in_array($existing->status, ['present', 'late', 'permission', 'sick'])) {
            return [
                'success' => false,
                'message' => 'Anda sudah melakukan absensi (status: '.$existing->status_label.') untuk sesi ini.',
            ];
        }

        $scheduleDate = $schedule->date instanceof Carbon ? $schedule->date->format('Y-m-d') : (string) $schedule->date;
        $startTime = Carbon::parse($scheduleDate.' '.$schedule->start_time);
        $isLate = now()->gt($startTime->copy()->addMinutes(15));
        $status = $isLate ? 'late' : 'present';

        $attendance = Attendance::updateOrCreate(
            [
                'attendance_session_id' => $session->id,
                'member_id' => $member->id,
            ],
            [
                'status' => $status,
                'check_in_time' => now(),
                'recorded_by' => auth()->id(),
            ]
        );

        return [
            'success' => true,
            'status' => $attendance->status,
            'message' => $isLate
                ? 'Absensi berhasil dicatat! Anda tercatat terlambat.'
                : 'Absensi berhasil dicatat! Hadir tepat waktu.',
        ];
    }

    /**
     * Get statistics for a session.
     *
     * @return array<string, int>
     */
    public function getSessionStatistics(AttendanceSession $session): array
    {
        $totalMembers = Member::where('status', 'active')->count();
        $attendances = $session->attendances;

        $present = $attendances->where('status', 'present')->count();
        $late = $attendances->where('status', 'late')->count();
        $permission = $attendances->where('status', 'permission')->count();
        $sick = $attendances->where('status', 'sick')->count();
        $absent = $attendances->where('status', 'absent')->count();

        // If records are fewer than total, remainder are absent
        $recordedCount = $attendances->count();
        if ($recordedCount < $totalMembers) {
            $absent += ($totalMembers - $recordedCount);
        }

        return [
            'total_members' => $totalMembers,
            'present' => $present,
            'late' => $late,
            'permission' => $permission,
            'sick' => $sick,
            'absent' => $absent,
            'recorded' => $recordedCount,
        ];
    }

    /**
     * Pre-populate all active members with absent status for a session.
     */
    public function initializeSessionAttendances(AttendanceSession $session): void
    {
        $activeMembers = Member::where('status', 'active')->get();

        foreach ($activeMembers as $member) {
            Attendance::firstOrCreate(
                [
                    'attendance_session_id' => $session->id,
                    'member_id' => $member->id,
                ],
                [
                    'status' => 'absent',
                    'recorded_by' => $session->opened_by,
                ]
            );
        }
    }
}

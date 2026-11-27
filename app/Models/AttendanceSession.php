<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AttendanceSession extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'practice_schedule_id',
        'opened_by',
        'opened_at',
        'closed_at',
        'is_active',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the practice schedule for this session.
     */
    public function practiceSchedule(): BelongsTo
    {
        return $this->belongsTo(PracticeSchedule::class);
    }

    /**
     * Get the user who opened this session.
     */
    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    /**
     * Get all attendance records for this session.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the QR token for this session.
     */
    public function attendanceToken(): HasOne
    {
        return $this->hasOne(AttendanceToken::class);
    }

    /**
     * Get present count for this session.
     */
    public function getPresentCountAttribute(): int
    {
        if (array_key_exists('present_count', $this->attributes)) {
            return (int) $this->attributes['present_count'];
        }

        if ($this->relationLoaded('attendances')) {
            return $this->attendances->where('status', 'present')->count();
        }

        return $this->attendances()->where('status', 'present')->count();
    }

    /**
     * Get late count for this session.
     */
    public function getLateCountAttribute(): int
    {
        if (array_key_exists('late_count', $this->attributes)) {
            return (int) $this->attributes['late_count'];
        }

        if ($this->relationLoaded('attendances')) {
            return $this->attendances->where('status', 'late')->count();
        }

        return $this->attendances()->where('status', 'late')->count();
    }

    /**
     * Get absent count for this session.
     */
    public function getAbsentCountAttribute(): int
    {
        if (array_key_exists('absent_count', $this->attributes)) {
            return (int) $this->attributes['absent_count'];
        }

        if ($this->relationLoaded('attendances')) {
            return $this->attendances->where('status', 'absent')->count();
        }

        return $this->attendances()->where('status', 'absent')->count();
    }

    /**
     * Get permission count for this session.
     */
    public function getPermissionCountAttribute(): int
    {
        if (array_key_exists('permission_count', $this->attributes)) {
            return (int) $this->attributes['permission_count'];
        }

        if ($this->relationLoaded('attendances')) {
            return $this->attendances->where('status', 'permission')->count();
        }

        return $this->attendances()->where('status', 'permission')->count();
    }

    /**
     * Get sick count for this session.
     */
    public function getSickCountAttribute(): int
    {
        if (array_key_exists('sick_count', $this->attributes)) {
            return (int) $this->attributes['sick_count'];
        }

        if ($this->relationLoaded('attendances')) {
            return $this->attendances->where('status', 'sick')->count();
        }

        return $this->attendances()->where('status', 'sick')->count();
    }

    /**
     * Automatically close active sessions whose practice schedule end time has passed.
     */
    public static function closeStaleSessions(): int
    {
        $staleSessions = static::where('is_active', true)
            ->with(['practiceSchedule'])
            ->get()
            ->filter(function (self $session): bool {
                $schedule = $session->practiceSchedule;
                if (! $schedule) {
                    return true;
                }

                $endDateTime = Carbon::parse(
                    $schedule->date->format('Y-m-d').' '.$schedule->end_time
                );

                return now()->isAfter($endDateTime);
            });

        $closedCount = 0;
        foreach ($staleSessions as $stale) {
            $stale->update(['is_active' => false, 'closed_at' => now()]);
            $closedCount++;
        }

        return $closedCount;
    }
}

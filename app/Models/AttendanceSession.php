<?php

namespace App\Models;

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
        return $this->attendances()->where('status', 'present')->count();
    }

    /**
     * Get late count for this session.
     */
    public function getLateCountAttribute(): int
    {
        return $this->attendances()->where('status', 'late')->count();
    }

    /**
     * Get absent count for this session.
     */
    public function getAbsentCountAttribute(): int
    {
        return $this->attendances()->where('status', 'absent')->count();
    }

    /**
     * Get permission count for this session.
     */
    public function getPermissionCountAttribute(): int
    {
        return $this->attendances()->where('status', 'permission')->count();
    }

    /**
     * Get sick count for this session.
     */
    public function getSickCountAttribute(): int
    {
        return $this->attendances()->where('status', 'sick')->count();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PracticeSchedule extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'type',
        'date',
        'start_time',
        'end_time',
        'location',
        'dress_code',
        'description',
        'officer_id',
        'organizational_period_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the officer responsible for this schedule.
     */
    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    /**
     * Get the organizational period for this schedule.
     */
    public function organizationalPeriod(): BelongsTo
    {
        return $this->belongsTo(OrganizationalPeriod::class);
    }

    /**
     * Get all attendance sessions for this schedule.
     */
    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }

    /**
     * Get the active attendance session.
     */
    public function activeSession(): HasOne
    {
        return $this->hasOne(AttendanceSession::class)->where('is_active', true);
    }

    /**
     * Get all permission requests for this schedule.
     */
    public function permissionRequests(): HasMany
    {
        return $this->hasMany(PermissionRequest::class);
    }

    /**
     * Get type label in Indonesian.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'regular' => 'Latihan Rutin',
            'additional' => 'Latihan Tambahan',
            'competition' => 'Persiapan Kompetisi',
            default => 'Latihan',
        };
    }

    /**
     * Get type badge color.
     */
    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'regular' => 'blue',
            'additional' => 'yellow',
            'competition' => 'red',
            default => 'gray',
        };
    }

    /**
     * Check if schedule is today.
     */
    public function getIsTodayAttribute(): bool
    {
        return $this->date->isToday();
    }

    /**
     * Check if schedule is tomorrow.
     */
    public function getIsTomorrowAttribute(): bool
    {
        return $this->date->isTomorrow();
    }

    /**
     * Check if schedule is upcoming.
     */
    public function getIsUpcomingAttribute(): bool
    {
        return $this->date->isFuture();
    }

    /**
     * Get formatted time range string.
     */
    public function getTimeRangeAttribute(): string
    {
        $start = substr($this->start_time, 0, 5);
        $end = substr($this->end_time, 0, 5);

        return "{$start} - {$end} WIB";
    }
}

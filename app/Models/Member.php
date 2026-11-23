<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'member_code',
        'user_id',
        'full_name',
        'gender',
        'class',
        'phone',
        'address',
        'photo',
        'enrollment_date',
        'division_id',
        'instrument_id',
        'position_id',
        'organizational_period_id',
        'status',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'enrollment_date' => 'date',
    ];

    /**
     * Get the user account linked to this member.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the division this member belongs to.
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Get the instrument assigned to this member.
     */
    public function instrument(): BelongsTo
    {
        return $this->belongsTo(Instrument::class);
    }

    /**
     * Get the organizational position of this member.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Get the organizational period this member belongs to.
     */
    public function organizationalPeriod(): BelongsTo
    {
        return $this->belongsTo(OrganizationalPeriod::class);
    }

    /**
     * Get all attendance records for this member.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get all permission requests submitted by this member.
     */
    public function permissionRequests(): HasMany
    {
        return $this->hasMany(PermissionRequest::class);
    }

    /**
     * Generate the next member code.
     */
    public static function generateMemberCode(): string
    {
        $year = date('Y');
        $latest = self::withTrashed()
            ->where('member_code', 'like', "SMB-{$year}-%")
            ->orderByDesc('member_code')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->member_code, -3);
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        return "SMB-{$year}-{$nextNumber}";
    }

    /**
     * Calculate attendance percentage for this member.
     */
    public function getAttendancePercentageAttribute(): float
    {
        $total = $this->attendances()->count();
        if ($total === 0) {
            return 0.0;
        }

        $present = $this->attendances()->whereIn('status', ['present', 'late'])->count();

        return round(($present / $total) * 100, 1);
    }

    /**
     * Get the photo URL or a default avatar.
     */
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo && file_exists(public_path('storage/'.$this->photo))) {
            return asset('storage/'.$this->photo);
        }

        return asset('images/default-avatar.png');
    }

    /**
     * Get gender label in Indonesian.
     */
    public function getGenderLabelAttribute(): string
    {
        return $this->gender === 'male' ? 'Laki-laki' : 'Perempuan';
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'green',
            'inactive' => 'gray',
            'alumni' => 'blue',
            default => 'gray',
        };
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasRoles;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'photo',
        'phone',
        'password',
        'plain_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the member profile linked to this user.
     */
    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    /**
     * Get all activity logs for this user.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get all notifications for this user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get all app notifications for this user.
     */
    public function appNotifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get unread notifications count.
     */
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->appNotifications()->where('is_read', false)->count();
    }

    /**
     * Check if user is super admin.
     */
    public function getIsSuperAdminAttribute(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if user is any kind of admin.
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->hasAnyRole(['super_admin', 'administrator']);
    }

    /**
     * Check if user is a member (regular member role).
     */
    public function getIsMemberRoleAttribute(): bool
    {
        return $this->hasRole('member');
    }

    /**
     * Get the resolved relative storage path for the user's profile photo.
     */
    public function getEffectivePhotoAttribute(): ?string
    {
        $candidates = array_filter([
            $this->photo,
            $this->member?->photo,
        ]);

        foreach ($candidates as $path) {
            if (Storage::disk('public')->exists($path) || file_exists(public_path('storage/'.$path))) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Get the public URL for the user's profile photo, or null if none exists.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        $path = $this->effective_photo;

        return $path ? asset('storage/'.$path) : null;
    }

    /**
     * Get the user's phone number, falling back to linked member phone.
     */
    public function getEffectivePhoneAttribute(): ?string
    {
        return $this->phone ?: $this->member?->phone;
    }
}

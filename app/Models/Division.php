<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'color',
    ];

    /**
     * Get all instruments belonging to this division.
     */
    public function instruments(): HasMany
    {
        return $this->hasMany(Instrument::class);
    }

    /**
     * Get all members in this division.
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * Get active members count.
     */
    public function getActiveMembersCountAttribute(): int
    {
        return $this->members()->where('status', 'active')->count();
    }
}

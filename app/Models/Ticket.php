<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'text',
        'status',
        'slot_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'slot_number' => 'integer',
    ];

    public static function getStatuses()
    {
        return config('ticket_statuses');
    }

    public static function getLastSlot(string $status, int $userId): int
    {
        return User::find($userId)
            ->tickets()
            ->where('status', $status)
            ->orderByDesc('ticket_user.slot_number')
            ->pluck('ticket_user.slot_number')
            ->first() ?? -1;
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('slot_number');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}

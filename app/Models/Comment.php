<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'content',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function date()
    {
        return $this->updated_at != null ? Comment::timestamp($this->updated_at) : Comment::timestamp($this->created_at);
    }

    private static function timestamp($time)
    {
        $time = Carbon::parse($time);
        $now = Carbon::now();
        $diffInSeconds = $now->diffInSeconds($time);
        $diffInMinutes = $now->diffInMinutes($time);
        $diffInHours = $now->diffInHours($time);
        $diffInDays = $now->diffInDays($time);
        $diffInWeeks = $now->diffInWeeks($time);

        if ($diffInSeconds < 5) {
            return 'now';
        } elseif ($diffInSeconds < 60) {
            return $diffInSeconds . ' seconds ago';
        } elseif ($diffInMinutes < 60) {
            return $diffInMinutes . ' minute' . ($diffInMinutes === 1 ? '' : 's') . ' ago';
        } elseif ($diffInHours < 24) {
            return $diffInHours . ' hour' . ($diffInHours === 1 ? '' : 's') . ' ago';
        } elseif ($diffInDays < 7) {
            return $diffInDays . ' day' . ($diffInDays === 1 ? '' : 's') . ' ago';
        } elseif ($diffInWeeks < 4) {
            return $diffInWeeks . ' week' . ($diffInWeeks === 1 ? '' : 's') . ' ago';
        } else {
            return $time->format('Y-m-d');
        }

        return $time;
    }
}

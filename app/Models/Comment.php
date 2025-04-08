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

    public static function timestamp($time)
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
            return $diffInMinutes . ' minutes ago';
        } elseif ($diffInHours < 24) {
            return $diffInHours . ' hours ago';
        } elseif ($diffInDays < 7) {
            return $diffInDays . ' days ago';
        } elseif ($diffInWeeks < 4) {
            return $diffInWeeks . ' weeks ago';
        } else {
            return $time->format('Y-m-d');
        }

        return $time;
    }
}

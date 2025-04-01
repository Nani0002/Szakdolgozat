<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Computer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'manufacturer',
        'type',
        'serial_number',
    ];

    public function worksheets(): BelongsToMany
    {
        return $this->belongsToMany(Worksheet::class)->withPivot('password', 'condition', 'imagename', 'imagename_hash')->withTimestamps();
    }

    public function latestInfo(): ?Worksheet
    {
        return $this->worksheets()
            ->withPivot('created_at', 'password', 'condition', 'imagename', 'imagename_hash')
            ->orderBy('pivot_created_at', 'desc')
            ->first();
    }

    protected $appends = ["latest_info_pivot"];
    public function getLatestInfoPivotAttribute()
    {
        $latest = $this->latestInfo();

        if (!$latest || !$latest->pivot) {
            return null;
        }

        return [
            'created_at' => $latest->pivot->created_at,
            'password' => $latest->pivot->password,
            'condition' => $latest->pivot->condition,
            'imagename' => $latest->pivot->imagename,
            'imagename_hash' => $latest->pivot->imagename_hash,
        ];
    }
}

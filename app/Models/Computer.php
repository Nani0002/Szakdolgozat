<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Computer extends Model
{
    use HasFactory, SoftDeletes;

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

    public static function booted() {
        static::deleting(function ($computer){
            $computer->extras()->each(function($extra){
                $extra->delete();
            });
        });
    }

    public function worksheets(): BelongsToMany
    {
        return $this->belongsToMany(Worksheet::class)->withPivot('password', 'condition', 'imagename', 'imagename_hash', 'id')->withTimestamps();
    }

    public function extras(): BelongsToMany
    {
        return $this->belongsToMany(Extra::class)->withPivot('worksheet_id')->withTimestamps();
    }

    public function extrasWithWorksheet()
    {
        return $this->belongsToMany(Extra::class, 'computer_extra')
            ->withPivot('worksheet_id')
            ->with('worksheet')
            ->withTimestamps();
    }

    public function extrasForWorksheet($worksheetId)
    {
        return $this->extras()
            ->wherePivot('worksheet_id', $worksheetId)
            ->get();
    }

    public function latestInfo(): ?Worksheet
    {
        return $this->worksheets()
            ->withPivot('created_at', 'password', 'condition', 'imagename', 'imagename_hash', 'id')
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
            'id' => $latest->pivot->id,
        ];
    }
}

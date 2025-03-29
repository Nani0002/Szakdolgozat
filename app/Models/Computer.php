<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}

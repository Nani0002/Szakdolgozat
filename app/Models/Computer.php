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
        return $this->belongsToMany(Worksheet::class);
    }

    public function outsourcings() : HasMany
    {
        return $this->hasMany(Outsourcing::class);
    }
}

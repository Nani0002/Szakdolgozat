<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Extra extends Model
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

    public function worksheet() : BelongsToMany
    {
        return $this->belongsToMany(Worksheet::class, 'computer_extra')
                    ->withPivot('computer_id')
                    ->withTimestamps();
    }

    public function computer() : BelongsToMany
    {
        return $this->belongsToMany(Computer::class, 'computer_extra')
                    ->withPivot('worksheet_id')
                    ->withTimestamps();
    }
}

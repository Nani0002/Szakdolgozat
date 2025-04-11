<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Worksheet extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sheet_number',
        'sheet_type',
        'print_date',
        'declaration_time',
        'declaration_mode',
        'error_description',
        'comment',
        'final',
        'work_start',
        'work_end',
        'work_time',
        'work_description',
        'current_step',
        'slot_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'print_date' => 'datetime',
        'declaration_time' => 'datetime',
        'work_start' => 'datetime',
        'work_end' => 'datetime',
        'final' => 'boolean',
    ];

    public function coworker(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function liable(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function computers(): BelongsToMany
    {
        return $this->belongsToMany(Computer::class)->withPivot('password', 'condition', 'imagename', 'imagename_hash', 'id')->withTimestamps();
    }

    public function outsourcing(): BelongsTo
    {
        return $this->belongsTo(Outsourcing::class);
    }

    public function extras()
    {
        return $this->hasManyThrough(
            Extra::class,
            'computer_extra',
            'worksheet_id',
            'id',
            'id',
            'extra_id'
        );
    }

    public static function getTypes()
    {
        return config('worksheet_steps');
    }
}

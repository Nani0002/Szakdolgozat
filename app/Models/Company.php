<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'post_code',
        'city',
        'street',
        'phone',
        'email',
        'type'
    ];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}

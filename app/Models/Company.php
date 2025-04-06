<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

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

    public static function sortedCompanies()
    {
        $companies = ["partner" => [], "customer" => []];
        foreach (Company::all() as $company) {
            $companies[$company->type][] = $company;
        }
        return $companies;
    }
}

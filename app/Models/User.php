<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'imagename',
        'imagename_hash'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed'
    ];


    /**
     * Returns if user has admin privilige.
     */
    public function isAdmin(): Bool
    {
        return $this->role === 'admin';
    }

    /**
     * Returns navbar urls depending on whether a user is logged in or not.
     */
    public static function getNavUrls($auth, $routes = []): array
    {
        $navUrls = [['name' => 'Főoldal', 'url' => route('home')]];
        if ($auth === true) {
            array_push($navUrls, ['name' => 'Munkalapok', 'url' => route('worksheet.index')], ['name' => 'Ügyfelek', 'url' => route('company.index')]);
            foreach ($routes as $route) {
                switch ($route['type']) {
                    case 'create':
                        array_push($navUrls, ['name' => "Új {$route['text']}", 'url' => $route['url']]);
                        break;
                }
            }
        }
        return $navUrls;
    }

    /**
     * Returns user controll navbar urls depending on the users priviliges.
     */
    public function getUserUrls($search = false): array
    {
        $userUrls = [];
        if ($search) {
            array_push($userUrls, ['name' => 'search', 'url' => route('worksheet.search')]);
        }
        array_push($userUrls, ["name" => 'Profilom', 'url' => route('user')]);
        if ($this->isAdmin()) {
            array_push($userUrls, ['name' => 'Munkatárs felvétele', 'url' => '/register']);
        }
        return $userUrls;
    }

    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class);
    }

    public function sortedTickets(): array
    {
        $tickets = [];
        foreach (Ticket::getStatuses() as $status) {
            $tickets[$status] = $this->tickets()->where("status", $status)->orderBy('slot_number', 'asc')->get();
        }
        return $tickets;
    }

    public function ticketsByStatus($status): Collection
    {
        return $this->tickets()->where("status", $status)->orderBy('slot_number', 'asc')->get();
    }

    public function sortedWorksheets(): array
    {
        $worksheets = [];
        foreach (Worksheet::getTypes() as $type) {
            $liable = $this->liableWorksheets()->where('current_step', $type)->get();
            $coworker = $this->coworkerWorksheets()->where('current_step', $type)->get();

            $merged = $liable->merge($coworker);
            $sorted = $merged->sortBy('slot_number')->values();

            $worksheets[$type] = $sorted;
        }
        return $worksheets;
    }

    public function worksheetsByStep($step): Collection
    {
        $liable = $this->liableWorksheets()->where('current_step', $step)->get();
        $coworker = $this->coworkerWorksheets()->where('current_step', $step)->get();
        $merged = $liable->merge($coworker);
        return $merged->sortBy('slot_number')->values();
    }

    public function coworkerWorksheets(): HasMany
    {
        return $this->hasMany(Worksheet::class, "coworker_id");
    }

    public function liableWorksheets(): HasMany
    {
        return $this->hasMany(Worksheet::class, "liable_id");
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }
}

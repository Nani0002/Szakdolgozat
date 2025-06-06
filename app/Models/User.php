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
        'imagename_hash',
        'must_change_password '
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'must_change_password ',
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
        'password' => 'hashed',
        'must_change_password ' => 'boolean'
    ];


    /**
     * Returns if user has admin priviliges.
     */
    public function isAdmin(): Bool
    {
        return $this->role === 'admin';
    }

    /**
     * Returns if user has liable priviliges.
     */
    public function isLiable(): Bool
    {
        return $this->role === 'liable';
    }

    /**
     * Returns if user has coworker priviliges.
     */
    public function isCoworker(): Bool
    {
        return $this->role === 'coworker';
    }

    /**
     * Returns navbar urls depending on whether a user is logged in or not.
     */
    public static function getNavUrls($role, $routes = []): array
    {
        $navUrls = [['name' => 'Főoldal', 'url' => route('home')]];
        if ($role != null) {
            if ($role != "admin")
                array_push($navUrls, ['name' => 'Munkalapok', 'url' => route('worksheet.index')], ['name' => 'Számítógépek', 'url' => route('computer.index')], ['name' => 'Ügyfelek', 'url' => route('company.index')]);
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
        return $this->belongsToMany(Ticket::class)->withPivot('slot_number');
    }

    public function sortedTickets(): array
    {
        $tickets = [];
        foreach (Ticket::getStatuses() as $status => $_) {
            $tickets[$status] = $this->tickets()->where("status", $status)->orderBy('slot_number', 'asc')->get();
        }
        return $tickets;
    }

    public function ticketsByStatus(string $status)
    {
        return $this->tickets->where('status', $status)->sortBy('pivot.slot_number');
    }

    public function sortedWorksheets(): array
    {
        $worksheets = [];

        foreach (Worksheet::getTypes() as $type => $_) {
            $liable = $this->liableWorksheets()
                ->where('current_step', $type)
                ->get();

            $coworker = $this->coworkerWorksheets()
                ->where('current_step', $type)
                ->get();

            $merged = $liable->merge($coworker);

            $sorted = $merged->sortBy(function ($ws) {
                return $ws->liable_id === $this->id
                    ? $ws->liable_slot_number
                    : $ws->coworker_slot_number;
            })->values();

            $worksheets[$type] = $sorted;
        }

        return $worksheets;
    }

    public function worksheetsByStep($step): Collection
    {
        $liable = $this->liableWorksheets()
            ->where('current_step', $step)
            ->get();

        $coworker = $this->coworkerWorksheets()
            ->where('current_step', $step)
            ->get();

        $merged = $liable->merge($coworker);

        return $merged->sortBy(function ($ws) {
            return $ws->liable_id === $this->id
                ? $ws->liable_slot_number
                : $ws->coworker_slot_number;
        })->values();
    }

    public function coworkerWorksheets(): HasMany
    {
        return $this->hasMany(Worksheet::class, "coworker_id");
    }

    public function liableWorksheets(): HasMany
    {
        return $this->hasMany(Worksheet::class, "liable_id");
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}

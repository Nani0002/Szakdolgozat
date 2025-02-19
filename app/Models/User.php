<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
    public static function getNavUrls($auth): array
    {
        $navUrls = [['name' => 'Főoldal', 'url' => route('home')]];
        if ($auth === true) {
            array_push($navUrls, ['name' => 'Munkalapok', 'url' => route('worksheet.index')]);
        }
        return $navUrls;
    }

    /**
     * Returns user controll navbar urls depending on the users priviliges.
     */
    public function getUserUrls(): array
    {
        $userUrls = [["name" => 'Profilom', 'url' => route('user')]];
        if ($this->isAdmin()) {
            array_push($userUrls, ['name' => 'Munkatárs felvétele', 'url' => '/register']);
        }
        return $userUrls;
    }

    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class);
    }

    public function usersettings(): HasMany
    {
        return $this->hasMany(Usersetting::class);
    }

    public function sortedTickets() : array {
        $tickets = [];
        foreach (Ticket::getStatuses() as $status) {
            $tickets[$status] = $this->tickets()->where("status", $status)->orderBy('slot_number', 'asc')->get();
        }
        return $tickets;
    }

    public function ticketsByStatus($status) : Collection{
        return $this->tickets()->where("status", $status)->orderBy('slot_number', 'asc')->get();
    }
}

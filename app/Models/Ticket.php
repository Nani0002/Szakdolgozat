<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'text',
        'status',
        'slot_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'slot_number' => 'integer',
    ];

    public static function getStatuses(){
        return ["open", "started", "ongoing", "price_offered", "waiting", "to_invoice", "closed"];
    }

    public static function singleType($type){
        return Ticket::all()->where('status', $type );
    }

    public static function allSorted(){
        $tickets = [];
        foreach (Ticket::getStatuses() as $value) {
            $tickets[$value] = Ticket::singleType($value);
        }
        return $tickets;
    }
}

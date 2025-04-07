<?php

namespace Database\Seeders;

use App\Models\Comment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userTicketPairs = DB::table('ticket_user')->get();

        foreach ($userTicketPairs as $pair) {
            Comment::factory(rand(1, 3))->create([
                'user_id' => $pair->user_id,
                'ticket_id' => $pair->ticket_id,
            ]);;
        }
    }
}

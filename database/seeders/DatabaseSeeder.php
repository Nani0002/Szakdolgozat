<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create(["name" => "admin", "role" => "admin", "email" => "email@gmail.com", "password" => env('PASSWORD')]);
        User::factory()->create(["name" => "user", "role" => "liable", "email" => "email2@gmail.com", "password" => "pass"]);

        User::factory(5)->create(["role" => "coworker"]);
        User::factory(2)->create(["role" => "liable"]);

        $this->call([TicketSeeder::class, CompanySeeder::class, CustomerSeeder::class, WorksheetSeeder::class, ComputerSeeder::class, ExtraSeeder::class]);
    }
}

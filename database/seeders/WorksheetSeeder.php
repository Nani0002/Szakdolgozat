<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use App\Models\Worksheet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorksheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i=0; $i < 30; $i++) {
            Worksheet::factory()->create([
                'customer_id' => Customer::inRandomOrder()->first(),
                'coworker_id' => User::where("role", "coworker")->inRandomOrder()->first() ?? null,
                'liable_id' => User::where("role", "liable")->inRandomOrder()->first() ?? null,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Outsourcing;
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
        for ($i=0; $i < User::count() * 8; $i++) {
            Worksheet::factory()->create([
                'customer_id' => Customer::inRandomOrder()->first(),
                'coworker_id' => User::where("role", "!=" ,"admin")->inRandomOrder()->first() ?? null,
                'liable_id' => User::where("role", "liable")->inRandomOrder()->first() ?? null,
            ]);
        }

        $outsourcings = Outsourcing::factory(3)->create();

        $outsourced = Worksheet::where("current_step", "waiting")->get();

        foreach ($outsourcings as $outsourcing) {
            $worksheet = $outsourced->pop();
            $worksheet->outsourcing_id = $outsourcing->id;
            $worksheet->save();
        }

        foreach ($outsourced as $worksheet) {
            $worksheet->outsourcing_id = $outsourcings->random()->id;
            $worksheet->save();
        }
    }
}

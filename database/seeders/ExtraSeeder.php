<?php

namespace Database\Seeders;

use App\Models\Extra;
use App\Models\Worksheet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExtraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Worksheet::with('computers')->get() as $worksheet) {
            $computers = $worksheet->computers;

            if ($computers->isEmpty()) {
                continue;
            }

            foreach ($computers as $computer) {
                $extras = Extra::factory(rand(0, 3))->create();

                foreach ($extras as $extra) {
                    DB::table('computer_extra')->insert([
                        'computer_id' => $computer->id,
                        'extra_id' => $extra->id,
                        'worksheet_id' => $worksheet->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

    }
}

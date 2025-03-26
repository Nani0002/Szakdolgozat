<?php

namespace Database\Seeders;

use App\Models\Extra;
use App\Models\Worksheet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExtraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Worksheet::all() as $worksheet) {
            Extra::factory(rand(0, 3))->for($worksheet)->create();
        }
    }
}

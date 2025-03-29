<?php

namespace Database\Seeders;

use App\Models\Computer;
use App\Models\Worksheet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComputerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Worksheet::all() as $worksheet) {
            $computers = Computer::factory(rand(1, 3))->create();

            foreach ($computers as $computer) {
                $worksheet->computers()->syncWithoutDetaching([
                    $computer->id => [
                        'password' => 'password',
                        'condition' => fake()->randomElement(['perfect', 'small problems', 'large problems']),
                        'imagename' => 'default_computer.jpg',
                        'imagename_hash' => 'default_computer.jpg',
                        'created_at' => now(),
                    ]
                ]);
            }
        }

        foreach (Computer::inRandomOrder()->limit(5)->get() as $computer) {
            $worksheetIds = Worksheet::inRandomOrder()->limit(2)->pluck('id');

            foreach ($worksheetIds as $worksheetId) {
                $computer->worksheets()->syncWithoutDetaching([
                    $worksheetId => [
                        'password' => 'password',
                        'condition' => fake()->randomElement(['perfect', 'small problems', 'large problems']),
                        'imagename' => 'default_computer.jpg',
                        'imagename_hash' => 'default_computer.jpg',
                        'created_at' => now(),
                    ]
                ]);
            }
        }
    }
}

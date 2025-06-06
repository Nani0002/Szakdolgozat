<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::factory(1)->create(["type" => "customer"]);
        Company::factory(1)->create(["type" => "partner"]);

        Company::factory(8)->create();
    }
}

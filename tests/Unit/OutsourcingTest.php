<?php

namespace Tests\Unit;

use App\Models\Outsourcing;
use App\Models\Worksheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutsourcingTest extends TestCase
{
    use RefreshDatabase;

    public function test_worksheet_belongs_to_outsourcing()
    {
        $outsourcing = Outsourcing::factory()->create();
        $worksheet = Worksheet::factory()->create(['outsourcing_id' => $outsourcing->id]);

        $this->assertTrue($worksheet->outsourcing->is($outsourcing));
    }

    public function test_outsourcing_has_one_worksheet()
    {
        $outsourcing = Outsourcing::factory()->create();
        $worksheet = Worksheet::factory()->create(['outsourcing_id' => $outsourcing->id]);

        $this->assertTrue($outsourcing->worksheet->is($worksheet));
    }
}

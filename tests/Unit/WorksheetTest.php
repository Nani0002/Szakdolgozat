<?php

namespace Tests\Unit;

use App\Models\Computer;
use App\Models\Customer;
use App\Models\Extra;
use App\Models\User;
use App\Models\Worksheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorksheetTest extends TestCase
{
    use RefreshDatabase;

    public function test_worksheet_belongs_to_coworker()
    {
        $worksheet = Worksheet::factory()->forCoworker()->create();
        $this->assertInstanceOf(User::class, $worksheet->coworker);
    }

    public function test_worksheet_belongs_to_liable()
    {
        $worksheet = Worksheet::factory()->forLiable()->create();
        $this->assertInstanceOf(User::class, $worksheet->liable);
    }

    public function test_worksheet_belongs_to_customer()
    {
        $worksheet = Worksheet::factory()->forCustomer()->create();
        $this->assertInstanceOf(Customer::class, $worksheet->customer);
    }

    public function test_worksheet_can_have_multiple_computers_with_pivot()
    {
        $worksheet = Worksheet::factory()->create();

        $computer1 = Computer::factory()->create();
        $computer2 = Computer::factory()->create();

        $worksheet->computers()->attach($computer1->id, [
            'password' => 'pw1',
            'condition' => 'good',
            'imagename' => 'img1.png',
            'imagename_hash' => 'img1.png',
        ]);

        $worksheet->computers()->attach($computer2->id, [
            'password' => 'pw2',
            'condition' => 'fair',
            'imagename' => 'img2.png',
            'imagename_hash' => 'img2.png',
        ]);

        $this->assertCount(2, $worksheet->computers);
        $this->assertEquals('pw1', $worksheet->computers[0]->pivot->password);
        $this->assertEquals('pw2', $worksheet->computers[1]->pivot->password);
    }

    public function test_worksheet_can_have_extras_via_computers()
    {
        $worksheet = Worksheet::factory()->create();
        $computer = Computer::factory()->create();

        $worksheet->computers()->attach($computer->id, [
            'password' => 'pw',
            'condition' => 'good',
            'imagename' => 'img.png',
            'imagename_hash' => 'img.png',
        ]);

        $extra1 = Extra::factory()->create();
        $extra2 = Extra::factory()->create();

        $computer->extras()->attach($extra1->id, ['worksheet_id' => $worksheet->id]);
        $computer->extras()->attach($extra2->id, ['worksheet_id' => $worksheet->id]);

        $extras = collect();
        foreach ($worksheet->computers as $computer) {
            $extras = $extras->merge(
                $computer->extras()->wherePivot('worksheet_id', $worksheet->id)->get()
            );
        }

        $this->assertCount(2, $extras);
        $this->assertTrue($extras->contains('id', $extra1->id));
        $this->assertTrue($extras->contains('id', $extra2->id));
    }

    public function test_get_types_returns_configured_steps()
    {
        $this->assertEquals(config('worksheet_steps'), Worksheet::getTypes());
    }
}

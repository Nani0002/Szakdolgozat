<?php

namespace Tests\Unit;

use App\Models\Computer;
use App\Models\Extra;
use App\Models\Worksheet;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ComputerTest extends TestCase
{
    use RefreshDatabase;

    public function test_computer_can_have_multiple_worksheets_with_pivot_data()
    {
        $worksheets = Worksheet::factory(3)->create();

        $computer = Computer::factory()->create();

        $computer->worksheets()->attach(
            $worksheets->pluck('id')->mapWithKeys(function ($id) {
                return [
                    $id => [
                        'password' => Hash::make('password'),
                        'condition' => 'good',
                        'imagename' => 'default_computer.png',
                        'imagename_hash' => 'default_computer.png',
                    ]
                ];
            })->toArray()
        );

        $this->assertInstanceOf(Collection::class, $computer->worksheets);
        $this->assertCount(3, $computer->worksheets);

        foreach ($computer->worksheets as $worksheet) {
            $this->assertInstanceOf(Worksheet::class, $worksheet);
        }
    }

    public function test_computer_can_have_multiple_extras_for_different_worksheets()
    {
        $computer = Computer::factory()->create();

        $worksheet1 = Worksheet::factory()->create();
        $worksheet2 = Worksheet::factory()->create();

        $extra1 = Extra::factory()->create();
        $extra2 = Extra::factory()->create();
        $extra3 = Extra::factory()->create();
        $extra4 = Extra::factory()->create();

        $computer->extras()->attach($extra1->id, ['worksheet_id' => $worksheet1->id]);
        $computer->extras()->attach($extra2->id, ['worksheet_id' => $worksheet2->id]);
        $computer->extras()->attach($extra3->id, ['worksheet_id' => $worksheet2->id]);
        $computer->extras()->attach($extra4->id, ['worksheet_id' => $worksheet2->id]);

        $this->assertEquals(1, $computer->extrasForWorksheet($worksheet1->id)->count());
        $this->assertTrue($computer->extrasForWorksheet($worksheet1->id)->contains($extra1));

        $this->assertEquals(3, $computer->extrasForWorksheet($worksheet2->id)->count());
        $this->assertTrue($computer->extrasForWorksheet($worksheet2->id)->contains($extra2));
        $this->assertTrue($computer->extrasForWorksheet($worksheet2->id)->contains($extra3));
        $this->assertTrue($computer->extrasForWorksheet($worksheet2->id)->contains($extra4));
        $this->assertFalse($computer->extrasForWorksheet($worksheet2->id)->contains($extra1));
    }

    public function test_computer_deletes_related_extras_when_soft_deleted()
    {
        $computer = Computer::factory()->create();
        $worksheet = Worksheet::factory()->create();
        $extra1 = Extra::factory()->create();
        $extra2 = Extra::factory()->create();
        $computer->extras()->attach($extra1->id, ['worksheet_id' => $worksheet->id]);
        $computer->extras()->attach($extra2->id, ['worksheet_id' => $worksheet->id]);

        $computer->delete();

        $extra1->refresh();
        $extra2->refresh();

        $this->assertNotNull($computer->deleted_at);
        $this->assertNotNull($extra1->deleted_at);
        $this->assertNotNull($extra2->deleted_at);
    }

    public function test_latest_info_returns_most_recent_worksheet()
    {
        $createdAt = now();

        $computer = Computer::factory()->create();
        $worksheet = Worksheet::factory()->create();
        $computer->worksheets()->attach($worksheet, [
            'password' => Hash::make('password'),
            'condition' => 'good',
            'imagename' => 'default_computer.png',
            'imagename_hash' => 'default_computer.png',
        ]);

        $this->assertEquals($worksheet->id, $computer->latestInfo()->id);

        Carbon::setTestNow($createdAt->copy()->addSeconds(5));

        $worksheet2 = Worksheet::factory()->create();
        $computer->worksheets()->attach($worksheet2, [
            'password' => Hash::make('password'),
            'condition' => 'better',
            'imagename' => 'default_computer.png',
            'imagename_hash' => 'default_computer.png',
        ]);

        $this->assertEquals($worksheet2->id, $computer->latestInfo()->id);

        Carbon::setTestNow();
    }

    public function test_get_latest_info_pivot_returns_correct_pivot_data_or_null() {
        $computer = Computer::factory()->create();
        $this->assertNull($computer->latestInfo());
    }
}

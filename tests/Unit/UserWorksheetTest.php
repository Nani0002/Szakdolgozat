<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Worksheet;
use Tests\TestCase;

class UserWorksheetTest extends TestCase
{
    public function test_sorted_worksheets_grouped_by_step()
    {
        $user = User::factory()->create();
        Worksheet::factory()->create(["current_step" => "open", "liable_id" => $user->id]);
        Worksheet::factory()->create(["current_step" => "closed", "liable_id" => $user->id]);
        Worksheet::factory()->create(["current_step" => "ongoing", "coworker_id" => $user->id]);
        Worksheet::factory()->create(["current_step" => "waiting", "coworker_id" => $user->id]);

        $sorted = $user->sortedWorksheets();

        $this->assertArrayHasKey('open', $sorted);
        $this->assertArrayHasKey('closed', $sorted);
        $this->assertArrayHasKey('ongoing', $sorted);
        $this->assertArrayHasKey('waiting', $sorted);
    }

    public function test_worksheets_by_step_returns_only_that_step()
    {
        $user = User::factory()->create();
        Worksheet::factory(3)->create(["current_step" => "open", "liable_id" => $user->id]);
        Worksheet::factory(4)->create(["current_step" => "closed", "liable_id" => $user->id]);
        Worksheet::factory(2)->create(["current_step" => "ongoing", "coworker_id" => $user->id]);
        Worksheet::factory(1)->create(["current_step" => "closed", "coworker_id" => $user->id]);

        $worksheets = $user->worksheetsByStep('open');

        $this->assertCount(3, $worksheets);
        $this->assertEquals('open', $worksheets->first()->current_step);

        $worksheets = $user->worksheetsByStep('closed');

        $this->assertCount(5, $worksheets);
        $this->assertEquals('closed', $worksheets->first()->current_step);
    }

    public function test_liable_worksheet_connection() {
        $user = User::factory()->create(["role" => "liable"]);
        Worksheet::factory(3)->create(["liable_id" => $user->id]);

        foreach ($user->liableWorksheets as $worksheet) {
            $this->assertEquals($user->id, $worksheet->liable_id);
        }
    }
}

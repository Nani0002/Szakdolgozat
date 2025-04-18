<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use App\Models\Worksheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WorksheetControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    protected function authenticateLiableUser()
    {
        $user = User::factory()->create(["role" => "liable"]);
        $this->actingAs($user);
        return $user;
    }

    public function test_guest_cannot_use_worksheets()
    {
        $response = $this->get(route('worksheet.create'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('worksheet.update', 1), []);
        $response->assertRedirect(route('login'));

        $response = $this->get(route('worksheet.destroy', 1));
        $response->assertRedirect(route('login'));
    }

    protected function getWorksheet(bool $final = false, string $sheet_number = "", ?User $user = null, string $current_step = "")
    {
        $user = $user ?? User::factory()->create();
        $customer = Customer::factory()->forCompany()->create();

        $data = [
            "customer_id" => $customer->id,
            "liable_id" => $user->id,
            "coworker_id" => $user->id,
            "final" => $final,
        ];

        if ($sheet_number !== "") {
            $data["sheet_number"] = $sheet_number;
        }

        if ($current_step !== "") {
            $data["current_step"] = $current_step;
        }

        return Worksheet::factory()
            ->hasComputers(3)
            ->create($data);
    }

    public function test_index_returns_view_with_user_worksheets()
    {
        $this->authenticateUser();
        $response = $this->get(route('worksheet.index'));
        $response->assertOk();
        $response->assertViewIs('layouts.menu');
        $response->assertViewHas('worksheets');
    }

    public function test_create_returns_view_with_dependencies()
    {
        $this->authenticateUser();
        Company::factory()->hasCustomers(5)->create(["type" => "customer"]);
        $response = $this->get(route('worksheet.create'));
        $response->assertOk();
        $response->assertViewIs('layouts.menu');
        $response->assertViewHas('worksheetTypes');
    }

    public function test_store_validates_and_saves_worksheet()
    {
        $this->authenticateUser();

        $data = [
            'sheet_number' => 'WSH-00123',
            'sheet_type' => 'warranty',
            'current_step' => 'ongoing',
            'declaration_mode' => 'email',
            'declaration_time' => now()->format('Y-m-d'),
            'declaration_time_hour' => '14:00',
            'print_date' => now()->addDay()->format('Y-m-d'),
            'print_date_hour' => '10:00',
            'liable_id' => User::factory()->create(["role" => "liable"])->id,
            'coworker_id' => User::factory()->create()->id,
            'customer_id' => Customer::factory()->create()->id,
            'work_start' => now()->format('Y-m-d'),
            'work_start_hour' => '09:00',
            'work_end' => now()->addHours(2)->format('Y-m-d'),
            'work_end_hour' => '11:00',
            'work_time' => 120,
            'comment' => 'Minor fix needed',
            'error_description' => 'The system won’t boot.',
            'work_description' => 'Replaced the power supply unit.',
            'outsourcing' => false,
        ];
        $response = $this->postJson(route('worksheet.store'), $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('worksheets', ['sheet_number' => $data['sheet_number']]);
    }

    public function test_store_fails_with_invalid_data()
    {
        $this->authenticateUser();
        $response = $this->postJson(route('worksheet.store'), []);

        $response->assertJsonValidationErrors([
            'sheet_number',
            'sheet_type',
            'current_step',
            'declaration_mode',
            'declaration_time',
            'declaration_time_hour',
            'liable_id',
            'coworker_id',
            'customer_id',
            'work_start',
            'work_start_hour',
            'error_description',
            'outsourcing',
        ]);
    }

    public function test_show_returns_correct_worksheet()
    {
        $this->authenticateUser();
        $worksheet = $this->getWorksheet();
        $response = $this->get(route('worksheet.show', $worksheet->id));
        $response->assertOk();
        $response->assertViewHas('worksheet');
    }

    public function test_show_returns_404_for_missing_worksheet()
    {
        $this->authenticateUser();
        $response = $this->get(route('worksheet.show', 1000));
        $response->assertNotFound();
        $response->assertStatus(404);
    }

    public function test_edit_returns_edit_view_if_not_final()
    {
        $this->authenticateUser();
        $worksheet = $this->getWorksheet();
        $response = $this->get(route('worksheet.edit', $worksheet->id));
        $response->assertStatus(200);
        $response->assertViewIs('layouts.menu');
        $response->assertViewHas('worksheet');
    }

    public function test_edit_redirects_if_final()
    {
        $this->authenticateUser();
        $worksheet = $this->getWorksheet(true);
        $response = $this->get(route('worksheet.edit', $worksheet->id));
        $response->assertRedirect(route('worksheet.show', $worksheet->id));
    }

    public function test_update_applies_valid_changes()
    {
        $this->authenticateUser();
        $worksheet = $this->getWorksheet();
        $data = [
            'sheet_number' => $worksheet->sheet_number,
            'sheet_type' => 'warranty',
            'current_step' => 'ongoing',
            'declaration_mode' => 'email',
            'declaration_time' => now()->format('Y-m-d'),
            'declaration_time_hour' => '14:00',
            'print_date' => now()->addDay()->format('Y-m-d'),
            'print_date_hour' => '10:00',
            'liable_id' => User::factory()->create(["role" => "liable"])->id,
            'coworker_id' => User::factory()->create()->id,
            'customer_id' => Customer::factory()->create()->id,
            'work_start' => now()->format('Y-m-d'),
            'work_start_hour' => '09:00',
            'work_end' => now()->addHours(2)->format('Y-m-d'),
            'work_end_hour' => '11:00',
            'work_time' => 120,
            'comment' => 'Minor fix needed',
            'error_description' => 'The system won’t boot.',
            'work_description' => 'Replaced the power supply unit.',
            'outsourcing' => false,
        ];

        $response = $this->putJson(route('worksheet.update', $worksheet->id), $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('worksheets', ['error_description' => $data['error_description']]);
    }

    public function test_destroy_deletes_existing_worksheet()
    {
        $this->authenticateUser();
        $worksheet = $this->getWorksheet();
        $response = $this->delete(route('worksheet.destroy', $worksheet->id));
        $response->assertRedirect(route('worksheet.index'));
        $this->assertSoftDeleted($worksheet);
    }

    public function test_search_returns_matching_worksheets()
    {
        $user = $this->authenticateUser();

        $this->getWorksheet(sheet_number: '12312421342', user: $user);
        $this->getWorksheet(sheet_number: '12312421345', user: $user);

        $otherUser = User::factory()->create();
        $this->getWorksheet(sheet_number: 'WS-00999', user: $otherUser);

        $response = $this->get(route('worksheet.search'), ['id' => '123']);

        $response->assertOk();
        $response->assertSee('12312421342');
        $response->assertSee('12312421345');
        $response->assertDontSee('WS-00999');
    }

    public function test_close_sets_step_to_closed()
    {
        $this->authenticateUser();
        $worksheet = $this->getWorksheet(current_step: "open");
        $response = $this->patch(route('worksheet.close', $worksheet->id));
        $response->assertRedirect(route('worksheet.index'));
        $this->assertDatabaseHas('worksheets', ['sheet_number' => $worksheet['sheet_number'], "current_step" => "closed"]);
    }

    public function test_move_within_same_step()
    {
        $user = $this->authenticateUser();

        $ws1 = $this->getWorksheet(sheet_number: 'WS-A', user: $user);
        $ws1->update(['current_step' => 'ongoing', 'liable_slot_number' => 0]);

        $ws2 = $this->getWorksheet(sheet_number: 'WS-B', user: $user);
        $ws2->update(['current_step' => 'ongoing', 'liable_slot_number' => 1]);

        $data = [
            'id' => $ws1->id,
            'newStatus' => 'ongoing',
            'newSlot' => 1,
        ];

        $response = $this->postJson(route('worksheet.move'), $data);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('worksheets', [
            'id' => $ws1->id,
            'liable_slot_number' => 1,
        ]);
    }

    public function test_move_to_different_step()
    {
        $user = $this->authenticateLiableUser();

        $ws = $this->getWorksheet(sheet_number: 'WS-Move', user: $user);
        $ws->update(['current_step' => 'ongoing', 'liable_slot_number' => 0]);

        $data = [
            'id' => $ws->id,
            'newStatus' => 'open',
            'newSlot' => 0,
        ];

        $response = $this->postJson(route('worksheet.move'), $data);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('worksheets', [
            'id' => $ws->id,
            'current_step' => 'open',
            'liable_slot_number' => 0,
        ]);
    }

    public function test_move_rejects_final_worksheet_status_change()
    {
        $user = $this->authenticateLiableUser();

        $ws = $this->getWorksheet(final: true, user: $user);
        $ws->update(['current_step' => 'ongoing', 'liable_slot_number' => 0]);

        $data = [
            'id' => $ws->id,
            'newStatus' => 'open',
            'newSlot' => 0,
        ];

        $response = $this->postJson(route('worksheet.move'), $data);
        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
    }

    public function test_print_sets_print_date() {
        $this->authenticateUser();
        $worksheet = $this->getWorksheet();
        $print_date = $worksheet->print_date;

        $response = $this->get(route('worksheet.print', $worksheet->id));
        $worksheet->refresh();

        $response->assertViewIs('layouts.print');
        $this->assertNotEquals($worksheet->print_date, $print_date);
    }

    public function test_liable_uer_cannot_final_worksheet()
    {
        $this->authenticateUser();
        $worksheet = $this->getWorksheet();
        $response = $this->post(route('worksheet.final', $worksheet->id));
        $response->assertForbidden();
    }

    public function test_final_closes_worksheet()
    {
        $this->authenticateLiableUser();
        $worksheet = $this->getWorksheet();
        $response = $this->post(route('worksheet.final', $worksheet->id));
        $response->assertRedirect(route('worksheet.show', $worksheet->id));
        $this->assertDatabaseHas('worksheets', ['sheet_number' => $worksheet['sheet_number'], "current_step" => "closed", "final" => true]);
    }
}

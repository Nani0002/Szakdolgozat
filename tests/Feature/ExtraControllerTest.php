<?php

namespace Tests\Feature\Feature;

use App\Models\Computer;
use App\Models\Extra;
use App\Models\User;
use App\Models\Worksheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExtraControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_user_can_access_extra_create_form_for_non_final_worksheet()
    {
        $this->authenticateUser();

        $worksheet = Worksheet::factory()->create(['final' => false]);
        $computer = Computer::factory()->create();

        $response = $this->get(route('extra.create', [
            'worksheet' => $worksheet->id,
            'computer' => $computer->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('layouts.menu');
        $response->assertViewHas(['connected_worksheet', 'connected_computer']);
    }

    public function test_user_is_redirected_when_creating_extra_for_final_worksheet()
    {
        $this->authenticateUser();

        $worksheet = Worksheet::factory()->create(['final' => true]);
        $computer = Computer::factory()->create();

        $response = $this->get(route('extra.create', [
            'worksheet' => $worksheet->id,
            'computer' => $computer->id,
        ]));

        $response->assertRedirect(route('worksheet.show', $worksheet->id));
    }

    public function test_create_form_returns_404_for_invalid_computer_or_worksheet()
    {
        $this->authenticateUser();

        $worksheet = Worksheet::factory()->create(['final' => false]);
        $computer = Computer::factory()->create();

        $response = $this->get(route('extra.create', ["worksheet" => 1000, "computer" => $computer->id]));

        $response->assertNotFound();
        $response->assertStatus(404);

        $response = $this->get(route('extra.create', ["worksheet" => $worksheet->id, "computer" => 1000]));

        $response->assertNotFound();
        $response->assertStatus(404);

        $response = $this->get(route('extra.create', ["worksheet" => 1000, "computer" => 1000]));

        $response->assertNotFound();
        $response->assertStatus(404);
    }

    public function test_user_can_store_extra_with_valid_data()
    {
        $this->authenticateUser();

        $worksheet = Worksheet::factory()->create(['final' => false]);
        $computer = Computer::factory()->create();

        $data = [
            "manufacturer" => "Test company",
            "type" => "Processor",
            "serial_number" => "123_456_789",
            "worksheet_id" => $worksheet->id,
            "computer_id" => $computer->id
        ];

        $response = $this->post(route('extra.store'), $data);

        $response->assertCreated();
        $response->assertRedirect(route('computer.show', $computer->id));
        $this->assertDatabaseHas('extras', ['serial_number' => $data['serial_number']]);
    }

    public function test_store_redirects_if_worksheet_is_final()
    {
        $this->authenticateUser();

        $worksheet = Worksheet::factory()->create(['final' => true]);
        $computer = Computer::factory()->create();

        $data = [
            "manufacturer" => "Test company",
            "type" => "Processor",
            "serial_number" => "123_456_789",
            "worksheet_id" => $worksheet->id,
            "computer_id" => $computer->id
        ];

        $response = $this->post(route('extra.store'), $data);

        $response->assertRedirect(route('worksheet.show', $worksheet->id));
    }

    public function test_store_validates_required_fields()
    {
        $this->authenticateUser();

        $response = $this->post(route('extra.store'), []);

        $response->assertSessionHasErrors([
            'worksheet_id',
            'computer_id',
            'manufacturer',
            'type',
            'serial_number'
        ]);
    }

    public function test_extra_is_attached_to_computer_after_store()
    {
        $this->authenticateUser();

        $worksheet = Worksheet::factory()->create(['final' => false]);
        $computer = Computer::factory()->create();

        $data = [
            "manufacturer" => "Test company",
            "type" => "Processor",
            "serial_number" => "123_456_789",
            "worksheet_id" => $worksheet->id,
            "computer_id" => $computer->id
        ];

        $this->post(route('extra.store'), $data);

        $this->assertDatabaseHas('extras', [
            'serial_number' => '123_456_789',
        ]);

        $extra = Extra::where('serial_number', '123_456_789')->first();
        $this->assertTrue($extra->computer->contains($computer));
    }

    public function test_user_can_access_extra_edit_form_for_non_final_worksheet()
    {
        $this->authenticateUser();
        $extra = Extra::factory()->create();
        $worksheet = Worksheet::factory()->create(["final" => false]);
        $computer = Computer::factory()->create();

        $extra->computer()->attach($computer->id, [
            'worksheet_id' => $worksheet->id,
        ]);

        $response = $this->get(route('extra.edit', [
            "extra" => $extra->id,
            'worksheet' => $worksheet->id,
            'computer' => $computer->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('layouts.menu');
        $response->assertViewHas(['connected_worksheet', 'connected_computer']);
    }

    public function test_user_is_redirected_when_editing_extra_on_final_worksheet()
    {
        $this->authenticateUser();
        $extra = Extra::factory()->create();
        $worksheet = Worksheet::factory()->create(["final" => true]);
        $computer = Computer::factory()->create();

        $extra->computer()->attach($computer->id, [
            'worksheet_id' => $worksheet->id,
        ]);

        $response = $this->get(route('extra.edit', [
            "extra" => $extra->id,
            'worksheet' => $worksheet->id,
            'computer' => $computer->id,
        ]));

        $response->assertRedirect(route('worksheet.show', $worksheet->id));
    }

    public function test_edit_form_returns_404_for_invalid_extra_or_computer_or_worksheet()
    {
        $this->authenticateUser();
        $extra = Extra::factory()->create();
        $worksheet = Worksheet::factory()->create(["final" => false]);
        $computer = Computer::factory()->create();

        $this->authenticateUser();

        $cases = [
            ['extra' => 9999, 'worksheet' => $worksheet->id, 'computer' => $computer->id],
            ['extra' => $extra->id, 'worksheet' => 9999, 'computer' => $computer->id],
            ['extra' => $extra->id, 'worksheet' => $worksheet->id, 'computer' => 9999],
        ];

        foreach ($cases as $case) {
            $response = $this->get(route('extra.edit', $case));
            $response->assertStatus(404);
        }
    }

    public function test_user_can_update_extra_fields()
    {
        $this->authenticateUser();

        $extra = Extra::factory()->create();
        $worksheet = Worksheet::factory()->create(["final" => false]);
        $computer = Computer::factory()->create();

        $extra->computer()->attach($computer->id, [
            'worksheet_id' => $worksheet->id,
        ]);

        $data = [
            "manufacturer" => "Test company",
            "type" => "Processor",
            "serial_number" => "123_456_789",
            "worksheet_id" => $worksheet->id,
            "computer_id" => $computer->id
        ];

        $response = $this->put(route('extra.update', ["extra" => $extra->id]), $data);

        $response->assertCreated();
        $response->assertRedirect(route('computer.show', $computer->id));
        $this->assertDatabaseHas('extras', ['manufacturer' => $data['manufacturer']]);
    }

    public function test_update_redirects_if_worksheet_is_final()
    {
        $this->authenticateUser();

        $extra = Extra::factory()->create();
        $worksheet = Worksheet::factory()->create(["final" => true]);
        $computer = Computer::factory()->create();

        $extra->computer()->attach($computer->id, [
            'worksheet_id' => $worksheet->id,
        ]);

        $data = [
            "manufacturer" => "Test company",
            "type" => "Processor",
            "serial_number" => "123_456_789",
            "worksheet_id" => $worksheet->id,
            "computer_id" => $computer->id
        ];

        $response = $this->put(route('extra.update', ["extra" => $extra->id]), $data);

        $response->assertRedirect(route('worksheet.show', $worksheet->id));
    }

    public function test_update_validates_fields()
    {
        $this->authenticateUser();

        $extra = Extra::factory()->create();

        $response = $this->put(route('extra.update', $extra->id), []);

        $response->assertSessionHasErrors([
            'worksheet_id',
            'computer_id',
            'manufacturer',
            'type',
        ]);
    }

    public function test_user_can_soft_delete_extra()
    {
        $this->authenticateUser();

        $extra = Extra::factory()->create();
        $worksheet = Worksheet::factory()->create(["final" => false]);
        $computer = Computer::factory()->create();

        $extra->computer()->attach($computer->id, [
            'worksheet_id' => $worksheet->id,
        ]);

        $response = $this->delete(route('extra.destroy', $extra->id));

        $response->assertRedirect(route('computer.show', $computer->id));
        $this->assertSoftDeleted($extra);
    }

    public function test_destroy_detaches_extra_from_computer()
    {
        $this->authenticateUser();

        $extra = Extra::factory()->create();
        $worksheet = Worksheet::factory()->create();
        $computer = Computer::factory()->create();

        $extra->computer()->attach($computer->id, [
            'worksheet_id' => $worksheet->id
        ]);

        $this->delete(route('extra.destroy', $extra->id));

        $this->assertDatabaseMissing('computer_extra', [
            'extra_id' => $extra->id,
            'computer_id' => $computer->id
        ]);
    }

    public function test_destroy_redirects_if_worksheet_is_final()
    {
        $this->authenticateUser();

        $extra = Extra::factory()->create();
        $worksheet = Worksheet::factory()->create(["final" => true]);
        $computer = Computer::factory()->create();

        $extra->computer()->attach($computer->id, [
            'worksheet_id' => $worksheet->id,
        ]);

        $response = $this->delete(route('extra.destroy', $extra->id));

        $response->assertRedirect(route('worksheet.show', $worksheet->id));
    }
}

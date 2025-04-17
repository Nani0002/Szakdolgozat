<?php

namespace Tests\Feature;

use App\Models\Computer;
use App\Models\User;
use App\Models\Worksheet;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ComputerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_guest_cannot_use_computers()
    {
        $response = $this->get(route('computer.create'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('computer.update', 1), []);
        $response->assertRedirect(route('login'));

        $response = $this->get(route('computer.destroy', 1));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_computer_index()
    {
        $this->authenticateUser();
        $response = $this->get(route('computer.index'));
        $response->assertOk();
        $response->assertViewIs('layouts.menu');
    }

    public function test_it_stores_computer_with_valid_data()
    {
        $this->authenticateUser();

        $data = [
            "manufacturer" => "Test company",
            "type" => "PC",
            "serial_number" => "123_456_789",
        ];

        $response = $this->post(route('computer.store'), $data);

        $computer = Computer::where('serial_number', $data['serial_number'])->first();

        $response->assertCreated();
        $response->assertRedirect(route('computer.show', $computer->id));
        $this->assertDatabaseHas('computers', ['serial_number' => $data['serial_number']]);
    }

    public function test_it_validates_fields_on_store()
    {
        $this->authenticateUser();

        $response = $this->post(route('computer.store'), []);

        $response->assertSessionHasErrors([
            'manufacturer',
            'type',
            'serial_number'
        ]);
    }

    public function test_user_can_view_computer_detail_page()
    {
        $this->authenticateUser();

        $computer = Computer::factory()->create();

        $response = $this->get(route('computer.show', $computer->id));

        $response->assertOk();
        $response->assertViewIs('layouts.menu');
        $response->assertViewHas('computer', function ($viewComputer) use ($computer) {
            return $viewComputer->id === $computer->id;
        });
        $response->assertSee($computer->manufacturer);
        $response->assertSee($computer->serial_number);
    }

    public function test_it_returns_404_for_invalid_computer_on_show()
    {
        $this->authenticateUser();
        $response = $this->get(route('computer.show', 1000));
        $response->assertNotFound();
        $response->assertStatus(404);
    }

    public function test_it_updates_computer_with_valid_data()
    {
        $this->authenticateUser();

        $computer = Computer::factory()->create();

        $data = [
            "manufacturer" => "Test company",
            "type" => "PC",
        ];

        $response = $this->put(route('computer.update', $computer->id), $data);

        $response->assertCreated();
        $response->assertRedirect(route('computer.show', $computer->id));
        $this->assertDatabaseHas('computers', ['type' => $data['type']]);
    }

    public function test_it_soft_deletes_computer_on_destroy()
    {
        $this->authenticateUser();

        $computer = Computer::factory()->create();

        $response = $this->delete(route('computer.destroy', $computer->id));

        $response->assertRedirect(route('computer.index'));
        $this->assertSoftDeleted($computer);
    }

    public function test_it_returns_computers_not_attached_to_given_worksheet()
    {
        $this->authenticateUser();
        $worksheet = Worksheet::factory()->create();
        $attached = Computer::factory()->count(2)->create();
        $unattached = Computer::factory()->count(3)->create();

        $worksheet->computers()->attach($attached->pluck('id')->toArray());

        $response = $this->getJson(route('computer.select', $worksheet->id));

        $response->assertOk();
        $response->assertJsonCount(3, 'computers');
    }

    public function test_it_attaches_computer_to_worksheet_with_valid_data()
    {
        $this->authenticateUser();
        $worksheet = Worksheet::factory()->create(['final' => false]);
        $computer = Computer::factory()->create();

        $data = [
            'computer_id' => $computer->id,
            'condition' => 'good',
            'password' => 'secret123',
        ];

        $response = $this->postJson(route('computer.attach', $worksheet->id), $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('computer_worksheet', [
            'worksheet_id' => $worksheet->id,
            'computer_id' => $computer->id,
        ]);
    }

    public function test_it_returns_error_if_attaching_to_final_worksheet()
    {
        $this->authenticateUser();
        $worksheet = Worksheet::factory()->create(['final' => true]);
        $computer = Computer::factory()->create();

        $data = [
            'computer_id' => $computer->id,
            'condition' => 'good',
            'password' => 'secret123',
        ];

        $response = $this->postJson(route('computer.attach', $worksheet->id), $data);

        $response->assertRedirect(route('worksheet.show', $worksheet->id));
    }

    public function test_it_returns_validation_errors_when_attaching_computer()
    {
        $this->authenticateUser();
        $worksheet = Worksheet::factory()->create();

        $response = $this->postJson(route('computer.attach', $worksheet->id), []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['computer_id', 'condition', 'password']);
    }

    public function test_it_stores_uploaded_image_when_attaching_computer()
    {
        $this->authenticateUser();
        Storage::fake('public');

        $worksheet = Worksheet::factory()->create(['final' => false]);
        $computer = Computer::factory()->create();

        $file = UploadedFile::fake()->image('pc.jpg');

        $this->postJson(route('computer.attach', $worksheet->id), [
            'computer_id' => $computer->id,
            'condition' => 'good',
            'password' => 'secret123',
            'imagefile' => $file,
        ])->assertStatus(201);

        $pivot = DB::table('computer_worksheet')->where('worksheet_id', $worksheet->id)->where('computer_id', $computer->id)->first();

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $disk->assertExists('images/' . $pivot->imagename_hash);
    }

    public function test_it_detaches_computer_from_worksheet()
    {
        $this->authenticateUser();
        $worksheet = Worksheet::factory()->create(['final' => false]);
        $computer = Computer::factory()->create();

        $worksheet->computers()->attach($computer->id);

        $response = $this->delete(route('computer.detach', [$worksheet->id, $computer->id]));

        $response->assertRedirect(route('worksheet.show', $worksheet->id));
        $this->assertDatabaseMissing('computer_worksheet', [
            'computer_id' => $computer->id,
            'worksheet_id' => $worksheet->id,
        ]);
    }

    public function test_it_redirects_if_detach_target_is_final_worksheet()
    {
        $this->authenticateUser();
        $worksheet = Worksheet::factory()->create(['final' => true]);
        $computer = Computer::factory()->create();

        $response = $this->delete(route('computer.detach', [$worksheet->id, $computer->id]));

        $response->assertRedirect(route('worksheet.show', $worksheet->id));
    }

    public function test_it_returns_computer_and_pivot_data_in_json()
    {
        $this->authenticateUser();
        $worksheet = Worksheet::factory()->create();
        $computer = Computer::factory()->create();

        $worksheet->computers()->attach($computer->id, [
            'password' => 'pw',
            'condition' => 'ok',
        ]);

        $pivotId = $worksheet->computers()->first()->pivot->id;

        $response = $this->getJson(route('computer.get', [$pivotId, $computer->id]));

        $response->assertOk();
        $response->assertJsonStructure(['success', 'pivot', 'computer']);
    }

    public function test_it_updates_pivot_data_and_image_on_refresh()
    {
        $this->authenticateUser();
        Storage::fake('public');

        $worksheet = Worksheet::factory()->create(['final' => false]);
        $computer = Computer::factory()->create();

        $worksheet->computers()->attach($computer->id, [
            'password' => 'pw',
            'condition' => 'old',
            'imagename_hash' => 'default_computer.jpg',
        ]);

        $pivotId = $worksheet->computers()->first()->pivot->id;

        $file = UploadedFile::fake()->image('new.jpg');

        $response = $this->putJson(route('computer.refresh'), [
            'pivot_id' => $pivotId,
            'key' => 0,
            'condition' => 'updated',
            'password' => 'newpass',
            'imagefile' => $file,
        ]);

        $response->assertStatus(201);
        $pivot = DB::table('computer_worksheet')->where('worksheet_id', $worksheet->id)->where('computer_id', $computer->id)->first();

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $disk->assertExists('images/' . $pivot->imagename_hash);
    }

    public function test_it_validates_fields_on_pivot_refresh()
    {
        $this->authenticateUser();

        $response = $this->putJson(route('computer.refresh'), []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['pivot_id', 'key', 'condition', 'password']);
    }

    public function test_it_deletes_old_image_when_refreshing_attachment()
    {
        $this->authenticateUser();
        Storage::fake('public');

        $oldImage = UploadedFile::fake()->image('old.jpg');
        $oldImage->storeAs('images', 'old_image.jpg', 'public');

        $worksheet = Worksheet::factory()->create(['final' => false]);
        $computer = Computer::factory()->create();

        $worksheet->computers()->attach($computer->id, [
            'password' => 'pw',
            'condition' => 'ok',
            'imagename_hash' => 'old_image.jpg',
        ]);

        $pivotId = $worksheet->computers()->first()->pivot->id;

        $newFile = UploadedFile::fake()->image('new.jpg');

        $this->putJson(route('computer.refresh'), [
            'pivot_id' => $pivotId,
            'key' => 0,
            'condition' => 'updated',
            'password' => 'newpass',
            'imagefile' => $newFile,
        ]);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        $disk->assertMissing('images/old_image.jpg');
        $disk->assertExists('images/' . $newFile->hashName());
    }


    public function test_it_aborts_refresh_if_worksheet_is_final()
    {
        $this->authenticateUser();

        $worksheet = Worksheet::factory()->create(['final' => true]);
        $computer = Computer::factory()->create();

        $worksheet->computers()->attach($computer->id);

        $pivotId = $worksheet->computers()->first()->pivot->id;

        $response = $this->put(route('computer.refresh'), [
            'pivot_id' => $pivotId,
            'key' => 0,
            'condition' => 'updated',
            'password' => 'newpass',
        ]);

        $response->assertRedirect(route('worksheet.show', $worksheet->id));
    }
}

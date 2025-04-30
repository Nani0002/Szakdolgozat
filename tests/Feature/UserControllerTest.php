<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    protected function authenticateAdminUser()
    {
        $user = User::factory()->create(["role" => "admin"]);
        $this->actingAs($user);
        return $user;
    }

    public function test_guest_sees_guest_home_with_nav_urls()
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('layouts.menu');
        $response->assertViewHas('navUrls');
    }

    public function test_logged_in_user_sees_sorted_tickets()
    {
        $this->authenticateUser();
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('layouts.menu');
        $response->assertViewHas('tickets');
    }

    public function test_admin_user_can_access_register_form()
    {
        $this->authenticateAdminUser();

        $response = $this->get('register');

        $response->assertStatus(200);
        $response->assertViewIs('layouts.menu');
    }

    public function test_admin_user_cannot_access_worksheets() {
        $this->authenticateAdminUser();

        $response = $this->get(route('worksheet.index'));

        $response->assertForbidden();
    }

    public function test_non_admin_user_is_redirected_from_create_form()
    {
        $this->authenticateUser();

        $response = $this->get('register');

        $response->assertForbidden();
    }

    public function test_user_can_register_new_user_with_valid_data()
    {
        $this->authenticateAdminUser();

        $data = [
            "name" => "Test user",
            "email" => "email@example.com",
            "password" => "password",
            "password_confirmation" => "password",
            "role" => "1"
        ];

        $response = $this->post(route('register'), $data);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', ['email' => 'email@example.com']);
    }

    public function test_user_role_defaults_to_coworker_if_no_role_is_given()
    {
        $this->authenticateAdminUser();

        $data = [
            "name" => "Test user",
            "email" => "email@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ];

        $response = $this->post(route('register'), $data);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', ['email' => $data["email"], "role" => "coworker"]);

        $this->authenticateAdminUser();

        $data = [
            "name" => "Test user",
            "email" => "email2@example.com",
            "password" => "password",
            "password_confirmation" => "password",
            "role" => "1"
        ];

        $response = $this->post(route('register'), $data);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', ['email' => $data["email"], "role" => "liable"]);
    }

    public function test_user_is_logged_in_after_registration()
    {
        $this->authenticateAdminUser();

        $data = [
            "name" => "New User",
            "email" => "email@example.com",
            "password" => "password",
            "password_confirmation" => "password",
            "role" => "liable",
        ];

        $response = $this->post(route('register'), $data);

        $user = User::where('email', $data["email"])->first();

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_validates_fields_on_registration()
    {
        $this->authenticateAdminUser();
        $response = $this->post(route('register'), []);
        $response->assertSessionHasErrors([
            "name",
            "email",
            "password",
        ]);
    }

    public function test_register_rejects_duplicate_emails()
    {
        $this->authenticateAdminUser();
        User::factory()->create(["email" => "email@example.com"]);

        $data = [
            "name" => "New User",
            "email" => "email@example.com",
            "password" => "password",
            "password_confirmation" => "password",
            "role" => "liable",
        ];

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasErrors([
            "email",
        ]);
    }

    public function test_user_can_view_own_profile_page()
    {
        $user = $this->authenticateUser();

        $response = $this->get(route('user'));

        $response->assertStatus(200);
        $response->assertViewIs('layouts.menu');
        $response->assertViewHas('profile', $user);
    }

    public function test_user_can_update_password_with_confirmation()
    {
        $user = $this->authenticateUser();
        $data = [
            "password" => "password2",
            "password_confirmation" => "password2",
        ];

        $response = $this->post(route('user.new_password'), $data);

        $response->assertStatus(201);

        $this->assertTrue(Hash::check("password2", $user->fresh()->password));
    }

    public function test_password_update_fails_without_confirmation()
    {
        $this->authenticateUser();
        $data = [
            "password" => "password",
        ];

        $response = $this->post(route('user.new_password'), $data);
        $response->assertSessionHasErrors([
            "password",
        ]);
    }

    public function test_password_is_hashed_after_update()
    {
        $user = $this->authenticateUser();
        $data = [
            "password" => "password2",
            "password_confirmation" => "password2",
        ];

        $response = $this->post(route('user.new_password'), $data);
        $response->assertStatus(201);

        $updatedUser = $user->fresh();

        $this->assertNotEquals($data["password"], $updatedUser->password);
        $this->assertTrue(Hash::check($data["password"], $updatedUser->password));
    }

    public function test_user_can_upload_new_profile_image()
    {
        Storage::fake('public');
        $user = $this->authenticateUser();

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->postJson(route('user.new_image'), [
            'image' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $disk->assertExists('images/' . $file->hashName());

        $this->assertEquals($file->getClientOriginalName(), $user->fresh()->imagename);
        $this->assertEquals($file->hashName(), $user->fresh()->imagename_hash);
    }

    public function test_old_user_image_is_deleted_on_new_upload()
    {
        Storage::fake('public');
        $user = $this->authenticateUser();

        $oldImage = UploadedFile::fake()->image('old.png');
        $oldName = 'old_img.jpg';
        Storage::disk('public')->put('images/' . $oldName, $oldImage->getContent());

        $user->update([
            'imagename' => 'old.png',
            'imagename_hash' => $oldName,
        ]);

        $newFile = UploadedFile::fake()->image('new.jpg');

        $this->postJson(route('user.new_image'), [
            'image' => $newFile,
        ]);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $disk->assertMissing('images/' . $oldName);
        $disk->assertExists('images/' . $newFile->hashName());
    }

    public function test_set_image_validates_file_type()
    {
        $this->authenticateUser();

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->postJson(route('user.new_image'), [
            'image' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['image']);
    }

    public function test_new_image_is_saved_and_user_record_is_updated()
    {
        Storage::fake('public');
        $user = $this->authenticateUser();

        $file = UploadedFile::fake()->image('profile.jpg');

        $this->postJson(route('user.new_image'), [
            'image' => $file,
        ]);

        $user = $user->fresh();

        $this->assertEquals($file->getClientOriginalName(), $user->imagename);
        $this->assertEquals($file->hashName(), $user->imagename_hash);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $disk->assertExists('images/' . $file->hashName());
    }
}

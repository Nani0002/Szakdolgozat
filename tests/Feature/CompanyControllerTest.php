<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_guest_cannot_use_companies() {
        $response = $this->get(route('company.create'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('company.update', 1), []);
        $response->assertRedirect(route('login'));

        $response = $this->get(route('company.destroy', 1));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_company_index()
    {
        $this->authenticateUser();
        $response = $this->get(route('company.index'));
        $response->assertOk();
        $response->assertViewIs('layouts.menu');
    }

    public function test_company_index_returns_sorted_companies()
    {
        $this->authenticateUser();

        Company::factory()->count(2)->create(['type' => 'partner']);
        Company::factory()->count(3)->create(['type' => 'customer']);

        $response = $this->get(route('company.index'));

        $response->assertOk();
        $response->assertViewHas('companies', function ($companies) {
            return isset($companies['partner'], $companies['customer']) &&
                count($companies['partner']) === 2 &&
                count($companies['customer']) === 3;
        });
    }

    public function test_user_can_access_create_form_with_default_type()
    {
        $this->authenticateUser();

        $response = $this->get(route('company.create'));
        $response->assertOk();
        $response->assertViewIs('layouts.menu');
        $response->assertViewHas('type', 'partner');
    }

    public function test_user_can_access_create_form_with_specified_type()
    {
        $this->authenticateUser();

        $response = $this->get(route('company.create', ['type' => 'customer']));
        $response->assertOk();
        $response->assertViewHas('type', 'customer');

        $response = $this->get(route('company.create', ['type' => 'partner']));
        $response->assertOk();
        $response->assertViewHas('type', 'partner');
    }

    public function test_it_creates_company_with_valid_data()
    {
        $this->authenticateUser();

        $data = [
            'name' => 'Test Company',
            'type' => 'partner',
            'post_code' => '1234',
            'city' => 'Testville',
            'street' => 'Example St. 123',
            'phone' => '123456789',
            'email' => 'company@example.com',
        ];

        $response = $this->post(route('company.store'), $data);

        $response->assertRedirect(route('company.index'));
        $this->assertDatabaseHas('companies', ['email' => $data['email']]);
    }

    public function test_it_validates_fields_on_create()
    {
        $this->authenticateUser();

        $response = $this->post(route('company.store'), []);

        $response->assertSessionHasErrors([
            'name',
            'post_code',
            'city',
            'street',
            'phone',
            'email',
        ]);
    }

    public function test_user_can_edit_existing_company()
    {
        $this->authenticateUser();
        $company = Company::factory()->create(["type" => "customer"]);

        $response = $this->get(route('company.edit', $company->id));

        $response->assertOk();
        $response->assertViewIs('layouts.menu');
        $response->assertViewHas('company', $company);
    }

    public function test_editing_nonexistent_company_returns_404()
    {
        $this->authenticateUser();

        $data = [
            'name' => 'Test Company',
            'type' => 'partner',
            'post_code' => '1234',
            'city' => 'Testville',
            'street' => 'Example St. 123',
            'phone' => '123456789',
            'email' => 'company@example.com',
        ];

        $response = $this->put(route('company.update', 1000), $data);

        $response->assertNotFound();
        $response->assertStatus(404);
    }

    public function test_it_updates_company_with_valid_data()
    {
        $this->authenticateUser();

        $company = Company::factory()->create(["type" => "customer"]);

        $data = [
            'name' => 'Test Company',
            'type' => 'partner',
            'post_code' => '1234',
            'city' => 'Testville',
            'street' => 'Example St. 123',
            'phone' => '123456789',
            'email' => 'company@example.com',
        ];

        $response = $this->put(route('company.update', $company->id), $data);

        $response->assertRedirect(route('company.index'));
        $this->assertDatabaseHas('companies', ['email' => $data['email']]);
    }

    public function test_it_validates_fields_on_update()
    {
        $this->authenticateUser();

        $company = Company::factory()->create(["type" => "customer"]);

        $response = $this->put(route('company.update', $company->id), []);

        $response->assertSessionHasErrors([
            'name',
            'post_code',
            'city',
            'street',
            'phone',
            'email',
        ]);
    }

    public function test_it_deletes_company()
    {
        $this->authenticateUser();

        $company = Company::factory()->create(["type" => "customer"]);

        $response = $this->delete(route('company.destroy', $company->id));

        $response->assertRedirect(route('company.index'));
        $this->assertSoftDeleted($company);
    }

    public function test_deleting_nonexistent_company_returns_404()
    {
        $this->authenticateUser();
        $response = $this->delete(route('company.destroy', 1000));
        $response->assertNotFound();
        $response->assertStatus(404);
    }

    public function test_it_returns_customers_for_given_company_in_json()
    {
        $this->authenticateUser();
        $company = Company::factory()->hasCustomers(3)->create(["type" => "customer"]);

        $response = $this->get(route('company.customers', ["id" => $company->id]));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $response->assertJsonCount(3, 'customers');

        $response->assertJsonStructure([
            'success',
            'customers' => [
                ['id', 'name', 'email', 'mobile']
            ],
        ]);
    }

    public function test_it_returns_404_if_company_id_is_invalid_in_getCustomers()
    {
        $this->authenticateUser();
        $response = $this->get(route('company.customers', 1000));
        $response->assertNotFound();
        $response->assertStatus(404);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_guest_cannot_use_customers() {
        $response = $this->get(route('customer.create'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('customer.update', 1), []);
        $response->assertRedirect(route('login'));

        $response = $this->get(route('customer.destroy', 1));
        $response->assertRedirect(route('login'));
    }

    public function test_it_creates_customer_with_valid_data()
    {
        $this->authenticateUser();

        $company = Company::factory()->create();

        $data = [
            'id' => $company->id,
            'name' => 'Test Steve',
            'email' => 'email@example.com',
            'phone' => '123456789',
        ];

        $response = $this->postJson(route('customer.store'), $data);

        $response->assertCreated();
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('customers', ['email' => $data['email'], 'company_id' => $company->id]);
        $response->assertSee('Test Steve');
        $response->assertSee('email@example.com');
    }

    public function test_it_validates_required_fields_on_create()
    {
        $this->authenticateUser();

        $response = $this->post(route('customer.store'), []);

        $response->assertSessionHasErrors([
            'name',
            'email',
            'phone'
        ]);
    }

    public function test_it_returns_404_for_invalid_company_id_on_create() {
        $this->authenticateUser();

        $data = [
            'id' => 1000,
            'name' => 'Test Steve',
            'email' => 'email@example.com',
            'phone' => '123456789',
        ];

        $response = $this->postJson(route('customer.store'), $data);

        $response->assertNotFound();
        $response->assertStatus(404);
    }

    public function test_it_updates_customer_with_valid_data() {
        $this->authenticateUser();

        $customer = Customer::factory()->forCompany()->create();

        $data = [
            'name' => 'Test Steve',
            'email' => 'email@example.com',
            'phone' => '123456789',
        ];

        $response = $this->putJson(route('customer.update', $customer->id), $data);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('customers', ['email' => $data['email']]);

        $response->assertSee('Test Steve');
        $response->assertSee('email@example.com');
    }

    public function test_it_validates_fields_on_update() {
        $this->authenticateUser();
        $customer = Customer::factory()->forCompany()->create();

        $response = $this->post(route('customer.store', $customer->id), []);

        $response->assertSessionHasErrors([
            'name',
            'email',
            'phone'
        ]);
    }

    public function test_it_soft_deletes_customer_on_destroy() {
        $this->authenticateUser();

        $customer = Customer::factory()->forCompany()->create();

        $response = $this->delete(route('customer.destroy', $customer->id));

        $response->assertRedirect(route('company.index'));
        $this->assertSoftDeleted($customer);
    }

    public function test_deleting_nonexistent_customer_returns_404() {
        $this->authenticateUser();
        $response = $this->delete(route('customer.destroy', 1000));
        $response->assertNotFound();
        $response->assertStatus(404);
    }
}

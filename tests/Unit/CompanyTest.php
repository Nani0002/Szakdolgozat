<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_can_have_customers() {
        $company = Company::factory()->hasCustomers(3)->create(["type" => "customer"]);

        $this->assertInstanceOf(Collection::class, $company->customers);
        $this->assertCount(3, $company->customers);

        foreach ($company->customers as $customer) {
            $this->assertInstanceOf(Customer::class, $customer);
        }
    }

    public function test_sorted_companies_groups_by_type () {
        Company::factory(4)->create(["type" => "customer"]);
        Company::factory(5)->create(["type" => "partner"]);

        $companies = Company::sortedCompanies();
        $this->assertCount(4, $companies["customer"]);
        $this->assertCount(5, $companies["partner"]);
    }
}

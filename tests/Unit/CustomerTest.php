<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Worksheet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_belongs_to_company() {
        $customer = Customer::factory()->forCompany()->create();
        $this->assertInstanceOf(Company::class, $customer->company);
    }

    public function test_customer_has_many_worksheets() {
        $customer = Customer::factory()->hasWorksheets(4)->create();

        $this->assertInstanceOf(Collection::class, $customer->worksheets);
        $this->assertCount(4, $customer->worksheets);

        foreach ($customer->worksheets as $worksheet) {
            $this->assertInstanceOf(Worksheet::class, $worksheet);
        }
    }
}

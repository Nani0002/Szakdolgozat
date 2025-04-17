<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Company;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request)
    {
        $company = Company::findOrFail($request["id"]);

        $customer = new Customer();
        $customer->name = $request["name"];
        $customer->email = $request["email"];
        $customer->mobile = $request["phone"];
        $customer->company_id = $company->id;

        $customer->save();

        return response()->json([
            'success' => true,
            "html" => view('companies._card', compact('customer', 'company'))->render()
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        $company = Company::findOrFail($customer->company_id);

        $customer->name = $request["name"];
        $customer->email = $request["email"];
        $customer->mobile = $request["phone"];

        $customer->save();

        return response()->json([
            'success' => true,
            "html" => view('companies._card', compact('customer', 'company'))->render()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect(route('company.index'));
    }
}

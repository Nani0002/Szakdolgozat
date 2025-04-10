<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $company = Company::findOrFail($request["id"]);

        $validated = $request->validate([
            "name" => "required|string",
            "email" => "required|string|email",
            "phone" => "required|string",
            "id" => "required|string",
        ]);

        $customer = new Customer();
        $customer->name = $validated["name"];
        $customer->email = $validated["email"];
        $customer->mobile = $validated["phone"];
        $customer->company_id = $validated["id"];

        $customer->save();

        return response()->json([
            'success' => true,
            "html" => view('companies._card', compact('customer', 'company'))->render()
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $customer = Customer::find($id);
        $company = Company::findOrFail($customer->company_id);

        $validated = $request->validate([
            "name" => "required|string",
            "email" => "required|string|email",
            "phone" => "required|string",
        ]);

        $customer->name = $validated["name"];
        $customer->email = $validated["email"];
        $customer->mobile = $validated["phone"];

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
        $customer = Customer::find($id);
        $customer->delete();
        return redirect(route('company.index'));
    }
}

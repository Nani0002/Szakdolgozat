<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('layouts.menu', ["companies" => Company::sortedCompanies()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('layouts.menu', ["type" => $request->query('type', 'partner')]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => "string|required",
            "type" => "string|required",
            "post_code" => "string|required",
            "city" => "string|required",
            "street" => "string|required",
            "phone" => "string|required",
            "email" => "string|email|required"
        ]);

        $company = new Company();
        $company["name"] = $validated["name"];
        $company["type"] = $validated["type"];
        $company["post_code"] = $validated["post_code"];
        $company["city"] = $validated["city"];
        $company["street"] = $validated["street"];
        $company["phone"] = $validated["phone"];
        $company["email"] = $validated["email"];

        $company->save();

        return redirect(route('company.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('layouts.menu', ["company" => Company::find($id)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            "name" => "string|required",
            "post_code" => "string|required",
            "city" => "string|required",
            "street" => "string|required",
            "phone" => "string|required",
            "email" => "string|email|required"
        ]);

        $company = Company::findOrFail($id);
        $company["name"] = $validated["name"];
        $company["post_code"] = $validated["post_code"];
        $company["city"] = $validated["city"];
        $company["street"] = $validated["street"];
        $company["phone"] = $validated["phone"];
        $company["email"] = $validated["email"];

        $company->update();

        return redirect(route('company.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return redirect(route('company.index'));
    }

    /**
     * Get all customers in a customer company.
     */
    public function getCustomers(Request $request)
    {
        $id = $request->query('id');
        $company = Company::findOrFail($id);

        return response()->json([
            "success" => true,
            "customers" => $company->customers()->get()
        ]);
    }
}

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
        if (Auth::check()) {
            return view('layouts.menu', ["navUrls" => User::getNavUrls(true), "userUrls" => Auth::user()->getUserUrls(), "companies" => Company::sortedCompanies()]);
        } else {
            return redirect(route('home'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (Auth::check()) {
            return view('layouts.menu', ["navUrls" => User::getNavUrls(true), "userUrls" => Auth::user()->getUserUrls(), "type" => $request->query('type', 'partner')]);
        } else {
            return redirect(route('home'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => "string|required",
            "type" => "string|required",
            "post_code" => "string|required",
            "city" => "string|required",
            "street" => "string|required",
            "phone" => "string|required",
            "email" => "string|email|required"
        ]);

        $company = new Company();
        $company->name = $request->name;
        $company->type = $request->type;
        $company->post_code = $request->post_code;
        $company->city = $request->city;
        $company->street = $request->street;
        $company->phone = $request->phone;
        $company->email = $request->email;

        $company->save();

        return redirect(route('company.index'));
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
        if (Auth::check()) {
            return view('layouts.menu', ["navUrls" => User::getNavUrls(true), "userUrls" => Auth::user()->getUserUrls(), "company" => Company::find($id)]);
        } else {
            return redirect(route('home'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "name" => "string|required",
            "post_code" => "string|required",
            "city" => "string|required",
            "street" => "string|required",
            "phone" => "string|required",
            "email" => "string|email|required"
        ]);

        $company = Company::find($id);
        $company->name = $request->name;
        $company->post_code = $request->post_code;
        $company->city = $request->city;
        $company->street = $request->street;
        $company->phone = $request->phone;
        $company->email = $request->email;

        $company->update();

        return redirect(route('company.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $company = Company::find($id);

        $company->delete();

        return redirect(route('company.index'));
    }

    public function getCustomers(Request $request)
    {
        $id = $request->query('id');
        $company = Company::find($id);

        if ($company) {
            return response()->json([
                "success" => true,
                "customers" => $company->customers()->get()
            ]);
        }

        return response()->json(["success" => false]);
    }
}

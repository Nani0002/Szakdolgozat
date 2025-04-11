<?php

namespace App\Http\Controllers;

use App\Models\Computer;
use App\Models\Extra;
use App\Models\User;
use App\Models\Worksheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExtraController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('layouts.menu', [
            "connected_worksheet" => Worksheet::findOrFail($request["worksheet"]),
            "connected_computer" => Computer::findOrFail($request["computer"])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "worksheet_id" => "required|exists:worksheets,id",
            "computer_id" => "required|exists:computers,id",
            "manufacturer" => "required|string",
            "type" => "required|string",
            "serial_number" => "required|string|unique:extras,serial_number",
        ]);

        $extra = new Extra();
        $extra["manufacturer"] = $validated["manufacturer"];
        $extra["type"] = $validated["type"];
        $extra["serial_number"] = $validated["serial_number"];

        $extra->save();

        $extra->computer()->attach($validated['computer_id'], [
            'worksheet_id' => $validated['worksheet_id'],
        ]);

        return redirect(route('computer.show', $validated["computer_id"]));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        return view('layouts.menu', [
            "extra" =>  Extra::findOrFail($id),
            "connected_worksheet" => Worksheet::findOrFail($request["worksheet"]),
            "connected_computer" => Computer::findOrFail($request["computer"])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            "worksheet_id" => "required|exists:worksheets,id",
            "computer_id" => "required|exists:computers,id",
            "manufacturer" => "required|string",
            "type" => "required|string",
        ]);

        $extra = Extra::findOrFail($id);

        $extra["manufacturer"] = $validated["manufacturer"];
        $extra["type"] = $validated["type"];

        $extra->save();

        return redirect(route('computer.show', $validated["computer_id"]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $extra = Extra::findOrFail($id);

        $computer_id = $extra->computer[0]->id;
        $extra->computer()->detach();
        $extra->delete();

        return redirect(route('computer.show', $computer_id));
    }
}

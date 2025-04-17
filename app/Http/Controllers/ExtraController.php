<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExtraRequest;
use App\Models\Computer;
use App\Models\Extra;
use App\Models\Worksheet;
use Illuminate\Http\Request;

class ExtraController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $worksheet = Worksheet::findOrFail($request["worksheet"]);
        if($worksheet["final"] == true){
            return redirect(route('worksheet.show', $worksheet->id));
        }
        return view('layouts.menu', [
            "connected_worksheet" => $worksheet,
            "connected_computer" => Computer::findOrFail($request["computer"])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExtraRequest $request)
    {
        $worksheet = Worksheet::findOrFail($request["worksheet_id"]);
        if($worksheet["final"] == true){
            return redirect(route('worksheet.show', $worksheet->id));
        }

        $extra = new Extra();
        $extra->manufacturer = $request["manufacturer"];
        $extra->type = $request["type"];
        $extra->serial_number = $request["serial_number"];

        $extra->save();

        $extra->computer()->attach($request['computer_id'], [
            'worksheet_id' => $request['worksheet_id'],
        ]);

        return redirect(route('computer.show', $request["computer_id"]), 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $worksheet = Worksheet::findOrFail($request["worksheet"]);
        if($worksheet["final"] == true){
            return redirect(route('worksheet.show', $worksheet->id));
        }

        return view('layouts.menu', [
            "extra" =>  Extra::findOrFail($id),
            "connected_worksheet" => $worksheet,
            "connected_computer" => Computer::findOrFail($request["computer"])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExtraRequest $request, Extra $extra)
    {
        $worksheet = $extra->worksheet()->first();
        if($worksheet->final == true){
            return redirect(route('worksheet.show', $worksheet->id));
        }

        $extra->manufacturer = $request["manufacturer"];
        $extra->type = $request["type"];

        $extra->save();

        return redirect(route('computer.show', $request["computer_id"]), 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $extra = Extra::findOrFail($id);
        $worksheet = $extra->worksheet()->first();
        if($worksheet["final"] == true){
            return redirect(route('worksheet.show', $worksheet->id));
        }

        $computer_id = $extra->computer[0]->id;
        $extra->computer()->detach();
        $extra->delete();

        return redirect(route('computer.show', $computer_id));
    }
}

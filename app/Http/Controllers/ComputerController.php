<?php

namespace App\Http\Controllers;

use App\Models\Computer;
use App\Models\User;
use App\Models\Worksheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ComputerController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('layouts.menu');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "manufacturer" => "string|required",
            "type" => "string|required",
            "serial_number" => "string|required|unique:computers,serial_number",
        ]);

        $computer = new Computer();
        $computer->manufacturer = $validated["manufacturer"];
        $computer->type = $validated["type"];
        $computer->serial_number = $validated["serial_number"];

        $computer->save();

        return redirect(route('computer.show', $computer->id));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $computer = Computer::findOrFail($id);
        return view('layouts.menu', [
            "computer" => $computer,
            "latest" => $computer->latestInfo()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('layouts.menu', ["computer" => Computer::findOrFail($id)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            "manufacturer" => "string|required",
            "type" => "string|required",
        ]);

        $computer = Computer::findOrFail($id);
        $computer->manufacturer = $validated["manufacturer"];
        $computer->type = $validated["type"];

        $computer->save();

        return redirect(route('computer.show', $computer->id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $computer = Computer::findOrFail($id);

        $computer->delete();

        return redirect(route('worksheet.index'));
    }

    /**
     * Get all computers with no attachment to the worksheet.
     */
    public function select(string $worksheet)
    {
        return response()->json([
            "success" => true,
            "computers" => Computer::whereDoesntHave('worksheets', function ($f) use ($worksheet) {
                $f->where('worksheet_id', $worksheet);
            })->get()
        ]);
    }

    /**
     * Attach a computer to the worksheet.
     */
    public function attach(Request $request, string $worksheet_id)
    {
        $worksheet = Worksheet::findOrFail($worksheet_id);
        if($worksheet["final"] == true){
            return redirect(route('worksheet.show', $worksheet->id));
        }

        $validated = $request->validate([
            "computer_id" => "required|integer",
            "condition" => "required|string",
            "password" => "required|string",
            "imagefile" => "nullable|image|mimes:jpeg,png,jpg,gif",
        ]);

        $computer = Computer::findOrFail($validated["computer_id"]);
        if (!$worksheet || !$computer) {
            return response()->json([
                "success" => false,
            ]);
        }

        $originalName = "default_computer.jpg";
        $hashedName = "default_computer.jpg";
        if ($request->hasFile('imagefile')) {
            $originalName = $request->file('imagefile')->getClientOriginalName();
            $hashedName = Str::random(40) . '.' . $request->file('imagefile')->getClientOriginalExtension();

            $request->file('imagefile')->storeAs('public/images', $hashedName);
        }

        $worksheet->computers()->attach($computer->id, [
            "password" => $validated["password"],
            "condition" => $validated["condition"],
            "imagename" => $originalName,
            "imagename_hash" => $hashedName,
        ]);

        $computer = $worksheet->computers()->where('computers.id', $computer->id)->first();

        $key = $worksheet->computers()->count() - 1;

        return response()->json([
            "success" => true,
            "html" => view('computers._card', compact('computer', 'key', 'worksheet'))->render(),
        ], 201);
    }

    /**
     * Remove the connection between the computer and the worksheet.
     */
    public function detach(string $worksheet, string $computer)
    {
        $ws = Worksheet::findOrFail($worksheet);
        if($ws["final"] == true){
            return redirect(route('worksheet.show', $ws->id));
        }

        $ws->computers()->detach($computer);

        return redirect(route('worksheet.show', $ws));
    }

    /**
     * Get the attached computer and its attachment.
     */
    public function get(string $pivot, string $computer)
    {
        $comp = Computer::findOrFail($computer);
        $piv = DB::table('computer_worksheet')->where('id', $pivot)->first();

        return response()->json([
            "success" => true,
            "pivot" => $piv,
            "computer" => $comp->only(['id', 'manufacturer', 'type', 'serial_number']),
        ]);
    }

    /**
     * Update the connection between the computer and the worksheet.
     */
    public function refresh(Request $request)
    {
        $validated = $request->validate([
            "pivot_id" => "required|integer",
            "key" => "required|integer",
            "condition" => "required|string",
            "password" => "required|string",
            "imagefile" => "nullable|image|mimes:jpeg,png,jpg,gif",
        ]);

        $pivot = DB::table('computer_worksheet')->where('id', $validated["pivot_id"])->first();
        $worksheet = Worksheet::findOrFail($pivot->worksheet_id);
        if($worksheet["final"] == true){
            return redirect(route('worksheet.show', $worksheet->id));
        }

        $originalName = "default_computer.jpg";
        $hashedName = "default_computer.jpg";
        if ($request->hasFile('imagefile')) {

            $originalName = $request->file('imagefile')->getClientOriginalName();
            $hashedName = Str::random(40) . '.' . $request->file('imagefile')->getClientOriginalExtension();

            if ($pivot->imagename_hash != "default_computer.jpg" && $pivot->imagename_hash  != "default_user.png") {
                Storage::delete('public/images/' . $pivot->imagename_hash);
            }

            $request->file('imagefile')->storeAs('public/images', $hashedName);
        }

        DB::table('computer_worksheet')
            ->where('id', $request['pivot_id'])
            ->update([
                'condition' => $validated['condition'],
                'password' => $validated['password'],
                'imagename' => $originalName,
                'imagename_hash' => $hashedName,
                'updated_at' => now(),
            ]);

        $computer = Computer::findOrFail($pivot->computer_id);
        $computer->pivot = (object) [
            'id' => $pivot->id,
            'imagename' => $originalName,
            'imagename_hash' => $hashedName,
            'condition' => $validated['condition'],
            'password' => $validated['password'],
            'worksheet_id' => $pivot->worksheet_id,
            'computer_id' => $pivot->computer_id,
            'created_at' => $pivot->created_at,
            'updated_at' => now(),
        ];

        $key = $validated["key"];

        return response()->json([
            "success" => true,
            "html" => view('computers._card', compact('computer', 'key', 'worksheet'))->render(),
        ], 201);
    }
}

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
        if (Auth::check()) {
            return view('layouts.menu', ["navUrls" => User::getNavUrls(true), "userUrls" => Auth::user()->getUserUrls()]);
        } else {
            return redirect(route('home'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (Auth::check()) {
            $computer = Computer::findOrFail($id);
            return view('layouts.menu', ["navUrls" => User::getNavUrls(true), "userUrls" => Auth::user()->getUserUrls(), "computer" => $computer, "latest" => $computer->latestInfo()]);
        } else {
            return redirect(route('home'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (Auth::check()) {
            $computer = Computer::findOrFail($id);
            return view('layouts.menu', ["navUrls" => User::getNavUrls(true), "userUrls" => Auth::user()->getUserUrls(), "computer" => $computer]);
        } else {
            return redirect(route('home'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $computer = Computer::findOrFail($id);

        $$computer->delete();

        return redirect(route('worksheet.index'));
    }

    public function select(string $worksheet)
    {
        if (Auth::check()) {
            return response()->json([
                "success" => true,
                "computers" => Computer::whereDoesntHave('worksheets', function ($f) use ($worksheet) {
                    $f->where('worksheet_id', $worksheet);
                })->get()
            ]);
        } else {
            return response()->json([
                "success" => false,
            ]);
        }
    }

    public function attach(Request $request, string $worksheet_id)
    {
        if (Auth::check()) {
            $worksheet = Worksheet::findOrFail($worksheet_id);
            $computer = Computer::findOrFail($request["computer_id"]);
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
                "password" => $request["password"],
                "condition" => $request["condition"],
                "imagename" => $originalName,
                "imagename_hash" => $hashedName,
            ]);

            $computer = $worksheet->computers()->where('computers.id', $computer->id)->first();

            $key = $worksheet->computers()->count() - 1;


            return response()->json([
                "success" => true,
                "html" => view('computers._card', compact('computer', 'key', 'worksheet'))->render(),
            ]);
        } else {
            return response()->json([
                "success" => false,
            ]);
        }
    }

    public function detach(string $worksheet, string $computer)
    {
        $ws = Worksheet::findOrFail($worksheet);

        $ws->computers()->detach($computer);

        return redirect(route('worksheet.show', $ws));
    }

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

    public function refresh(Request $request)
    {
        $originalName = "default_computer.jpg";
        $hashedName = "default_computer.jpg";
        if ($request->hasFile('imagefile')) {
            $pivot = DB::table('computer_worksheet')->where('id', $request["pivot_id"])->first();

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
                'condition' => $request['condition'],
                'password' => $request['password'],
                'imagename' => $originalName,
                'imagename_hash' => $hashedName,
            ]);

        return response()->json([
            "success" => true,
            "html" => null,
        ]);
    }
}

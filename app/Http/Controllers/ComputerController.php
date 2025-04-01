<?php

namespace App\Http\Controllers;

use App\Models\Computer;
use App\Models\User;
use App\Models\Worksheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function attach(Request $request, string $worksheet)
    {
        if (Auth::check()) {
            $ws = Worksheet::findOrFail($worksheet);
            $computer = Computer::findOrFail($request["computer_id"]);
            if (!$ws || !$computer) {
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

            $ws->computers()->attach($computer->id, [
                "password" => $request["password"],
                "condition" => $request["condition"],
                "imagename" => $originalName,
                "imagename_hash" => $hashedName,
            ]);

            $computer = $ws->computers()->where('computers.id', $computer->id)->first();

            $key = $ws->computers()->count() - 1;

            return response()->json([
                "success" => true,
                "html" => view('computers._card', compact('computer', 'key'))->render(),
            ]);
        } else {
            return response()->json([
                "success" => false,
            ]);
        }
    }
}

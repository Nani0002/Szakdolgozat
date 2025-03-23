<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Worksheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorksheetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::check()) {
            return view('layouts.menu', ["navUrls" => User::getNavUrls(true), "userUrls" => Auth::user()->getUserUrls(), "worksheets" => Auth::user()->sortedWorksheets(), "worksheetTypes" => Worksheet::getTypes(), "user_id" => Auth::id()]);
        } else {
            return redirect(route('home'));
        }
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $worksheet = Worksheet::findOrFail($id);
        if ($worksheet)
            $worksheet->delete();
        return redirect(route('worksheet.index'));
    }

    public function search(Request $request)
    {
        $search = $request->query("id");
        $user = Auth::user();
        $liable = $user->liableWorksheets()
            ->where('sheet_number', 'like', "%{$search}%")
            ->get();

        $coworker = $user->coworkerWorksheets()
            ->where('sheet_number', 'like', "%{$search}%")
            ->get();

        $results = $liable->merge($coworker)->sortBy('sheet_number')->values();

        dd($results);
    }

    public function close(string $id)
    {
        $worksheet = Worksheet::findOrFail($id);
        if ($worksheet) {
            $worksheet->current_step = 'closed';
            $worksheet->update();
        }
        return redirect(route('worksheet.index'));
    }

    public function move(Request $request)
    {
        $id = $request["id"];
        $newStep = $request["newStatus"];
        $newSlot = $request["newSlot"];
        $newWorksheet = Worksheet::findOrFail($id);

        if ($newWorksheet) {
            $oldStep = $newWorksheet->current_step;
            foreach (Auth::user()->worksheetsByStep($oldStep) as $worksheet) {
                if ($worksheet->id != $id && $worksheet->slot_number > $newWorksheet->slot_number) {
                    $worksheet->slot_number = $worksheet->slot_number - 1;
                    $worksheet->save();
                }
            }

            foreach (Auth::user()->worksheetsByStep($newStep) as $worksheet) {
                if ($worksheet->id != $id && $worksheet->slot_number >= $newSlot) {
                    $worksheet->slot_number = $worksheet->slot_number + 1;
                    $worksheet->save();
                }
            }

            $newWorksheet->current_step = $newStep;
            $newWorksheet->slot_number = $newSlot;
            $newWorksheet->save();

            return response()->json([
                'success' => true,
                'old' => Auth::user()->worksheetsByStep($oldStep),
                'new' => Auth::user()->worksheetsByStep($newStep)
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'Could not find worksheet with id of ' . $id]);
        }
    }
}

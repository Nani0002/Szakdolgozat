<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Outsourcing;
use App\Models\User;
use App\Models\Worksheet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorksheetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return view('layouts.menu', [
            "navActions" =>[['type' => 'create', 'text' => "munkalap", "url" => route('worksheet.create')]],
            "worksheets" => $user->sortedWorksheets(),
            "worksheetTypes" => Worksheet::getTypes(),
            "user_id" => Auth::id()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('layouts.menu', [
            "navActions" =>[['type' => 'create', 'text' => "munkalap", "url" => route('worksheet.create')]],
            "worksheetTypes" => Worksheet::getTypes(),
            "users" => User::all(),
            "loggedIn" => Auth::id(),
            "companies" => Company::all(),
            "current_step" => isset($request["current_step"]) ? $request["current_step"] : ""
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(["outsourcing" => "required|boolean"]);
        $isOutourced = $validated["outsourcing"];
        if ($isOutourced == true) {
            $validated = $request->validate([
                "sheet_number" => "required|unique:worksheets,sheet_number",
                "sheet_type" => "required|in:maintanance,paid,warranty",
                "current_step" => "required|in:open,started,ongoing,price_offered,waiting,to_invoice,closed",
                "declaration_mode" => "required|in:email,phone,personal,onsite",
                "declaration_time" => "required|date",
                "declaration_time_hour" => "required|date_format:H:i",
                "print_date" => "nullable|date",
                "print_date_hour" => "nullable|date_format:H:i",
                "liable_id" => "required|integer",
                "coworker_id" => "required|integer",
                "customer_id" => "required|integer",
                "work_start" => "nullable|date",
                "work_start_hour" => "nullable|date_format:H:i",
                "work_end" => "nullable|date",
                "work_end_hour" => "nullable|date_format:H:i",
                "work_time" => "nullable|integer",
                "error_description" => "required",
                "partner_id" => "required|integer",
                "entry_time" => "required|date",
                "entry_time_hour" => "required|date_format:H:i",
                "finished" => "required|in:ongoing,finished,brought",
                "outsourced_number" => "required|unique:outsourcings,outsourced_number",
                "outsourced_price" => "required|integer",
                "our_price" => "required|integer",
            ]);
        } else {
            $validated = $request->validate([
                "sheet_number" => "required|unique:worksheets,sheet_number",
                "sheet_type" => "required|in:maintanance,paid,warranty",
                "current_step" => "required|in:open,started,ongoing,price_offered,waiting,to_invoice,closed",
                "declaration_mode" => "required|in:email,phone,personal,onsite",
                "declaration_time" => "required|date",
                "declaration_time_hour" => "required|date_format:H:i",
                "print_date" => "nullable|date",
                "print_date_hour" => "nullable|date_format:H:i",
                "liable_id" => "required|integer",
                "coworker_id" => "required|integer",
                "customer_id" => "required|integer",
                "work_start" => "nullable|date",
                "work_start_hour" => "nullable|date_format:H:i",
                "work_end" => "nullable|date",
                "work_end_hour" => "nullable|date_format:H:i",
                "work_time" => "nullable|integer",
                "error_description" => "required",
            ]);
        }

        $ws = new Worksheet();
        $ws["sheet_number"] = $validated["sheet_number"];
        $ws["sheet_type"] = $validated["sheet_type"];
        $ws["current_step"] = $validated["current_step"];
        $ws["declaration_mode"] = $validated["declaration_mode"];

        $date = $validated['declaration_time'];
        $time = $validated['declaration_time_hour'];
        $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
        $ws["declaration_time"] = $datetime;

        $date = $validated['print_date'];
        $time = $validated['print_date_hour'];
        if ($date && $time) {
            $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
            $ws["print_date"] = $datetime;
        } else {
            $ws["print_date"] = null;
        }
        $ws["liable_id"] = $validated["liable_id"];
        $ws["coworker_id"] = $validated["coworker_id"];
        $ws["customer_id"] = $validated["customer_id"];

        $date = $validated['work_start'];
        $time = $validated['work_start_hour'];
        $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
        $ws["work_start"] = $datetime;

        $date = $validated['work_end'];
        $time = $validated['work_end_hour'];
        if ($date && $time) {
            $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
            $ws["work_end"] = $datetime;
        } else {
            $ws["work_end"] = null;
        }
        $ws["work_time"] = $validated["work_time"];
        $ws["error_description"] = $validated["error_description"];

        if ($isOutourced == true) {
            $outsourcing = new Outsourcing();

            $date = $validated['entry_time'];
            $time = $validated['entry_time_hour'];
            $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
            $outsourcing["entry_time"] = $datetime;

            $outsourcing["finished"] = $validated["finished"];
            $outsourcing["outsourced_number"] = $validated["outsourced_number"];
            $outsourcing["outsourced_price"] = $validated["outsourced_price"];
            $outsourcing["our_price"] = $validated["our_price"];
            $outsourcing["company_id"] = $validated["partner_id"];

            $outsourcing->save();

            $ws["outsourcing_id"] = $outsourcing["id"];
        }

        $ws->save();

        return response()->json(['success' => true, 'id' => $ws->id], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('layouts.menu', [
            "navActions" =>[['type' => 'create', 'text' => "munkalap", "url" => route('worksheet.create')]],
            "worksheet" => Worksheet::findOrFail($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('layouts.menu', [
            "navActions" =>[['type' => 'create', 'text' => "munkalap", "url" => route('worksheet.create')]],
            "worksheet" => Worksheet::findOrFail($id),
            "worksheetTypes" => Worksheet::getTypes(),
            "users" => User::all(),
            "loggedIn" => Auth::id(),
            "companies" => Company::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ws = Worksheet::findOrFail($id);
        $validated = $request->validate(["outsourcing" => "required|boolean"]);

        $isOutourced = $validated["outsourcing"];
        if ($isOutourced == true) {
            $validated = $request->validate([
                "sheet_type" => "required|in:maintanance,paid,warranty",
                "current_step" => "required|in:open,started,ongoing,price_offered,waiting,to_invoice,closed",
                "declaration_mode" => "required|in:email,phone,personal,onsite",
                "declaration_time" => "required|date",
                "declaration_time_hour" => "required|date_format:H:i",
                "print_date" => "nullable|date",
                "print_date_hour" => "nullable|date_format:H:i",
                "liable_id" => "required|integer",
                "coworker_id" => "required|integer",
                "customer_id" => "required|integer",
                "work_start" => "nullable|date",
                "work_start_hour" => "nullable|date_format:H:i",
                "work_end" => "nullable|date",
                "work_end_hour" => "nullable|date_format:H:i",
                "work_time" => "nullable|integer",
                "error_description" => "required",
                "partner_id" => "required|integer",
                "entry_time" => "required|date",
                "entry_time_hour" => "required|date_format:H:i",
                "finished" => "required|in:ongoing,finished,brought",
                "outsourced_number" => "required|unique:outsourcings,outsourced_number",
                "outsourced_price" => "required|integer",
                "our_price" => "required|integer",
            ]);
        } else {
            $validated = $request->validate([
                "sheet_type" => "required|in:maintanance,paid,warranty",
                "current_step" => "required|in:open,started,ongoing,price_offered,waiting,to_invoice,closed",
                "declaration_mode" => "required|in:email,phone,personal,onsite",
                "declaration_time" => "required|date",
                "declaration_time_hour" => "required|date_format:H:i",
                "print_date" => "nullable|date",
                "print_date_hour" => "nullable|date_format:H:i",
                "liable_id" => "required|integer",
                "coworker_id" => "required|integer",
                "customer_id" => "required|integer",
                "work_start" => "nullable|date",
                "work_start_hour" => "nullable|date_format:H:i",
                "work_end" => "nullable|date",
                "work_end_hour" => "nullable|date_format:H:i",
                "work_time" => "nullable|integer",
                "error_description" => "required",
            ]);
        }

        $ws["sheet_type"] = $validated["sheet_type"];
        $ws["current_step"] = $validated["current_step"];
        $ws["declaration_mode"] = $validated["declaration_mode"];

        $date = $validated['declaration_time'];
        $time = $validated['declaration_time_hour'];
        $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
        $ws["declaration_time"] = $datetime;

        $date = $validated['print_date'];
        $time = $validated['print_date_hour'];
        if ($date && $time) {
            $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
            $ws["print_date"] = $datetime;
        } else {
            $ws["print_date"] = null;
        }
        $ws["liable_id"] = $validated["liable_id"];
        $ws["coworker_id"] = $validated["coworker_id"];
        $ws["customer_id"] = $validated["customer_id"];

        $date = $validated['work_start'];
        $time = $validated['work_start_hour'];
        $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
        $ws["work_start"] = $datetime;

        $date = $validated['work_end'];
        $time = $validated['work_end_hour'];
        if ($date && $time) {
            $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
            $ws["work_end"] = $datetime;
        } else {
            $ws["work_end"] = null;
        }
        $ws["work_time"] = $validated["work_time"];
        $ws["error_description"] = $validated["error_description"];

        if (isset($ws->outsourcing)) {
            $outsourcing = Outsourcing::findOrFail($ws["outsourcing_id"]);

            $date = $validated['entry_time'];
            $time = $validated['entry_time_hour'];
            $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
            $outsourcing["entry_time"] = $datetime;

            $outsourcing["finished"] = $request["finished"];
            $outsourcing["outsourced_price"] = $request["outsourced_price"];
            $outsourcing["our_price"] = $request["our_price"];
            $outsourcing["company_id"] = $request["partner_id"];

            $outsourcing->save();
        } else if ($isOutourced == true) {
            $outsourcing = new Outsourcing();
            $date = $validated['entry_time'];
            $time = $validated['entry_time_hour'];
            $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
            $outsourcing["entry_time"] = $datetime;

            $outsourcing["finished"] = $validated["finished"];
            $outsourcing["outsourced_price"] = $validated["outsourced_price"];
            $outsourcing["our_price"] = $validated["our_price"];
            $outsourcing["company_id"] = $validated["partner_id"];

            $outsourcing->save();

            $ws["outsourcing_id"] = $outsourcing["id"];
        }
        $ws->save();

        return response()->json(['success' => true, 'id' => $ws->id], 201);
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

    /**
     * Search for all worksheets that contain a string in their sheet number field.
     */
    public function search(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $search = $request["id"];
        $liable = $user->liableWorksheets()
            ->where('sheet_number', 'like', "%{$search}%")
            ->get();

        $coworker = $user->coworkerWorksheets()
            ->where('sheet_number', 'like', "%{$search}%")
            ->get();

        $results = $liable->merge($coworker)->sortBy('sheet_number')->values();

        return view('layouts.menu', [
            "navActions" =>[['type' => 'create', 'text' => "munkalap", "url" => route('worksheet.create')]],
            "worksheets" => $results,
            "querry" => $search
        ]);
    }

    /**
     * Set woksheet step to closed.
     */
    public function close(string $id)
    {
        $worksheet = Worksheet::findOrFail($id);
        if ($worksheet) {
            $worksheet->current_step = 'closed';
            $worksheet->update();
        }
        return redirect(route('worksheet.index'));
    }

    /**
     * Update the worksheet's current step via drag and drop.
     */
    public function move(Request $request)
    {
        $id = $request["id"];
        $newWorksheet = Worksheet::findOrFail($id);

        if ($newWorksheet) {
            $newStep = $request["newStatus"];
            $newSlot = $request["newSlot"];

            /** @var \App\Models\User $user */
            $user = Auth::user();

            if ($newWorksheet->current_step == $newStep) {
                $worksheets = $user->worksheetsByStep($newStep)->sortBy('slot_number')->values();

                $filtered = $worksheets->reject(fn($ws) => $ws->id == $newWorksheet->id)->values();

                $filtered->splice($newSlot, 0, [$newWorksheet]);

                foreach ($filtered as $index => $worksheet) {
                    $worksheet->slot_number = $index;
                    $worksheet->save();
                }
            } else {
                $oldStep = $newWorksheet->current_step;
                foreach ($user->worksheetsByStep($oldStep) as $worksheet) {
                    if ($worksheet->id != $id && $worksheet->slot_number > $newWorksheet->slot_number) {
                        $worksheet->slot_number = $worksheet->slot_number - 1;
                        $worksheet->save();
                    }
                }

                foreach ($user->worksheetsByStep($newStep) as $worksheet) {
                    if ($worksheet->id != $id && $worksheet->slot_number >= $newSlot) {
                        $worksheet->slot_number = $worksheet->slot_number + 1;
                        $worksheet->save();
                    }
                }

                $newWorksheet->current_step = $newStep;
                $newWorksheet->slot_number = $newSlot;
                $newWorksheet->save();
            }
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'Could not find worksheet with id of ' . $id], 404);
        }
    }

    /**
     * Display the printing preview page of the workssheet.
     */
    public function getPrintPage($id)
    {
        $worksheet = Worksheet::findOrFail($id);
        $worksheet["print_date"] = now();

        $worksheet->save();
        return view('layouts.print', ["worksheet" => $worksheet]);
    }

    /**
     * Make worksheet uneditable.
     */
    public function final($worksheet)
    {
        $worksheet = Worksheet::findOrFail($worksheet);
        $worksheet["final"] = true;
        return redirect(route('worksheet.show', $worksheet));
    }
}

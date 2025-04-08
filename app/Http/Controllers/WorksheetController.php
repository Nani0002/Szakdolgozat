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
        if (Auth::check()) {
            return view('layouts.menu', ["navUrls" => User::getNavUrls(true, [['type' => 'create', 'text' => "munkalap", "url" => route('worksheet.create')]]), "userUrls" => Auth::user()->getUserUrls(true), "worksheets" => Auth::user()->sortedWorksheets(), "worksheetTypes" => Worksheet::getTypes(), "user_id" => Auth::id()]);
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
            return view('layouts.menu', [
                "navUrls" => User::getNavUrls(true, [['type' => 'create', 'text' => "munkalap", "url" => route('worksheet.create')]]),
                "userUrls" => Auth::user()->getUserUrls(),
                "worksheetTypes" => Worksheet::getTypes(),
                "users" => User::all(),
                "loggedIn" => Auth::user()->id,
                "companies" => Company::all(),
                "current_step" => isset($request["current_step"]) ? $request["current_step"] : ""
            ]);
        } else {
            return redirect(route('home'));
        }
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
        $ws["sheet_number"] = $request["sheet_number"];
        $ws["sheet_type"] = $request["sheet_type"];
        $ws["current_step"] = $request["current_step"];
        $ws["declaration_mode"] = $request["declaration_mode"];

        $date = $request['declaration_time'];
        $time = $request['declaration_time_hour'];
        $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
        $ws["declaration_time"] = $datetime;

        $date = $request['print_date'];
        $time = $request['print_date_hour'];
        if ($date && $time) {
            $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
            $ws["print_date"] = $datetime;
        } else {
            $ws["print_date"] = null;
        }
        $ws["liable_id"] = $request["liable_id"];
        $ws["coworker_id"] = $request["coworker_id"];
        $ws["customer_id"] = $request["customer_id"];

        $date = $request['work_start'];
        $time = $request['work_start_hour'];
        $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
        $ws["work_start"] = $datetime;

        $date = $request['work_end'];
        $time = $request['work_end_hour'];
        if ($date && $time) {
            $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
            $ws["work_end"] = $datetime;
        } else {
            $ws["work_end"] = null;
        }
        $ws["work_time"] = $request["work_time"];
        $ws["error_description"] = $request["error_description"];

        if ($isOutourced == true) {
            $outsourcing = new Outsourcing();

            $date = $request['entry_time'];
            $time = $request['entry_time_hour'];
            $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
            $outsourcing["entry_time"] = $datetime;

            $outsourcing["finished"] = $request["finished"];
            $outsourcing["outsourced_number"] = $request["outsourced_number"];
            $outsourcing["outsourced_price"] = $request["outsourced_price"];
            $outsourcing["our_price"] = $request["our_price"];
            $outsourcing["company_id"] = $request["partner_id"];

            $outsourcing->save();

            $ws["outsourcing_id"] = $outsourcing["id"];
        }

        $ws->save();

        return (redirect(route('worksheet.show', $ws->id)));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (Auth::check()) {
            return view('layouts.menu', ["navUrls" => User::getNavUrls(true, [['type' => 'create', 'text' => "munkalap", "url" => route('worksheet.create')]]), "userUrls" => Auth::user()->getUserUrls(true), "worksheet" => Worksheet::findOrFail($id)]);
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
            return view('layouts.menu', [
                "navUrls" => User::getNavUrls(true, [['type' => 'create', 'text' => "munkalap", "url" => route('worksheet.create')]]),
                "userUrls" => Auth::user()->getUserUrls(),
                "worksheet" => Worksheet::findOrFail($id),
                "worksheetTypes" => Worksheet::getTypes(),
                "users" => User::all(),
                "loggedIn" => Auth::user()->id,
                "companies" => Company::all(),
            ]);
        } else {
            return redirect(route('home'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate(["outsourcing" => "required|boolean"]);
        $ws = Worksheet::findOrFail($id);
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

        $ws["sheet_type"] = $request["sheet_type"];
        $ws["current_step"] = $request["current_step"];
        $ws["declaration_mode"] = $request["declaration_mode"];

        $date = $request['declaration_time'];
        $time = $request['declaration_time_hour'];
        $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
        $ws["declaration_time"] = $datetime;

        $date = $request['print_date'];
        $time = $request['print_date_hour'];
        if ($date && $time) {
            $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
            $ws["print_date"] = $datetime;
        } else {
            $ws["print_date"] = null;
        }
        $ws["liable_id"] = $request["liable_id"];
        $ws["coworker_id"] = $request["coworker_id"];
        $ws["customer_id"] = $request["customer_id"];

        $date = $request['work_start'];
        $time = $request['work_start_hour'];
        $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
        $ws["work_start"] = $datetime;

        $date = $request['work_end'];
        $time = $request['work_end_hour'];
        if ($date && $time) {
            $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
            $ws["work_end"] = $datetime;
        } else {
            $ws["work_end"] = null;
        }
        $ws["work_time"] = $request["work_time"];
        $ws["error_description"] = $request["error_description"];

        if (isset($ws->outsourcing)) {
            $outsourcing = Outsourcing::findOrFail($ws["outsourcing_id"]);

            $date = $request['entry_time'];
            $time = $request['entry_time_hour'];
            $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
            $outsourcing["entry_time"] = $datetime;

            $outsourcing["finished"] = $request["finished"];
            $outsourcing["outsourced_price"] = $request["outsourced_price"];
            $outsourcing["our_price"] = $request["our_price"];
            $outsourcing["company_id"] = $request["partner_id"];

            $outsourcing->save();
        } else if ($isOutourced == true) {
            $outsourcing = new Outsourcing();
            $date = $request['entry_time'];
            $time = $request['entry_time_hour'];
            $datetime = Carbon::createFromFormat('Y-m-d H:i', "$date $time");
            $outsourcing["entry_time"] = $datetime;

            $outsourcing["finished"] = $request["finished"];
            $outsourcing["outsourced_price"] = $request["outsourced_price"];
            $outsourcing["our_price"] = $request["our_price"];
            $outsourcing["company_id"] = $request["partner_id"];

            $outsourcing->save();

            $ws["outsourcing_id"] = $outsourcing["id"];
        }
        $ws->save();

        return (redirect(route('worksheet.show', $ws->id)));
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
        if (Auth::check()) {
            $search = $request->query("id");
            $user = Auth::user();
            $liable = $user->liableWorksheets()
                ->where('sheet_number', 'like', "%{$search}%")
                ->get();

            $coworker = $user->coworkerWorksheets()
                ->where('sheet_number', 'like', "%{$search}%")
                ->get();

            $results = $liable->merge($coworker)->sortBy('sheet_number')->values();

            return view('layouts.menu', ["navUrls" => User::getNavUrls(true, [['type' => 'create', 'text' => "munkalap", "url" => route('worksheet.create')]]), "userUrls" => Auth::user()->getUserUrls(), "worksheets" => $results, "querry" => $search]);
        } else {
            return redirect(route('home'));
        }
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
            if ($newWorksheet->current_step == $newStep) {
                $worksheets = Auth::user()->worksheetsByStep($newStep)->sortBy('slot_number')->values();

                $filtered = $worksheets->reject(fn($ws) => $ws->id == $newWorksheet->id)->values();

                $filtered->splice($newSlot, 0, [$newWorksheet]);

                foreach ($filtered as $index => $worksheet) {
                    $worksheet->slot_number = $index;
                    $worksheet->save();
                }
            } else {
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
            }
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'Could not find worksheet with id of ' . $id]);
        }
    }
}

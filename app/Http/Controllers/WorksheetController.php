<?php

namespace App\Http\Controllers;

use App\Http\Requests\DragAndDropRequest;
use App\Http\Requests\WorksheetRequest;
use App\Models\Company;
use App\Models\Outsourcing;
use App\Models\User;
use App\Models\Worksheet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WorksheetController extends Controller
{
    /**
     * Helper function to convert into DateTime.
     */
    private function toDateTime(?string $date, ?string $time): ?Carbon
    {
        if ($date && $time) {
            return Carbon::createFromFormat('Y-m-d H:i', "$date $time");
        }
        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return view('layouts.menu', [
            "navActions" => [['type' => 'create', 'text' => "munkalap", "url" => route('worksheet.create')]],
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
            "navActions" => [['type' => 'create', 'text' => "munkalap", "url" => route('worksheet.create')]],
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
    public function store(WorksheetRequest $request)
    {
        $validated = $request->validated();
        $isOutourced = $validated["outsourcing"];

        $ws = new Worksheet();
        $ws->sheet_number = $validated["sheet_number"];

        $ws->sheet_type = $validated["sheet_type"];
        $ws->current_step = $validated["current_step"];
        $ws->declaration_mode = $validated["declaration_mode"];

        $ws->declaration_time = $this->toDateTime($validated['declaration_time'], $validated['declaration_time_hour']);
        $ws->print_date = $this->toDateTime($validated['print_date'], $validated['print_date_hour']);

        $ws->liable_id = $validated["liable_id"];
        $ws->coworker_id = $validated["coworker_id"];
        $ws->customer_id = $validated["customer_id"];

        $ws->work_start = $this->toDateTime($validated['work_start'], $validated['work_start_hour']);
        $ws->work_end = $this->toDateTime($validated['work_end'], $validated['work_end_hour']);
        $ws->work_time = $validated["work_time"];
        $ws->comment = $validated["comment"];
        $ws->error_description = $validated["error_description"];
        $ws->work_description = $validated["work_description"];

        if ($isOutourced == true) {
            $outsourcing = new Outsourcing();

            $outsourcing->entry_time = $this->toDateTime($validated['entry_time'], $validated['entry_time_hour']);

            $outsourcing->finished = $validated["finished"];
            $outsourcing->outsourced_number = $validated["outsourced_number"];
            $outsourcing->outsourced_price = $validated["outsourced_price"];
            $outsourcing->our_price = $validated["our_price"];
            $outsourcing->company_id = $validated["partner_id"];

            $outsourcing->save();

            $ws->outsourcing_id = $outsourcing->id;
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
            "navActions" => [['type' => 'create', 'text' => "munkalap", "url" => route('worksheet.create')]],
            "worksheet" => Worksheet::findOrFail($id),
            "worksheetTypes" => Worksheet::getTypes(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $worksheet = Worksheet::findOrFail($id);
        if ($worksheet["final"] == true) {
            return redirect(route('worksheet.show', $id));
        }
        return view('layouts.menu', [
            "navActions" => [['type' => 'create', 'text' => "munkalap", "url" => route('worksheet.create')]],
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
    public function update(WorksheetRequest $request, Worksheet $worksheet)
    {
        if ($worksheet->final) {
            return redirect(route('worksheet.show', $worksheet->id));
        }

        $validated = $request->validated();
        $isOutsourced = $validated["outsourcing"];

        $worksheet->sheet_type = $validated["sheet_type"];
        $worksheet->current_step = $validated["current_step"];
        $worksheet->declaration_mode = $validated["declaration_mode"];

        $worksheet->declaration_time = $this->toDateTime($validated['declaration_time'], $validated['declaration_time_hour']);
        $worksheet->print_date = $this->toDateTime($validated['print_date'], $validated['print_date_hour']);

        $worksheet->liable_id = $validated["liable_id"];
        $worksheet->coworker_id = $validated["coworker_id"];
        $worksheet->customer_id = $validated["customer_id"];

        $worksheet->work_start = $this->toDateTime($validated['work_start'], $validated['work_start_hour']);
        $worksheet->work_end = $this->toDateTime($validated['work_end'], $validated['work_end_hour']);

        $worksheet->work_time = $validated["work_time"];
        $worksheet->comment = $validated["comment"];
        $worksheet->error_description = $validated["error_description"];
        $worksheet->work_description = $validated["work_description"];

        if ($isOutsourced) {
            $existingOutsourcing = $worksheet->outsourcing;

            if (!($existingOutsourcing instanceof Outsourcing)) {
                $existingOutsourcing = null;
            }

            $outsourcing = $existingOutsourcing ?? new Outsourcing();

            $outsourcing->entry_time = $this->toDateTime($validated['entry_time'], $validated['entry_time_hour']);
            $outsourcing->finished = $validated["finished"];
            $outsourcing->outsourced_price = $validated["outsourced_price"];
            $outsourcing->our_price = $validated["our_price"];
            $outsourcing->company_id = $validated["partner_id"];

            if (!$worksheet->outsourcing instanceof Outsourcing) {
                $outsourcing->outsourced_number = $validated["outsourced_number"];
            }

            $outsourcing->save();
            $worksheet->outsourcing_id = $outsourcing->id;
        }

        $worksheet->save();

        return response()->json(['success' => true, 'id' => $worksheet->id], 201);
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
            "navActions" => [['type' => 'create', 'text' => "munkalap", "url" => route('worksheet.create')]],
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
    public function move(DragAndDropRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $worksheet = Worksheet::findOrFail($request->id);

        if ($worksheet->final && $worksheet->current_step !== $request->newStatus) {
            return response()->json(['success' => false], 403);
        }

        if ($worksheet->current_step === $request->newStatus) {
            $worksheets = $user->worksheetsByStep($request->newStatus)
                ->sortBy('slot_number')
                ->reject(fn($ws) => $ws->id === $worksheet->id)
                ->values();

            $worksheets->splice($request->newSlot, 0, [$worksheet]);

            foreach ($worksheets as $i => $ws) {
                $ws->slot_number = $i;
                $ws->save();
            }
        } else {
            foreach ($user->worksheetsByStep($worksheet->current_step) as $ws) {
                if ($ws->id !== $worksheet->id && $ws->slot_number > $worksheet->slot_number) {
                    $ws->slot_number -= 1;
                    $ws->save();
                }
            }

            foreach ($user->worksheetsByStep($request->newStatus) as $ws) {
                if ($ws->id !== $worksheet->id && $ws->slot_number >= $request->newSlot) {
                    $ws->slot_number += 1;
                    $ws->save();
                }
            }

            $worksheet->current_step = $request->newStatus;
            $worksheet->slot_number = $request->newSlot;
            $worksheet->save();
        }

        return response()->json(['success' => true]);
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
        $worksheet["current_step"] = "closed";

        $worksheet->save();
        return redirect(route('worksheet.show', $worksheet));
    }
}

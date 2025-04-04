<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
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
        $ticket = Ticket::findOrFail($id);
        if ($ticket)
            $ticket->delete();
        return redirect(route('home'));
    }

    public function close(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        if ($ticket) {
            $ticket->status = 'closed';
            $ticket->update();
        }
        return redirect(route('home'));
    }

    public function move(Request $request)
    {
        $id = $request["id"];
        $newStatus = $request["newStatus"];
        $newSlot = $request["newSlot"];
        $newTicket = Ticket::findOrFail($id);

        if ($newTicket) {
            if ($newTicket->status == $newStatus) {
                $tickets = Auth::user()->ticketsByStatus($newStatus)->sortBy('slot_number')->values();

                $filtered = $tickets->reject(fn($ticket) => $ticket->id == $newTicket->id)->values();

                $filtered->splice($newSlot, 0, [$newTicket]);

                foreach ($filtered as $index => $ticket) {
                    $ticket->slot_number = $index;
                    $ticket->save();
                }
            } else {
                $oldStatus = $newTicket->status;
                foreach (Auth::user()->ticketsByStatus($oldStatus) as $ticket) {
                    if ($ticket->id != $id && $ticket->slot_number > $newTicket->slot_number) {
                        $ticket->slot_number = $ticket->slot_number - 1;
                        $ticket->save();
                    }
                }

                foreach (Auth::user()->ticketsByStatus($newStatus) as $ticket) {
                    if ($ticket->id != $id && $ticket->slot_number >= $newSlot) {
                        $ticket->slot_number = $ticket->slot_number + 1;
                        $ticket->save();
                    }
                }

                $newTicket->status = $newStatus;
                $newTicket->slot_number = $newSlot;
                $newTicket->save();
            }
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'Could not find ticket with id of ' . $id]);
        }
    }
}

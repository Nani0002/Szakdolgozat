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
            $oldStatus = $newTicket->status;
            $i = 0;
            $user = Auth::user()->id;
            /*foreach ( as $ticket) {
                if ($ticket->id != $id) {
                    $i++;
                }
            }*/
            /*return response()->json([
                'success' => true,
                'num' =>
            ]);*/
            /*foreach (Ticket::where('status', $oldStatus)->orderBy('slot_number', 'asc')->get() as $ticket) {
                if ($ticket->id != $newTicket->id) {
                    $ticket->slot_number = $i;
                    $i++;
                    $ticket->save();
                }
            }*/

            /*$newTicket->status = $newStatus;
            $newTicket->slot_number = $newSlot;
            $i = 0;
            foreach (Ticket::where(['status', $newStatus])->orderBy('slot_number', 'asc')->get() as $ticket) {
                if($ticket->slot_number != $i && $ticket->slot_number < $newSlot){
                    $ticket->slot_number = $i;
                    $i++;
                }
                else if($ticket->slot_number != $i && $ticket->slot_number >= $newSlot){
                    $ticket->slot_number = $i + 1;
                    $i++;
                }
            }

            $newTicket->save();*/
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'Could not find ticket with id of ' + $id]);
        }
    }
}

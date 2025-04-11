<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('layouts.menu', [
            "navActions" => [['type' => 'create', 'text' => "hibajegy", "url" => route('ticket.create')]],
            "user_id" => Auth::id(),
            "users" => User::all(),
            "ticketTypes" => Ticket::getStatuses(),
            "status" => isset($request["status"]) ? $request["status"] : ""
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "title" => "required|string",
            "text" => "required|string",
            "status" => "required|string",
            "users" => "required|array"
        ]);

        $user_id = Auth::id();
        $ticket = new Ticket();
        $ticket["title"] = $validated["title"];
        $ticket["text"] = $validated["text"];
        $ticket["status"] = $validated["status"];
        $ticket["slot_number"] = Ticket::getLastSlot($validated["status"], $user_id) + 1;

        $ticket->save();

        $ticket->users()->attach($validated['users']);

        return redirect(route('ticket.show', $ticket->id));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('layouts.menu', [
            "navActions" => [['type' => 'create', 'text' => "hibajegy", "url" => route('ticket.create')]],
            "user_id" => Auth::id(),
            "ticket" => Ticket::findOrFail($id),
            "users" => User::all(),
            "ticketTypes" => Ticket::getStatuses()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            "title" => "required|string",
            "text" => "required|string",
            "status" => "required|string",
            "users" => "required|array"
        ]);

        $ticket = Ticket::findOrFail($id);

        $ticket["title"] = $validated["title"];
        $ticket["text"] = $validated["text"];
        $ticket["status"] = $validated["status"];

        $ticket->save();

        $ticket->users()->sync($validated['users']);

        return redirect(route('ticket.show', $ticket->id));
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

    /**
     * Set ticket status to closed.
     */
    public function close(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        if ($ticket) {
            $ticket->status = 'closed';
            $ticket->update();
        }
        return redirect(route('home'));
    }

    /**
     * Update the ticket's status via drag and drop.
     */
    public function move(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $id = $request["id"];
        $newStatus = $request["newStatus"];
        $newSlot = $request["newSlot"];
        $newTicket = Ticket::findOrFail($id);

        if ($newTicket) {
            if ($newTicket->status == $newStatus) {
                $tickets = $user->ticketsByStatus($newStatus)->sortBy('slot_number')->values();

                $filtered = $tickets->reject(fn($ticket) => $ticket->id == $newTicket->id)->values();

                $filtered->splice($newSlot, 0, [$newTicket]);

                foreach ($filtered as $index => $ticket) {
                    $ticket->slot_number = $index;
                    $ticket->save();
                }
            } else {
                $oldStatus = $newTicket->status;
                foreach ($user->ticketsByStatus($oldStatus) as $ticket) {
                    if ($ticket->id != $id && $ticket->slot_number > $newTicket->slot_number) {
                        $ticket->slot_number = $ticket->slot_number - 1;
                        $ticket->save();
                    }
                }

                foreach ($user->ticketsByStatus($newStatus) as $ticket) {
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
            return response()->json(['success' => false, 'message' => 'Could not find ticket with id of ' . $id], 404);
        }
    }

    /**
     * Attach a comment to the ticket.
     */
    public function comment(Request $request, string $ticket)
    {
        $validated = $request->validate(["content" => "required|string"]);

        $tic = Ticket::findOrFail($ticket);
        $tic->comments()->create([
            'user_id' => Auth::id(),
            'content' => $validated["content"]
        ]);

        return redirect(route('ticket.show', $ticket));
    }

    /**
     * Edit a comment in the ticket.
     */
    public function edit(Request $request, string $comment, string $ticket)
    {
        DB::table('comments')
            ->where('id', $comment)
            ->update([
                'content' => $request['content'],
            ]);

        return redirect(route('ticket.show', $ticket));
    }

    /**
     * Remove a comment from the ticket.
     */
    public function uncomment(string $comment)
    {
        $comment = Comment::findOrFail($comment);
        $ticket = $comment->ticket_id;

        $comment->delete();

        return redirect(route('ticket.show', $ticket));
    }
}

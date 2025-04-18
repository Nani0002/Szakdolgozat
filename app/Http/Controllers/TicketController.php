<?php

namespace App\Http\Controllers;

use App\Http\Requests\DragAndDropRequest;
use App\Http\Requests\TicketRequest;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function store(TicketRequest $request)
    {
        $user_id = Auth::id();

        $ticket = new Ticket();
        $ticket->title = $request->title;
        $ticket->text = $request->text;
        $ticket->status = $request->status;

        $ticket->save();

        foreach ($request->users as $userId) {
            $slot = Ticket::getLastSlot($request->status, $userId) + 1;
            $ticket->users()->attach($userId, ['slot_number' => $slot]);
        }

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
    public function update(TicketRequest $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->title = $request->title;
        $ticket->text = $request->text;
        $ticket->status = $request->status;

        $ticket->save();

        $ticket->users()->sync($request->users);

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
    public function move(DragAndDropRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $ticket = Ticket::findOrFail($request->id);

        $currentPivot = $ticket->users()->where('user_id', $user->id)->first()->pivot;

        if ($ticket->status === $request->newStatus) {
            $tickets = $user->tickets()
                ->where('status', $request->newStatus)
                ->get()
                ->sortBy(fn($t) => $t->pivot->slot_number)
                ->reject(fn($t) => $t->id === $ticket->id)
                ->values();

            $tickets->splice($request->newSlot, 0, [$ticket]);

            foreach ($tickets as $i => $t) {
                $t->users()->updateExistingPivot($user->id, ['slot_number' => $i]);
            }
        } else {
            $oldTickets = $user->tickets()
                ->where('status', $ticket->status)
                ->get()
                ->sortBy(fn($t) => $t->pivot->slot_number);

            foreach ($oldTickets as $t) {
                if ($t->id !== $ticket->id && $t->pivot->slot_number > $currentPivot->slot_number) {
                    $newSlot = $t->pivot->slot_number - 1;
                    $t->users()->updateExistingPivot($user->id, ['slot_number' => $newSlot]);
                }
            }

            $newTickets = $user->tickets()
                ->where('status', $request->newStatus)
                ->get()
                ->sortBy(fn($t) => $t->pivot->slot_number);

            foreach ($newTickets as $t) {
                if ($t->id !== $ticket->id && $t->pivot->slot_number >= $request->newSlot) {
                    $newSlot = $t->pivot->slot_number + 1;
                    $t->users()->updateExistingPivot($user->id, ['slot_number' => $newSlot]);
                }
            }

            $ticket->status = $request->newStatus;
            $ticket->save();

            $ticket->users()->updateExistingPivot($user->id, ['slot_number' => $request->newSlot]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Attach a comment to the ticket.
     */
    public function comment(Request $request, string $ticket)
    {
        $validated = $request->validate(["content" => "required|string"]);

        $ticket = Ticket::findOrFail($ticket);
        $ticket->comments()->create([
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
        $comment = Comment::findOrFail($comment);
        if ($comment->user_id != Auth::id()) {
            return redirect(route('ticket.show', $ticket));
        }

        $validated = $request->validate(["content" => "required|string"]);

        $comment->content = $validated["content"];

        $comment->save();

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

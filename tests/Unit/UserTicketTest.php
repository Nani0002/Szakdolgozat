<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_sorted_tickets_grouped_by_status()
    {
        $user = User::factory()->create();
        $ticket1 = Ticket::factory()->create(['status' => 'open']);
        $ticket2 = Ticket::factory()->create(['status' => 'waiting']);
        $user->tickets()->attach([$ticket1->id, $ticket2->id]);

        $sorted = $user->sortedTickets();
        $this->assertArrayHasKey('open', $sorted);
        $this->assertArrayHasKey('waiting', $sorted);
    }

    public function test_tickets_by_status_returns_only_that_status()
    {
        $user = User::factory()->create();
        Ticket::factory(3)->hasAttached($user)->create(["status" => "open"]);
        Ticket::factory(2)->hasAttached($user)->create(["status" => "closed"]);

        $tickets = $user->ticketsByStatus('open');

        $this->assertCount(3, $tickets);
        $this->assertEquals('open', $tickets->first()->status);

        $tickets = $user->ticketsByStatus('closed');

        $this->assertCount(2, $tickets);
        $this->assertEquals('closed', $tickets->first()->status);
    }

    public function test_user_can_have_comments()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();
        $user->tickets()->attach($ticket->id);

        $comment = Comment::factory()->create(['user_id' => $user->id, 'ticket_id' => $ticket->id]);

        $this->assertTrue($user->comments->contains($comment));
    }

    public function test_user_can_have_tickets()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();
        $user->tickets()->attach($ticket->id);

        $this->assertTrue($user->tickets->contains($ticket));
    }
}

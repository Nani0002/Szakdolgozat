<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_belongs_to_multiple_users()
    {
        $ticket = Ticket::factory()->hasUsers(2)->create();

        $this->assertInstanceOf(Collection::class, $ticket->users);
        $this->assertCount(2, $ticket->users);

        foreach ($ticket->users as $user) {
            $this->assertInstanceOf(User::class, $user);
        }
    }

    public function test_ticket_can_have_comments()
    {

        $ticket = Ticket::factory()->create();
        $user1 = User::factory()->hasAttached($ticket)->create();
        $user2 = User::factory()->hasAttached($ticket)->create();

        Comment::factory(2)->create(["user_id" => $user1->id, "ticket_id" => $ticket->id]);
        Comment::factory(2)->create(["user_id" => $user2->id, "ticket_id" => $ticket->id]);
        $this->assertInstanceOf(Collection::class, $ticket->comments);
        $this->assertCount(4, $ticket->comments);

        foreach ($ticket->comments as $comment) {
            $this->assertInstanceOf(Comment::class, $comment);
        }
    }

    public function test_get_last_slot_returns_correct_value_per_user_and_status()
    {
        $user = User::factory()->create();

        $ticket1 = Ticket::factory()->create(['status' => 'open']);
        $ticket2 = Ticket::factory()->create(['status' => 'open']);
        $ticket3 = Ticket::factory()->create(['status' => 'open']);

        // Attach with pivot data
        $user->tickets()->attach([
            $ticket1->id => ['slot_number' => 1],
            $ticket2->id => ['slot_number' => 3],
            $ticket3->id => ['slot_number' => 2],
        ]);

        $otherUser = User::factory()->create();
        $ticket4 = Ticket::factory()->create(['status' => 'open']);
        $otherUser->tickets()->attach($ticket4->id, ['slot_number' => 99]);

        $ticket5 = Ticket::factory()->create(['status' => 'closed']);
        $user->tickets()->attach($ticket5->id, ['slot_number' => 50]);

        $lastSlot = Ticket::getLastSlot('open', $user->id);


        $this->assertEquals(3, $lastSlot);
    }

    public function test_get_statuses_returns_configured_statuses()
    {
        $this->assertEquals(config('ticket_statuses'), Ticket::getStatuses());
    }
}

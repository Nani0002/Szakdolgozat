<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_guest_cannot_use_tickets()
    {
        $response = $this->get(route('ticket.create'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('ticket.update', 1), []);
        $response->assertRedirect(route('login'));

        $response = $this->get(route('ticket.destroy', 1));
        $response->assertRedirect(route('login'));
    }

    public function test_create_returns_ticket_index_view()
    {
        $this->authenticateUser();
        $response = $this->get(route('home'));
        $response->assertOk();
        $response->assertViewHas('tickets');
    }

    public function test_create_returns_ticket_creation_view()
    {
        $this->authenticateUser();
        $response = $this->get(route('ticket.create'));
        $response->assertOk();
        $response->assertViewIs('layouts.menu');
        $response->assertViewHas('ticketTypes');
    }

    public function test_store_validates_saves_ticket_and_assigns_users()
    {
        $user1 = $this->authenticateUser();
        $user2 = User::factory()->create();
        $data = [
            'title' => 'Cannot connect to Wi-Fi',
            'text' => 'The laptop fails to connect to any network even after reboot.',
            'status' => 'open',
            'users' => [$user1->id, $user2->id],
        ];

        $response = $this->post(route('ticket.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('tickets', ['title' => $data['title']]);

        $this->assertDatabaseHas('ticket_user', [
            'user_id' => $user1->id,
        ]);

        $this->assertDatabaseHas('ticket_user', [
            'user_id' => $user2->id,
        ]);
    }

    public function test_store_fails_with_missing_fields()
    {
        $this->authenticateUser();
        $response = $this->post(route('ticket.store'), []);
        $response->assertSessionHasErrors([
            "title",
            "text",
            "status",
            "users"
        ]);
    }

    public function test_show_displays_ticket()
    {
        $this->authenticateUser();
        $ticket = Ticket::factory()->create();

        $response = $this->get(route('ticket.show', $ticket->id));

        $response->assertViewHas('ticket');
    }

    public function test_show_404_for_invalid_ticket_id()
    {
        $this->authenticateUser();
        $response = $this->get(route('ticket.show', 1000));
        $response->assertNotFound();
        $response->assertStatus(404);
    }

    public function test_update_updates_ticket()
    {
        $user1 = $this->authenticateUser();
        $user2 = User::factory()->create();
        $data = [
            'title' => 'Cannot connect to Wi-Fi',
            'text' => 'The laptop fails to connect to any network even after reboot.',
            'status' => 'open',
            'users' => [$user1->id, $user2->id],
        ];
        $ticket = Ticket::factory()->create();

        $response = $this->put(route('ticket.update', $ticket->id), $data);
        $ticket->refresh();

        $response->assertRedirect();
        $this->assertDatabaseHas('tickets', ['title' => $data['title']]);

        $this->assertDatabaseHas('ticket_user', [
            'user_id' => $user1->id,
        ]);

        $this->assertDatabaseHas('ticket_user', [
            'user_id' => $user2->id,
        ]);
    }

    public function test_destroy_removes_ticket()
    {
        $this->authenticateUser();
        $ticket = Ticket::factory()->create();
        $response = $this->delete(route('ticket.destroy', $ticket->id));
        $response->assertRedirect(route('home'));
        $this->assertSoftDeleted($ticket);
    }

    public function test_close_sets_status_to_closed()
    {
        $this->authenticateUser();
        $ticket = Ticket::factory()->create(["status" => "open"]);
        $response = $this->patch(route('ticket.close', $ticket->id));
        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('tickets', ['title' => $ticket['title'], "status" => "closed"]);
    }

    public function test_move_reorders_within_same_status()
    {
        $user = $this->authenticateUser();

        $tickets = collect([
            Ticket::factory()->create(['status' => 'open']),
            Ticket::factory()->create(['status' => 'open']),
            Ticket::factory()->create(['status' => 'open']),
        ]);

        foreach ($tickets as $i => $ticket) {
            $ticket->users()->attach($user->id, ['slot_number' => $i]);
        }

        $response = $this->postJson(route('ticket.move'), [
            'id' => $tickets[2]->id,
            'newStatus' => 'open',
            'newSlot' => 0
        ]);

        $response->assertJson(['success' => true]);

        $updated = $user->ticketsByStatus('open')->values();

        $this->assertEquals($tickets[2]->id, $updated[0]->id);
        $this->assertEquals(0, $updated[0]->pivot->slot_number);
        $this->assertEquals(1, $updated[1]->pivot->slot_number);
        $this->assertEquals(2, $updated[2]->pivot->slot_number);
    }

    public function test_move_changes_status_and_updates_slots()
    {
        $user = $this->authenticateUser();

        $ticket = Ticket::factory()->create(['status' => 'open']);
        $ticket->users()->attach($user->id, ['slot_number' => 0]);

        $doneTickets = collect([
            Ticket::factory()->create(['status' => 'done']),
            Ticket::factory()->create(['status' => 'done']),
        ]);

        foreach ($doneTickets as $i => $t) {
            $t->users()->attach($user->id, ['slot_number' => $i]);
        }

        $response = $this->postJson(route('ticket.move'), [
            'id' => $ticket->id,
            'newStatus' => 'done',
            'newSlot' => 1
        ]);

        $response->assertJson(['success' => true]);

        $updatedDoneTickets = $user->ticketsByStatus('done')->values();

        $this->assertEquals($doneTickets[0]->id, $updatedDoneTickets[0]->id);
        $this->assertEquals($ticket->id, $updatedDoneTickets[1]->id);
        $this->assertEquals($doneTickets[1]->id, $updatedDoneTickets[2]->id);

        $this->assertEquals(0, $updatedDoneTickets[0]->pivot->slot_number);
        $this->assertEquals(1, $updatedDoneTickets[1]->pivot->slot_number);
        $this->assertEquals(2, $updatedDoneTickets[2]->pivot->slot_number);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'done',
        ]);
    }

    public function test_comment_creates_new_comment()
    {
        $user = $this->authenticateUser();
        $ticket = Ticket::factory()->create();
        $ticket->users()->attach($user->id);

        $data = ["content" => "test content", "user_id" => $user->id];
        $response = $this->post(route('comment.create', $ticket->id), $data);

        $response->assertRedirect(route('ticket.show', $ticket->id));
        $this->assertDatabaseHas('comments', ['content' => $data['content']]);
    }

    public function test_edit_updates_own_comment()
    {
        $user = $this->authenticateUser();
        $ticket = Ticket::factory()->create();
        $ticket->users()->attach($user->id);
        $comment = Comment::factory()->create(["user_id" => $user->id, "ticket_id" => $ticket->id]);

        $data = ["content" => "test content", "user_id" => $user->id];
        $response = $this->put(route('comment.edit', ["comment" => $comment->id, "ticket" => $ticket->id]), $data);
        $comment->refresh();

        $response->assertRedirect(route('ticket.show', $ticket->id));
        $this->assertDatabaseHas('comments', ['content' => $data['content']]);
    }

    public function test_edit_returns_404_for_missing_comment(){
        $this->authenticateUser();
        $response = $this->put(route('comment.edit', ["comment" => 1000, "ticket" => 1000]), []);
        $response->assertNotFound();
        $response->assertStatus(404);
    }

    public function test_edit_denies_editing_others_comment()
    {
        $this->authenticateUser();
        $ticket = Ticket::factory()->create();
        $user = User::factory()->create();
        $ticket->users()->attach($user->id);
        $comment = Comment::factory()->create(["user_id" => $user->id, "ticket_id" => $ticket->id]);

        $data = ["content" => "test content", "user_id" => $user->id];
        $response = $this->put(route('comment.edit', ["comment" => $comment->id, "ticket" => $ticket->id]), $data);

        $response->assertRedirect(route('ticket.show', $ticket->id));
        $this->assertDatabaseMissing('comments', ['content' => $data['content']]);
    }

    public function test_uncomment_deletes_comment() {
        $this->authenticateUser();
        $ticket = Ticket::factory()->create();
        $user = User::factory()->create();
        $ticket->users()->attach($user->id);
        $comment = Comment::factory()->create(["user_id" => $user->id, "ticket_id" => $ticket->id]);

        $response = $this->delete(route('comment.delete', $comment->id));

        $response->assertRedirect(route('ticket.show', $ticket->id));
        $this->assertSoftDeleted($comment);
    }
}

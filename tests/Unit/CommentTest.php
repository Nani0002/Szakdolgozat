<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public static function getComment()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();
        $user->tickets()->attach($ticket->id);
        return Comment::factory()->create([
            'user_id' => $user->id,
            'ticket_id' => $ticket->id
        ])->fresh();
    }


    public function test_comment_belongs_to_user()
    {
        $comment = $this->getComment();
        $this->assertInstanceOf(User::class, $comment->user);
        $this->assertEquals($comment->user_id, $comment->user->id);
    }

    public function test_comment_belongs_to_ticket()
    {
        $comment = $this->getComment();
        $this->assertInstanceOf(Ticket::class, $comment->ticket);
        $this->assertEquals($comment->ticket_id, $comment->ticket->id);
    }

    public function test_comment_date_returns_now_if_just_created()
    {
        $comment = $this->getComment();
        $this->assertEquals("now", $comment->date());
    }

    public function test_comment_date_returns_correct_relative_time()
    {
        $createdAt = now();

        $comment = $this->getComment();
        $comment->created_at = $createdAt;
        $comment->updated_at = null;
        $comment->save();

        Carbon::setTestNow($createdAt->copy()->addSeconds(5));
        $this->assertEquals("5 seconds ago", $comment->date());

        Carbon::setTestNow($createdAt->copy()->addMinute()->addSecond());
        $this->assertEquals("1 minute ago", $comment->date());

        Carbon::setTestNow($createdAt->copy()->addMinutes(2)->addSecond());
        $this->assertEquals("2 minutes ago", $comment->date());

        Carbon::setTestNow($createdAt->copy()->addHour()->addSecond());
        $this->assertEquals("1 hour ago", $comment->date());

        Carbon::setTestNow($createdAt->copy()->addHours(2)->addSecond());
        $this->assertEquals("2 hours ago", $comment->date());

        Carbon::setTestNow($createdAt->copy()->addDay()->addSecond());
        $this->assertEquals("1 day ago", $comment->date());

        Carbon::setTestNow($createdAt->copy()->addDays(2)->addSecond());
        $this->assertEquals("2 days ago", $comment->date());

        Carbon::setTestNow($createdAt->copy()->addWeek()->addSecond());
        $this->assertEquals("1 week ago", $comment->date());

        Carbon::setTestNow($createdAt->copy()->addWeeks(2)->addSecond());
        $this->assertEquals("2 weeks ago", $comment->date());

        $future = $createdAt->copy()->addMonthsNoOverflow(2);
        Carbon::setTestNow($future);
        $this->assertEquals($createdAt->format("Y-m-d"), $comment->date());

        Carbon::setTestNow();
    }

    public function test_comment_date_falls_back_to_created_at_if_updated_at_is_null()
    {
        $comment = $this->getComment();
        $originalTime = $comment->date();

        $comment->updated_at = now()->addMinutes(1);
        $this->assertNotEquals($originalTime, $comment->date());

        $comment->updated_at = null;
        $this->assertEquals($originalTime, $comment->date());
    }
}

<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\AgentAction;
use App\Models\CalendarEvent;
use App\Models\Email;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgentActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_actions()
    {
        $response = $this->get('/actions');
        $response->assertRedirect('/');
    }

    public function test_authenticated_user_can_view_actions_and_logs()
    {
        $user = User::factory()->create();
        $email = Email::create([
            'user_id' => $user->id,
            'sender' => 'test@example.com',
            'subject' => 'Test email',
            'content' => 'Test content',
            'received_at' => now(),
        ]);

        $action = AgentAction::create([
            'email_id' => $email->id,
            'type' => 'draft_reply',
            'content' => 'Reply content',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get('/actions');

        $response->assertStatus(200);
        $response->assertSee('Đề xuất của AI');
        $response->assertSee('Reply content');
        $response->assertSee('Chờ duyệt');
        $response->assertSee('Nhật ký hoạt động');
    }

    public function test_can_approve_action_and_records_log()
    {
        $user = User::factory()->create();
        $email = Email::create([
            'user_id' => $user->id,
            'sender' => 'test@example.com',
            'subject' => 'Test email',
            'content' => 'Test content',
            'received_at' => now(),
        ]);

        $action = AgentAction::create([
            'email_id' => $email->id,
            'type' => 'create_event',
            'content' => "Lịch họp quý 3" . PHP_EOL . "Online",
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->post("/actions/{$action->id}/approve");

        $response->assertRedirect();
        $this->assertEquals('approved', $action->fresh()->status);
        $this->assertDatabaseHas('activity_logs', [
            'action_description' => "Đã duyệt đề xuất #{$action->id} - create_event",
        ]);
        $this->assertDatabaseHas('calendar_events', [
            'agent_action_id' => $action->id,
            'title' => 'Lịch họp quý 3',
        ]);
    }

    public function test_can_reject_action_and_records_log()
    {
        $user = User::factory()->create();
        $email = Email::create([
            'user_id' => $user->id,
            'sender' => 'test@example.com',
            'subject' => 'Test email',
            'content' => 'Test content',
            'received_at' => now(),
        ]);

        $action = AgentAction::create([
            'email_id' => $email->id,
            'type' => 'draft_reply',
            'content' => 'Reply content',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->post("/actions/{$action->id}/reject");

        $response->assertRedirect();
        $this->assertEquals('rejected', $action->fresh()->status);
        $this->assertDatabaseHas('activity_logs', [
            'action_description' => "Đã từ chối đề xuất #{$action->id} - draft_reply",
        ]);
        $this->assertDatabaseMissing('calendar_events', [
            'agent_action_id' => $action->id,
        ]);
    }
}

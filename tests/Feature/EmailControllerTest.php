<?php

namespace Tests\Feature;

use App\Models\AgentAction;
use App\Models\Email;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EmailControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_email_and_no_draft()
    {
        $user = User::factory()->create();
        $email = Email::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('emails.show', $email->id));

        $response->assertStatus(200);
        $response->assertSee($email->subject);
        $response->assertSee('Generate AI Draft');
    }

    public function test_it_prevents_viewing_others_email()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $email = Email::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->get(route('emails.show', $email->id));

        $response->assertStatus(403);
    }

    public function test_it_generates_draft_successfully()
    {
        $user = User::factory()->create();
        $email = Email::factory()->create(['user_id' => $user->id]);

        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [['message' => ['content' => 'Mock AI reply']]]
            ], 200)
        ]);

        $response = $this->actingAs($user)->post(route('emails.draft.generate', $email->id));

        $response->assertRedirect(route('emails.show', $email->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('agent_actions', [
            'email_id' => $email->id,
            'type' => 'draft_reply',
            'content' => 'Mock AI reply',
            'status' => 'pending',
        ]);
    }

    public function test_it_updates_draft_successfully()
    {
        $user = User::factory()->create();
        $email = Email::factory()->create(['user_id' => $user->id]);
        
        $action = AgentAction::create([
            'email_id' => $email->id,
            'type' => 'draft_reply',
            'content' => 'Old draft',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->put(route('emails.draft.update', $email->id), [
            'content' => 'Updated draft content',
        ]);

        $response->assertRedirect(route('emails.show', $email->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('agent_actions', [
            'id' => $action->id,
            'content' => 'Updated draft content',
            'status' => 'pending',
        ]);
        
        // Ensure no new action was created
        $this->assertEquals(1, AgentAction::count());
    }
}

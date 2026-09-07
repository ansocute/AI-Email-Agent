<?php

namespace Tests\Feature;

use App\Models\Email;
use App\Models\User;
use App\Services\AIDraftService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Exception;

class AIDraftServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AIDraftService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AIDraftService();
        config(['ai.openai.api_key' => 'test-key']);
    }

    public function test_it_generates_draft_using_openai_successfully()
    {
        config(['ai.provider' => 'openai']);
        
        $email = Email::factory()->create();

        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'This is a mock draft from OpenAI.'
                        ]
                    ]
                ]
            ], 200)
        ]);

        $draft = $this->service->draftReply($email);
        $this->assertEquals('This is a mock draft from OpenAI.', $draft);
    }

    public function test_it_throws_exception_on_openai_failure()
    {
        config(['ai.provider' => 'openai']);
        $email = Email::factory()->create();

        Http::fake([
            'api.openai.com/*' => Http::response(['error' => 'Server Error'], 500)
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to generate AI draft from OpenAI.');

        $this->service->draftReply($email);
    }

    public function test_it_generates_draft_using_anthropic_successfully()
    {
        config(['ai.provider' => 'anthropic']);
        config(['ai.anthropic.api_key' => 'test-key']);
        
        $email = Email::factory()->create();

        Http::fake([
            'api.anthropic.com/*' => Http::response([
                'content' => [
                    [
                        'text' => 'This is a mock draft from Anthropic.'
                    ]
                ]
            ], 200)
        ]);

        $draft = $this->service->draftReply($email);
        $this->assertEquals('This is a mock draft from Anthropic.', $draft);
    }
}

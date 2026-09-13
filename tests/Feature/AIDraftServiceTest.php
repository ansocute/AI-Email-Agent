<?php

namespace Tests\Feature;

use App\Models\Email;
use App\Services\AIDraftService;
use App\Services\GeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AIDraftServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_draft_using_gemini_successfully(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Chào bạn, mình đã nhận được email và sẽ phản hồi sớm.'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $email = Email::factory()->create([
            'sender' => 'khach@example.com',
            'subject' => 'Hỏi về báo giá',
            'content' => 'Chào bạn, cho mình xin báo giá sản phẩm A nhé.',
        ]);

        $service = new AIDraftService(new GeminiService());
        $draft = $service->draftReply($email);

        $this->assertNotEmpty($draft);
        $this->assertStringContainsString('phản hồi', $draft);
    }

    public function test_it_throws_exception_on_gemini_failure(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'error' => ['message' => 'Internal error'],
            ], 500),
        ]);

        $email = Email::factory()->create([
            'sender' => 'khach@example.com',
            'subject' => 'Hỏi về báo giá',
            'content' => 'Chào bạn, cho mình xin báo giá sản phẩm A nhé.',
        ]);

        $service = new AIDraftService(new GeminiService());

        $this->expectException(\RuntimeException::class);
        $service->draftReply($email);
    }

    public function test_it_throws_exception_when_gemini_returns_empty_text(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => ''],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $email = Email::factory()->create([
            'sender' => 'khach@example.com',
            'subject' => 'Hỏi về báo giá',
            'content' => 'Chào bạn, cho mình xin báo giá sản phẩm A nhé.',
        ]);

        $service = new AIDraftService(new GeminiService());

        $this->expectException(\RuntimeException::class);
        $service->draftReply($email);
    }
}
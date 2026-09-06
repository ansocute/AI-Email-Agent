<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SchedulingIntentService
{
    public function detect(string $subject, string $content): bool
    {
        $apiKey = env('GEMINI_API_KEY');

        $response = Http::post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key={$apiKey}",
            [
                'contents' => [[
                    'parts' => [[
                        'text' => "Email sau có phải là lời mời/đề nghị hẹn gặp mặt, cuộc họp, hay lịch hẹn cụ thể không? Chỉ trả lời đúng 1 từ: yes hoặc no.\n\nTiêu đề: {$subject}\nNội dung: {$content}",
                    ]],
                ]],
            ]
        );

        $text = strtolower(trim($response->json('candidates.0.content.parts.0.text') ?? 'no'));
        return str_contains($text, 'yes');
    }
}
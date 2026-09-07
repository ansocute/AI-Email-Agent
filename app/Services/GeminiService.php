<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiService
{
    public function generate(string $prompt): string
    {
        $apiKey = config('services.gemini.api_key');

        if (!$apiKey) {
            throw new RuntimeException('GEMINI_API_KEY chưa được cấu hình.');
        }

        $response = Http::withHeaders([
            'x-goog-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent',
            [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt,
                            ],
                        ],
                    ],
                ],
            ]
        );

        if ($response->failed()) {
            throw new RuntimeException(
                'Gemini API Error: ' . $response->body()
            );
        }

        return $response->json(
            'candidates.0.content.parts.0.text',
            ''
        );
    }
}
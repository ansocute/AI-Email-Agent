<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiClassifierService
{
    public function classify(string $subject, string $content): string
    {
        $apiKey = env('GEMINI_API_KEY');

        $response = Http::post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}",
            [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => "Phân loại email sau vào đúng 1 trong 3 nhãn: important, urgent, spam. Chỉ trả lời đúng 1 từ tiếng Anh, không giải thích gì thêm.\n\nTiêu đề: {$subject}\nNội dung: {$content}"
                            ]
                        ]
                    ]
                ]
            ]
        );

        $text = trim($response->json('candidates.0.content.parts.0.text') ?? 'spam');
        $text = strtolower(preg_replace('/[^a-z]/i', '', $text));

        $validLabels = ['important', 'urgent', 'spam'];
        return in_array($text, $validLabels) ? $text : 'spam';
    }
}
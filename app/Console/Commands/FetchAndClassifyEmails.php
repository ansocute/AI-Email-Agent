<?php

namespace App\Console\Commands;

use App\Models\Email;
use App\Models\User;
use App\Services\AiClassifierService;
use App\Services\GmailService;
use Illuminate\Console\Command;

class FetchAndClassifyEmails extends Command
{
    protected $signature = 'emails:fetch';
    protected $description = 'Tự động lấy email mới từ Gmail và phân loại bằng AI cho tất cả người dùng';

    public function handle(): void
    {
        $classifier = new AiClassifierService();

        // Lặp qua tất cả user đã kết nối Google (có google_token)
        $users = User::whereNotNull('google_token')->get();

        $this->info("Tìm thấy {$users->count()} người dùng đã kết nối Google.");

        foreach ($users as $user) {
            try {
                $emails = (new GmailService($user))->fetchRecentEmails(5);

                foreach ($emails as $emailData) {
                    $category = $classifier->classify($emailData['subject'], $emailData['content']);

                    Email::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'subject' => $emailData['subject'],
                            'sender' => $emailData['sender'],
                        ],
                        [
                            'content' => $emailData['content'],
                            'received_at' => $emailData['received_at'],
                            'category' => $category,
                        ]
                    );
                }

                $this->info("✔ User {$user->email}: đã xử lý " . count($emails) . " email.");
            } catch (\Exception $e) {
                $this->error("✘ Lỗi với user {$user->email}: " . $e->getMessage());
            }
        }
    }
}
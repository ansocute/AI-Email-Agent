<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\EmailSyncPipeline;
use Illuminate\Console\Command;

class FetchAndClassifyEmails extends Command
{
    protected $signature = 'emails:fetch';
    protected $description = 'Tự động lấy email mới, phân loại, và phát hiện lịch hẹn cho tất cả người dùng';

    public function handle(): void
    {
        $pipeline = new EmailSyncPipeline();
        $users = User::whereNotNull('google_token')->get();

        $this->info("Tìm thấy {$users->count()} người dùng đã kết nối Google.");

        foreach ($users as $user) {
            try {
                $count = $pipeline->run($user, 5);
                $this->info("✔ User {$user->email}: đã xử lý {$count} email.");
            } catch (\Exception $e) {
                $this->error("✘ Lỗi với user {$user->email}: " . $e->getMessage());
            }
        }
    }
}
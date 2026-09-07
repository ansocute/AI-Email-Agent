<?php

namespace App\Services;

use App\Models\AgentAction;
use App\Models\Email;
use App\Models\User;

class EmailSyncPipeline
{
    public function run(User $user, int $max = 5): int
    {
        $classifier = new AiClassifierService();
        $schedulingDetector = new SchedulingIntentService();

        $emails = (new GmailService($user))->fetchRecentEmails($max);
        $count = 0;

        foreach ($emails as $emailData) {
            $category = $classifier->classify($emailData['subject'], $emailData['content']);

            $emailModel = Email::updateOrCreate(
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
            $count++;

            // Bỏ qua spam, không cần kiểm tra lịch hẹn
            if ($category === 'spam') {
                continue;
            }

            // UC04: phát hiện ý định đặt lịch
            if (!$schedulingDetector->detect($emailData['subject'], $emailData['content'])) {
                continue;
            }

            // Tránh tạo trùng đề xuất nếu email này đã có sẵn 1 đề xuất lịch
            if ($emailModel->agentActions()->where('type', 'create_event')->exists()) {
                continue;
            }

            // UC05: kiểm tra lịch trống & đề xuất giờ
            try {
                $slot = (new CalendarService($user))->findNextAvailableSlot();

                if ($slot) {
                    AgentAction::create([
                        'email_id' => $emailModel->id,
                        'type' => 'create_event',
                        'content' => 'Đề xuất lịch hẹn: ' . $slot['start']->format('d/m/Y H:i') . '–' . $slot['end']->format('H:i'),
                        'status' => 'pending',
                        'event_start' => $slot['start'],
                        'event_end' => $slot['end'],
                    ]);
                }
            } catch (\Exception $e) {
                logger()->error("Kiểm tra lịch thất bại cho {$user->email}: " . $e->getMessage());
            }
        }

        return $count;
    }
}
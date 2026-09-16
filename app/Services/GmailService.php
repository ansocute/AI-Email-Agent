<?php

namespace App\Services;

use App\Models\User;
use App\Services\Concerns\RefreshesGoogleToken;
use Google\Service\Gmail;
use Illuminate\Support\Facades\Mail;

class GmailService
{
    use RefreshesGoogleToken;

    protected Gmail $service;

    public function __construct(User $user)
    {
        $this->service = new Gmail($this->buildAuthenticatedClient($user));
    }

    /**
     * Lấy email gần đây từ Gmail.
     */
    public function fetchRecentEmails(int $maxResults = 10): array
    {
        $results = $this->service
            ->users_messages
            ->listUsersMessages('me', [
                'maxResults' => $maxResults,
            ]);

        $emails = [];

        foreach ($results->getMessages() as $message) {

            $msg = $this->service
                ->users_messages
                ->get('me', $message->getId());

            $headers = $msg
                ->getPayload()
                ->getHeaders();

            $subject = '';
            $from = '';

            foreach ($headers as $header) {

                if ($header->getName() === 'Subject') {
                    $subject = $header->getValue();
                }

                if ($header->getName() === 'From') {
                    $from = $header->getValue();
                }
            }

            $emails[] = [
                'sender' => $from,
                'subject' => $subject,
                'content' => $msg->getSnippet(),
                'received_at' => now(),
            ];
        }

        return $emails;
    }

    /**
     * Gửi email qua mailer đã cấu hình (SMTP trong môi trường production).
     */
    public function sendEmail(
        string $to,
        string $subject,
        string $body
    ): array {

        Mail::raw($body, function ($message) use ($to, $subject): void {
            $message->to($to)->subject($subject);
        });

        return [
            'to' => $to,
            'subject' => $subject,
        ];
    }
}
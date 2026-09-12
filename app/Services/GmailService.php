<?php

namespace App\Services;

use App\Models\User;
use App\Services\Concerns\RefreshesGoogleToken;
use Google\Service\Gmail;

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
     * Gửi email thông qua Gmail API.
     */
    public function sendEmail(
        string $to,
        string $subject,
        string $body
    ): array {

        /*
        |--------------------------------------------------------------------------
        | Tạo MIME message
        |--------------------------------------------------------------------------
        */

        $rawMessage =
            "To: {$to}\r\n" .
            "Subject: {$subject}\r\n" .
            "MIME-Version: 1.0\r\n" .
            "Content-Type: text/plain; charset=UTF-8\r\n" .
            "\r\n" .
            $body;

        /*
        |--------------------------------------------------------------------------
        | Gmail yêu cầu Base64 URL-safe
        |--------------------------------------------------------------------------
        */

        $encodedMessage = rtrim(
            strtr(
                base64_encode($rawMessage),
                '+/',
                '-_'
            ),
            '='
        );

        /*
        |--------------------------------------------------------------------------
        | Tạo Gmail Message
        |--------------------------------------------------------------------------
        */

        $message = new \Google\Service\Gmail\Message();

        $message->setRaw($encodedMessage);

        /*
        |--------------------------------------------------------------------------
        | Gửi
        |--------------------------------------------------------------------------
        */

        $sentMessage = $this->service
            ->users_messages
            ->send(
                'me',
                $message
            );

        return [
            'id' => $sentMessage->getId(),
            'thread_id' => $sentMessage->getThreadId(),
        ];
    }
}
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
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));

        $this->client->setAccessToken([
            'access_token' => $user->google_token,
            'expires_in' => $user->google_token_expires_at
                ? now()->diffInSeconds($user->google_token_expires_at, false)
                : 0,
        ]);

        // Nếu token đã hết hạn (hoặc gần hết), tự động refresh
        if ($this->client->isAccessTokenExpired()) {
            if (!$user->google_refresh_token) {
                throw new \Exception("User {$user->email} không có refresh_token, cần đăng nhập lại thủ công.");
            }

            $newToken = $this->client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);

            if (isset($newToken['error'])) {
                throw new \Exception("Refresh token thất bại cho {$user->email}: " . $newToken['error']);
            }

            $user->update([
                'google_token' => $newToken['access_token'],
                'google_token_expires_at' => now()->addSeconds($newToken['expires_in']),
            ]);

            $this->client->setAccessToken($newToken);
        }

        $this->service = new Gmail($this->client);
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
<?php

namespace App\Services;

use App\Models\User;
use Google\Client;
use Google\Service\Gmail;

class GmailService
{
    protected Client $client;
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

    public function fetchRecentEmails(int $maxResults = 10): array
    {
        $results = $this->service->users_messages->listUsersMessages('me', [
            'maxResults' => $maxResults,
        ]);

        $emails = [];

        foreach ($results->getMessages() as $message) {
            $msg = $this->service->users_messages->get('me', $message->getId());
            $headers = $msg->getPayload()->getHeaders();

            $subject = '';
            $from = '';
            foreach ($headers as $header) {
                if ($header->getName() === 'Subject') $subject = $header->getValue();
                if ($header->getName() === 'From') $from = $header->getValue();
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
}
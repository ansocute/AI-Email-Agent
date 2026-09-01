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
        $this->client->setAccessToken($user->google_token);
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
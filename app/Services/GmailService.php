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
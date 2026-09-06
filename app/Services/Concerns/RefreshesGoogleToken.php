<?php

namespace App\Services\Concerns;

use App\Models\User;
use Google\Client;

trait RefreshesGoogleToken
{
    protected function buildAuthenticatedClient(User $user): Client
    {
        $client = new Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));

        $client->setAccessToken([
            'access_token' => $user->google_token,
            'expires_in' => $user->google_token_expires_at
                ? now()->diffInSeconds($user->google_token_expires_at, false)
                : 0,
        ]);

        if ($client->isAccessTokenExpired()) {
            if (!$user->google_refresh_token) {
                throw new \Exception("User {$user->email} không có refresh_token, cần đăng nhập lại thủ công.");
            }

            $newToken = $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);

            if (isset($newToken['error'])) {
                throw new \Exception("Refresh token thất bại cho {$user->email}: " . $newToken['error']);
            }

            $user->update([
                'google_token' => $newToken['access_token'],
                'google_token_expires_at' => now()->addSeconds($newToken['expires_in']),
            ]);

            $client->setAccessToken($newToken);
        }

        return $client;
    }
}
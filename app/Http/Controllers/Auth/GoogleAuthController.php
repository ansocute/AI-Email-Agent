<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes([
                'https://www.googleapis.com/auth/gmail.readonly',
                'https://www.googleapis.com/auth/gmail.send',
                'https://www.googleapis.com/auth/calendar',
            ])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $updateData = [
            'name' => $googleUser->getName(),
            'google_token' => $googleUser->token,
            'google_token_expires_at' => now()->addSeconds($googleUser->expiresIn ?? 3600),
        ];

        // Google chỉ trả refresh_token ở LẦN ĐẦU cấp quyền, nên chỉ ghi đè nếu có giá trị mới
        if (!empty($googleUser->refreshToken)) {
            $updateData['google_refresh_token'] = $googleUser->refreshToken;
        }

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            $updateData + ['password' => bcrypt(str()->random(16))]
        );

        Auth::login($user);

        return redirect('/dashboard');
    }
}
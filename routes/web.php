<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Auth;
use App\Services\GmailService;
use App\Models\Email;
use App\Services\AiClassifierService;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');
 Route::get('/dashboard', function () {
    return 'Đăng nhập thành công! User: ' . Auth::user()->email;
})->middleware('auth');

Route::get('/test-gmail', function () {
    $emails = (new GmailService(Auth::user()))->fetchRecentEmails(5);
    $classifier = new AiClassifierService();

    foreach ($emails as $emailData) {
        $category = $classifier->classify($emailData['subject'], $emailData['content']);

        Email::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'subject' => $emailData['subject'],
                'sender' => $emailData['sender'],
            ],
            [
                'content' => $emailData['content'],
                'received_at' => $emailData['received_at'],
                'category' => $category,
            ]
        );
    }

    return response()->json(['message' => 'Đã lưu và phân loại ' . count($emails) . ' email', 'emails' => Email::where('user_id', Auth::id())->get()]);
})->middleware('auth');
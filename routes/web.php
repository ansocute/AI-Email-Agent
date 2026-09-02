<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Models\Email;
use App\Services\AiClassifierService;
use App\Services\GmailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    $emails = Email::where('user_id', Auth::id())
        ->orderByDesc('received_at')
        ->get();

    return view('dashboard', ['emails' => $emails]);
})->middleware('auth')->name('dashboard');

Route::post('/emails/sync', function () {
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

    return redirect()->route('dashboard')->with('synced', count($emails));
})->middleware('auth')->name('emails.sync');
<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\AgentActionController;
use App\Models\Email;
use App\Services\AiClassifierService;
use App\Services\GmailService;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
})->name('login');

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

Route::get('/actions', [AgentActionController::class, 'index'])->middleware('auth')->name('actions.index');
Route::post('/actions/{agentAction}/approve', [AgentActionController::class, 'approve'])
    ->middleware('auth')
    ->name('actions.approve');
Route::post('/actions/{agentAction}/reject', [AgentActionController::class, 'reject'])
    ->middleware('auth')
    ->name('actions.reject');

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

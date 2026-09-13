<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\AgentActionController;
use App\Models\Email;
use App\Services\AiClassifierService;
use App\Services\GmailService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\EmailController;

Route::get('/', function () {
    return view('welcome');
})->name('login');

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

// Route::get('/dashboard', function () {
//     return 'Đăng nhập thành công! User: ' . Auth::user()->email;
// })->middleware('auth');

Route::get('/dashboard', function () {
    $emails = Email::where('user_id', Auth::id())
        ->orderByDesc('received_at')
        ->get();

    return view('dashboard', ['emails' => $emails]);
})->middleware('auth')->name('dashboard');

Route::get('/emails', [EmailController::class, 'index'])->middleware('auth')->name('emails.index');
Route::get('/emails/{email}', [EmailController::class, 'show'])->middleware('auth')->name('emails.show');
Route::post('/emails/{email}/generate-draft', [EmailController::class, 'generateDraft'])->middleware('auth')->name('emails.generate-draft');
Route::post('/emails/{email}/update-draft', [EmailController::class, 'updateDraft'])->middleware('auth')->name('emails.update-draft');
Route::post('/emails/{email}/send-draft', [EmailController::class, 'sendEmail'])->middleware('auth')->name('emails.send-draft');
Route::get('/actions', [AgentActionController::class, 'index'])->middleware('auth')->name('actions.index');
Route::post('/actions/{agentAction}/approve', [AgentActionController::class, 'approve'])
    ->middleware('auth')
    ->name('actions.approve');
Route::post('/actions/{agentAction}/reject', [AgentActionController::class, 'reject'])
    ->middleware('auth')
    ->name('actions.reject');

Route::post('/emails/sync', function () {
    $count = (new \App\Services\EmailSyncPipeline())->run(Auth::user(), 5);
    return redirect()->route('dashboard')->with('synced', $count);
})->middleware('auth')->name('emails.sync');
<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\AgentAction;
use App\Services\AIDraftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    /**
     * Display a listing of the emails.
     */
    public function index()
    {
        $emails = Email::where('user_id', Auth::id())->latest('received_at')->get();
        return view('emails.index', compact('emails'));
    }

    /**
     * Display the specified email.
     */
    public function show(Email $email)
    {
        // Authorization: Ensure the email belongs to the authenticated user
        if ($email->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Get existing draft if any
        $draft = AgentAction::where('email_id', $email->id)
            ->where('type', 'draft_reply')
            ->first();

        return view('emails.show', compact('email', 'draft'));
    }

    /**
     * Generate an AI draft for the email.
     */
    public function generateDraft(Request $request, Email $email, AIDraftService $aiService)
    {
        if ($email->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $draftContent = $aiService->draftReply($email);

            // Update or create AgentAction
            AgentAction::updateOrCreate(
                [
                    'email_id' => $email->id,
                    'type' => 'draft_reply',
                ],
                [
                    'content' => $draftContent,
                    'status' => 'pending',
                ]
            );

            return redirect()->route('emails.show', $email->id)->with('success', 'AI Draft generated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('emails.show', $email->id)->with('error', $e->getMessage());
        }
    }

    /**
     * Update the generated draft.
     */
    public function updateDraft(Request $request, Email $email)
    {
        if ($email->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'content' => 'required|string|max:10000',
        ]);

        $draft = AgentAction::where('email_id', $email->id)
            ->where('type', 'draft_reply')
            ->firstOrFail();

        $draft->update([
            'content' => $request->input('content'),
            // Keep status pending as per UC03 requirement (not approved yet)
            'status' => 'pending', 
        ]);

        return redirect()->route('emails.show', $email->id)->with('success', 'Draft saved successfully!');
    }

    /**
 * Send the saved draft through Gmail.
 */
public function sendEmail(
    Request $request,
    Email $email
) {
    
    if ($email->user_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }
    $request->validate([
        'recipient' => ['required', 'email', 'max:255'],
    ]);

    $draft = AgentAction::where('email_id', $email->id)
        ->where('type', 'draft_reply')
        ->firstOrFail();

    if (empty(trim($draft->content))) {
        return redirect()
            ->route('emails.show', $email->id)
            ->with('error', 'Draft không có nội dung để gửi.');
    }

    try {
        $to = $request->string('recipient')->toString();

        $subject = $email->subject;

        // Thêm Re: nếu chưa có
        if (!str_starts_with(
            strtolower(trim($subject)),
            're:'
        )) {
            $subject = 'Re: ' . $subject;
        }

        Mail::raw($draft->content, function ($message) use ($to, $subject): void {
            $message->to($to)->subject($subject);
        });

        // Gmail gửi thành công → cập nhật DB
        $draft->update([
            'status' => 'sent',
        ]);

        return redirect()
            ->route('emails.show', $email->id)
            ->with(
                'success',
                'Email đã được gửi thành công!'
            );

    } catch (\Exception $e) {

        return redirect()
            ->route('emails.show', $email->id)
            ->with(
                'error',
                'Gửi email thất bại: ' . $e->getMessage()
            );
    }
}
}

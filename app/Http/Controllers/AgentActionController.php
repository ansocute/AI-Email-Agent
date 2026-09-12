<?php

namespace App\Http\Controllers;

use App\Models\AgentAction;
use App\Models\ActivityLog;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AgentActionController extends Controller
{
    public function index()
    {
        $actions = AgentAction::with(['email', 'user'])
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'actions_page');

        $logs = ActivityLog::with('user')
            ->orderByDesc('timestamp')
            ->paginate(15, ['*'], 'logs_page');

        return view('agent-actions.index', compact('actions', 'logs'));
    }

    public function approve(AgentAction $agentAction, Request $request)
    {
        if ($agentAction->status !== 'pending') {
            return back()->with('error', 'Yêu cầu đã được xử lý');
        }

        $agentAction->update(['status' => 'approved']);

        if ($agentAction->type === 'create_event') {
            $title = (string) (Str::of($agentAction->content)->before(PHP_EOL) ?: $agentAction->content);
            $start = $agentAction->event_start ?? now();
            $end = $agentAction->event_end ?? now()->addHour();

            try {
                (new \App\Services\CalendarService(auth()->user()))->createEvent($title, $start, $end);

                CalendarEvent::create([
                    'agent_action_id' => $agentAction->id,
                    'title' => $title,
                    'start_time' => $start,
                    'end_time' => $end,
                    'status' => 'confirmed',
                ]);
            } catch (\Exception $e) {
                \Log::error("Tạo sự kiện Google Calendar thất bại cho AgentAction #{$agentAction->id}: " . $e->getMessage());
                return back()->with('error', 'Đã duyệt nhưng tạo sự kiện lịch thất bại: ' . $e->getMessage());
            }
        }

        ActivityLog::create([
            'user_id' => auth()->id() ?? $agentAction->email?->user_id,
            'action_description' => "Đã duyệt đề xuất #{$agentAction->id} - {$agentAction->type}",
            'timestamp' => now(),
        ]);

        return back()->with('status', "Đã duyệt đề xuất #{$agentAction->id}.");
    }

    public function reject(AgentAction $agentAction, Request $request)
    {
        if ($agentAction->status !== 'pending') {
            return back()->with('error', 'Yêu cầu đã được xử lý');
        }

        $agentAction->update(['status' => 'rejected']);

        ActivityLog::create([
            'user_id' => auth()->id() ?? $agentAction->email?->user_id,
            'action_description' => "Đã từ chối đề xuất #{$agentAction->id} - {$agentAction->type}",
            'timestamp' => now(),
        ]);

        return back()->with('status', "Đã từ chối đề xuất #{$agentAction->id}.");
    }
}

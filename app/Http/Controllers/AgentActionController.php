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
            ->paginate(20);

        $logs = ActivityLog::with('user')
            ->orderByDesc('timestamp')
            ->limit(20)
            ->get();

        return view('agent-actions.index', compact('actions', 'logs'));
    }

    public function approve(AgentAction $agentAction, Request $request)
    {
        if ($agentAction->status !== 'pending') {
            return back()->with('error', 'Yêu cầu đã được xử lý');
        }

        $agentAction->update(['status' => 'approved']);

        if ($agentAction->type === 'create_event') {
            CalendarEvent::create([
                'agent_action_id' => $agentAction->id,
                'title' => Str::of($agentAction->content)->before(PHP_EOL) ?: $agentAction->content,
                'start_time' => now(),
                'end_time' => now()->addHour(),
                'status' => 'confirmed',
            ]);
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

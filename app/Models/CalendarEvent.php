<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = ['agent_action_id', 'title', 'start_time', 'end_time', 'status'];

    public function agentAction()
    {
        return $this->belongsTo(AgentAction::class);
    }
}

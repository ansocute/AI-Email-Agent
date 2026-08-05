<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentAction extends Model
{
    use HasFactory;

    protected $fillable = ['email_id', 'type', 'content', 'status'];

    public function email()
    {
        return $this->belongsTo(Email::class);
    }

    public function calendarEvent()
    {
        return $this->hasOne(CalendarEvent::class);
    }
}
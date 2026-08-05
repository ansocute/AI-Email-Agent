<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'sender', 'subject', 'content', 'category', 'received_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agentActions()
    {
        return $this->hasMany(AgentAction::class);
    }
}
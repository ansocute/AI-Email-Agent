<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'sender', 'subject', 'content', 'category', 'received_at'];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agentActions()
    {
        return $this->hasMany(AgentAction::class);
    }

    /**
     * Tách tên người gửi sạch từ chuỗi dạng "Tên" <email@domain.com>
     */
    public function getSenderNameAttribute(): string
    {
        if (preg_match('/^"?([^"<]+)"?\s*<.*>$/', trim($this->sender), $matches)) {
            return trim($matches[1]);
        }
        return $this->sender;
    }

    /**
     * Nhãn hiển thị tiếng Việt + màu sắc theo category
     */
    public function getCategoryBadgeAttribute(): array
    {
        return match ($this->category) {
            'urgent' => ['label' => 'Gấp', 'bg' => '#FBE8E8', 'text' => '#B23B3B'],
            'important' => ['label' => 'Quan trọng', 'bg' => '#FCEFD9', 'text' => '#966B0C'],
            'spam' => ['label' => 'Spam', 'bg' => '#ECEBE7', 'text' => '#71706A'],
            default => ['label' => 'Chưa phân loại', 'bg' => '#ECEBE7', 'text' => '#71706A'],
        };
    }
}
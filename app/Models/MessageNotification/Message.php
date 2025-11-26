<?php

namespace App\Models\MessageNotification;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Message extends Model
{
    protected $fillable = [
        'sender_id', 'sender_role', 'title', 'body', 'type', 'target_scope', 'status', 'scheduled_at', 'expires_at'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipients()
    {
        return $this->hasMany(MessageRecipient::class);
    }

    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class);
    }
}


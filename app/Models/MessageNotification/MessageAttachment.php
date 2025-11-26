<?php

namespace App\Models\MessageNotification;

use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    protected $fillable = [
        'message_id', 'file_path', 'file_name', 'mime_type', 'file_size',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}


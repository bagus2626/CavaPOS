<?php

namespace App\Models\MessageNotification;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Owner;
use App\Models\Partner\HumanResource\Employee;

class MessageRecipient extends Model
{
    protected $fillable = [
        'message_id', 'recipient_type', 'recipient_id', 'is_read','is_starred', 'read_at', 'message_type', 'recipient_target'
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function outlets()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function owners()
    {
        return $this->belongsTo(Owner::class, 'recipient_id');
    }

    public function employees()
    {
        return $this->belongsTo(Employee::class, 'recipient_id');
    }
}

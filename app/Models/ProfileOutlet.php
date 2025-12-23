<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileOutlet extends Model
{
    use HasFactory;

    protected $table = 'profile_outlet';

    protected $fillable = [
        'user_id',
        'contact_person',
        'contact_phone',
        'gmaps_url',
        'instagram',
        'facebook',
        'twitter',
        'tiktok',
        'whatsapp',
        'website'
    ];

    /**
     * Get the user that owns the profile outlet.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
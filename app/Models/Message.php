<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'school_id',
        'subject',
        'body',
        'attachment_path',
        'is_read',
        'recipient_type',
    ];

    // Relationships
    public function sender()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}

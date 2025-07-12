<?php

namespace App\Models\addon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'twilio_sid',
        'twilio_token',
        'twilio_from',
    ];
}

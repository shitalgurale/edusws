<?php

namespace App\Models\Addon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrDailyAttendence extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $table = 'hr_daily_attendences';

    protected $fillable = [ 'id','user_id', 'role_id','school_id', 'session_id','status','created_at','updated_at' ];
}

<?php

namespace App\Models\Addon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hr_user_list extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $table = 'hr_user_list';

    protected $fillable = [ 'id','name', 'email','role_id','address','phone','gender','joining_salary','school_id' ];
}

<?php

namespace App\Models\Addon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrPayroll extends Model
{

    use HasFactory;
    public $timestamps=false;
    protected $table = 'hr_payroll';

    protected $fillable = [ 'id','user_id','school_id', 'allowances','deducition','status','created_at','updated_at' ];

}

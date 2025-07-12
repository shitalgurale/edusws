<?php

namespace App\Models\Addon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hr_roles extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $table = 'hr_roles';

    protected $fillable = [ 'id','name','permanent', 'school_id','created_at' ];
}

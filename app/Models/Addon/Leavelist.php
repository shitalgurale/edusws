<?php
namespace App\Models\Addon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leavelist extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $table = 'leavelists';

    protected $fillable = [ 'id','user_id', 'role_id','start_date', 'end_date', 'reason','status','created_at','updated_at' ];


}

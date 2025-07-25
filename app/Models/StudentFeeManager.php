<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class StudentFeeManager extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'student_fee_managers'; 
    protected $fillable = [
        'title', 'total_amount', 'class_id', 'parent_id','student_id', 'payment_method', 'paid_amount', 'due_amount' ,'status', 'school_id', 'session_id', 'timestamp', 'discounted_price', 'amount'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id'); // Fetch student info from users table
    }
}

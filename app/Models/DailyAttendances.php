<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyAttendances extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
   // protected $fillable = [
     //   'class_id', 'section_id', 'student_id', 'status', 'session_id', 'school_id', 'timestamp'
   // ];
   public $timestamps=false;

   protected $fillable = [
    'class_id',
    'section_id',
    'student_id',
    'status',
    'session_id',
    'school_id',
    'device_id',
    'stu_intime',
    'stu_outtime',
    'timestamp',
    'created_at',
    'updated_at',
    'notification_sent'
];

// Relationship: A daily attendance belongs to an enrollment (student)
    public function enrollment()
   {
    return $this->belongsTo(Enrollment::class, 'student_id', 'user_id');
                //->whereColumn('daily_attendances.school_id', 'enrollments.school_id');
   }
  
  
}
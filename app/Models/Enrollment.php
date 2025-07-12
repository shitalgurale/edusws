<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Classes;
use App\Models\Section;
use App\Models\User;
use App\Models\DailyAttendances;

class Enrollment extends Model
{
    use HasFactory;
    public $timestamps=false;

    protected $table = 'enrollments'; // Ensure table name is correct

    protected $fillable = [
        'user_id',
        'school_id',
        'stu_bioid',
        'class_id',
        'section_id',
        'session_id',
        'department_id',
        'parent_id'
    ];


    

    // getAttendance Function Relationship: One enrollment can have many attendances
   // public function attendances()
 //   {
  //      return $this->hasMany(DailyAttendances::class, 'student_id', 'user_id')
     //               ->whereColumn('daily_attendances.school_id', 'enrollments.school_id');
   // }


   public function attendance()
   {
       return $this->hasMany(DailyAttendances::class, 'student_id', 'user_id');
   }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id'); // Assuming user_id is the foreign key
    }

    
    
}


<?php

namespace App\Models\Addon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hr_user_list extends Model
{
    use HasFactory;
    
    protected $table = 'hr_user_list'; // Table name

    public $timestamps = false; // Disable timestamps if not needed

    protected $fillable = [
        'user_id',
        'hr_roles_role_id',
        'school_id',
        'name',
        'email',
        'emp_bioid',
        'role_id',
        'address',
        'phone',
        'gender',
        'blood_group',
        'joining_salary'
    ];
    
    
    /**
     * Relationship: An employee has a role
     */
    public function hrDailyAttendances()
    {
        return $this->hasMany(HrDailyAttendence::class, 'role_id', 'role_id');
    }
    
    
    public function hrRole()
    {
        return $this->belongsTo(Hr_roles::class, 'hr_roles_role_id');
    }
}
    /**
     * Relationship: An employee (Hr_user_list) has many attendance records
     */
   // public function attendances()
  //  {
     //   return $this->hasMany(HrDailyAttendence::class, 'emp_bioid', 'emp_bioid')
   //                 ->whereColumn('hr_user_list.school_id', 'hr_daily_attendances.school_id');
   // }

     
    /**
     * Relationship: An employee belongs to a User (if applicable)
     */
   


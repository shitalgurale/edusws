<?php

namespace App\Models\Addon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrDailyAttendence extends Model
{
    use HasFactory;
    
    protected $table = 'hr_daily_attendences'; // Table name

    protected $primaryKey = 'id'; // Primary key

    //public $timestamps = True; // Enable timestamps

    protected $fillable = [
        'user_id',
        'hr_roles_role_id',
        'role_id',
        'school_id',
        'device_id',
        'emp_intime',
        'emp_outtime',
        'punchstatus',
        'session_id',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * Relationship: A daily attendance belongs to an employee (Hr_user_list)
     */
   // public function employee()
    //{
      //  return $this->belongsTo(Hr_user_list::class, 'emp_bioid', 'emp_bioid')
        //            ->whereColumn('hr_daily_attendances.school_id', 'hr_user_list.school_id');
    //}
    
    
    
        /**
     * Relationship: A daily attendance belongs to an employee/user.
     */
    public function employee()
    {
        // Assuming employee data is stored in Hr_user_list and matched by user_id
        return $this->belongsTo(Hr_user_list::class, 'user_id');
    }

    /**
     * Relationship: A daily attendance belongs to a role.
     */
    public function role()
    {
        return $this->belongsTo(Hr_roles::class, 'role_id');
    }
    
    

    public function hrUser()
    {
        return $this->belongsTo(Hr_user_list::class, 'role_id', 'role_id');
    }


    /**
     * Relationship: Each attendance belongs to a specific employee.
     */
    public function user()
    {
        return $this->belongsTo(Hr_user_list::class, 'user_id');
    }

}

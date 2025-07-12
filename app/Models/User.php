<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'parent_id', 'school_id', 'code', 'user_information', 'student_info', 'documents', 'status', 'department_id', 'designation', 'language', 'school_role', 'account_status'
    ];



        // For parent to access their assigned children
        public function children()
        {
            return $this->hasMany(User::class, 'parent_id')->where('role_id', 7);
        }
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function checkEnrollment(){
        return $this->hasMany(Enrollment::class,'user_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'parent_id');
    }
    
     public function getTomoNameAttribute()
    {
        return $this->checkEnrollment()->class_id;  
    }


    public function hrUserList()
    {
        return $this->hasOne(Hr_user_list::class, 'user_id', 'id'); // ✅ One-to-One Relationship with hr_user_list
    }

    public function hrUser()
    {
        return $this->hasOne(Hr_user_list::class, 'user_id', 'id');
    }


    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

}

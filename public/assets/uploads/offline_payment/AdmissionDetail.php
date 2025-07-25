<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionDetail extends Model
{
    use HasFactory;

    protected $table = 'admission_details';

    protected $fillable = [
        'user_id',
        'class_id',
        'section_id',
        'school_id',
        'session_id',
        'dob',
        'user_information',
        'nationality',
        'cast',
        'admission_date',
        'mother_name',
        'father_name',
    ];

    protected $dates = ['dob', 'admission_date', 'created_at', 'updated_at'];

    // Relationships (optional, if you want to use)
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

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}

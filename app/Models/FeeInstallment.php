<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeInstallment extends Model
{
    use HasFactory;




protected $fillable = ['invoice_id', 'student_id', 'amount_paid', 'payment_method', 'paid_at'];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'mg_attendance';
    protected $fillable = ['employee_id', 'checkin', 'checkout', 'isattended'];

}

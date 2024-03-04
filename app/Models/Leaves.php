<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leaves extends Model
{
    use HasFactory;
    protected $table = 'mg_leave';
    protected $fillable = [
        'leave_type',
        'total_days_leave',
        'total_leave_year',
        'start_date',
        'end_date',
        'leave_status',
        'leave_reason',
        'handover_by',
        'employee_id',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

    public function handOver()
    {
        return $this->belongsToMany(Employees::class, 'mg_employee', 'employee_id');
    }
    public function empLoyee()
    {
        return $this->belongsToMany(Employees::class, 'mg_employee', 'employee_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class Task extends Model
{
    protected $table = 'mg_tasks';
    protected $fillable = [
        'project_id',
        'assign_by',
        'task_name',
        'task_description',
        'start_date',
        'end_date',
        'percentage_task',
        'completed_date',
        'task_status',
        'task_submit_status',
    ];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
    public function project()
    {
        return $this->belongsToMany(Project::class, 'mg_projects', 'project_id');
    }
    public function employee()
    {
        return $this->belongsToMany(Employees::class, 'mg_employee', 'employee_id');
    }
    public function employeeAssignees()
    {
        return $this->belongsToMany(Employees::class, 'mg_employee_tasks', 'tasks_id', 'employee_id');
    }
}

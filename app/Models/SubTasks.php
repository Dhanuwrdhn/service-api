<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTasks extends Model
{
    protected $table = 'mg_sub_tasks';
    protected $fillable = [
        'subtask_name',
        'subtask_description',
        'task_id',
        'assign_by',
        'start_date',
        'end_date',
        'subtask_status',
        'subtask_submit_status',
        'subtask_percentage',
        'subtask_image',
        'reason',
    ];
    protected $casts = [
        'start_date' => 'datetime:Y-m-d H:m:s',
        'end_date' => 'datetime:Y-m-d H:m:s'
    ];

    public function Tasks()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
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
        return $this->belongsToMany(Employees::class, 'mg_employee_subtasks', 'tasks_id', 'employee_id');
    }
}


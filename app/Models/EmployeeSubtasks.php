<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSubtasks extends Model
{
    use HasFactory;
    protected $table = 'mg_employee_subtask';
    protected $fillable = [
        'employee_id',
        'tasks_id',
        'subtasks_id'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

    public function employee()
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }
    public function tasks()
    {
        return $this->belongsTo(Task::class, 'tasks_id');
    }
    public function projects()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    public function Subtasks()
    {
        return $this->belongsTo(SubTasks::class, 'subtasks_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'mg_tasks';
    protected $fillable = [
        'project_id',
        'task_name',
        'task_description',
        'start_date',
        'end_date',
        'assigned_by',
        'assigned_to',
        'percentage_task',
        'total_subtask_completed',
        'task_status',
        'task_submit_status',
    ];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}

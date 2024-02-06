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
        'start_date',
        'end_date',
        'subtask_status',
        'subtask_submit_status',
        'subtask_percentage',
        'subtask_image',
    ];
    protected $casts = [
        'start_date' => 'datetime:Y-m-d H:m:s',
        'end_date' => 'datetime:Y-m-d H:m:s'
    ];
    public function Tasks()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}


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
        'task_status',
        'task_submit_status',
        'percentage_subtask',
        'image-subtask',
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


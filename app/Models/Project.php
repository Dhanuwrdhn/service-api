<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = "mg_projects";

    protected $fillable = [
        'project_name',
        'project_desc',
        'role_id',
        'jobs_id',
        'team_id',
        'assign_by',
        'start_date',
        'end_date',
        'project_status',
        'total_task_created',
        'total_task_completed',
    ];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

    public function jobs()
    {
        return $this->belongsTo('App\Models\Job')->orderBy('id','ASC');
    }
    public function teams()
    {
        return $this->belongsTo('App\Models\Team')->orderBy('id','ASC');
    }
    public function roles()
    {
        return $this->belongsTo('App\Models\Role')->orderBy('id','ASC');
    }
    // public function client()
    // {
    //     return $this->hasMany('App\Models\Client')->orderBy('id','ASC');
    // }
    public function tasks()
    {
        return $this->belongsTo('App\Models\Task')->orderBy('id','ASC');
    }
    public function employeeAssignees()
    {
        return $this->belongsToMany(Employees::class, 'mg_employee_project', 'project_id', 'employee_id');
    }
}

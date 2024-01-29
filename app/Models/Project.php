<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = "mg_projects";

    protected $fillable = [
        'project_name',
        'role_id',
        'jobs_id',
        'team_id',
        'pm_id',
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
        return $this->belongsTo('App\Models\Jobs')->orderBy('id','ASC');
    }
    public function teams()
    {
        return $this->hasMany('App\Models\Teams')->orderBy('id','ASC');
    }
    public function roles()
    {
        return $this->hasMany('App\Models\Roles')->orderBy('id','ASC');
    }
    // public function client()
    // {
    //     return $this->hasMany('App\Models\Client')->orderBy('id','ASC');
    // }
    public function tasks()
    {
        return $this->hasMany('App\Models\Tasks')->orderBy('id','ASC');
    }
    public function projectM()
    {
        return $this->hasMany('App\Models\Employees')->orderBy('id','ASC');
    }
}

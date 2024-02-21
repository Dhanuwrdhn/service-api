<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docs extends Model
{
    use HasFactory;

    protected $table = 'mg_documents';
    protected $fillable = [
        'team_id',
        'role_id',
        'jobs_id',
        'project_id',
        'document_name',
        'document_desc',
        'creator_id',
        'document_file',
    ];

    // Relationship with Creator (Employee)
    public function creator()
    {
        return $this->belongsTo(Employee::class, 'creator_id');
    }

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
    public function project()
    {
        return $this->belongsToMany(Project::class, 'mg_projects', 'project_id');
    }
}

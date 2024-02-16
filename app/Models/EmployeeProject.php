<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class EmployeeProject extends Model
{
    use HasFactory;
    protected $table = 'mg_employee_project';

    protected $fillable = [
        'employee_id',
        'project_id',
    ];

    protected $hidden =[
        'password'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

  public function employee()
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }

    /**
     * Mendapatkan data proyek yang terkait dengan entitas EmployeeProject.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}

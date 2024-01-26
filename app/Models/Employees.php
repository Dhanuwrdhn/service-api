<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\belongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Job;
use App\Models\Role;
use App\Models\Team;
use App\Models\Employee;


class Employees extends Model
{
    use HasFactory, HasApiTokens, Notifiable;
    protected $table = 'mg_employee';

    protected $fillable = [
        'role_id',
        'jobs_id',
        'team_id',
        'employee_name',
        'date_of_birth',
        'age',
        'mobile_number',
        'email',
        'username',
        'password',
        'gender',
        'religion',
        'npwp_number',
        'identity_number',
    ];

    protected $hidden =[
        'password'
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
        return $this->hasMany('App\Models\Team')->orderBy('id','ASC');
    }
    public function roles()
    {
        return $this->hasMany('App\Models\Role')->orderBy('id','ASC');
    }
    public function projects()
    {
        return $this->hasMany('App\Models\Project')->orderBy('id','ASC');
    }
    public function client()
    {
        return $this->hasMany('App\Models\Client')->orderBy('id','ASC');
    }
}

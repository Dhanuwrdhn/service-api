<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\Sanctum;

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

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'jobs_id');
    }

    public function team(): HasMany
    {
        return $this->hasMany(Team::class, 'team_id');
    }

    public function role(): HasMany
    {
        return $this->hasMany(Role::class, 'role_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'mg_employee_project', 'employee_id', 'project_id');
    }
}

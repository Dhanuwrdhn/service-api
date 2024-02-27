<?php

use Illuminate\Support\Facades\Route;

// API JOBS
require __DIR__.'/api/jobs.routes.php';
// API TEAMS
require __DIR__.'/api/teams.routes.php';
// API CLIENTS
require __DIR__.'/api/client.routes.php';
// API ROLES
require __DIR__.'/api/role.routes.php';
// API PROJECTS
require __DIR__.'/api/project.routes.php';
// API EMPLOYEES
require __DIR__.'/api/employee.routes.php';
// API ATTENDANCE
require __DIR__.'/api/attendance.routes.php';
// API DOCUMENTS
require __DIR__.'/api/documents.routes.php';
// API TASKS
require __DIR__.'/api/task.routes.php';
// API SUBTASKS
require __DIR__.'/api/subtask.routes.php';
// API EMPLOYEE PROJECTS
require __DIR__.'/api/employeeProject.routes.php';
// API EMPLOYEE TASKS
require __DIR__.'/api/employeeTask.routes.php';
// API EMPLOYEE SUBTASKS
require __DIR__.'/api/employeeSubtask.routes.php';
// API Authentication
require __DIR__.'/api/auth.routes.php';

// refresh-token
Route::middleware('auth:sanctum')->group(function () {
    // You can add any middleware-specific routes here

});

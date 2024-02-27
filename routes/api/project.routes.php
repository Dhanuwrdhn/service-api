
<?php

use Illuminate\Support\Facades\Route;


Route::get('projects', [\App\Http\Controllers\ProjectsController::class, 'index']);
Route::post('projects', [\App\Http\Controllers\ProjectsController::class, 'create']);
Route::put('projects/{id}', [\App\Http\Controllers\ProjectsController::class, 'update']);
Route::delete('projects/{id}', [\App\Http\Controllers\ProjectsController::class, 'destroy']);
Route::put('projects-status/{id}', [\App\Http\Controllers\ProjectsController::class, 'updateProjectStatus']);
Route::get('projects/{id}', [\App\Http\Controllers\ProjectsController::class, 'show']);

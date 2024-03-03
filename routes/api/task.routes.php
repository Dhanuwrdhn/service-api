<?php

use Illuminate\Support\Facades\Route;

Route::get('show-tasks', [\App\Http\Controllers\TasksController::class, 'index']);
Route::get('show-tasks-by-project/{projectid}', [\App\Http\Controllers\TasksController::class, 'showByProject']);
Route::get('show-tasks/{taskid}', [\App\Http\Controllers\TasksController::class, 'showTaskSpecific']);
Route::post('create-tasks', [\App\Http\Controllers\TasksController::class, 'createTask']);
Route::put('edit-task/{id}', [\App\Http\Controllers\TasksController::class, 'edittask']);
Route::put('accept-task/{id}', [\App\Http\Controllers\TasksController::class, 'acceptTask']);
Route::put('reject-task/{id}', [\App\Http\Controllers\TasksController::class, 'rejectTask']);
Route::put('submit-task/{id}', [\App\Http\Controllers\TasksController::class, 'submitTask']);
Route::delete('delete-task/{id}', [\App\Http\Controllers\TasksController::class, 'deleteTask']);
Route::put('review-task/{id}', [\App\Http\Controllers\TasksController::class, 'reviewTask']);
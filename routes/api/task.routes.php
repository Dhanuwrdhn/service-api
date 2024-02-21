<?php

use Illuminate\Support\Facades\Route;

Route::post('create-tasks', [\App\Http\Controllers\TasksController::class, 'createTask']);
Route::get('show-tasks', [\App\Http\Controllers\TasksController::class, 'index']);
Route::get('show-tasks-by-project/{projectid}', [\App\Http\Controllers\TasksController::class, 'showByProject']);
Route::get('show-tasks/{taskid}', [\App\Http\Controllers\TasksController::class, 'showTaskSpecific']);
Route::put('update-status/{id}', [\App\Http\Controllers\TasksController::class, 'updateStatus']);
Route::delete('delete-task/{id}', [\App\Http\Controllers\TasksController::class, 'destroy']);


<?php

use Illuminate\Support\Facades\Route;

Route::post('create-subtasks', [\App\Http\Controllers\SubTaskController::class, 'createSubTasks']);
Route::put('edit-subtask/{id}', [\App\Http\Controllers\SubTaskController::class, 'editSubTask']);
Route::get('show-subtask/{id}', [\App\Http\Controllers\SubTaskController::class, 'showSubTask']);
Route::get('show-subtasks/{task_id}', [\App\Http\Controllers\SubTaskController::class, 'showSubTasksByTask']);
Route::get('show-subtasks',[\App\Http\Controllers\SubTaskController::class, 'showSubTasksByEmployee']);
Route::put('accept-subtask/{id}', [\App\Http\Controllers\SubTaskController::class, 'acceptSubTask']);
Route::put('reject-subtask/{id}', [\App\Http\Controllers\SubTaskController::class, 'rejectSubTask']);
Route::put('review-subtask/{id}', [\App\Http\Controllers\SubTaskController::class, 'reviewSubTask']);
Route::put('submit-subtask/{id}',[\App\Http\Controllers\SubTaskController::class, 'submitSubtask']);
Route::delete('delete-subtask/{id}', [\App\Http\Controllers\SubTaskController::class, 'destroy']);  

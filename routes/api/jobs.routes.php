<?php

use Illuminate\Support\Facades\Route;

Route::get('jobs', [\App\Http\Controllers\JobsController::class, 'index']);
Route::get('jobs/{id}', [\App\Http\Controllers\JobsController::class, 'show']);
Route::post('jobs', [\App\Http\Controllers\JobsController::class, 'create']);
Route::put('jobs/{id}', [\App\Http\Controllers\JobsController::class, 'update']);
Route::delete('jobs/{id}', [\App\Http\Controllers\JobsController::class, 'destroy']);
<?php

use Illuminate\Support\Facades\Route;

Route::get('teams', [\App\Http\Controllers\TeamsController::class, 'index']);
Route::post('teams', [\App\Http\Controllers\TeamsController::class, 'create']);
Route::put('teams/{id}', [\App\Http\Controllers\TeamsController::class, 'update']);
Route::delete('teams/{id}', [\App\Http\Controllers\TeamsController::class, 'destroy']);
Route::get('teams/{id}', [\App\Http\Controllers\TeamsController::class, 'show']);
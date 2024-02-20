<?php

use Illuminate\Support\Facades\Route;


Route::post('roles', [\App\Http\Controllers\RolesController::class, 'create']);
Route::put('roles/{id}', [\App\Http\Controllers\RolesController::class, 'update']);
Route::delete('roles/{id}', [\App\Http\Controllers\RolesController::class, 'destroy']);
Route::get('roles/{id}', [\App\Http\Controllers\RolesController::class, 'show']);

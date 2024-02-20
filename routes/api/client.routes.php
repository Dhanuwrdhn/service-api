<?php

use Illuminate\Support\Facades\Route;

Route::get('clients', [\App\Http\Controllers\ClientsController::class, 'index']);
Route::post('clients', [\App\Http\Controllers\ClientsController::class, 'create']);
Route::put('clients/{id}', [\App\Http\Controllers\ClientsController::class, 'update']);
Route::delete('clients/{id}', [\App\Http\Controllers\ClientsController::class, 'destroy']);
Route::get('clients/{id}', [\App\Http\Controllers\ClientsController::class, 'show']);

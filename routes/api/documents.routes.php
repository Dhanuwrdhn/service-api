<?php

use Illuminate\Support\Facades\Route;

Route::post('storeDocs', [\App\Http\Controllers\DocsController::class, 'store']);
Route::get('getDocs', [\App\Http\Controllers\DocsController::class, 'getDocs']);
Route::get('getDoc/{id}', [\App\Http\Controllers\DocsController::class, 'getDoc']);
Route::put('updateDoc/{id}', [\App\Http\Controllers\DocsController::class, 'updateDoc']);
Route::delete('deleteDoc/{id}', [\App\Http\Controllers\DocsController::class, 'deleteDoc']);

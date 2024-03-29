<?php

use Illuminate\Support\Facades\Route;

Route::post('storeDocs', [\App\Http\Controllers\DocsController::class, 'store']);
Route::get('getDocs', [\App\Http\Controllers\DocsController::class, 'getAll']);
Route::get('getDoc/{id}', [\App\Http\Controllers\DocsController::class, 'getById']);
Route::get('/documents/{id}/download', [\App\Http\Controllers\DocsController::class, 'downloadDocument']);
Route::put('updateDoc/{id}', [\App\Http\Controllers\DocsController::class, 'updateDocument']);
Route::delete('deleteDoc/{id}', [\App\Http\Controllers\DocsController::class, 'deleteDocs']);

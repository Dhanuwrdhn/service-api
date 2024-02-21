<?php

use Illuminate\Support\Facades\Route;

Route::post('storeDocs', [\App\Http\Controllers\DocsController::class, 'store']);

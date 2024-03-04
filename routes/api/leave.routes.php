
<?php

use Illuminate\Support\Facades\Route;


Route::get('leaveAll', [\App\Http\Controllers\LeaveController::class, 'index']);
Route::get('leave/year', [\App\Http\Controllers\LeaveController::class, 'getByLeaveYear']);
Route::get('leave/sick', [\App\Http\Controllers\LeaveController::class, 'getByLeaveSick']);
Route::get('leave/special', [\App\Http\Controllers\LeaveController::class, 'getByLeaveSpecial']);
Route::post('setTotalLeaveYearmployee', [\App\Http\Controllers\LeaveController::class, 'setTotalLeaveYearForAllEmployees']);
Route::post('requestLemployee', [\App\Http\Controllers\LeaveController::class, 'requestLeaveForEmployee']);

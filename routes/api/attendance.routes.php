
<?php

use Illuminate\Support\Facades\Route;


Route::post('attendance', [\App\Http\Controllers\AttendanceController::class, 'attendanceCheckIn']);
Route::put('attendance', [\App\Http\Controllers\AttendanceController::class, 'attendanceCheckOut']);

Route::get('attendance', [\App\Http\Controllers\AttendanceController::class, 'getAllAttendance']);
Route::get('attendance/{id}', [\App\Http\Controllers\AttendanceController::class, 'getAttendanceByEmployee']);

Route::get('checkin/{employee_id}',[\App\Http\Controllers\AttendanceController::class, 'getCheckIn']);
Route::get('checkout/{employee_id}',[\App\Http\Controllers\AttendanceController::class, 'getCheckOut']);

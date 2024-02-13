<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employees;

class AttendanceController extends Controller
{
    //attendance tapping (checkin)
  public function attendanceCheckIn(Request $request)
{
    try {
        // Terima input employee_id
        $employee_id = $request->input('employee_id');

        // Periksa apakah employee_id ada di mg_employees
        $employee = Employees::find($employee_id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        // Periksa apakah employee sudah check-in hari ini
        $today = (new \DateTime())->setTime(0, 0);
        $attendance = Attendance::where('employee_id', $employee_id)
            ->whereDate('checkin', $today)
            ->first();

        if ($attendance) {
            return response()->json(['message' => 'Employee already checked in today'], 400);
        }

        // Buat entri baru di tabel attendance dengan timestamp checkin
        $checkinTime = (new \DateTime())->format('Y-m-d H:i:s'); // Tambahkan tanggal saat membuat objek DateTime
        Attendance::create([
            'employee_id' => $employee_id,
            'checkin' => $checkinTime,
            'checkout' => null,
            'isattended' => true,
        ]);

        return response()->json(['message' => 'Check-in successful', 'employee_id' => $employee_id, 'checkin_time' => $checkinTime], 201);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to check in: ' . $e->getMessage()
        ], 500);
    }
}


        //attendance leave tapping (checkout)
        public function attendanceCheckOut(Request $request)
    {
        try {
        $employee_id = $request->input('employee_id');

        $employee = Employees::find($employee_id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        // Check if already checkout
        $attendance = Attendance::where('employee_id', $employee_id)
            ->whereDate('checkout', (new \DateTime())->setTime(0, 0))
            ->first();

        if ($attendance) {
            return response()->json(['message' => 'Employee has already checked out today'], 400);
        }

        // Check if already checkin
        $today = (new \DateTime())->setTime(0, 0);
        $attendance = Attendance::where('employee_id', $employee_id)
            ->whereDate('checkin', $today)
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'Employee did not check in today'], 400);
        }

        // Update the checkout to current time
        $checkoutTime = new \DateTime();
        $updatedAttendance = Attendance::where('employee_id', $employee_id)->update(['checkout' => $checkoutTime]);

        // Hitung waktu antara check-in dan check-out
        $checkinTime = new \DateTime($attendance->checkin);
        $duration = $checkoutTime->diff($checkinTime)->format('%H:%I:%S');

        return response()->json(['message' => 'Check-out successful', 'duration' => $duration], 201);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal melakukan checkout: ' . $e->getMessage()
        ], 500);
    }
}



}


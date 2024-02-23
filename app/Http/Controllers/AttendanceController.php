<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employees;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\DB;

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
        $checkinTimeStamp = (new \DateTime())->format('Y-m-d H:i:s'); // Tambahkan tanggal saat membuat objek DateTime

        //check time if below 08:30 is early, if between 08:30 - 09:00 is on time, if above 09:00 is late then assign to status
        $checkinTime = new \DateTime($checkinTimeStamp);
        $status = 'On Time';
        if ($checkinTime->format('H:i') < '08:30') {
            $status = 'Early';
        } elseif ($checkinTime->format('H:i') > '09:00') {
            $status = 'Late';
        }

        //reason for late, if status is late, if not, then null
        $note = $request->input('note') ?? null;
        Attendance::create([
            'employee_id' => $employee_id,
            'checkin' => $checkinTimeStamp,
            'checkout' => null,
            'status' => $status,
            'note' => $note
        ]);

        return response()->json(['message' => 'Check-in successful','status' => $status, 'employee_id' => $employee_id,'note' => $note, 'checkin_time' => $checkinTimeStamp], 201);
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
        // Check if already checkin
        $today = (new \DateTime())->format('Y-m-d');
        $attendance = Attendance::where('employee_id', $employee_id)
        ->whereDate('checkin', $today)
        ->first();

        if (!$attendance) {
            return response()->json(['message' => 'Employee did not check in today'], 400);
        }


        // Periksa apakah sudah ada catatan checkout untuk karyawan pada hari ini
        $checkoutattendance = Attendance::where('employee_id', $employee_id)
        ->whereDate('checkout', $today)
        ->first();


        if ($checkoutattendance) {
            return response()->json(['message' => 'Employee has already checked out today', "fsts"=>$attendance], 400);
        }
        // Update the checkout to current time
        $checkoutTime = new \DateTime();
        $updatedAttendance = Attendance::where('employee_id', $employee_id)
                                        ->whereDate('checkin',$today) // Bandingkan jam dan menit saja
                                        ->update(['checkout' => $checkoutTime]);
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

    // GET All Attendance for admin
    public function getAllAttendance()
    {
        try {
            $attendance = Attendance::all();

            if ($attendance->isEmpty()) {
                return response()->json(['message' => 'No attendance records found'], 404);
            }

            return response()->json(['attendance' => $attendance], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get attendance records: ' . $e->getMessage()
            ], 500);
        }
    }

    // GET All Attendance per employee
    public function getAttendanceByEmployee($employee_id)
    {
        try {
            $employee = Employees::find($employee_id);
            if (!$employee) {
                return response()->json(['message' => 'Employee not found'], 404);
            }

            $attendance = Attendance::where('employee_id', $employee_id)
                                    ->select('id', 'employee_id', 'checkin', 'checkout', 'status', 'note')
                                    ->get();

            if ($attendance->isEmpty()) {
                return response()->json(['message' => 'No attendance records found for the employee'], 404);
            }

            return response()->json(['attendance' => $attendance], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get attendance records: ' . $e->getMessage()
            ], 500);
        }
    }


    // Get check-in time for an employee
    public function getCheckIn($employee_id){

    try {
        $employee = Employees::find($employee_id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $checkin = Attendance::where('employee_id', $employee_id)
            ->whereNotNull('checkin')
            ->orderBy('checkin', 'desc')
            ->first();

        if (!$checkin) {
            return response()->json(['message' => 'No check-in record found for the employee'], 404);
        }

        // Split date and time
        $checkin_time = explode(' ', $checkin->checkin);
        $date = $checkin_time[0];
        $time = $checkin_time[1];

        return response()->json([
            'id' => $checkin->id,
            'employee_id' => $checkin->employee_id,
            'date' => $date,
            'time' => $time
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to get check-in time: ' . $e->getMessage()
        ], 500);
    }
}

// Get check-out time for an employee
public function getCheckOut($employee_id)
{
    try {
        $employee = Employees::find($employee_id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $checkout = Attendance::where('employee_id', $employee_id)
            ->whereNotNull('checkout')
            ->orderBy('checkout', 'desc')
            ->first();

        if (!$checkout) {
            return response()->json(['message' => 'No check-out record found for the employee'], 404);
        }

        // Split date and time
        $checkout_time = explode(' ', $checkout->checkout);
        $date = $checkout_time[0];
        $time = $checkout_time[1];

        return response()->json([
            'id' => $checkout->id,
            'employee_id' => $checkout->employee_id,
            'date' => $date,
            'time' => $time
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to get check-out time: ' . $e->getMessage()
        ], 500);
    }
}

    public function autoCheckOut()
    {
        try {
            DB::beginTransaction();

            $result = Attendance::whereNull('checkout')
                ->update(['checkout' => now()]);

            $discordWebhookUrl = 'https://discord.com/api/webhooks/1210505645334335521/Ke4lTZFQypZrHLYYwC2Gbwm_Dv4hwC5UunltvrSzzlb8VsXKK3e8ofrWd8hLIMih2gTP';

            Http::post($discordWebhookUrl, [
                'content' => ' @474968068490264577 p ' . now(),
            ]);

                
            return response()->json([
                'status' => 'success',
                'message' => 'Auto checkout successful for ' . $result . ' employees'
            ], 200);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to auto checkout: ' . $e->getMessage()
            ], 500);
        }
    }
}


<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employees;

class AttendanceController extends Controller
{
    //attendance tapping (checkin)
    public function attendanceCheckIn(Request $request){
        try{

            
            // Receive input employee_id
            $employee_id = $request->input('employee_id');

            // Check if employee_id exists in mg_employees
            $employee = Employees::find($employee_id);

            if (!$employee) {
                return response()->json(['message' => 'Employee not found'], 404);
            }

            // Check if the employee already checked in today
            $today =  (new \DateTime())->setTime(0, 0);
            $attendance = Attendance::where('employee_id', $employee_id)
                ->whereDate('checkin', $today)
                ->first();

            if ($attendance) {
                return response()->json(['message' => 'Employee already checked in today'], 400);
            }

            // Create a new entry in the attendance table with checkin timestamp
            $newAttendance = Attendance::create([
                'employee_id' => $employee_id,
                'checkin' => new \DateTime(),
                'checkout' => null,
                'isattended' => true,
            ]);

            return response()->json(['message' => 'Check-in successful', 'attendance' => $newAttendance], 201);
        }catch (\Exception $e) {

            return response()->json([
            'status' => 'error',
            'message' => 'Gagal melakukan checkin' . $e->getMessage()
            ], 500);
        }
    }

    //attendance leave tapping (checkout)
    public function attendanceCheckOut(Request $request){
        try{
            $employee_id = $request->input('employee_id');
            $employee = Employees::find($employee_id);
            if (!$employee) {
                return response()->json(['message' => 'Employee not found'], 404);
            }

            //check if already checkin 
            $today =  (new \DateTime())->setTime(0, 0);
            $attendance = Attendance::where('employee_id', $employee_id)
                ->whereDate('checkin', $today)
                ->first();


            if (!$attendance) {
                return response()->json(['message' => 'Employee did not checked in today'], 400);
            }

            //update the checkout to 1
            $updatedAttendance = Attendance::where('employee_id', $employee_id)->update(['checkout' => new \DateTime()]);
            
            return response()->json(['message' => 'Check-out successful', 'attendance' => $updatedAttendance], 201);

        }catch (\Exception $e) {

            return response()->json([
            'status' => 'error',
            'message' => 'Gagal melakukan checkout' . $e->getMessage()
            ], 500);
        }

    }


}

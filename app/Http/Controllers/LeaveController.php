<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees;
use App\Models\Leaves;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class LeaveController extends Controller
{
    // get all
    public function index()
    {
        $leaves = Leaves::all();
        if($leaves->isEmpty()){
            return response()->json([
                'status' => 'error',
                'messages' => 'No leave found'
            ]);
        }
        return response()->json([
            'status' => 'success',
            'data' => $leaves
        ]);
    }
    //get leave by leave_type leave_year
    public function getByLeaveYear()
    {
        $leave_type = 'leave_year'; // Tetapkan leave_type yang diinginkan

        // Dapatkan data leave berdasarkan leave_type
        $leaves = Leaves::where('leave_type', $leave_type)->get();

        // Jika tidak ada data yang ditemukan
        if ($leaves->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'messages' => 'No leave found for the the specified leave year'
            ], 404);
        }

        // Jika berhasil, kembalikan data cuti dengan status sukses
        return response()->json([
            'status' => 'success',
            'data' => $leaves
        ], 200);
    }
    //get leave by leave_type leave_sick
    public function getByLeaveSick()
    {
        $leave_type = 'leave_sick'; // Tetapkan leave_type yang diinginkan

        // Dapatkan data leave berdasarkan leave_type
        $leaves = Leaves::where('leave_type', $leave_type)->get();

        // Jika tidak ada data yang ditemukan
        if ($leaves->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'messages' => 'No leave found for the specified leave sick'
            ], 404);
        }

        // Jika berhasil, kembalikan data cuti dengan status sukses
        return response()->json([
            'status' => 'success',
            'data' => $leaves
        ], 200);
    }

    //get leave by leave_type leave_special
    public function getByLeaveSpecial()
    {
        $leave_type = 'leave_special'; // Tetapkan leave_type yang diinginkan

        // Dapatkan data leave berdasarkan leave_type
        $leaves = Leaves::where('leave_type', $leave_type)->get();

        // Jika tidak ada data yang ditemukan
        if ($leaves->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'messages' => 'No leave found for the specified leave special '
            ], 404);
        }

        // Jika berhasil, kembalikan data cuti dengan status sukses
        return response()->json([
            'status' => 'success',
            'data' => $leaves
        ], 200);
    }
    //input semua total cuti tahunan Karyawan
    public function setTotalLeaveYearForAllEmployees(Request $request)
    {
        // Validasi input dari request
        $request->validate([
            'total_leave_year' => 'required|integer',
        ]);

        // Dapatkan semua karyawan
        $employees = Employees::all();

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Update total cuti tahunan untuk setiap karyawan
            foreach ($employees as $employee) {
                // Cari atau buat entri cuti untuk karyawan
                $leave = Leaves::updateOrCreate(
                    ['employee_id' => $employee->id],
                    ['total_leave_year' => $request->total_leave_year]
                );
            }

            // Commit transaksi jika semuanya berhasil
            DB::commit();

            return response()->json([
                'status'=> 'success',
                'message' => 'Total leave year updated successfully for all employees'], 200);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            return response()->json([
                'status'=> 'error',
                'message' => 'Failed to update total leave year for all employees', 'error' => $e->getMessage()], 500);
        }
    }

    //edit total cuti tahunan semua karyawan
        public function updateTotalLeaveYearForAllEmployees(Request $request)
    {
        // Validasi input dari request
        $validator = Validator::make($request->all(), [
            'total_leave_year' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid input data',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Dapatkan semua karyawan
        $employees = Employees::all();

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Update total cuti tahunan untuk setiap karyawan
            foreach ($employees as $employee) {
                // Cari atau buat entri cuti untuk karyawan
                $leave = Leaves::updateOrCreate(
                    ['employee_id' => $employee->id],
                    ['total_leave_year' => $request->total_leave_year]
                );
            }

            // Commit transaksi jika semuanya berhasil
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Total leave year updated successfully for all employees'
            ], 200);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update total leave year for all employees',
                'error' => $e->getMessage()
            ], 500);
        }
    }
        //edit total cuti tahunan sesuai id karyawan
        public function setTotalLeaveYearByEmployeeId(Request $request)
    {
        // Validasi input dari request
        $validator = Validator::make($request->all(), [
            'total_leave_year' => 'required|integer',
            'employee_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Cari karyawan berdasarkan employee_id
        $employee = Employees::find($employeeId);

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found',
            ], 404);
        }

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Update total cuti tahunan untuk karyawan tertentu
            $leave = Leaves::updateOrCreate(
                ['employee_id' => $employee->id],
                ['total_leave_year' => $request->total_leave_year]
            );

            // Commit transaksi jika semuanya berhasil
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Total leave year updated successfully for employee',
                'employee' => $employee
            ], 200);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update total leave year for employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //request

    public function requestLeaveForEmployee(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer',
            'leave_type' => 'required|in:leave_year,leave_sick,leave_special',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'total_days_leave' => 'nullable|integer',
            'handover_by' => 'nullable|integer',
            'leave_status' => 'nullable|in:onPending,onReview,Allowed',
            'total_days_special' => 'nullable|integer',
            'leave_reason' => 'nullable|string',
        ]);

        // Return error response if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid data sent',
                'errors' => $validator->errors()
            ], 400);
        }

        // Extract validated data
        $validatedData = $validator->validated();

        try {
            // Start database transaction
            DB::beginTransaction();

            // Create leave request
            $leave = Leaves::create($validatedData);
            $leave->leave_status = 'onReview';
            $leave->save();

            // Commit transaction
            DB::commit();

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Leave request submitted successfully',
                'data' => $leave
            ], 200);
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            // Return error response
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to submit leave request',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function acceptLeave(Request $request, $id){
        try{

            DB::beginTransaction();
            // Get the leave request by id
            $leave = Leaves::findOrFail($id);
            if ($leave->leave_status != "onApproval")
            throw new \Exception("Invalid action");
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to accept task: ' . $e->getMessage()
            ], 500);
        }
    }
}

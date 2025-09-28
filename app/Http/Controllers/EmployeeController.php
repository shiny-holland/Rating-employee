<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function __construct()
    {
        // No middleware - direct access
    }

    /**
     * Display the main Vue component
     */
    public function index()
    {
        return view('vue-app');
    }

    /**
     * Save employee via API
     */
    public function saveEmployee(Request $request)
    {
        try {
            if ($request->has('id')) {
                // Update existing employee
                $updateData = [
                    'employee_name' => $request->input('employee_name'),
                    'dept_id' => $request->input('dept_id'),
                    'mdate' => now(),
                    'mip' => $request->ip(),
                ];

                DB::table('employee')
                    ->where('id', $request->id)
                    ->update($updateData);

                return response()->json([
                    'status' => 200,
                    'data' => [
                        'button' => 'Success',
                        'message' => 'Employee updated successfully.',
                        'notify' => 'success'
                    ]
                ]);
            } else {
                // Create new employee
                $employee_name = $request->input('employee_name');
                $dept_id = $request->input('dept_id');

                $insertData = [
                    'employee_name' => $employee_name,
                    'dept_id' => $dept_id,
                    'cdate' => now(),
                    'cip' => $request->ip(),
                    'is_delete' => 0,
                    'cby' => 'admin',
                ];

                DB::table('employee')->insertOrIgnore($insertData);

                return response()->json([
                    'status' => 200,
                    'data' => [
                        'button' => 'Success',
                        'message' => 'Employee added successfully.',
                        'notify' => 'success'
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'data' => [
                    'button' => 'Error',
                    'message' => 'Database error: ' . $e->getMessage(),
                    'notify' => 'error'
                ]
            ]);
        }
    }

    /**
     * Get all employees via API
     */
    public function getEmployees(Request $request)
    {
        $employees = DB::table('employee')
            ->join('department', 'employee.dept_id', '=', 'department.id')
            ->where('employee.is_delete', 0)
            ->select('employee.id', 'employee.employee_name', 'employee.dept_id', 'department.dept_name')
            ->orderBy('employee.id', 'desc')
            ->get();
        
        return response()->json([
            'status' => 200,
            'data' => $employees
        ]);
    }

    /**
     * Get single employee via API
     */
    public function getSingleEmployee(Request $request)
    {
        $employee = Employee::findOrFail($request->id);
        
        return response()->json([
            'status' => 200,
            'data' => $employee
        ]);
    }

    /**
     * Delete employee via API
     */
    public function deleteEmployee(Request $request)
    {
        try {
            $updateData = [
                'is_delete' => 1,
                'mip' => $request->ip(),
                'mdate' => now(),
            ];

            DB::table('employee')
                ->where('id', $request->id)
                ->update($updateData);

            return response()->json([
                'status' => 200,
                'data' => [
                    'button' => 'Success',
                    'message' => 'Employee deleted successfully.',
                    'notify' => 'success'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'data' => [
                    'button' => 'Error',
                    'message' => 'Database error: ' . $e->getMessage(),
                    'notify' => 'error'
                ]
            ]);
        }
    }
}

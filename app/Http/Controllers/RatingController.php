<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\DepartmentRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
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
     * Get employees by department
     */
    public function getEmployeesByDepartment(Request $request)
    {
        try {
            $dept_id = $request->input('dept_id');
            
            if (!$dept_id) {
                return response()->json([
                    'status' => 400,
                    'data' => [],
                    'message' => 'Department ID is required'
                ]);
            }
            
            $employees = Employee::where('dept_id', $dept_id)
                ->where('is_delete', 0)
                ->select('id', 'employee_name', 'rating')
                ->get()
                ->map(function($employee) {
                    // Ensure rating is initialized to 0 if null
                    $employee->rating = $employee->rating ?? 0;
                    return $employee;
                });
            
            return response()->json([
                'status' => 200,
                'data' => $employees
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'data' => [],
                'message' => 'Error loading employees: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Save department rating
     */
    public function saveDepartmentRating(Request $request)
    {
        try {
            $dept_id = $request->input('dept_id');
            $department_head_name = $request->input('department_head_name');
            $employee_ratings = $request->input('employee_ratings'); // Array of {employee_id, rating}
            
            // Calculate average rating
            $total_rating = 0;
            $employee_count = count($employee_ratings);
            
            foreach ($employee_ratings as $emp_rating) {
                $total_rating += $emp_rating['rating'];
            }
            
            $average_rating = $employee_count > 0 ? $total_rating / $employee_count : 0;
            
            // Start transaction
            DB::beginTransaction();
            
            // Update employee ratings
            foreach ($employee_ratings as $emp_rating) {
                DB::table('employee')
                    ->where('id', $emp_rating['employee_id'])
                    ->update([
                        'rating' => $emp_rating['rating'],
                        'mdate' => now(),
                        'mip' => $request->ip(),
                    ]);
            }
            
            // Save department rating
            $ratingData = [
                'dept_id' => $dept_id,
                'department_head_name' => $department_head_name,
                'average_rating' => round($average_rating, 2),
                'rating_date' => now(),
                'cdate' => now(),
                'cip' => $request->ip(),
                'is_delete' => 0,
                'cby' => 'admin',
            ];
            
            DB::table('department_rating')->insert($ratingData);
            
            DB::commit();
            
            return response()->json([
                'status' => 200,
                'data' => [
                    'button' => 'Success',
                    'message' => 'Department rating saved successfully.',
                    'notify' => 'success'
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
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
     * Check if department already has rating
     */
    public function checkDepartmentRating(Request $request)
    {
        $dept_id = $request->input('dept_id');
        
        $existing_rating = DB::table('department_rating')
            ->where('dept_id', $dept_id)
            ->where('is_delete', 0)
            ->exists();
        
        return response()->json([
            'status' => 200,
            'data' => [
                'has_rating' => $existing_rating
            ]
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
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
     * Save department via API
     */
    public function saveDepartment(Request $request)
    {
        try {
            if ($request->has('id')) {
                // Update existing department
                $updateData = [
                    'dept_name' => $request->input('dept_name'),
                    'mdate' => now(),
                    'mip' => $request->ip(),
                ];

                DB::table('department')
                    ->where('id', $request->id)
                    ->update($updateData);

                return response()->json([
                    'status' => 200,
                    'data' => [
                        'button' => 'Success',
                        'message' => 'Department updated successfully.',
                        'notify' => 'success'
                    ]
                ]);
            } else {
                // Create new department
                $dept_name = $request->input('dept_name');

                if (DB::table('department')->where('dept_name', $dept_name)->exists()) {
                    return response()->json([
                        'status' => 400,
                        'data' => [
                            'button' => 'Error',
                            'message' => 'Department already exists',
                            'notify' => 'error'
                        ]
                    ]);
                }

                $insertData = [
                    'dept_name' => $dept_name,
                    'cdate' => now(),
                    'cip' => $request->ip(),
                    'is_delete' => 0,
                    'cby' => 'admin',
                ];

                DB::table('department')->insertOrIgnore($insertData);

                return response()->json([
                    'status' => 200,
                    'data' => [
                        'button' => 'Success',
                        'message' => 'Department added successfully.',
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
     * Display the specified department.
     */


    /**
     * Update the specified department in storage.
     */



    /**
     * Remove the specified department from storage.
     */
 

    /**
     * Get all departments via API
     */
    public function getDepartments(Request $request)
    {
        $departments = Department::where('is_delete', 0)->select('id', 'dept_name')->orderBy('id', 'desc')->get();
        
        return response()->json([
            'status' => 200,
            'data' => $departments
        ]);
    }

    /**
     * Get single department via API
     */
    public function getSingleDepartment(Request $request)
    {
        $department = Department::findOrFail($request->id);
        
        return response()->json([
            'status' => 200,
            'data' => $department
        ]);
    }

    /**
     * Delete department via API
     */
    public function deleteDepartment(Request $request)
    {
        try {
            $updateData = [
                'is_delete' => 1,
                'mip' => $request->ip(),
                'mdate' => now(),
            ];

            DB::table('department')
                ->where('id', $request->id)
                ->update($updateData);

            return response()->json([
                'status' => 200,
                'data' => [
                    'button' => 'Success',
                    'message' => 'Department deleted successfully.',
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

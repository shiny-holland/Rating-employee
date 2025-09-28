<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RatingController;
use Illuminate\Support\Facades\Route;

// Routes that serve the Vue component through controller
Route::get('/', [DepartmentController::class, 'index']);
Route::get('/departments', [DepartmentController::class, 'index']);
Route::get('/departments/manage', [DepartmentController::class, 'index']);
Route::get('/employees', [EmployeeController::class, 'index']);
Route::get('/employees/manage', [EmployeeController::class, 'index']);
Route::get('/rating', [RatingController::class, 'index']);
Route::get('/rating/manage', [RatingController::class, 'index']);

// Department API routes (no authentication)
Route::post('/getDepartments', [DepartmentController::class, 'getDepartments']);
Route::post('/getSingleDepartment', [DepartmentController::class, 'getSingleDepartment']);
Route::post('/saveDepartment', [DepartmentController::class, 'saveDepartment']);
Route::post('/deleteDepartment', [DepartmentController::class, 'deleteDepartment']);

// Employee API routes (no authentication)
Route::post('/getEmployees', [EmployeeController::class, 'getEmployees']);
Route::post('/getSingleEmployee', [EmployeeController::class, 'getSingleEmployee']);
Route::post('/saveEmployee', [EmployeeController::class, 'saveEmployee']);
Route::post('/deleteEmployee', [EmployeeController::class, 'deleteEmployee']);

// Rating API routes (no authentication)
Route::post('/getEmployeesByDepartment', [RatingController::class, 'getEmployeesByDepartment']);
Route::post('/saveDepartmentRating', [RatingController::class, 'saveDepartmentRating']);
Route::post('/checkDepartmentRating', [RatingController::class, 'checkDepartmentRating']);


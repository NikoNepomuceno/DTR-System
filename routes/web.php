<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DTRController;
use App\Http\Controllers\AuthController;

// Public routes
Route::get('/', function () {
    return view('employee.login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showAdminLogin']);
Route::post('/login', [AuthController::class, 'adminLogin']);
Route::get('/employee/login', [AuthController::class, 'showEmployeeLogin']);
Route::post('/employee/login', [AuthController::class, 'employeeLogin']);
Route::get('/logout', [AuthController::class, 'logout']);
Route::get('/employee/logout', [AuthController::class, 'logout']);

// Employee registration routes
Route::get('/employee/register', function () {
    return view('employee.register');
});
Route::post('/employee/register', [AuthController::class, 'employeeRegister']);



// Employee dashboard routes - Protected by employee middleware
Route::middleware(['employee.auth'])->group(function () {
    Route::get('/employee/dashboard', [DTRController::class, 'employeeDashboard']);
    Route::get('/employee/history', [DTRController::class, 'employeeHistory']);
    Route::get('/employee/qr-code', [DTRController::class, 'employeeQRCode']);
});

// DTR System routes (admin) - Protected by admin middleware
Route::middleware(['admin.auth'])->group(function () {
    Route::get('/dtr', [DTRController::class, 'index']);
    Route::get('/dtr/scan', [DTRController::class, 'scan']);
    Route::post('/dtr/scan', [DTRController::class, 'processScan']);
    Route::get('/dtr/export-pdf', [DTRController::class, 'exportPDF'])->name('dtr.export-pdf');
    Route::get('/dtr/employee', [DTRController::class, 'employees']);
});

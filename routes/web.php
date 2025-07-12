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

// Debug routes for session testing (remove in production)
Route::get('/debug/session', function () {
    return response()->json([
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'employee_user_id' => session('employee_user_id'),
        'admin_user_id' => session('admin_user_id'),
        'csrf_token' => csrf_token(),
        'app_env' => app()->environment(),
        'is_secure' => request()->isSecure(),
    ]);
});

Route::get('/debug/test-redirect', function () {
    session(['test_data' => 'test_value_' . time()]);
    return redirect('/debug/session');
});

Route::get('/debug/test-employee-session', function () {
    // Simulate employee login for testing
    session(['employee_user_id' => 1]);
    session(['employee_user' => ['id' => 1, 'name' => 'Test Employee']]);
    return response()->json([
        'message' => 'Employee session set',
        'session_data' => session()->all(),
        'redirect_url' => '/employee/dashboard'
    ]);
});

Route::get('/debug/cloud-status', function () {
    return response()->json([
        'environment' => app()->environment(),
        'session_driver' => config('session.driver'),
        'session_lifetime' => config('session.lifetime'),
        'is_secure' => request()->isSecure(),
        'app_url' => config('app.url'),
        'session_domain' => config('session.domain'),
        'session_path' => config('session.path'),
        'session_secure' => config('session.secure'),
        'session_same_site' => config('session.same_site'),
        'csrf_token' => csrf_token(),
        'server_info' => [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        ],
        'database_connection' => [
            'driver' => config('database.default'),
            'connection_test' => 'Testing...'
        ]
    ]);
});

Route::post('/debug/test-login', function (Request $request) {
    // Emergency test login route
    $email = $request->input('email', 'admin@test.com');
    $userType = $request->input('type', 'admin');

    // Force session regeneration
    $request->session()->regenerate();

    if ($userType === 'admin') {
        session(['admin_user_id' => 999]);
        session(['admin_user' => ['id' => 999, 'name' => 'Test Admin', 'email' => $email]]);
        $redirect = '/dtr';
    } else {
        session(['employee_user_id' => 999]);
        session(['employee_user' => ['id' => 999, 'name' => 'Test Employee', 'email' => $email]]);
        $redirect = '/employee/dashboard';
    }

    // Force session save
    session()->save();

    return response()->json([
        'success' => true,
        'message' => 'Test login successful!',
        'redirect' => $redirect,
        'session_id' => session()->getId(),
        'session_data' => session()->all()
    ]);
});



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

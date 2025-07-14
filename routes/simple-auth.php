<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SimpleAuthController;
use App\Http\Controllers\DTRController;

/*
|--------------------------------------------------------------------------
| Simple Authentication Routes
|--------------------------------------------------------------------------
|
| Clean, simple authentication system with direct redirects
|
*/

// Root redirect
Route::get('/', function () {
    return redirect('/employee/login');
});

// Admin Authentication
Route::get('/admin/login', [SimpleAuthController::class, 'showAdminLogin']);
Route::post('/admin/login', [SimpleAuthController::class, 'adminLogin'])->middleware('login.rate.limit:5,15');

// Employee Authentication
Route::get('/employee/login', [SimpleAuthController::class, 'showEmployeeLogin']);
Route::post('/employee/login', [SimpleAuthController::class, 'employeeLogin'])->middleware('login.rate.limit:5,15');

// Employee Registration
Route::get('/employee/register', function () {
    return view('employee.register');
});
Route::post('/employee/register', [SimpleAuthController::class, 'employeeRegister'])->middleware('login.rate.limit:3,30');

// Logout (works for both admin and employee)
Route::get('/logout', [SimpleAuthController::class, 'logout']);
Route::get('/admin/logout', [SimpleAuthController::class, 'logout']);
Route::get('/employee/logout', [SimpleAuthController::class, 'logout']);

// Protected Admin Routes
Route::middleware(['simple.auth:admin', 'secure.session'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('dtr.index');
    });

    // All existing DTR admin routes
    Route::get('/dtr', [DTRController::class, 'index']);
    Route::get('/dtr/scan', [DTRController::class, 'scan']);
    Route::post('/dtr/scan', [DTRController::class, 'processScan']);
    Route::get('/dtr/export-pdf', [DTRController::class, 'exportPDF'])->name('dtr.export-pdf');
    Route::get('/dtr/employee', [DTRController::class, 'employees']);
});

// Protected Employee Routes
Route::middleware(['simple.auth:employee', 'secure.session'])->group(function () {
    Route::get('/employee/dashboard', [DTRController::class, 'employeeDashboard']);
    Route::get('/employee/history', [DTRController::class, 'employeeHistory']);
    Route::get('/employee/qr-code', [DTRController::class, 'employeeQRCode']);
});

// Debug routes (remove in production)
Route::get('/debug/simple-session', function () {
    return response()->json([
        'user_id' => session('user_id'),
        'user_role' => session('user_role'),
        'user_name' => session('user_name'),
        'employee_user_id' => session('employee_user_id'),
        'session_id' => session()->getId(),
        'session_started' => session()->isStarted(),
        'all_session' => session()->all(),
    ]);
});

// Test employee session after login
Route::get('/debug/test-employee-auth', function () {
    $hasUserId = session('user_id') ? 'YES' : 'NO';
    $hasUserRole = session('user_role') ? 'YES' : 'NO';
    $userRole = session('user_role');
    $roleMatch = (session('user_role') === 'employee') ? 'YES' : 'NO';

    return response()->json([
        'middleware_check_user_id' => $hasUserId,
        'middleware_check_user_role' => $hasUserRole,
        'user_role_value' => $userRole,
        'role_matches_employee' => $roleMatch,
        'session_data' => [
            'user_id' => session('user_id'),
            'user_role' => session('user_role'),
            'user_name' => session('user_name'),
            'employee_user_id' => session('employee_user_id'),
        ],
        'middleware_would_pass' => ($hasUserId === 'YES' && $roleMatch === 'YES') ? 'YES' : 'NO'
    ]);
});

Route::get('/debug/create-admin', function () {
    try {
        $user = \App\Models\User::where('email', 'admin@test.com')->first();
        
        if (!$user) {
            $user = \App\Models\User::create([
                'name' => 'Test Admin',
                'email' => 'admin@test.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
                'employee_id' => 'ADMIN001',
                'department' => 'IT',
                'position' => 'Administrator',
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Admin user ready',
            'credentials' => [
                'email' => 'admin@test.com',
                'password' => 'password'
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

Route::get('/debug/create-employee', function () {
    try {
        $user = \App\Models\User::where('email', 'employee@test.com')->first();
        
        if (!$user) {
            $user = \App\Models\User::create([
                'name' => 'Test Employee',
                'email' => 'employee@test.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'employee',
                'employee_id' => 'EMP001',
                'department' => 'HR',
                'position' => 'Staff',
            ]);
            
            $user->generateQRCode();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Employee user ready',
            'credentials' => [
                'email' => 'employee@test.com',
                'password' => 'password'
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

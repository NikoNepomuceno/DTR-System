<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use App\Http\Middleware\LoginRateLimit;
use App\Http\Requests\EmployeeLoginRequest;
use App\Http\Requests\EmployeeRegistrationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SimpleAuthController extends Controller
{
    /**
     * Show admin login form.
     */
    public function showAdminLogin()
    {
        return view('auth.simple-login');
    }

    /**
     * Show employee login form.
     */
    public function showEmployeeLogin()
    {
        return view('employee.simple-login');
    }

    /**
     * Handle admin login - direct and simple.
     */
    public function adminLogin(Request $request)
    {
        // Simple validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        Log::info('Simple admin login attempt', ['email' => $request->email]);

        // Find user
        $user = User::where('email', $request->email)->first();

        // Check credentials and role
        if (!$user || !Hash::check($request->password, $user->password) || $user->role !== 'admin') {
            Log::warning('Admin login failed', ['email' => $request->email]);
            
            return back()->withErrors([
                'email' => 'Invalid admin credentials.'
            ])->withInput();
        }

        // Simple session setup
        session()->regenerate();
        session(['user_id' => $user->id, 'user_role' => 'admin', 'user_name' => $user->name]);

        Log::info('Admin login successful', ['user_id' => $user->id]);

        // Direct redirect to admin dashboard
        return redirect('/admin/dashboard');
    }

    /**
     * Handle employee login - direct and simple.
     */
    public function employeeLogin(EmployeeLoginRequest $request)
    {
        $validated = $request->validated();

        Log::info('Simple employee login attempt', [
            'email' => $validated['email'],
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString()
        ]);

        // Find user
        $user = User::where('email', $validated['email'])->first();

        // Check credentials and role
        if (!$user || !Hash::check($validated['password'], $user->password) || $user->role !== 'employee') {
            // Determine failure reason for audit log
            $failureReason = 'invalid_credentials';
            if ($user && $user->role !== 'employee') {
                $failureReason = 'role_mismatch';
            } elseif (!$user) {
                $failureReason = 'user_not_found';
            }

            // Log failed login attempt
            AuditLog::logFailedLogin($validated['email'], 'employee', $failureReason, [
                'user_exists' => $user ? true : false,
                'actual_role' => $user?->role,
            ]);

            Log::warning('Employee login failed', [
                'email' => $validated['email'],
                'ip' => request()->ip(),
                'user_exists' => $user ? true : false,
                'role_mismatch' => $user && $user->role !== 'employee',
                'failure_reason' => $failureReason
            ]);

            return back()->withErrors([
                'email' => 'Invalid employee credentials.'
            ])->withInput($request->safe()->except('password'));
        }

        // Clear rate limiting on successful login
        LoginRateLimit::clearAttempts($request);

        // Simple session setup - set both session variables for compatibility
        session()->regenerate();
        session([
            'user_id' => $user->id,
            'user_role' => 'employee',
            'user_name' => $user->name,
            'employee_user_id' => $user->id,  // For DTRController compatibility
            'employee_user' => $user           // For EmployeeAuth middleware compatibility
        ]);

        // Force session save for cloud environments
        session()->save();

        // Log successful login
        AuditLog::logSuccessfulLogin($user, 'employee', [
            'session_variables_set' => ['user_id', 'user_role', 'user_name', 'employee_user_id', 'employee_user'],
            'remember_me' => $validated['remember'] ?? false,
        ]);

        Log::info('Employee login successful', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => request()->ip(),
            'session_id' => session()->getId(),
            'session_variables_set' => ['user_id', 'user_role', 'user_name', 'employee_user_id', 'employee_user'],
            'timestamp' => now()->toISOString()
        ]);

        // Direct redirect to employee dashboard
        return redirect('/employee/dashboard');
    }

    /**
     * Handle employee registration.
     */
    public function employeeRegister(EmployeeRegistrationRequest $request)
    {
        $validated = $request->validated();

        try {
            // Generate unique employee ID
            $employeeId = $this->generateEmployeeId();

            // Create user
            $user = User::create([
                'name' => "{$validated['first_name']} {$validated['last_name']}",
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'employee_id' => $employeeId,
                'department' => $validated['department'],
                'position' => $validated['position'],
                'role' => 'employee',
            ]);

            // Generate QR code
            $user->generateQRCode();

            // Auto login - set both session variables for compatibility
            session()->regenerate();
            session([
                'user_id' => $user->id,           // For SimpleAuth middleware
                'user_role' => 'employee',
                'user_name' => $user->name,
                'employee_user_id' => $user->id,  // For DTRController compatibility
                'employee_user' => $user           // For EmployeeAuth middleware compatibility
            ]);

            // Force session save for cloud environments
            session()->save();

            // Log successful registration and auto-login
            AuditLog::logAuth(
                'registration',
                'success',
                'employee',
                $user->id,
                $user->email,
                "New employee registered and auto-logged in: {$user->email}",
                [
                    'employee_id' => $employeeId,
                    'department' => $user->department,
                    'position' => $user->position,
                    'auto_login' => true,
                    'session_variables_set' => ['user_id', 'user_role', 'user_name', 'employee_user_id', 'employee_user'],
                ],
                'low'
            );

            Log::info('Employee registered and logged in', [
                'user_id' => $user->id,
                'email' => $user->email,
                'employee_id' => $employeeId,
                'ip' => request()->ip(),
                'session_id' => session()->getId(),
                'session_variables_set' => ['user_id', 'user_role', 'user_name', 'employee_user_id', 'employee_user'],
                'timestamp' => now()->toISOString()
            ]);

            return redirect('/employee/dashboard')->with('success', 'Welcome to DTR System! Your account has been created successfully and you are now logged in.');

        } catch (\Exception $e) {
            // Log failed registration
            AuditLog::logAuth(
                'registration',
                'failure',
                'employee',
                null,
                $validated['email'] ?? null,
                "Employee registration failed: {$e->getMessage()}",
                [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ],
                'medium'
            );

            Log::error('Employee registration failed', [
                'email' => $validated['email'] ?? 'unknown',
                'error' => $e->getMessage(),
                'ip' => request()->ip(),
                'timestamp' => now()->toISOString()
            ]);
            return back()->withErrors(['email' => 'Registration failed. Please try again.'])->withInput($request->safe()->except('password', 'password_confirmation'));
        }
    }

    /**
     * Enhanced logout with proper session security sequence.
     */
    public function logout(Request $request)
    {
        // Get user info for logging before clearing session
        $userRole = session('user_role');
        $userId = session('user_id');
        $sessionId = session()->getId();
        $userEmail = null;

        // Try to get user email for audit log
        if ($userId) {
            $user = User::find($userId);
            $userEmail = $user?->email;
        }

        Log::info('User logout initiated', [
            'user_id' => $userId,
            'role' => $userRole,
            'session_id' => $sessionId,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Log logout event before clearing session
        if ($userId) {
            AuditLog::logAuth(
                'logout',
                'success',
                $userRole ?: 'unknown',
                $userId,
                $userEmail,
                "User {$userEmail} logged out",
                [
                    'logout_method' => 'manual',
                    'session_duration' => session('_session_started') ? (time() - session('_session_started')) : null,
                ],
                'low'
            );
        }

        // Step 1: Clear remember me token if user is authenticated via Laravel Auth
        if (Auth::check()) {
            $user = Auth::user();
            $user->setRememberToken(null);
            $user->save();

            Log::info('Remember me token cleared', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }

        // Step 2: Logout from Laravel's auth system (clears remember me cookies)
        Auth::logout();

        // Step 3: Invalidate the entire session (security best practice)
        $request->session()->invalidate();

        // Step 4: Regenerate CSRF token to prevent CSRF attacks
        $request->session()->regenerateToken();

        Log::info('Logout completed successfully', [
            'previous_session_id' => $sessionId,
            'new_session_id' => session()->getId(),
            'user_role' => $userRole
        ]);

        // Redirect based on role
        if ($userRole === 'admin') {
            return redirect('/admin/login')->with('success', 'You have been logged out successfully. Have a great day!');
        } else {
            return redirect('/employee/login')->with('success', 'You have been logged out successfully. See you next time!');
        }
    }

    /**
     * Generate unique employee ID.
     */
    private function generateEmployeeId()
    {
        do {
            $employeeId = 'EMP' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        } while (User::where('employee_id', $employeeId)->exists());

        return $employeeId;
    }
}

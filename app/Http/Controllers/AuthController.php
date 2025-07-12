<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show admin login form.
     */
    public function showAdminLogin()
    {
        return view('auth.login');
    }

    /**
     * Show employee login form.
     */
    public function showEmployeeLogin()
    {
        return view('employee.login');
    }

    /**
     * Handle admin login.
     */
    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'boolean',
        ]);

        Log::info('Admin login attempt', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            Log::warning('Admin login failed - invalid credentials', [
                'email' => $request->email,
                'ip' => $request->ip()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.'
            ], 401);
        }

        if ($user->role !== 'admin') {
            Log::warning('Admin login failed - not admin role', [
                'email' => $request->email,
                'role' => $user->role
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        // Regenerate session ID for security
        $request->session()->regenerate();

        // Store user information in session
        session(['admin_user_id' => $user->id]);
        session(['admin_user' => $user]);

        // Handle remember me functionality
        if ($request->remember) {
            $user->setRememberToken(Str::random(60));
            $user->save();

            // Set remember me cookie (Laravel handles this automatically)
            Auth::login($user, true);

            Log::info('Admin remember me token set', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }

        // Force session save
        session()->save();

        Log::info('Admin login successful', [
            'user_id' => $user->id,
            'email' => $user->email,
            'session_id' => session()->getId(),
            'remember' => $request->remember ?? false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'redirect' => '/dtr'
        ]);
    }

    /**
     * Handle employee login.
     */
    public function employeeLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'boolean',
        ]);

        Log::info('Employee login attempt', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            Log::warning('Employee login failed - invalid credentials', [
                'email' => $request->email,
                'ip' => $request->ip()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.'
            ], 401);
        }

        if ($user->role !== 'employee') {
            Log::warning('Employee login failed - not employee role', [
                'email' => $request->email,
                'role' => $user->role
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Employee account required.'
            ], 403);
        }

        // Regenerate session ID for security
        $request->session()->regenerate();

        // Store user information in session
        session(['employee_user_id' => $user->id]);
        session(['employee_user' => $user]);

        // Handle remember me functionality
        if ($request->remember) {
            $user->setRememberToken(Str::random(60));
            $user->save();

            // Set remember me cookie (Laravel handles this automatically)
            Auth::login($user, true);

            Log::info('Employee remember me token set', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }

        // Force session save
        session()->save();

        Log::info('Employee login successful', [
            'user_id' => $user->id,
            'email' => $user->email,
            'session_id' => session()->getId(),
            'remember' => $request->remember ?? false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'redirect' => '/employee/dashboard'
        ]);
    }

    /**
     * Handle employee registration.
     */
    public function employeeRegister(Request $request)
    {
        Log::info('Employee registration attempt', $request->all());

        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'department' => 'required|string|max:255',
                'position' => 'required|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Registration validation failed', $e->errors());
            $errors = collect($e->errors())->flatten()->toArray();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(' ', $errors),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Registration error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during registration: ' . $e->getMessage()
            ], 500);
        }

        try {
            // Generate unique employee ID
            $employeeId = $this->generateEmployeeId();
            Log::info('Generated employee ID', ['employee_id' => $employeeId]);

            // Create the user
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'employee_id' => $employeeId,
                'department' => $request->department,
                'position' => $request->position,
                'role' => 'employee',
            ]);
            Log::info('User created successfully', ['user_id' => $user->id]);

            // Generate QR code for the user
            $user->generateQRCode();
            Log::info('QR code generated', ['qr_code' => $user->qr_code]);

            // Automatically sign in the user with remember me
            session(['employee_user_id' => $user->id]);
            session(['employee_user' => $user]);

            // Set remember me token for seamless experience
            $user->setRememberToken(Str::random(60));
            $user->save();

            // Login with remember me
            Auth::login($user, true);

            Log::info('User session created with remember me token', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully! Welcome to the DTR system.',
                'redirect' => '/employee/dashboard'
            ]);
        } catch (\Exception $e) {
            Log::error('User creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user account: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique employee ID.
     */
    private function generateEmployeeId()
    {
        do {
            // Generate employee ID in format: EMP + random 4 digits
            $employeeId = 'EMP' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        } while (User::where('employee_id', $employeeId)->exists());

        return $employeeId;
    }

    /**
     * Handle logout with enhanced security.
     */
    public function logout(Request $request)
    {
        // Get user info for logging before clearing session
        $adminUserId = session('admin_user_id');
        $employeeUserId = session('employee_user_id');
        $sessionId = session()->getId();

        Log::info('Logout initiated', [
            'admin_user_id' => $adminUserId,
            'employee_user_id' => $employeeUserId,
            'session_id' => $sessionId,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Clear remember me token if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            $user->setRememberToken(null);
            $user->save();

            Log::info('Remember me token cleared', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }

        // Logout from Laravel's auth system (clears remember me cookies)
        Auth::logout();

        // Invalidate the entire session (more secure than just forgetting keys)
        $request->session()->invalidate();

        // Regenerate CSRF token to prevent CSRF attacks
        $request->session()->regenerateToken();

        Log::info('Logout completed', [
            'previous_session_id' => $sessionId,
            'new_session_id' => session()->getId()
        ]);

        // Determine redirect based on user type
        if ($adminUserId) {
            return redirect('/login')->with('success', 'You have been logged out successfully.');
        } else {
            return redirect('/employee/login')->with('success', 'You have been logged out successfully.');
        }
    }
}

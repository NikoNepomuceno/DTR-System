<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
    public function employeeLogin(Request $request)
    {
        // Simple validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        Log::info('Simple employee login attempt', ['email' => $request->email]);

        // Find user
        $user = User::where('email', $request->email)->first();

        // Check credentials and role
        if (!$user || !Hash::check($request->password, $user->password) || $user->role !== 'employee') {
            Log::warning('Employee login failed', ['email' => $request->email]);
            
            return back()->withErrors([
                'email' => 'Invalid employee credentials.'
            ])->withInput();
        }

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

        Log::info('Employee login successful', [
            'user_id' => $user->id,
            'session_id' => session()->getId(),
            'session_variables_set' => ['user_id', 'user_role', 'user_name', 'employee_user_id', 'employee_user']
        ]);

        // Direct redirect to employee dashboard
        return redirect('/employee/dashboard');
    }

    /**
     * Handle employee registration.
     */
    public function employeeRegister(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
        ]);

        try {
            // Generate unique employee ID
            $employeeId = $this->generateEmployeeId();

            // Create user
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'employee_id' => $employeeId,
                'department' => $request->department,
                'position' => $request->position,
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

            Log::info('Employee registered and logged in', [
                'user_id' => $user->id,
                'session_id' => session()->getId(),
                'session_variables_set' => ['user_id', 'user_role', 'user_name', 'employee_user_id', 'employee_user']
            ]);

            return redirect('/employee/dashboard')->with('success', 'Welcome to DTR System! Your account has been created successfully and you are now logged in.');

        } catch (\Exception $e) {
            Log::error('Employee registration failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['email' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    /**
     * Simple logout.
     */
    public function logout(Request $request)
    {
        $userRole = session('user_role');
        
        Log::info('User logout', ['user_id' => session('user_id'), 'role' => $userRole]);

        // Clear session
        session()->flush();
        session()->regenerate();

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

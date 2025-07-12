<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.'
            ], 401);
        }

        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        // For now, we'll use a simple session approach
        session(['admin_user_id' => $user->id]);
        session(['admin_user' => $user]);

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
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.'
            ], 401);
        }

        if ($user->role !== 'employee') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Employee account required.'
            ], 403);
        }

        // For now, we'll use a simple session approach
        session(['employee_user_id' => $user->id]);
        session(['employee_user' => $user]);

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

            // Automatically sign in the user
            session(['employee_user_id' => $user->id]);
            session(['employee_user' => $user]);
            Log::info('User session created');

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
     * Handle logout.
     */
    public function logout(Request $request)
    {
        session()->forget(['admin_user_id', 'admin_user', 'employee_user_id', 'employee_user']);
        return redirect('/');
    }
}

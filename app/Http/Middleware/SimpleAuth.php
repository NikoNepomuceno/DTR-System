<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SimpleAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role = null): Response
    {
        // Enhanced logging for debugging
        Log::info('SimpleAuth middleware check', [
            'url' => $request->url(),
            'method' => $request->method(),
            'required_role' => $role,
            'session_id' => session()->getId(),
            'session_started' => session()->isStarted(),
            'user_id' => session('user_id'),
            'user_role' => session('user_role'),
            'user_name' => session('user_name'),
            'employee_user_id' => session('employee_user_id'),
            'all_session_keys' => array_keys(session()->all())
        ]);

        // Check if user is logged in
        if (!session('user_id')) {
            Log::warning('SimpleAuth failed - no user_id in session', [
                'url' => $request->url(),
                'required_role' => $role,
                'session_id' => session()->getId(),
                'session_data' => session()->all()
            ]);

            if ($role === 'admin') {
                return redirect('/admin/login')->with('error', 'Please log in to access admin area.');
            } else {
                return redirect('/employee/login')->with('error', 'Please log in to access your dashboard.');
            }
        }

        // Check role if specified
        if ($role && session('user_role') !== $role) {
            Log::warning('SimpleAuth failed - role mismatch', [
                'url' => $request->url(),
                'required_role' => $role,
                'actual_role' => session('user_role'),
                'user_id' => session('user_id')
            ]);

            if ($role === 'admin') {
                return redirect('/admin/login')->with('error', 'Admin access required.');
            } else {
                return redirect('/employee/login')->with('error', 'Employee access required.');
            }
        }

        Log::info('SimpleAuth passed', [
            'url' => $request->url(),
            'user_id' => session('user_id'),
            'user_role' => session('user_role'),
            'required_role' => $role
        ]);

        return $next($request);
    }
}

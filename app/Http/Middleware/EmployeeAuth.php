<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EmployeeAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Debug session information
        Log::info('EmployeeAuth middleware check', [
            'session_id' => session()->getId(),
            'employee_user_id' => session('employee_user_id'),
            'has_session' => session()->has('employee_user_id'),
            'url' => $request->url(),
            'method' => $request->method()
        ]);

        // Check if employee is logged in
        if (!session('employee_user_id')) {
            Log::warning('Employee authentication failed - no session', [
                'url' => $request->url(),
                'session_id' => session()->getId()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. Please log in as employee.'
                ], 401);
            }

            return redirect('/employee/login')->with('error', 'Please log in to access your dashboard.');
        }

        Log::info('Employee authentication successful', [
            'employee_user_id' => session('employee_user_id'),
            'url' => $request->url()
        ]);

        return $next($request);
    }
}

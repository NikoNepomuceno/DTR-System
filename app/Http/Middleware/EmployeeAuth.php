<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'auth_check' => Auth::check(),
            'auth_user_id' => Auth::id(),
            'url' => $request->url(),
            'method' => $request->method()
        ]);

        $isAuthenticated = false;
        $user = null;

        // First check Laravel's auth system (handles remember me tokens)
        if (Auth::check()) {
            $user = Auth::user();
            if ($user && $user->role === 'employee') {
                $isAuthenticated = true;

                // Sync with session if not already set
                if (!session('employee_user_id')) {
                    session(['employee_user_id' => $user->id]);
                    session(['employee_user' => $user]);
                    session()->save();

                    Log::info('Employee session synced from remember token', [
                        'user_id' => $user->id,
                        'email' => $user->email
                    ]);
                }
            }
        }

        // Fallback to session-based authentication
        if (!$isAuthenticated && session('employee_user_id')) {
            $isAuthenticated = true;
        }

        if (!$isAuthenticated) {
            Log::warning('Employee authentication failed - no session or remember token', [
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
            'auth_method' => Auth::check() ? 'remember_token' : 'session',
            'url' => $request->url()
        ]);

        return $next($request);
    }
}

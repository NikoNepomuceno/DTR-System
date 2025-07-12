<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Debug session information
        Log::info('AdminAuth middleware check', [
            'session_id' => session()->getId(),
            'admin_user_id' => session('admin_user_id'),
            'has_session' => session()->has('admin_user_id'),
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
            if ($user && $user->role === 'admin') {
                $isAuthenticated = true;

                // Sync with session if not already set
                if (!session('admin_user_id')) {
                    session(['admin_user_id' => $user->id]);
                    session(['admin_user' => $user]);
                    session()->save();

                    Log::info('Admin session synced from remember token', [
                        'user_id' => $user->id,
                        'email' => $user->email
                    ]);
                }
            }
        }

        // Fallback to session-based authentication
        if (!$isAuthenticated && session('admin_user_id')) {
            $isAuthenticated = true;
        }

        if (!$isAuthenticated) {
            Log::warning('Admin authentication failed - no session or remember token', [
                'url' => $request->url(),
                'session_id' => session()->getId()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. Please log in as admin.'
                ], 401);
            }

            return redirect('/login')->with('error', 'Please log in to access the admin dashboard.');
        }

        Log::info('Admin authentication successful', [
            'admin_user_id' => session('admin_user_id'),
            'auth_method' => Auth::check() ? 'remember_token' : 'session',
            'url' => $request->url()
        ]);

        return $next($request);
    }
}

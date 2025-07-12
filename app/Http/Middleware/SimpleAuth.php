<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SimpleAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role = null): Response
    {
        // Check if user is logged in
        if (!session('user_id')) {
            if ($role === 'admin') {
                return redirect('/admin/login')->with('error', 'Please log in to access admin area.');
            } else {
                return redirect('/employee/login')->with('error', 'Please log in to access your dashboard.');
            }
        }

        // Check role if specified
        if ($role && session('user_role') !== $role) {
            if ($role === 'admin') {
                return redirect('/admin/login')->with('error', 'Admin access required.');
            } else {
                return redirect('/employee/login')->with('error', 'Employee access required.');
            }
        }

        return $next($request);
    }
}

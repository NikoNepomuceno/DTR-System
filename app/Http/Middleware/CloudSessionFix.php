<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CloudSessionFix
{
    /**
     * Handle an incoming request.
     *
     * This middleware helps fix session issues in cloud environments
     * by ensuring proper session configuration and handling.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Force session start if not already started
        if (!session()->isStarted()) {
            session()->start();
        }

        // Set secure cookie settings for cloud environments
        if (app()->environment('production') || $request->isSecure()) {
            config([
                'session.secure' => true,
                'session.same_site' => 'lax',
                'session.http_only' => true,
            ]);
        }

        // Debug session information in non-production environments
        if (config('app.debug')) {
            Log::info('CloudSessionFix middleware', [
                'session_id' => session()->getId(),
                'session_started' => session()->isStarted(),
                'is_secure' => $request->isSecure(),
                'url' => $request->url(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
            ]);
        }

        $response = $next($request);

        // Ensure session is saved after request processing
        if (session()->isStarted()) {
            session()->save();
        }

        return $response;
    }
}

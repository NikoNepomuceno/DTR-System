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
                'session.domain' => null, // Let Laravel handle domain automatically
            ]);
        }

        // Force session configuration for cloud environments
        if (app()->environment('production')) {
            config([
                'session.lifetime' => 120,
                'session.expire_on_close' => false,
                'session.encrypt' => false,
                'session.path' => '/',
            ]);
        }

        // Debug session information
        Log::info('CloudSessionFix middleware', [
            'session_id' => session()->getId(),
            'session_started' => session()->isStarted(),
            'is_secure' => $request->isSecure(),
            'url' => $request->url(),
            'method' => $request->method(),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
            'environment' => app()->environment(),
            'session_driver' => config('session.driver'),
        ]);

        $response = $next($request);

        // Force session save for cloud environments
        if (session()->isStarted()) {
            session()->save();

            Log::info('Session saved after request', [
                'session_id' => session()->getId(),
                'url' => $request->url(),
                'method' => $request->method(),
            ]);
        }

        return $response;
    }
}

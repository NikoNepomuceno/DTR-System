<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecureSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure session is started
        if (!session()->isStarted()) {
            session()->start();
        }

        // Check for session hijacking
        if ($this->detectSessionHijacking($request)) {
            $this->handleSuspiciousActivity($request);
            return $this->invalidateAndRedirect($request);
        }

        // Check session timeout
        if ($this->isSessionExpired($request)) {
            $this->logSessionTimeout($request);
            return $this->invalidateAndRedirect($request, 'Your session has expired. Please log in again.');
        }

        // Update session activity
        $this->updateSessionActivity($request);

        // Set secure headers
        $response = $next($request);
        $this->setSecurityHeaders($response);

        return $response;
    }

    /**
     * Detect potential session hijacking.
     */
    protected function detectSessionHijacking(Request $request): bool
    {
        // Check if user agent changed
        $sessionUserAgent = session('_user_agent');
        $currentUserAgent = $request->userAgent();

        if ($sessionUserAgent && $sessionUserAgent !== $currentUserAgent) {
            Log::warning('Potential session hijacking detected - User agent mismatch', [
                'session_id' => session()->getId(),
                'session_user_agent' => $sessionUserAgent,
                'current_user_agent' => $currentUserAgent,
                'ip' => $request->ip(),
                'user_id' => session('user_id')
            ]);
            return true;
        }

        // Check if IP changed (optional - can be disabled for mobile users)
        if (config('session.strict_ip_check', false)) {
            $sessionIP = session('_ip_address');
            $currentIP = $request->ip();

            if ($sessionIP && $sessionIP !== $currentIP) {
                Log::warning('Potential session hijacking detected - IP address mismatch', [
                    'session_id' => session()->getId(),
                    'session_ip' => $sessionIP,
                    'current_ip' => $currentIP,
                    'user_id' => session('user_id')
                ]);
                return true;
            }
        }

        return false;
    }

    /**
     * Check if session has expired.
     */
    protected function isSessionExpired(Request $request): bool
    {
        $lastActivity = session('_last_activity');
        $maxLifetime = config('session.lifetime') * 60; // Convert minutes to seconds

        if (!$lastActivity) {
            return false;
        }

        return (time() - $lastActivity) > $maxLifetime;
    }

    /**
     * Update session activity tracking.
     */
    protected function updateSessionActivity(Request $request): void
    {
        $now = time();
        
        // Set initial session metadata if not exists
        if (!session()->has('_user_agent')) {
            session(['_user_agent' => $request->userAgent()]);
        }
        
        if (!session()->has('_ip_address')) {
            session(['_ip_address' => $request->ip()]);
        }
        
        if (!session()->has('_session_started')) {
            session(['_session_started' => $now]);
        }

        // Update last activity
        session(['_last_activity' => $now]);

        // Regenerate session ID periodically for security
        $sessionStarted = session('_session_started', $now);
        $sessionAge = $now - $sessionStarted;
        $regenerateInterval = config('session.regenerate_interval', 1800); // 30 minutes

        if ($sessionAge > $regenerateInterval && !session('_recently_regenerated')) {
            session()->regenerate(true);
            session(['_recently_regenerated' => true]);
            
            // Clear the flag after a short time
            session()->put('_regenerate_clear_time', $now + 300); // 5 minutes
            
            Log::info('Session ID regenerated for security', [
                'old_session_id' => session()->getId(),
                'user_id' => session('user_id'),
                'session_age' => $sessionAge
            ]);
        }

        // Clear regeneration flag if enough time has passed
        if (session('_regenerate_clear_time') && $now > session('_regenerate_clear_time')) {
            session()->forget(['_recently_regenerated', '_regenerate_clear_time']);
        }
    }

    /**
     * Handle suspicious activity.
     */
    protected function handleSuspiciousActivity(Request $request): void
    {
        Log::critical('Suspicious session activity detected', [
            'session_id' => session()->getId(),
            'user_id' => session('user_id'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->url(),
            'timestamp' => now()->toISOString()
        ]);

        // You could add additional security measures here:
        // - Send email notification to user
        // - Lock account temporarily
        // - Log to security monitoring system
    }

    /**
     * Invalidate session and redirect.
     */
    protected function invalidateAndRedirect(Request $request, string $message = 'Session security violation detected. Please log in again.'): Response
    {
        // Clear all session data
        session()->invalidate();
        session()->regenerateToken();

        // Determine redirect URL based on request path
        $redirectUrl = '/employee/login';
        if (str_contains($request->path(), 'admin')) {
            $redirectUrl = '/admin/login';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'redirect' => $redirectUrl
            ], 401);
        }

        return redirect($redirectUrl)->with('error', $message);
    }

    /**
     * Log session timeout.
     */
    protected function logSessionTimeout(Request $request): void
    {
        Log::info('Session timeout', [
            'session_id' => session()->getId(),
            'user_id' => session('user_id'),
            'last_activity' => session('_last_activity'),
            'current_time' => time(),
            'ip' => $request->ip()
        ]);
    }

    /**
     * Set security headers.
     */
    protected function setSecurityHeaders(Response $response): void
    {
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        if (config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
    }
}

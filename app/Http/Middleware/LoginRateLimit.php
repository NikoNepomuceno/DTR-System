<?php

namespace App\Http\Middleware;

use App\Models\AccountLockout;
use App\Models\AuditLog;
use App\Models\FailedLoginAttempt;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LoginRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $maxAttempts = '5', string $decayMinutes = '15'): Response
    {
        // Only apply rate limiting to login POST requests
        if (!$request->isMethod('POST') || !str_contains($request->path(), 'login')) {
            return $next($request);
        }

        $maxAttempts = (int) $maxAttempts;
        $decayMinutes = (int) $decayMinutes;
        $ip = $request->ip();
        $email = $request->input('email');

        // Check for account lockouts first
        if (AccountLockout::isIPLocked($ip)) {
            $lockoutTime = AccountLockout::getIPLockoutTime($ip);
            $remainingTime = max(1, $lockoutTime->diffInMinutes(now()));

            Log::warning('Login attempt blocked - IP locked', [
                'ip' => $ip,
                'email' => $email,
                'locked_until' => $lockoutTime->toISOString(),
                'remaining_minutes' => $remainingTime
            ]);

            return $this->blockedResponse($remainingTime, $request);
        }

        if ($email && AccountLockout::isEmailLocked($email)) {
            $lockoutTime = AccountLockout::getEmailLockoutTime($email);
            $remainingTime = max(1, $lockoutTime->diffInMinutes(now()));

            Log::warning('Login attempt blocked - Email locked', [
                'ip' => $ip,
                'email' => $email,
                'locked_until' => $lockoutTime->toISOString(),
                'remaining_minutes' => $remainingTime
            ]);

            return $this->blockedResponse($remainingTime, $request);
        }

        // Check cache-based rate limiting as fallback
        $key = $this->getRateLimitKey($request);
        if ($this->isBlocked($key, $maxAttempts)) {
            $remainingTime = $this->getRemainingBlockTime($key);

            Log::warning('Login attempt blocked due to rate limiting', [
                'ip' => $ip,
                'email' => $email,
                'path' => $request->path(),
                'attempts' => Cache::get($key, 0),
                'remaining_minutes' => $remainingTime
            ]);

            return $this->blockedResponse($remainingTime, $request);
        }

        $response = $next($request);

        // If login failed, record the attempt and check for lockout
        if ($this->isFailedLogin($response)) {
            $this->handleFailedLogin($request, $maxAttempts, $decayMinutes);
        }

        return $response;
    }

    /**
     * Generate rate limit key for the request.
     */
    protected function getRateLimitKey(Request $request): string
    {
        return 'login_attempts:' . $request->ip();
    }

    /**
     * Check if the IP is currently blocked.
     */
    protected function isBlocked(string $key, int $maxAttempts): bool
    {
        return Cache::has($key) && Cache::get($key) >= $maxAttempts;
    }

    /**
     * Get remaining block time in minutes.
     */
    protected function getRemainingBlockTime(string $key): int
    {
        $blockedUntil = Cache::get("{$key}:blocked_until", now());
        return max(1, $blockedUntil->diffInMinutes(now()));
    }

    /**
     * Increment failed login attempts.
     */
    protected function incrementFailedAttempts(string $key, int $maxAttempts, int $decayMinutes): void
    {
        $attempts = Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, now()->addMinutes($decayMinutes));

        if ($attempts >= $maxAttempts) {
            Cache::put("{$key}:blocked_until", now()->addMinutes($decayMinutes), now()->addMinutes($decayMinutes));
            
            Log::warning('IP blocked due to excessive login attempts', [
                'ip' => request()->ip(),
                'attempts' => $attempts,
                'blocked_until' => now()->addMinutes($decayMinutes)->toISOString()
            ]);
        }
    }

    /**
     * Generate blocked response.
     */
    protected function blockedResponse(int $remainingTime, Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => "Too many login attempts. Please try again in {$remainingTime} minutes.",
                'retry_after' => $remainingTime * 60
            ], 429);
        }

        return back()->withErrors([
            'email' => "Too many login attempts. Please try again in {$remainingTime} minutes."
        ])->withInput($request->except('password'));
    }

    /**
     * Check if the response indicates a failed login.
     */
    protected function isFailedLogin($response): bool
    {
        return $response instanceof \Illuminate\Http\RedirectResponse &&
               $response->getSession() &&
               $response->getSession()->has('errors');
    }

    /**
     * Handle failed login attempt.
     */
    protected function handleFailedLogin(Request $request, int $maxAttempts, int $decayMinutes): void
    {
        $ip = $request->ip();
        $email = $request->input('email');
        $type = str_contains($request->path(), 'admin') ? 'admin' : 'employee';

        // Record the failed attempt
        FailedLoginAttempt::record($email, $type, [
            'path' => $request->path(),
            'method' => $request->method(),
        ]);

        // Check if we should lock the account
        $emailAttempts = $email ? FailedLoginAttempt::getRecentAttemptsByEmail($email, $decayMinutes) : 0;
        $ipAttempts = FailedLoginAttempt::getRecentAttemptsByIP($ip, $decayMinutes);

        // Lock email if too many attempts
        if ($email && $emailAttempts >= $maxAttempts && !AccountLockout::isEmailLocked($email)) {
            AccountLockout::lockEmail($email, $decayMinutes, $emailAttempts);

            // Log account lockout
            AuditLog::logAccountLockout($email, 'email', $emailAttempts, [
                'locked_for_minutes' => $decayMinutes,
                'trigger_path' => $request->path(),
            ]);

            Log::warning('Email account locked due to failed login attempts', [
                'email' => $email,
                'attempts' => $emailAttempts,
                'locked_for_minutes' => $decayMinutes
            ]);
        }

        // Lock IP if too many attempts (double the threshold for IP vs email)
        if ($ipAttempts >= ($maxAttempts * 2) && !AccountLockout::isIPLocked($ip)) {
            AccountLockout::lockIP($ip, $decayMinutes * 2, $ipAttempts);

            // Log IP lockout
            AuditLog::logAccountLockout($ip, 'ip', $ipAttempts, [
                'locked_for_minutes' => $decayMinutes * 2,
                'trigger_path' => $request->path(),
            ]);

            Log::warning('IP address locked due to failed login attempts', [
                'ip' => $ip,
                'attempts' => $ipAttempts,
                'locked_for_minutes' => $decayMinutes * 2
            ]);
        }

        // Also increment cache-based counter for backward compatibility
        $this->incrementFailedAttempts($this->getRateLimitKey($request), $maxAttempts, $decayMinutes);
    }

    /**
     * Clear rate limiting for successful login.
     */
    public static function clearAttempts(Request $request): void
    {
        $key = 'login_attempts:' . $request->ip();
        Cache::forget($key);
        Cache::forget("{$key}:blocked_until");

        Log::info('Rate limiting cleared for successful login', [
            'ip' => $request->ip()
        ]);
    }
}

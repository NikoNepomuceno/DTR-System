<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'user_type',
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'session_id',
        'status',
        'message',
        'metadata',
        'risk_level',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    /**
     * Log an authentication event.
     */
    public static function logAuth(
        string $eventType,
        string $status,
        string $userType = 'employee',
        ?int $userId = null,
        ?string $email = null,
        string $message = '',
        array $metadata = [],
        string $riskLevel = 'low'
    ): self {
        return self::create([
            'event_type' => $eventType,
            'user_type' => $userType,
            'user_id' => $userId,
            'email' => $email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'status' => $status,
            'message' => $message,
            'metadata' => array_merge($metadata, [
                'url' => request()->url(),
                'method' => request()->method(),
                'timestamp' => now()->toISOString(),
            ]),
            'risk_level' => $riskLevel,
            'occurred_at' => now(),
        ]);
    }

    /**
     * Log successful login.
     */
    public static function logSuccessfulLogin(User $user, string $userType = 'employee', array $metadata = []): self
    {
        return self::logAuth(
            'login',
            'success',
            $userType,
            $user->id,
            $user->email,
            "User {$user->email} logged in successfully",
            array_merge($metadata, [
                'user_name' => $user->name,
                'employee_id' => $user->employee_id ?? null,
            ]),
            'low'
        );
    }

    /**
     * Log failed login attempt.
     */
    public static function logFailedLogin(
        ?string $email = null,
        string $userType = 'employee',
        string $reason = 'invalid_credentials',
        array $metadata = []
    ): self {
        return self::logAuth(
            'failed_login',
            'failure',
            $userType,
            null,
            $email,
            "Failed login attempt for {$email}",
            array_merge($metadata, [
                'failure_reason' => $reason,
            ]),
            'medium'
        );
    }

    /**
     * Log logout event.
     */
    public static function logLogout(?User $user = null, string $userType = 'employee', array $metadata = []): self
    {
        return self::logAuth(
            'logout',
            'success',
            $userType,
            $user?->id,
            $user?->email,
            $user ? "User {$user->email} logged out" : "Anonymous logout",
            $metadata,
            'low'
        );
    }

    /**
     * Log session security event.
     */
    public static function logSessionSecurity(
        string $eventType,
        string $message,
        ?User $user = null,
        array $metadata = [],
        string $riskLevel = 'high'
    ): self {
        return self::logAuth(
            $eventType,
            'warning',
            $user ? 'employee' : 'guest',
            $user?->id,
            $user?->email,
            $message,
            $metadata,
            $riskLevel
        );
    }

    /**
     * Log account lockout.
     */
    public static function logAccountLockout(
        string $identifier,
        string $type,
        int $attemptCount,
        array $metadata = []
    ): self {
        return self::logAuth(
            'account_lockout',
            'warning',
            'employee',
            null,
            $type === 'email' ? $identifier : null,
            "Account locked: {$type} {$identifier} after {$attemptCount} failed attempts",
            array_merge($metadata, [
                'lockout_type' => $type,
                'lockout_identifier' => $identifier,
                'attempt_count' => $attemptCount,
            ]),
            'high'
        );
    }

    /**
     * Get security statistics.
     */
    public static function getSecurityStats(int $days = 7): array
    {
        $since = now()->subDays($days);
        
        return [
            'total_events' => self::where('occurred_at', '>=', $since)->count(),
            'failed_logins' => self::where('event_type', 'failed_login')
                ->where('occurred_at', '>=', $since)->count(),
            'successful_logins' => self::where('event_type', 'login')
                ->where('status', 'success')
                ->where('occurred_at', '>=', $since)->count(),
            'lockouts' => self::where('event_type', 'account_lockout')
                ->where('occurred_at', '>=', $since)->count(),
            'high_risk_events' => self::where('risk_level', 'high')
                ->where('occurred_at', '>=', $since)->count(),
            'critical_events' => self::where('risk_level', 'critical')
                ->where('occurred_at', '>=', $since)->count(),
            'by_event_type' => self::where('occurred_at', '>=', $since)
                ->selectRaw('event_type, COUNT(*) as count')
                ->groupBy('event_type')
                ->pluck('count', 'event_type')
                ->toArray(),
            'by_risk_level' => self::where('occurred_at', '>=', $since)
                ->selectRaw('risk_level, COUNT(*) as count')
                ->groupBy('risk_level')
                ->pluck('count', 'risk_level')
                ->toArray(),
            'top_ips' => self::where('occurred_at', '>=', $since)
                ->selectRaw('ip_address, COUNT(*) as count')
                ->groupBy('ip_address')
                ->orderByDesc('count')
                ->limit(10)
                ->pluck('count', 'ip_address')
                ->toArray(),
        ];
    }

    /**
     * Get recent high-risk events.
     */
    public static function getHighRiskEvents(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return self::whereIn('risk_level', ['high', 'critical'])
            ->orderByDesc('occurred_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Clean up old audit logs.
     */
    public static function cleanup(int $daysOld = 90): int
    {
        return self::where('occurred_at', '<', now()->subDays($daysOld))->delete();
    }

    /**
     * Relationship to user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

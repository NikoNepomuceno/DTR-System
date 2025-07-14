<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AccountLockout extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'type',
        'reason',
        'attempt_count',
        'locked_at',
        'locked_until',
        'unlocked_at',
        'unlocked_by',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'locked_at' => 'datetime',
        'locked_until' => 'datetime',
        'unlocked_at' => 'datetime',
    ];

    /**
     * Check if an email is currently locked.
     */
    public static function isEmailLocked(string $email): bool
    {
        return self::where('identifier', $email)
            ->where('type', 'email')
            ->where('locked_until', '>', now())
            ->whereNull('unlocked_at')
            ->exists();
    }

    /**
     * Check if an IP is currently locked.
     */
    public static function isIPLocked(string $ip): bool
    {
        return self::where('identifier', $ip)
            ->where('type', 'ip')
            ->where('locked_until', '>', now())
            ->whereNull('unlocked_at')
            ->exists();
    }

    /**
     * Lock an email account.
     */
    public static function lockEmail(string $email, int $minutes = 30, int $attemptCount = 0, array $metadata = []): self
    {
        return self::create([
            'identifier' => $email,
            'type' => 'email',
            'reason' => 'failed_login_attempts',
            'attempt_count' => $attemptCount,
            'locked_at' => now(),
            'locked_until' => now()->addMinutes($minutes),
            'metadata' => array_merge($metadata, [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]),
        ]);
    }

    /**
     * Lock an IP address.
     */
    public static function lockIP(string $ip, int $minutes = 60, int $attemptCount = 0, array $metadata = []): self
    {
        return self::create([
            'identifier' => $ip,
            'type' => 'ip',
            'reason' => 'failed_login_attempts',
            'attempt_count' => $attemptCount,
            'locked_at' => now(),
            'locked_until' => now()->addMinutes($minutes),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get remaining lockout time for an email.
     */
    public static function getEmailLockoutTime(string $email): ?Carbon
    {
        $lockout = self::where('identifier', $email)
            ->where('type', 'email')
            ->where('locked_until', '>', now())
            ->whereNull('unlocked_at')
            ->first();

        return $lockout ? $lockout->locked_until : null;
    }

    /**
     * Get remaining lockout time for an IP.
     */
    public static function getIPLockoutTime(string $ip): ?Carbon
    {
        $lockout = self::where('identifier', $ip)
            ->where('type', 'ip')
            ->where('locked_until', '>', now())
            ->whereNull('unlocked_at')
            ->first();

        return $lockout ? $lockout->locked_until : null;
    }

    /**
     * Unlock an account manually.
     */
    public static function unlock(string $identifier, string $type, string $unlockedBy = 'admin'): bool
    {
        return self::where('identifier', $identifier)
            ->where('type', $type)
            ->where('locked_until', '>', now())
            ->whereNull('unlocked_at')
            ->update([
                'unlocked_at' => now(),
                'unlocked_by' => $unlockedBy,
            ]) > 0;
    }

    /**
     * Clean up expired lockouts.
     */
    public static function cleanupExpired(): int
    {
        return self::where('locked_until', '<', now())
            ->whereNull('unlocked_at')
            ->update([
                'unlocked_at' => now(),
                'unlocked_by' => 'auto',
            ]);
    }

    /**
     * Get lockout statistics.
     */
    public static function getStatistics(int $days = 7): array
    {
        $since = now()->subDays($days);
        
        return [
            'total_lockouts' => self::where('locked_at', '>=', $since)->count(),
            'active_lockouts' => self::where('locked_until', '>', now())->whereNull('unlocked_at')->count(),
            'by_type' => self::where('locked_at', '>=', $since)
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'by_reason' => self::where('locked_at', '>=', $since)
                ->selectRaw('reason, COUNT(*) as count')
                ->groupBy('reason')
                ->pluck('count', 'reason')
                ->toArray(),
        ];
    }
}

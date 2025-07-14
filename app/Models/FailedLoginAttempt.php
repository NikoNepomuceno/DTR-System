<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FailedLoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'type',
        'metadata',
        'attempted_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'attempted_at' => 'datetime',
    ];

    /**
     * Record a failed login attempt.
     */
    public static function record(string $email = null, string $type = 'employee', array $metadata = []): self
    {
        return self::create([
            'email' => $email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'type' => $type,
            'metadata' => array_merge($metadata, [
                'url' => request()->url(),
                'method' => request()->method(),
            ]),
            'attempted_at' => now(),
        ]);
    }

    /**
     * Get recent failed attempts for an email.
     */
    public static function getRecentAttemptsByEmail(string $email, int $minutes = 15): int
    {
        return self::where('email', $email)
            ->where('attempted_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Get recent failed attempts for an IP address.
     */
    public static function getRecentAttemptsByIP(string $ip, int $minutes = 15): int
    {
        return self::where('ip_address', $ip)
            ->where('attempted_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Clean up old failed attempts.
     */
    public static function cleanup(int $daysOld = 30): int
    {
        return self::where('attempted_at', '<', now()->subDays($daysOld))->delete();
    }

    /**
     * Get failed attempts statistics.
     */
    public static function getStatistics(int $days = 7): array
    {
        $since = now()->subDays($days);
        
        return [
            'total_attempts' => self::where('attempted_at', '>=', $since)->count(),
            'unique_emails' => self::where('attempted_at', '>=', $since)->distinct('email')->count('email'),
            'unique_ips' => self::where('attempted_at', '>=', $since)->distinct('ip_address')->count('ip_address'),
            'by_type' => self::where('attempted_at', '>=', $since)
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'top_ips' => self::where('attempted_at', '>=', $since)
                ->selectRaw('ip_address, COUNT(*) as count')
                ->groupBy('ip_address')
                ->orderByDesc('count')
                ->limit(10)
                ->pluck('count', 'ip_address')
                ->toArray(),
        ];
    }
}

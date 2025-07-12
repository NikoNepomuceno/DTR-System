<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'qr_code',
        'employee_id',
        'department',
        'position',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the DTR records for this user.
     */
    public function dtrs()
    {
        return $this->hasMany(DTR::class);
    }

    /**
     * Get today's DTR record.
     */
    public function todayDTR()
    {
        return $this->dtrs()->where('date', today())->first();
    }

    /**
     * Check if user is currently clocked in.
     */
    public function isClockedIn()
    {
        $todayDTR = $this->todayDTR();
        return $todayDTR ? $todayDTR->isClockedIn() : false;
    }

    /**
     * Check if user is on break.
     */
    public function isOnBreak()
    {
        $todayDTR = $this->todayDTR();
        return $todayDTR ? $todayDTR->isOnBreak() : false;
    }

    /**
     * Get current status.
     */
    public function getCurrentStatus()
    {
        $todayDTR = $this->todayDTR();
        return $todayDTR ? $todayDTR->getStatusText() : 'Not Present';
    }

    /**
     * Generate QR code for user.
     */
    public function generateQRCode()
    {
        if (!$this->qr_code) {
            // Create a more secure and informative QR code
            $this->qr_code = 'DTR-' . strtoupper($this->employee_id) . '-' . substr(md5($this->email . $this->id), 0, 6);
            $this->save();
        }
        return $this->qr_code;
    }

    /**
     * Get QR code data with additional information.
     */
    public function getQRCodeData()
    {
        return [
            'code' => $this->qr_code,
            'employee_id' => $this->employee_id,
            'name' => $this->name,
            'department' => $this->department,
            'generated_at' => now()->toISOString(),
        ];
    }

    /**
     * Get formatted QR code for display.
     */
    public function getFormattedQRCode()
    {
        return $this->qr_code ?: $this->generateQRCode();
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}

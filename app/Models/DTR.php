<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DTR extends Model
{
    use HasFactory;

    protected $table = 'dtrs';

    protected $fillable = [
        'user_id',
        'date',
        'time_in',
        'time_out',
        'break_start',
        'break_end',
        'total_hours',
        'break_hours',
        'status',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'break_start' => 'datetime',
        'break_end' => 'datetime',
        'total_hours' => 'decimal:2',
        'break_hours' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Check if employee is currently clocked in
    public function isClockedIn()
    {
        return !is_null($this->time_in) && is_null($this->time_out);
    }

    // Check if employee is on break
    public function isOnBreak()
    {
        return !is_null($this->break_start) && is_null($this->break_end);
    }

    // Calculate total hours worked
    public function calculateTotalHours()
    {
        if (!$this->time_in || !$this->time_out) {
            return 0;
        }

        $totalMinutes = $this->time_in->diffInMinutes($this->time_out);
        $breakMinutes = $this->break_hours * 60;

        return round(($totalMinutes - $breakMinutes) / 60, 2);
    }

    // Get formatted time in
    public function getTimeInFormatted()
    {
        return $this->time_in ? $this->time_in->format('g:i A') : 'Not clocked in';
    }

    // Get formatted time out
    public function getTimeOutFormatted()
    {
        return $this->time_out ? $this->time_out->format('g:i A') : 'Not clocked out';
    }

    // Get current status text
    public function getStatusText()
    {
        if ($this->isOnBreak()) {
            return 'On Break';
        } elseif ($this->isClockedIn()) {
            return 'Clocked In';
        } elseif ($this->time_out) {
            return 'Clocked Out';
        } else {
            return 'Not Present';
        }
    }

    // Get status color for UI
    public function getStatusColor()
    {
        if ($this->isOnBreak()) {
            return 'yellow';
        } elseif ($this->isClockedIn()) {
            return 'green';
        } elseif ($this->time_out) {
            return 'gray';
        } else {
            return 'red';
        }
    }
}

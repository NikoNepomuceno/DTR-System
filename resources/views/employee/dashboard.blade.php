@extends('layouts.app')

@section('content')
<x-employee-nav />
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="card rounded-xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="bg-interactive-primary bg-opacity-10 rounded-full p-3">
                    <svg class="w-8 h-8 interactive-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-primary">Welcome back, {{ $user->name }}!</h1>
                    <p class="text-secondary">Employee ID: {{ $user->employee_id }} • Department: {{ $user->department }}</p>
                </div>
            </div>
            <div class="text-right">
                <div id="current-time" class="text-2xl font-bold interactive-primary">--:--:--</div>
                <div id="current-date" class="text-sm text-muted">--/--/----</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Clock In/Out Status -->
        <div class="card rounded-xl p-6">
            <div class="text-center">
                @if($todayDTR && $todayDTR->isClockedIn())
                    <div class="bg-success bg-opacity-10 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-success" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary mb-2">Current Status</h3>
                    <div class="text-2xl font-bold text-success mb-4">CLOCKED IN</div>
                    <p class="text-sm text-secondary mb-4">Since {{ $todayDTR->getTimeInFormatted() }}</p>
                @elseif($todayDTR && $todayDTR->isOnBreak())
                    <div class="bg-warning bg-opacity-10 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-warning" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary mb-2">Current Status</h3>
                    <div class="text-2xl font-bold text-warning mb-4">ON BREAK</div>
                    <p class="text-sm text-secondary mb-4">Since {{ $todayDTR->break_start->format('g:i A') }}</p>
                @elseif($todayDTR && $todayDTR->time_out)
                    <div class="bg-tertiary rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary mb-2">Current Status</h3>
                    <div class="text-2xl font-bold text-muted mb-4">CLOCKED OUT</div>
                    <p class="text-sm text-secondary mb-4">At {{ $todayDTR->getTimeOutFormatted() }}</p>
                @else
                    <div class="bg-error bg-opacity-10 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-error" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-primary mb-2">Current Status</h3>
                    <div class="text-2xl font-bold text-error mb-4">NOT CLOCKED IN</div>
                    <p class="text-sm text-secondary mb-4">Please scan your QR code at the DTR station</p>
                @endif
            </div>
        </div>

        <!-- Today's Summary -->
        <div class="card rounded-xl p-6">
            <div class="text-center">
                <div class="bg-interactive-primary bg-opacity-10 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 interactive-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-primary mb-2">Today's Summary</h3>
                @if($todayDTR && $todayDTR->total_hours)
                    <div class="text-2xl font-bold interactive-primary mb-2">{{ number_format($todayDTR->total_hours, 1) }}h</div>
                    <p class="text-sm text-secondary">Hours worked today</p>
                @elseif($todayDTR && $todayDTR->time_in)
                    <div class="text-2xl font-bold interactive-primary mb-2">0.0h</div>
                    <p class="text-sm text-secondary">Hours worked today</p>
                @else
                    <div class="text-2xl font-bold text-muted mb-2">0.0h</div>
                    <p class="text-sm text-secondary">Hours worked today</p>
                @endif
                <div class="mt-4 text-sm text-secondary space-y-1">
                    <div class="flex justify-between">
                        <span>Time In:</span>
                        <span class="font-semibold text-primary">{{ $todayDTR ? $todayDTR->getTimeInFormatted() : 'Not clocked in' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Break Time:</span>
                        <span class="font-semibold text-primary">{{ $todayDTR && $todayDTR->break_hours ? number_format($todayDTR->break_hours, 1) . 'h' : '0.0h' }}</span>
                    </div>
                    @if($todayDTR && $todayDTR->time_out)
                    <div class="flex justify-between">
                        <span>Time Out:</span>
                        <span class="font-semibold text-primary">{{ $todayDTR->getTimeOutFormatted() }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- QR Code -->
        <div class="card rounded-xl p-6">
            <div class="text-center">
                <div class="bg-interactive-secondary bg-opacity-10 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 interactive-secondary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-primary mb-2">Your QR Code</h3>
                <div class="bg-secondary rounded-lg p-4 mb-4">
                    <div class="w-32 h-32 mx-auto card rounded border-2 border-dashed border-primary flex items-center justify-center">
                        <div id="dashboard-qr" class="w-full h-full flex items-center justify-center"></div>
                    </div>
                </div>
                <p class="text-sm text-secondary">Scan this at the DTR station</p>
                <a href="/employee/qr-code" class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 mt-2">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    View Full QR Code
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card rounded-xl p-6 mb-6">
        <div class="flex items-center mb-4">
            <svg class="w-5 h-5 interactive-primary mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-semibold text-lg text-primary">Recent Activity</span>
        </div>
        <div class="space-y-3">
            @forelse($recentActivity as $dtr)
                <div class="flex items-center justify-between p-3 bg-secondary rounded-lg">
                    <div class="flex items-center">
                        <div class="bg-interactive-primary rounded-full p-1 mr-3">
                            <svg class="w-4 h-4 text-inverse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-primary">{{ $dtr->getStatusText() }}</div>
                            <div class="text-sm text-secondary">{{ $dtr->date->format('M j, Y') }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm text-muted">{{ $dtr->getTimeInFormatted() }}</span>
                        @if($dtr->time_out)
                            <br><span class="text-sm text-muted">{{ $dtr->getTimeOutFormatted() }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-muted">
                    <svg class="w-12 h-12 mx-auto mb-4 text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p>No recent activity</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- This Week's Summary -->
    <div class="card rounded-xl p-6">
        <div class="flex items-center mb-4">
            <svg class="w-5 h-5 interactive-primary mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="font-semibold text-lg text-primary">This Week's Summary</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-7 gap-2">
            @php
                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                $weekStart = now()->startOfWeek();
            @endphp
            @foreach($days as $index => $day)
                @php
                    $date = $weekStart->copy()->addDays($index);
                    $dayDTR = $weeklyDTRs->where('date', $date->format('Y-m-d'))->first();
                    $isToday = $date->isToday();
                @endphp
                <div class="text-center p-3 {{ $isToday ? 'bg-interactive-primary bg-opacity-10 border-2 border-interactive-primary' : 'bg-secondary' }} rounded-lg">
                    <div class="text-sm text-secondary">{{ $day }}</div>
                    <div class="font-semibold {{ $isToday ? 'interactive-primary' : 'text-primary' }}">
                        @if($dayDTR && $dayDTR->total_hours)
                            {{ number_format($dayDTR->total_hours, 1) }}h
                        @elseif($dayDTR && $dayDTR->time_in)
                            -
                        @else
                            -
                        @endif
                    </div>
                    <div class="text-xs {{ $isToday ? 'interactive-primary' : ($dayDTR && $dayDTR->time_in ? 'text-success' : 'text-muted') }}">
                        @if($isToday)
                            ●
                        @elseif($dayDTR && $dayDTR->time_in)
                            ✓
                        @else
                            -
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 text-center">
            <div class="text-lg font-semibold text-primary">Total This Week: {{ number_format($weeklyHours, 1) }} hours</div>
        </div>
    </div>
</div>

<script>
// Update current time and date
function updateDateTime() {
    const now = new Date();
    const time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
    const date = now.toLocaleDateString();
    document.getElementById('current-time').textContent = time;
    document.getElementById('current-date').textContent = date;
}

updateDateTime();
setInterval(updateDateTime, 1000);

// Generate mini QR code for dashboard
const dashboardQREl = document.getElementById('dashboard-qr');
if (dashboardQREl) {
    const qrCodeValue = '{{ $user->qr_code }}';
    new QRCode(dashboardQREl, {
        text: qrCodeValue,
        width: 100,
        height: 100,
        colorDark: "#374151",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.M
    });
}
</script>

<!-- QRCode.js CDN for dashboard -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
@endsection 
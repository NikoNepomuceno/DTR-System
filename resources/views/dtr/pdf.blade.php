<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DTR Report - {{ $user->name }}</title>
    <style>
        /* CSS Variables for theme support */
        :root {
            --text-primary: #333;
            --text-secondary: #666;
            --text-muted: #888;
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --bg-tertiary: #f1f3f4;
            --border-color: #ddd;
            --stats-value-color: #2563eb;
        }

        /* Dark mode variables */
        [data-theme="dark"] {
            --text-primary: #f8fafc;
            --text-secondary: #e2e8f0;
            --text-muted: #cbd5e1;
            --bg-primary: #1e293b;
            --bg-secondary: #334155;
            --bg-tertiary: #475569;
            --border-color: #4b5563;
            --stats-value-color: #ffffff;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: var(--text-primary);
            background-color: var(--bg-primary);
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--text-primary);
            padding-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            color: var(--text-primary);
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: var(--text-secondary);
            margin-bottom: 10px;
        }

        .report-period {
            font-size: 14px;
            color: var(--text-muted);
        }

        .employee-info {
            background-color: var(--bg-secondary);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .employee-info h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: var(--text-primary);
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .info-item {
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            color: var(--text-secondary);
        }

        .summary-section {
            margin-bottom: 25px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .summary-card {
            background-color: var(--bg-tertiary);
            padding: 12px;
            border-radius: 5px;
            text-align: center;
        }

        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: var(--stats-value-color);
            margin-bottom: 5px;
        }

        .summary-label {
            font-size: 11px;
            color: var(--text-secondary);
            text-transform: uppercase;
        }

        .dtr-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .dtr-table th,
        .dtr-table td {
            border: 1px solid var(--border-color);
            padding: 8px;
            text-align: left;
            color: var(--text-primary);
        }

        .dtr-table th {
            background-color: var(--bg-secondary);
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        .dtr-table td {
            font-size: 11px;
            background-color: var(--bg-primary);
        }

        .status-present {
            color: #16a34a;
            font-weight: bold;
        }

        .status-late {
            color: #ea580c;
            font-weight: bold;
        }

        .status-absent {
            color: #dc2626;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            text-align: center;
            font-size: 10px;
            color: var(--text-muted);
        }

        .page-break {
            page-break-before: always;
        }

        @media print {
            body {
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">DTR SYSTEM</div>
        <div class="report-title">Daily Time Record Report</div>
        <div class="report-period">
            Period: {{ \Carbon\Carbon::parse($request->from_date)->format('F j, Y') }} -
            {{ \Carbon\Carbon::parse($request->to_date)->format('F j, Y') }}
        </div>
    </div>

    <!-- Employee Information -->
    <div class="employee-info">
        <h3>Employee Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Name:</span> {{ $user->name }}
            </div>
            <div class="info-item">
                <span class="info-label">Employee ID:</span> {{ $user->employee_id }}
            </div>
            <div class="info-item">
                <span class="info-label">Department:</span> {{ $user->department ?? 'N/A' }}
            </div>
            <div class="info-item">
                <span class="info-label">Position:</span> {{ $user->position ?? 'N/A' }}
            </div>
            <div class="info-item">
                <span class="info-label">Email:</span> {{ $user->email }}
            </div>
            <div class="info-item">
                <span class="info-label">Report Generated:</span> {{ now()->format('F j, Y g:i A') }}
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-section">
        <h3 style="margin-bottom: 15px; font-size: 16px; color: var(--text-primary);">Summary Statistics</h3>
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-value">{{ $totalDays ?? 0 }}</div>
                <div class="summary-label">Total Days</div>
            </div>
            <div class="summary-card">
                <div class="summary-value">{{ number_format($totalHours ?? 0, 1) }}</div>
                <div class="summary-label">Total Hours</div>
            </div>
            <div class="summary-card">
                <div class="summary-value">{{ number_format($averageHours ?? 0, 1) }}</div>
                <div class="summary-label">Avg Hours/Day</div>
            </div>
            <div class="summary-card">
                <div class="summary-value">{{ $presentDays ?? 0 }}</div>
                <div class="summary-label">Present Days</div>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-value">{{ $lateDays ?? 0 }}</div>
                <div class="summary-label">Late Days</div>
            </div>
            <div class="summary-card">
                <div class="summary-value">{{ $absentDays ?? 0 }}</div>
                <div class="summary-label">Absent Days</div>
            </div>
            <div class="summary-card">
                <div class="summary-value">{{ number_format($totalBreakHours ?? 0, 1) }}</div>
                <div class="summary-label">Break Hours</div>
            </div>
            <div class="summary-card">
                <div class="summary-value">
                    {{ number_format((($presentDays ?? 0) / max($totalDays ?? 1, 1)) * 100, 1) }}%
                </div>
                <div class="summary-label">Attendance Rate</div>
            </div>
        </div>
    </div>

    <!-- DTR Records Table -->
    <div>
        <h3 style="margin-bottom: 15px; font-size: 16px; color: var(--text-primary);">Daily Time Records</h3>

        @if($dtrs->count() > 0)
            <table class="dtr-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Time In</th>
                        <th>Break Start</th>
                        <th>Break End</th>
                        <th>Time Out</th>
                        <th>Break Hours</th>
                        <th>Total Hours</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dtrs as $dtr)
                        <tr>
                            <td>{{ $dtr->date->format('M j, Y') }}</td>
                            <td>{{ $dtr->date->format('D') }}</td>
                            <td>{{ $dtr->time_in ? $dtr->time_in->format('g:i A') : '-' }}</td>
                            <td>{{ $dtr->break_start ? $dtr->break_start->format('g:i A') : '-' }}</td>
                            <td>{{ $dtr->break_end ? $dtr->break_end->format('g:i A') : '-' }}</td>
                            <td>{{ $dtr->time_out ? $dtr->time_out->format('g:i A') : '-' }}</td>
                            <td>{{ number_format($dtr->break_hours, 1) }}</td>
                            <td>{{ number_format($dtr->total_hours, 1) }}</td>
                            <td>
                                <span class="status-{{ $dtr->status }}">
                                    {{ ucfirst($dtr->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="text-align: center; padding: 40px; color: var(--text-secondary); font-style: italic;">
                No DTR records found for the selected period.
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This report was generated automatically by the DTR System on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>© {{ date('Y') }} DTR System. All rights reserved.</p>
    </div>

</body>

</html>
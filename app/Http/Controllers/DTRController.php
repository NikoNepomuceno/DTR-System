<?php

namespace App\Http\Controllers;

use App\Models\DTR;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DTRController extends Controller
{
    /**
     * Display the main DTR dashboard.
     */
    public function index()
    {
        $today = today();
        $todayEntries = DTR::where('date', $today)->count();
        $activeEmployees = User::whereHas('dtrs', function($query) use ($today) {
            $query->where('date', $today)->whereNotNull('time_in')->whereNull('time_out');
        })->count();
        $totalRecords = DTR::count();
        $thisMonth = DTR::whereMonth('date', $today->month)->whereYear('date', $today->year)->count();

        $recentEntries = DTR::with('user')
            ->where('date', $today)
            ->orderBy('time_in', 'desc')
            ->limit(10)
            ->get();

        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('dtr.index', compact(
            'todayEntries',
            'activeEmployees',
            'totalRecords',
            'thisMonth',
            'recentEntries',
            'employees'
        ));
    }

    /**
     * Display the QR scanner page.
     */
    public function scan()
    {
        return view('dtr.scan');
    }

    /**
     * Process QR code scan or manual entry.
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string|max:50',
        ]);

        $qrCode = trim($request->qr_code);

        // Try to find by QR code first
        $user = User::where('qr_code', $qrCode)->first();

        // If not found, try by employee_id (manual entry)
        if (!$user) {
            $user = User::where('employee_id', $qrCode)->first();
        }

        // If still not found, try to extract employee_id from new QR format (DTR-EMPXXX-HASH)
        if (!$user && preg_match('/^DTR-(.+?)-[A-F0-9]{6}$/', $qrCode, $matches)) {
            $employeeId = $matches[1];
            $user = User::where('employee_id', $employeeId)->first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code or employee ID, or user not found.'
            ], 404);
        }

        $today = today();
        $dtr = DTR::firstOrCreate([
            'user_id' => $user->id,
            'date' => $today,
        ]);

        $now = now();
        $message = '';

        // Determine action based on current state
        if (!$dtr->time_in) {
            // Clock in
            $dtr->update(['time_in' => $now]);
            $message = "Clocked in successfully at " . $now->format('g:i A');
        } elseif (!$dtr->time_out) {
            if (!$dtr->break_start) {
                // Start break
                $dtr->update(['break_start' => $now]);
                $message = "Break started at " . $now->format('g:i A');
            } elseif (!$dtr->break_end) {
                // End break
                $dtr->update(['break_end' => $now]);
                $breakHours = Carbon::parse($dtr->break_start)->diffInMinutes($now) / 60;
                $dtr->update(['break_hours' => round($breakHours, 2)]);
                $message = "Break ended at " . $now->format('g:i A');
            } else {
                // Clock out
                $dtr->update(['time_out' => $now]);
                $totalHours = $dtr->calculateTotalHours();
                $dtr->update(['total_hours' => $totalHours]);
                $message = "Clocked out successfully at " . $now->format('g:i A');
            }
        } else {
            $message = "Already clocked out for today.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'user' => [
                'name' => $user->name,
                'employee_id' => $user->employee_id,
                'department' => $user->department,
            ],
            'status' => $dtr->getStatusText(),
            'time_in' => $dtr->getTimeInFormatted(),
            'time_out' => $dtr->getTimeOutFormatted(),
        ]);
    }

    /**
     * Display employee management page.
     */
    public function employees()
    {
        $employees = User::where('role', 'employee')
            ->orderBy('name')
            ->get();

        return view('dtr.employee', compact('employees'));
    }

    /**
     * Display employee dashboard.
     */
    public function employeeDashboard()
    {
        $user = User::find(session('employee_user_id'));

        $todayDTR = $user->todayDTR();
        $recentActivity = $user->dtrs()
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // Calculate weekly summary
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $weeklyDTRs = $user->dtrs()
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->get();

        $weeklyHours = $weeklyDTRs->sum('total_hours');
        $todayHours = $todayDTR ? $todayDTR->total_hours : 0;

        return view('employee.dashboard', compact(
            'user',
            'todayDTR',
            'recentActivity',
            'weeklyDTRs',
            'weeklyHours',
            'todayHours'
        ));
    }

    /**
     * Export DTR report as PDF.
     */
    public function exportPDF(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'from_date' => 'required|date|before_or_equal:today',
            'to_date' => 'required|date|after_or_equal:from_date|before_or_equal:today',
        ]);

        $user = User::findOrFail($request->user_id);
        $dtrs = DTR::where('user_id', $request->user_id)
            ->whereBetween('date', [$request->from_date, $request->to_date])
            ->orderBy('date')
            ->get();

        // Calculate summary statistics
        $totalDays = $dtrs->count();
        $totalHours = $dtrs->sum('total_hours');
        $totalBreakHours = $dtrs->sum('break_hours');
        $averageHours = $totalDays > 0 ? round($totalHours / $totalDays, 2) : 0;

        // Count status occurrences
        $presentDays = $dtrs->where('status', 'present')->count();
        $lateDays = $dtrs->where('status', 'late')->count();
        $absentDays = $dtrs->where('status', 'absent')->count();

        $data = compact('user', 'dtrs', 'request', 'totalDays', 'totalHours', 'totalBreakHours', 'averageHours', 'presentDays', 'lateDays', 'absentDays');

        // Generate PDF
        $pdf = Pdf::loadView('dtr.pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        // Generate filename
        $filename = 'DTR_Report_' . $user->employee_id . '_' .
                   Carbon::parse($request->from_date)->format('Y-m-d') . '_to_' .
                   Carbon::parse($request->to_date)->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Get employee's DTR history.
     */
    public function employeeHistory(Request $request)
    {
        $user = User::find(session('employee_user_id'));

        $dtrs = $user->dtrs()
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('employee.history', compact('user', 'dtrs'));
    }

    /**
     * Display employee's QR code.
     */
    public function employeeQRCode()
    {
        $user = User::find(session('employee_user_id'));

        $qrCode = $user->getFormattedQRCode();

        return view('employee.qr-code', compact('user', 'qrCode'));
    }
}

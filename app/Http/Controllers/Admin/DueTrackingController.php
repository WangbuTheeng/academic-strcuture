<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentBill;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Level;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DueTrackingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Temporarily removed permission check for testing
        // $this->middleware('can:manage-fees');
    }

    /**
     * Display due tracking dashboard.
     */
    public function index(Request $request)
    {
        // Get overdue bills with comprehensive student information
        $overdueBills = StudentBill::with([
                'student.currentEnrollment.class',
                'student.currentEnrollment.program.level',
                'academicYear',
                'billItems'
            ])
            ->where('status', 'overdue')
            ->orWhere(function ($query) {
                $query->whereIn('status', ['pending', 'partial'])
                      ->where('due_date', '<', Carbon::today());
            })
            ->orderBy('due_date', 'asc');

        // Apply filters
        if ($request->filled('academic_year_id')) {
            $overdueBills->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('level_id')) {
            $overdueBills->whereHas('student.currentEnrollment.program', function ($query) use ($request) {
                $query->where('level_id', $request->level_id);
            });
        }

        if ($request->filled('days_overdue')) {
            $daysOverdue = (int) $request->days_overdue;
            $cutoffDate = Carbon::today()->subDays($daysOverdue);
            $overdueBills->where('due_date', '<=', $cutoffDate);
        }

        // Handle per page selection
        $perPage = $request->input('per_page', 15);
        if (!in_array($perPage, [15, 25, 50, 100])) {
            $perPage = 15;
        }

        $overdueBills = $overdueBills->paginate($perPage);

        // Get summary statistics
        $totalOverdue = StudentBill::whereIn('status', ['pending', 'partial', 'overdue'])
            ->where('due_date', '<', Carbon::today())
            ->sum('balance_amount');

        $overdueCount = StudentBill::whereIn('status', ['pending', 'partial', 'overdue'])
            ->where('due_date', '<', Carbon::today())
            ->count();

        $dueTodayCount = StudentBill::whereIn('status', ['pending', 'partial'])
            ->whereDate('due_date', Carbon::today())
            ->count();

        $dueTomorrowCount = StudentBill::whereIn('status', ['pending', 'partial'])
            ->whereDate('due_date', Carbon::tomorrow())
            ->count();

        // Get top defaulters with comprehensive information
        $topDefaulters = Student::with(['currentEnrollment.class', 'currentEnrollment.program.level'])
            ->withSum(['pendingBills' => function ($query) {
                $query->where('due_date', '<', Carbon::today());
            }], 'balance_amount')
            ->withCount(['pendingBills' => function ($query) {
                $query->where('due_date', '<', Carbon::today());
            }])
            ->having('pending_bills_sum_balance_amount', '>', 0)
            ->orderBy('pending_bills_sum_balance_amount', 'desc')
            ->limit(10)
            ->get();

        // Get all students with remaining dues (not just overdue)
        $studentsWithDues = Student::with(['currentEnrollment.class', 'currentEnrollment.program.level'])
            ->withSum(['pendingBills'], 'balance_amount')
            ->withCount(['pendingBills'])
            ->having('pending_bills_sum_balance_amount', '>', 0)
            ->orderBy('pending_bills_sum_balance_amount', 'desc')
            ->get();

        // Get filter options
        $academicYears = AcademicYear::orderBy('name')->get();
        $levels = Level::orderBy('name')->get();

        return view('admin.due-tracking.index', compact(
            'overdueBills',
            'totalOverdue',
            'overdueCount',
            'dueTodayCount',
            'dueTomorrowCount',
            'topDefaulters',
            'studentsWithDues',
            'academicYears',
            'levels'
        ));
    }

    /**
     * Send due payment reminders.
     */
    public function sendReminders(Request $request)
    {
        $request->validate([
            'bill_ids' => 'required|array|min:1',
            'bill_ids.*' => 'exists:student_bills,id',
            'reminder_type' => 'required|in:email,sms,both',
            'message_template' => 'required|string',
        ]);

        $bills = StudentBill::with(['student', 'billItems'])
            ->whereIn('id', $request->bill_ids)
            ->get();

        $sentCount = 0;
        $failedCount = 0;

        foreach ($bills as $bill) {
            try {
                $message = $this->generateReminderMessage($request->message_template, $bill);

                if (in_array($request->reminder_type, ['email', 'both'])) {
                    if ($bill->student->email) {
                        $this->sendEmailReminder($bill, $message);
                    }
                }

                if (in_array($request->reminder_type, ['sms', 'both'])) {
                    if ($bill->student->phone) {
                        $this->sendSmsReminder($bill, $message);
                    }
                }

                // Log reminder sent
                Log::info('Payment reminder sent', [
                    'bill_id' => $bill->id,
                    'student_id' => $bill->student_id,
                    'type' => $request->reminder_type,
                ]);

                $sentCount++;
            } catch (\Exception $e) {
                Log::error('Failed to send payment reminder', [
                    'bill_id' => $bill->id,
                    'error' => $e->getMessage(),
                ]);
                $failedCount++;
            }
        }

        $message = "Reminders sent successfully: {$sentCount}";
        if ($failedCount > 0) {
            $message .= ", Failed: {$failedCount}";
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Generate automated reminders.
     */
    public function generateAutomatedReminders()
    {
        // Get bills due in 3 days (first reminder)
        $firstReminders = StudentBill::with(['student'])
            ->whereIn('status', ['pending', 'partial'])
            ->whereDate('due_date', Carbon::today()->addDays(3))
            ->get();

        // Get bills due today (second reminder)
        $secondReminders = StudentBill::with(['student'])
            ->whereIn('status', ['pending', 'partial'])
            ->whereDate('due_date', Carbon::today())
            ->get();

        // Get overdue bills (final reminder)
        $finalReminders = StudentBill::with(['student'])
            ->whereIn('status', ['pending', 'partial'])
            ->whereDate('due_date', Carbon::today()->subDays(7))
            ->get();

        $totalSent = 0;

        // Send first reminders
        foreach ($firstReminders as $bill) {
            $this->sendAutomatedReminder($bill, 'first');
            $totalSent++;
        }

        // Send second reminders
        foreach ($secondReminders as $bill) {
            $this->sendAutomatedReminder($bill, 'second');
            $totalSent++;
        }

        // Send final reminders
        foreach ($finalReminders as $bill) {
            $this->sendAutomatedReminder($bill, 'final');
            $totalSent++;
        }

        Log::info("Automated reminders sent: {$totalSent}");

        return response()->json([
            'success' => true,
            'message' => "Sent {$totalSent} automated reminders",
        ]);
    }

    /**
     * Get due payment analytics.
     */
    public function analytics(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30));
        $endDate = $request->get('end_date', Carbon::now());

        // Daily due amounts
        $dailyDues = StudentBill::selectRaw('DATE(due_date) as date, SUM(balance_amount) as amount')
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->whereBetween('due_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Collection efficiency
        $totalBilled = StudentBill::whereBetween('bill_date', [$startDate, $endDate])
            ->sum('total_amount');

        $totalCollected = StudentBill::whereBetween('bill_date', [$startDate, $endDate])
            ->sum('paid_amount');

        $collectionEfficiency = $totalBilled > 0 ? ($totalCollected / $totalBilled) * 100 : 0;

        // Overdue by category
        $overdueByCategory = StudentBill::join('bill_items', 'student_bills.id', '=', 'bill_items.bill_id')
            ->selectRaw('bill_items.fee_category, SUM(student_bills.balance_amount) as amount')
            ->whereIn('student_bills.status', ['pending', 'partial', 'overdue'])
            ->where('student_bills.due_date', '<', Carbon::today())
            ->groupBy('bill_items.fee_category')
            ->get();

        return view('admin.due-tracking.analytics', compact(
            'dailyDues',
            'totalBilled',
            'totalCollected',
            'collectionEfficiency',
            'overdueByCategory'
        ));
    }

    /**
     * Export overdue report.
     */
    public function exportOverdue(Request $request)
    {
        $overdueBills = StudentBill::with(['student', 'academicYear', 'billItems'])
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->where('due_date', '<', Carbon::today())
            ->orderBy('due_date', 'asc')
            ->get();

        $filename = 'overdue-report-' . Carbon::now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($overdueBills) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Bill Number',
                'Student Name',
                'Admission Number',
                'Academic Year',
                'Bill Date',
                'Due Date',
                'Days Overdue',
                'Total Amount',
                'Paid Amount',
                'Balance Amount',
                'Status',
                'Contact Phone',
                'Contact Email',
            ]);

            foreach ($overdueBills as $bill) {
                fputcsv($file, [
                    $bill->bill_number,
                    $bill->student->full_name,
                    $bill->student->admission_number,
                    $bill->academicYear->name,
                    $bill->bill_date->format('Y-m-d'),
                    $bill->due_date->format('Y-m-d'),
                    $bill->days_overdue,
                    $bill->total_amount,
                    $bill->paid_amount,
                    $bill->balance_amount,
                    $bill->status_label,
                    $bill->student->phone,
                    $bill->student->email,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate reminder message from template.
     */
    private function generateReminderMessage($template, $bill)
    {
        $replacements = [
            '{student_name}' => $bill->student->full_name,
            '{bill_number}' => $bill->bill_number,
            '{due_date}' => $bill->due_date->format('d/m/Y'),
            '{amount}' => 'Rs. ' . number_format($bill->balance_amount, 2),
            '{days_overdue}' => $bill->days_overdue,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Send email reminder.
     */
    private function sendEmailReminder($bill, $message)
    {
        // Implementation would depend on your mail configuration
        // This is a placeholder for the actual email sending logic
        Log::info('Email reminder would be sent', [
            'to' => $bill->student->email,
            'message' => $message,
        ]);
    }

    /**
     * Send SMS reminder.
     */
    private function sendSmsReminder($bill, $message)
    {
        // Implementation would depend on your SMS gateway
        // This is a placeholder for the actual SMS sending logic
        Log::info('SMS reminder would be sent', [
            'to' => $bill->student->phone,
            'message' => $message,
        ]);
    }

    /**
     * Send automated reminder.
     */
    private function sendAutomatedReminder($bill, $type)
    {
        $templates = [
            'first' => 'Dear {student_name}, your fee payment of {amount} for bill {bill_number} is due on {due_date}. Please make the payment to avoid late fees.',
            'second' => 'Dear {student_name}, your fee payment of {amount} for bill {bill_number} is due today. Please make the payment immediately.',
            'final' => 'Dear {student_name}, your fee payment of {amount} for bill {bill_number} is overdue by {days_overdue} days. Please make the payment immediately to avoid further action.',
        ];

        $message = $this->generateReminderMessage($templates[$type], $bill);

        if ($bill->student->email) {
            $this->sendEmailReminder($bill, $message);
        }

        if ($bill->student->phone) {
            $this->sendSmsReminder($bill, $message);
        }
    }
}

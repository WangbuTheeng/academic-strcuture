<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentBill;
use App\Models\Payment;
use App\Models\PaymentReceipt;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Level;
use App\Models\Program;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FeeReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Temporarily removed permission check for testing
        // $this->middleware('can:view-fee-reports');
    }

    /**
     * Display fee reports dashboard.
     */
    public function index()
    {
        // Get current academic year
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Summary statistics
        $totalBilled = StudentBill::sum('total_amount');
        $totalCollected = Payment::verified()->sum('amount');
        $totalOutstanding = StudentBill::sum('balance_amount');
        $collectionRate = $totalBilled > 0 ? ($totalCollected / $totalBilled) * 100 : 0;

        // Monthly collection data for chart
        $monthlyCollections = Payment::verified()
            ->selectRaw('MONTH(payment_date) as month, YEAR(payment_date) as year, SUM(amount) as total')
            ->whereYear('payment_date', Carbon::now()->year)
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Fee category wise collection
        $categoryWiseCollection = DB::table('bill_items')
            ->join('student_bills', 'bill_items.bill_id', '=', 'student_bills.id')
            ->join('payments', 'student_bills.id', '=', 'payments.bill_id')
            ->where('payments.is_verified', true)
            ->selectRaw('bill_items.fee_category, SUM(payments.amount) as total_collected')
            ->groupBy('bill_items.fee_category')
            ->get();

        // Recent payments
        $recentPayments = Payment::with(['student', 'bill'])
            ->verified()
            ->orderBy('payment_date', 'desc')
            ->limit(10)
            ->get();

        // Top paying students
        $topPayingStudents = Student::withSum(['payments' => function ($query) {
                $query->where('is_verified', true);
            }], 'amount')
            ->having('payments_sum_amount', '>', 0)
            ->orderBy('payments_sum_amount', 'desc')
            ->limit(10)
            ->get();

        return view('admin.fee-reports.index', compact(
            'totalBilled',
            'totalCollected',
            'totalOutstanding',
            'collectionRate',
            'monthlyCollections',
            'categoryWiseCollection',
            'recentPayments',
            'topPayingStudents',
            'currentAcademicYear'
        ));
    }

    /**
     * Daily collection report.
     */
    public function dailyCollection(Request $request)
    {
        $date = $request->get('date', Carbon::today());
        
        $payments = Payment::with(['student', 'bill', 'creator'])
            ->verified()
            ->whereDate('payment_date', $date)
            ->orderBy('payment_date', 'desc')
            ->get();

        $totalAmount = $payments->sum('amount');
        $paymentMethods = $payments->groupBy('payment_method')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->sum('amount'),
                ];
            });

        return view('admin.fee-reports.daily-collection', compact(
            'payments',
            'totalAmount',
            'paymentMethods',
            'date'
        ));
    }

    /**
     * Monthly collection report.
     */
    public function monthlyCollection(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $payments = Payment::with(['student', 'bill'])
            ->verified()
            ->whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->orderBy('payment_date', 'desc')
            ->get();

        $dailyBreakdown = $payments->groupBy(function ($payment) {
            return $payment->payment_date->format('Y-m-d');
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'amount' => $group->sum('amount'),
            ];
        });

        $totalAmount = $payments->sum('amount');
        $averageDaily = $dailyBreakdown->avg('amount') ?? 0;

        return view('admin.fee-reports.monthly-collection', compact(
            'payments',
            'dailyBreakdown',
            'totalAmount',
            'averageDaily',
            'month',
            'year'
        ));
    }

    /**
     * Outstanding dues report.
     */
    public function outstandingDues(Request $request)
    {
        $query = StudentBill::with(['student', 'academicYear'])
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->where('balance_amount', '>', 0);

        // Apply filters
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('level_id')) {
            $query->whereHas('student.currentEnrollment.program', function ($q) use ($request) {
                $q->where('level_id', $request->level_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Handle per page selection
        $perPage = $request->input('per_page', 20);
        if (!in_array($perPage, [15, 25, 50, 100])) {
            $perPage = 20;
        }

        $outstandingBills = $query->orderBy('due_date', 'asc')->paginate($perPage);

        $totalOutstanding = $query->sum('balance_amount');
        $overdueAmount = StudentBill::whereIn('status', ['pending', 'partial', 'overdue'])
            ->where('due_date', '<', Carbon::today())
            ->sum('balance_amount');

        // Get filter options
        $academicYears = AcademicYear::orderBy('name', 'asc')->get();
        $levels = Level::orderBy('name', 'asc')->get();

        return view('admin.fee-reports.outstanding-dues', compact(
            'outstandingBills',
            'totalOutstanding',
            'overdueAmount',
            'academicYears',
            'levels'
        ));
    }

    /**
     * Student wise fee report.
     */
    public function studentWise(Request $request)
    {
        $query = Student::with(['bills', 'payments'])
            ->withSum(['bills'], 'total_amount')
            ->withSum(['payments' => function ($q) {
                $q->where('is_verified', true);
            }], 'amount')
            ->withSum(['bills'], 'balance_amount');

        // Apply filters
        if ($request->filled('academic_year_id')) {
            $query->whereHas('bills', function ($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id);
            });
        }

        if ($request->filled('level_id')) {
            $query->whereHas('currentEnrollment.program', function ($q) use ($request) {
                $q->where('level_id', $request->level_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        // Handle per page selection
        $perPage = $request->input('per_page', 20);
        if (!in_array($perPage, [15, 25, 50, 100])) {
            $perPage = 20;
        }

        $students = $query->orderBy('first_name', 'asc')->paginate($perPage);

        // Get filter options
        $academicYears = AcademicYear::orderBy('name', 'asc')->get();
        $levels = Level::orderBy('name', 'asc')->get();

        return view('admin.fee-reports.student-wise', compact(
            'students',
            'academicYears',
            'levels'
        ));
    }

    /**
     * Fee category wise report.
     */
    public function categoryWise(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $categoryData = DB::table('bill_items')
            ->join('student_bills', 'bill_items.bill_id', '=', 'student_bills.id')
            ->leftJoin('payments', function ($join) {
                $join->on('student_bills.id', '=', 'payments.bill_id')
                     ->where('payments.is_verified', true);
            })
            ->selectRaw('
                bill_items.fee_category,
                SUM(bill_items.final_amount) as total_billed,
                COALESCE(SUM(payments.amount), 0) as total_collected,
                SUM(bill_items.final_amount) - COALESCE(SUM(payments.amount), 0) as outstanding
            ')
            ->whereBetween('student_bills.bill_date', [$startDate, $endDate])
            ->groupBy('bill_items.fee_category')
            ->get();

        return view('admin.fee-reports.category-wise', compact(
            'categoryData',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Payment method wise report.
     */
    public function paymentMethodWise(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $methodData = Payment::selectRaw('
                payment_method,
                COUNT(*) as transaction_count,
                SUM(amount) as total_amount,
                AVG(amount) as average_amount
            ')
            ->verified()
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->get();

        $totalTransactions = $methodData->sum('transaction_count');
        $totalAmount = $methodData->sum('total_amount');

        return view('admin.fee-reports.payment-method-wise', compact(
            'methodData',
            'totalTransactions',
            'totalAmount',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export collection report.
     */
    public function exportCollection(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $payments = Payment::with(['student', 'bill', 'creator'])
            ->verified()
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date', 'desc')
            ->get();

        $filename = 'collection-report-' . Carbon::parse($startDate)->format('Y-m-d') . '-to-' . Carbon::parse($endDate)->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Payment Number',
                'Payment Date',
                'Student Name',
                'Admission Number',
                'Bill Number',
                'Amount',
                'Payment Method',
                'Reference Number',
                'Collected By',
                'Status',
            ]);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->payment_number,
                    $payment->payment_date->format('Y-m-d'),
                    $payment->student->full_name,
                    $payment->student->admission_number,
                    $payment->bill->bill_number,
                    $payment->amount,
                    $payment->payment_method_label,
                    $payment->reference_number,
                    $payment->creator->name,
                    $payment->status_label,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get collection analytics data for charts.
     */
    public function analyticsData(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year

        switch ($period) {
            case 'day':
                $data = $this->getDailyAnalytics();
                break;
            case 'week':
                $data = $this->getWeeklyAnalytics();
                break;
            case 'month':
                $data = $this->getMonthlyAnalytics();
                break;
            case 'year':
                $data = $this->getYearlyAnalytics();
                break;
            default:
                $data = $this->getMonthlyAnalytics();
        }

        return response()->json($data);
    }

    /**
     * Get daily analytics data.
     */
    private function getDailyAnalytics()
    {
        return Payment::selectRaw('DATE(payment_date) as date, SUM(amount) as total')
            ->verified()
            ->whereDate('payment_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    /**
     * Get weekly analytics data.
     */
    private function getWeeklyAnalytics()
    {
        return Payment::selectRaw('YEARWEEK(payment_date) as week, SUM(amount) as total')
            ->verified()
            ->whereDate('payment_date', '>=', Carbon::now()->subWeeks(12))
            ->groupBy('week')
            ->orderBy('week', 'asc')
            ->get();
    }

    /**
     * Get monthly analytics data.
     */
    private function getMonthlyAnalytics()
    {
        return Payment::selectRaw('YEAR(payment_date) as year, MONTH(payment_date) as month, SUM(amount) as total')
            ->verified()
            ->whereDate('payment_date', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
    }

    /**
     * Get yearly analytics data.
     */
    private function getYearlyAnalytics()
    {
        return Payment::selectRaw('YEAR(payment_date) as year, SUM(amount) as total')
            ->verified()
            ->whereDate('payment_date', '>=', Carbon::now()->subYears(5))
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();
    }


}

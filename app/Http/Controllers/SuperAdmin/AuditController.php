<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AuditController extends Controller
{
    protected $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->middleware(['auth', 'role:super-admin']);
        $this->auditLogger = $auditLogger;
    }

    /**
     * Display audit logs
     */
    public function index(Request $request)
    {
        $this->auditLogger->logActivity('audit_logs_accessed', [
            'category' => 'super_admin',
            'severity' => 'info'
        ]);

        $filters = $request->only(['start_date', 'end_date', 'action', 'user_id', 'severity', 'category']);
        $logs = $this->auditLogger->getAuditLogs($filters, $request->get('per_page', 50));

        $stats = $this->getAuditStats($filters);

        return view('super-admin.audit.index', compact('logs', 'stats', 'filters'));
    }

    /**
     * Show detailed audit log
     */
    public function show(AuditLog $auditLog)
    {
        $this->auditLogger->logActivity('audit_log_viewed', [
            'resource_type' => 'audit_log',
            'resource_id' => $auditLog->id,
            'category' => 'super_admin',
            'severity' => 'info'
        ]);

        return view('super-admin.audit.show', compact('auditLog'));
    }

    /**
     * Generate audit report
     */
    public function report(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'type' => 'required|in:general,security,school_management,authentication,api'
        ]);

        $this->auditLogger->logActivity('audit_report_generated', [
            'new_values' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'type' => $request->type
            ],
            'category' => 'super_admin',
            'severity' => 'info'
        ]);

        $report = $this->auditLogger->generateAuditReport(
            $request->start_date,
            $request->end_date,
            $request->type
        );

        if ($request->wantsJson()) {
            return response()->json(['data' => $report]);
        }

        return view('super-admin.audit.report', compact('report'));
    }

    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'format' => 'required|in:csv,xlsx,pdf'
        ]);

        $this->auditLogger->logActivity('audit_logs_exported', [
            'new_values' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'format' => $request->format
            ],
            'category' => 'super_admin',
            'severity' => 'info'
        ]);

        $logs = AuditLog::with('user')
            ->whereBetween('timestamp', [$request->start_date, $request->end_date])
            ->orderBy('timestamp', 'desc')
            ->get();

        return $this->generateExport($logs, $request->format);
    }

    /**
     * Get audit statistics
     */
    private function getAuditStats(array $filters): array
    {
        $query = AuditLog::query();

        if (isset($filters['start_date'])) {
            $query->where('timestamp', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('timestamp', '<=', $filters['end_date']);
        }

        $logs = $query->get();

        return [
            'total_events' => $logs->count(),
            'by_severity' => $logs->groupBy('severity')->map->count(),
            'by_category' => $logs->groupBy('category')->map->count(),
            'by_user' => $logs->groupBy('user_id')->map->count(),
            'recent_security_events' => $logs->where('category', 'security')
                ->where('timestamp', '>=', now()->subDays(7))->count(),
            'failed_logins' => $logs->where('action', 'login_failed')
                ->where('timestamp', '>=', now()->subDays(7))->count(),
            'top_actions' => $logs->groupBy('action')->map->count()->sortDesc()->take(10)
        ];
    }

    /**
     * Generate export file
     */
    private function generateExport($logs, string $format)
    {
        $filename = 'audit_logs_' . now()->format('Y-m-d_H-i-s');

        switch ($format) {
            case 'csv':
                return $this->exportToCsv($logs, $filename);
            case 'xlsx':
                return $this->exportToExcel($logs, $filename);
            case 'pdf':
                return $this->exportToPdf($logs, $filename);
            default:
                abort(400, 'Invalid export format');
        }
    }

    /**
     * Export to CSV
     */
    private function exportToCsv($logs, string $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Timestamp', 'User', 'Action', 'Resource Type', 'Resource ID',
                'IP Address', 'Severity', 'Category'
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->timestamp,
                    $log->user->name ?? 'System',
                    $log->action,
                    $log->resource_type,
                    $log->resource_id,
                    $log->ip_address,
                    $log->severity,
                    $log->category
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to Excel (placeholder - would need Laravel Excel package)
     */
    private function exportToExcel($logs, string $filename)
    {
        // This would require the Laravel Excel package
        // For now, fallback to CSV
        return $this->exportToCsv($logs, $filename);
    }

    /**
     * Export to PDF (placeholder - would need PDF package)
     */
    private function exportToPdf($logs, string $filename)
    {
        // This would require a PDF package like DomPDF
        // For now, fallback to CSV
        return $this->exportToCsv($logs, $filename);
    }
}

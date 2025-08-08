<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    /**
     * Log an activity with comprehensive details
     */
    public function logActivity(string $action, array $details = []): void
    {
        try {
            AuditLog::create([
                'user_id' => auth()->id(),
                'user_type' => auth()->user()?->getTable(),
                'action' => $action,
                'resource_type' => $details['resource_type'] ?? null,
                'resource_id' => $details['resource_id'] ?? null,
                'old_values' => isset($details['old_values']) ? json_encode($details['old_values']) : null,
                'new_values' => isset($details['new_values']) ? json_encode($details['new_values']) : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId(),
                'timestamp' => now(),
                'severity' => $details['severity'] ?? 'info',
                'category' => $details['category'] ?? 'general'
            ]);
        } catch (\Exception $e) {
            // Fallback to regular logging if audit log fails
            Log::error('Failed to create audit log', [
                'action' => $action,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
        }
    }

    /**
     * Log a security event with high priority
     */
    public function logSecurityEvent(string $event, array $details = []): void
    {
        $this->logActivity($event, array_merge($details, [
            'category' => 'security',
            'severity' => 'warning'
        ]));
        
        // Trigger security alerts if necessary
        if ($this->isHighSeverityEvent($event)) {
            $this->triggerSecurityAlert($event, $details);
        }
    }

    /**
     * Log school-specific activities
     */
    public function logSchoolActivity(int $schoolId, string $action, string $description, array $metadata = []): void
    {
        $this->logActivity($action, [
            'resource_type' => 'school',
            'resource_id' => $schoolId,
            'new_values' => ['description' => $description],
            'category' => 'school_activity',
            'severity' => 'info'
        ]);
    }

    /**
     * Log API access and usage
     */
    public function logAPIAccess(string $endpoint, string $method, array $details = []): void
    {
        $this->logActivity('api_access', [
            'resource_type' => 'api_endpoint',
            'new_values' => [
                'endpoint' => $endpoint,
                'method' => $method,
                'response_code' => $details['response_code'] ?? null,
                'response_time' => $details['response_time'] ?? null
            ],
            'category' => 'api',
            'severity' => 'info'
        ]);
    }

    /**
     * Log authentication events
     */
    public function logAuthEvent(string $event, array $details = []): void
    {
        $severity = in_array($event, ['login_failed', 'account_locked', 'suspicious_login']) ? 'warning' : 'info';
        
        $this->logActivity($event, array_merge($details, [
            'category' => 'authentication',
            'severity' => $severity
        ]));
    }

    /**
     * Log data access events for compliance
     */
    public function logDataAccess(string $dataType, $resourceId, string $reason = null): void
    {
        $this->logActivity('data_access', [
            'resource_type' => $dataType,
            'resource_id' => $resourceId,
            'new_values' => ['access_reason' => $reason],
            'category' => 'data_access',
            'severity' => 'info'
        ]);
    }

    /**
     * Check if an event requires immediate security attention
     */
    private function isHighSeverityEvent(string $event): bool
    {
        $highSeverityEvents = [
            'unauthorized_access_attempt',
            'privilege_escalation_attempt',
            'data_breach_suspected',
            'multiple_failed_logins',
            'suspicious_api_usage'
        ];
        
        return in_array($event, $highSeverityEvents);
    }

    /**
     * Trigger security alert for high-severity events
     */
    private function triggerSecurityAlert(string $event, array $details): void
    {
        // Log critical security event
        Log::critical('Security Alert: ' . $event, [
            'event' => $event,
            'details' => $details,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);
        
        // Here you could add additional alerting mechanisms:
        // - Send email to security team
        // - Trigger webhook notifications
        // - Create incident tickets
        // - Send SMS alerts
    }

    /**
     * Get audit logs with filtering
     */
    public function getAuditLogs(array $filters = [], int $perPage = 50)
    {
        $query = AuditLog::with('user');
        
        if (isset($filters['start_date'])) {
            $query->where('timestamp', '>=', $filters['start_date']);
        }
        
        if (isset($filters['end_date'])) {
            $query->where('timestamp', '<=', $filters['end_date']);
        }
        
        if (isset($filters['action'])) {
            $query->where('action', 'like', '%' . $filters['action'] . '%');
        }
        
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        if (isset($filters['severity'])) {
            $query->where('severity', $filters['severity']);
        }
        
        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        
        return $query->orderBy('timestamp', 'desc')->paginate($perPage);
    }

    /**
     * Generate audit report for compliance
     */
    public function generateAuditReport(string $startDate, string $endDate, string $type = 'general'): array
    {
        $query = AuditLog::whereBetween('timestamp', [$startDate, $endDate]);
        
        if ($type !== 'general') {
            $query->where('category', $type);
        }
        
        $logs = $query->get();
        
        return [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'type' => $type,
            'total_events' => $logs->count(),
            'by_severity' => $logs->groupBy('severity')->map->count(),
            'by_category' => $logs->groupBy('category')->map->count(),
            'by_user' => $logs->groupBy('user_id')->map->count(),
            'top_actions' => $logs->groupBy('action')->map->count()->sortDesc()->take(10),
            'security_events' => $logs->where('category', 'security')->count(),
            'failed_attempts' => $logs->whereIn('action', ['login_failed', 'unauthorized_access'])->count()
        ];
    }
}

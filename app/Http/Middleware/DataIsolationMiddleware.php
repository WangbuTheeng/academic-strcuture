<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DataIsolationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for super admin routes
        if ($request->is('super-admin/*')) {
            return $next($request);
        }

        // Skip for login routes
        if ($request->is('login') || $request->is('super-admin/login')) {
            return $next($request);
        }

        // Ensure school context is set for authenticated users
        if (auth()->check()) {
            $this->ensureSchoolContext($request);
        }

        // Add query listener to ensure data isolation
        $this->addQueryListener();

        $response = $next($request);

        // Remove query listener after request
        DB::getEventDispatcher()->forget('eloquent.booted:*');

        return $response;
    }

    /**
     * Ensure school context is properly set
     */
    private function ensureSchoolContext(Request $request): void
    {
        $user = auth()->user();

        // Skip for super admins
        if ($user->hasRole('super-admin')) {
            return;
        }

        // Set school context from user's school_id
        if ($user->school_id && !session('school_context')) {
            session(['school_context' => $user->school_id]);
        }

        // Verify user belongs to the session school context
        if (session('school_context') && $user->school_id !== session('school_context')) {
            Log::warning('Data isolation violation attempt', [
                'user_id' => $user->id,
                'user_school_id' => $user->school_id,
                'session_school_context' => session('school_context'),
                'ip' => $request->ip(),
                'url' => $request->url()
            ]);

            // Force logout for security
            auth()->logout();
            session()->invalidate();
            abort(403, 'Access denied: School context mismatch');
        }
    }

    /**
     * Add query listener to monitor and enforce data isolation
     */
    private function addQueryListener(): void
    {
        DB::listen(function ($query) {
            // Skip monitoring for super admin context
            if (!session('school_context')) {
                return;
            }

            $sql = $query->sql;
            $bindings = $query->bindings;

            // List of tables that should be school-scoped
            $schoolScopedTables = [
                'students', 'teachers', 'classes', 'subjects', 'exams',
                'marks', 'fees', 'attendances', 'assignments', 'levels',
                'faculties', 'grading_scales', 'institute_settings',
                'fee_structures', 'student_bills', 'bill_items', 'payments',
                'payment_receipts', 'programs', 'student_enrollments'
            ];

            // Check if query involves school-scoped tables
            foreach ($schoolScopedTables as $table) {
                if (strpos($sql, "`{$table}`") !== false || strpos($sql, "from {$table}") !== false) {
                    // Check if school_id is in the query
                    if (strpos($sql, 'school_id') === false) {
                        Log::warning('Potential data isolation violation', [
                            'sql' => $sql,
                            'bindings' => $bindings,
                            'table' => $table,
                            'school_context' => session('school_context'),
                            'user_id' => auth()->id(),
                            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
                        ]);
                    }
                    break;
                }
            }
        });
    }
}

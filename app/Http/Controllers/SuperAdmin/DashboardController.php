<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->middleware(['auth', 'role:super-admin']);
        $this->auditLogger = $auditLogger;
    }

    public function index()
    {
        // Log dashboard access
        $this->auditLogger->logActivity('super_admin_dashboard_accessed', [
            'category' => 'super_admin',
            'severity' => 'info'
        ]);

        $stats = [
            'total_schools' => School::count(),
            'active_schools' => School::where('status', 'active')->count(),
            'inactive_schools' => School::where('status', 'inactive')->count(),
            'suspended_schools' => School::where('status', 'suspended')->count(),
            'total_users' => User::whereNotNull('school_id')->count(),
            'super_admins' => User::whereNull('school_id')->role('super-admin')->count(),
            'new_schools_this_month' => School::whereMonth('created_at', now()->month)->count(),
        ];

        $recentSchools = School::with(['creator', 'statistics'])
            ->latest()
            ->take(5)
            ->get();

        $schoolsWithStats = School::withCount(['users', 'students'])
            ->with('statistics')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('super-admin.dashboard', compact('stats', 'recentSchools', 'schoolsWithStats'));
    }
}

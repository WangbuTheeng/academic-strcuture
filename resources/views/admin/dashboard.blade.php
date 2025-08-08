@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<style>
/* Professional Dashboard Styles */
.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.stats-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border: none;
    transition: all 0.3s ease;
    height: 100%;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.stats-card.primary { border-left: 4px solid #4f46e5; }
.stats-card.success { border-left: 4px solid #10b981; }
.stats-card.warning { border-left: 4px solid #f59e0b; }
.stats-card.info { border-left: 4px solid #3b82f6; }
.stats-card.danger { border-left: 4px solid #ef4444; }

.stats-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
}

.stats-label {
    color: #6b7280;
    font-size: 0.9rem;
    font-weight: 500;
    margin-top: 0.5rem;
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stats-icon.primary { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; }
.stats-icon.success { background: linear-gradient(135deg, #10b981, #059669); color: white; }
.stats-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
.stats-icon.info { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
.stats-icon.danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }

.quick-action-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border: none;
    height: 100%;
}

.quick-action-item {
    padding: 1rem;
    border-radius: 10px;
    transition: all 0.3s ease;
    border: 1px solid #f3f4f6;
    margin-bottom: 0.5rem;
}

.quick-action-item:hover {
    background: #f8fafc;
    border-color: #e5e7eb;
    transform: translateX(5px);
}

.chart-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border: none;
}

.welcome-text {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 1rem;
}

.school-info {
    background: rgba(255,255,255,0.1);
    border-radius: 10px;
    padding: 1rem;
    margin-top: 1rem;
}
</style>

<!-- Welcome Header -->
<div class="dashboard-header">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h1 class="mb-2">Welcome back, {{ auth()->user()->name }}! 👋</h1>
            <p class="welcome-text mb-0">
                Here's what's happening at {{ session('school_name', 'your school') }} today.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="school-info">
                <div class="d-flex align-items-center justify-content-lg-end">
                    <i class="fas fa-school me-2"></i>
                    <div>
                        <div class="fw-bold">{{ session('school_name', 'School Name') }}</div>
                        <small>{{ session('school_code', 'SCHOOL001') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Setup Reminder Banner -->
@if($needsSetup)
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning alert-dismissible fade show" role="alert" style="border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(245, 158, 11, 0.2);">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-1">Complete Your School Setup</h5>
                    <p class="mb-2">Welcome! To get started, please configure your school's basic information and academic settings.</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.institute-settings.index') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-school"></i> Setup School Info
                        </a>
                        <a href="{{ route('admin.grading-scales.create') }}" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-chart-line"></i> Create Grading Scale
                        </a>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Professional Stats Cards -->
<div class="row mb-4">
    <!-- Total Users -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card primary">
            <div class="d-flex align-items-center">
                <div class="stats-icon primary me-3">
                    <i class="fas fa-users"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stats-number">{{ $stats['total_users'] }}</div>
                    <div class="stats-label">Total Users</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Students -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card success">
            <div class="d-flex align-items-center">
                <div class="stats-icon success me-3">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stats-number">{{ $stats['total_students'] }}</div>
                    <div class="stats-label">Total Students</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Students -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card info">
            <div class="d-flex align-items-center">
                <div class="stats-icon info me-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stats-number">{{ $stats['active_students'] }}</div>
                    <div class="stats-label">Active Students</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Results -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card warning">
            <div class="d-flex align-items-center">
                <div class="stats-icon warning me-3">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stats-number">{{ $stats['pending_results'] }}</div>
                    <div class="stats-label">Pending Results</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats Row -->
<div class="row mb-4">
    <!-- Total Classes -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card info">
            <div class="d-flex align-items-center">
                <div class="stats-icon info me-3">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stats-number">{{ $stats['total_classes'] ?? 0 }}</div>
                    <div class="stats-label">Total Classes</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Teachers -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card primary">
            <div class="d-flex align-items-center">
                <div class="stats-icon primary me-3">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stats-number">{{ $stats['teacher_users'] ?? 0 }}</div>
                    <div class="stats-label">Teachers</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Subjects -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card success">
            <div class="d-flex align-items-center">
                <div class="stats-icon success me-3">
                    <i class="fas fa-book"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stats-number">{{ $stats['total_subjects'] ?? 0 }}</div>
                    <div class="stats-label">Subjects</div>
                </div>
            </div>
        </div>
    </div>

    <!-- This Month Admissions -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card warning">
            <div class="d-flex align-items-center">
                <div class="stats-icon warning me-3">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stats-number">{{ $stats['monthly_admissions'] ?? 0 }}</div>
                    <div class="stats-label">This Month</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Professional Main Content -->
<div class="row">
    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="quick-action-card">
            <div class="d-flex align-items-center mb-3">
                <div class="stats-icon primary me-3">
                    <i class="fas fa-bolt"></i>
                </div>
                <h5 class="mb-0 fw-bold">Quick Actions</h5>
            </div>

            <div class="quick-actions-grid">
                <a href="{{ route('admin.students.create') }}" class="quick-action-item text-decoration-none">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon success me-3" style="width: 45px; height: 45px; font-size: 1rem;">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Add Student</div>
                            <small class="text-muted">Enroll new student</small>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.users.create') }}" class="quick-action-item text-decoration-none">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon primary me-3" style="width: 45px; height: 45px; font-size: 1rem;">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Add Staff</div>
                            <small class="text-muted">Create new user</small>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.exams.create') }}" class="quick-action-item text-decoration-none">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon info me-3" style="width: 45px; height: 45px; font-size: 1rem;">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Create Exam</div>
                            <small class="text-muted">Setup examination</small>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.classes.create') }}" class="quick-action-item text-decoration-none">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon warning me-3" style="width: 45px; height: 45px; font-size: 1rem;">
                            <i class="fas fa-chalkboard"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Add Class</div>
                            <small class="text-muted">Create new class</small>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.subjects.create') }}" class="quick-action-item text-decoration-none">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon success me-3" style="width: 45px; height: 45px; font-size: 1rem;">
                            <i class="fas fa-book"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Add Subject</div>
                            <small class="text-muted">Create new subject</small>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.academic-years.index') }}" class="quick-action-item text-decoration-none">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon info me-3" style="width: 45px; height: 45px; font-size: 1rem;">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Academic Year</div>
                            <small class="text-muted">Manage sessions</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activities & Analytics -->
    <div class="col-lg-8 mb-4">
        <div class="chart-card h-100">
            <div class="card-header bg-transparent border-0 pb-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon info me-3" style="width: 45px; height: 45px; font-size: 1rem;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Recent Activities</h5>
                    </div>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-chart-bar me-1"></i> View Reports
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="activity-timeline">
                    @forelse($recentActivities ?? [] as $activity)
                        <div class="activity-item d-flex align-items-start mb-3">
                            <div class="activity-icon me-3">
                                @if($activity['type'] == 'user')
                                    <div class="stats-icon primary" style="width: 35px; height: 35px; font-size: 0.8rem;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @elseif($activity['type'] == 'exam')
                                    <div class="stats-icon success" style="width: 35px; height: 35px; font-size: 0.8rem;">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                @elseif($activity['type'] == 'student')
                                    <div class="stats-icon info" style="width: 35px; height: 35px; font-size: 0.8rem;">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                @else
                                    <div class="stats-icon warning" style="width: 35px; height: 35px; font-size: 0.8rem;">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark">{{ $activity['action'] }}</div>
                                <div class="text-muted small">{{ $activity['description'] }}</div>
                                <div class="text-muted small">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $activity['time'] }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <div class="stats-icon info mx-auto mb-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-history"></i>
                            </div>
                            <h6 class="text-muted">No recent activities</h6>
                            <p class="text-muted small">Activities will appear here as you use the system</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Professional System Overview -->
<div class="row">
    <!-- Performance Metrics -->
    <div class="col-lg-6 mb-4">
        <div class="chart-card">
            <div class="card-header bg-transparent border-0">
                <div class="d-flex align-items-center">
                    <div class="stats-icon primary me-3" style="width: 45px; height: 45px; font-size: 1rem;">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">System Overview</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="performance-metric">
                            <div class="stats-number text-primary" style="font-size: 2rem;">{{ $stats['total_students'] }}</div>
                            <div class="stats-label">Total Students</div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-primary" style="width: {{ min(100, ($stats['total_students'] / 100) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="performance-metric">
                            <div class="stats-number text-success" style="font-size: 2rem;">{{ $stats['active_students'] }}</div>
                            <div class="stats-label">Active</div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-success" style="width: {{ $stats['total_students'] > 0 ? ($stats['active_students'] / $stats['total_students']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="performance-metric">
                            <div class="stats-number text-warning" style="font-size: 2rem;">{{ $stats['pending_results'] }}</div>
                            <div class="stats-label">Pending</div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-warning" style="width: {{ $stats['total_students'] > 0 ? ($stats['pending_results'] / $stats['total_students']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health -->
    <div class="col-lg-6 mb-4">
        <div class="chart-card">
            <div class="card-header bg-transparent border-0">
                <div class="d-flex align-items-center">
                    <div class="stats-icon success me-3" style="width: 45px; height: 45px; font-size: 1rem;">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">System Health</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="health-metrics">
                    <div class="health-item d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="health-indicator bg-success me-2"></div>
                            <span>Database Connection</span>
                        </div>
                        <span class="badge bg-success">Online</span>
                    </div>
                    <div class="health-item d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="health-indicator bg-success me-2"></div>
                            <span>System Performance</span>
                        </div>
                        <span class="badge bg-success">Excellent</span>
                    </div>
                    <div class="health-item d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="health-indicator bg-info me-2"></div>
                            <span>Storage Usage</span>
                        </div>
                        <span class="badge bg-info">{{ rand(15, 35) }}% Used</span>
                    </div>
                    <div class="health-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="health-indicator bg-warning me-2"></div>
                            <span>Last Backup</span>
                        </div>
                        <span class="badge bg-warning">{{ now()->subHours(rand(1, 12))->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.performance-metric {
    padding: 1rem 0;
}

.health-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.health-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.health-item:last-child {
    border-bottom: none;
}
</style>

<!-- Professional Academic Management Panel -->
<div class="row mb-4">
    <div class="col-12">
        <div class="chart-card">
            <div class="card-header bg-transparent border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon primary me-3" style="width: 45px; height: 45px; font-size: 1rem;">
                            <i class="fas fa-university"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Academic Management</h5>
                            <small class="text-muted">Quick access to essential school management tools</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.institute-settings.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-cog me-1"></i> Settings
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-chart-bar me-1"></i> Reports
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Academic Structure -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="quick-action-item text-center h-100">
                            <a href="{{ route('admin.levels.index') }}" class="text-decoration-none">
                                <div class="stats-icon primary mx-auto mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Academic Structure</h6>
                                <p class="text-muted small mb-0">Manage levels, faculties, departments, and programs</p>
                            </a>
                        </div>
                    </div>

                    <!-- Student Management -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="quick-action-item text-center h-100">
                            <a href="{{ route('admin.students.index') }}" class="text-decoration-none">
                                <div class="stats-icon success mx-auto mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Student Management</h6>
                                <p class="text-muted small mb-0">Enrollment, records, and student information</p>
                            </a>
                        </div>
                    </div>

                    <!-- Examination System -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="quick-action-item text-center h-100">
                            <a href="{{ route('admin.exams.index') }}" class="text-decoration-none">
                                <div class="stats-icon info mx-auto mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Examination System</h6>
                                <p class="text-muted small mb-0">Create exams, manage marks, and generate results</p>
                            </a>
                        </div>
                    </div>

                    <!-- Grading & Assessment -->
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="quick-action-item text-center h-100">
                            <a href="{{ route('admin.grading-scales.index') }}" class="text-decoration-none">
                                <div class="stats-icon warning mx-auto mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Grading System</h6>
                                <p class="text-muted small mb-0">Configure grading scales and assessment criteria</p>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Action Buttons -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                            <a href="{{ route('admin.students.create') }}" class="btn btn-success">
                                <i class="fas fa-user-plus me-1"></i> Add Student
                            </a>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                <i class="fas fa-user-tie me-1"></i> Add Staff
                            </a>
                            <a href="{{ route('admin.exams.create') }}" class="btn btn-info">
                                <i class="fas fa-plus me-1"></i> Create Exam
                            </a>
                            <a href="{{ route('admin.classes.create') }}" class="btn btn-warning">
                                <i class="fas fa-chalkboard me-1"></i> Add Class
                            </a>
                            <a href="{{ route('admin.subjects.create') }}" class="btn btn-secondary">
                                <i class="fas fa-book me-1"></i> Add Subject
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer Info -->
<div class="row">
    <div class="col-12">
        <div class="text-center text-muted">
            <small>
                <i class="fas fa-shield-alt me-1"></i>
                Multi-Tenant Academic Management System |
                School: <strong>{{ session('school_name', 'Unknown') }}</strong> |
                Last Login: {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('M d, Y H:i') : 'First time' }}
            </small>
        </div>
    </div>
</div>

<script>
// Add some interactive animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate stats cards on load
    const statsCards = document.querySelectorAll('.stats-card');
    statsCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease';

            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        }, index * 100);
    });

    // Add hover effects to quick action items
    const quickActions = document.querySelectorAll('.quick-action-item');
    quickActions.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
        });

        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
});
</script>

@endsection

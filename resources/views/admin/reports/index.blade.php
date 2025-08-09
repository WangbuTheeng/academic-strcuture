@extends('layouts.admin')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')

@section('content')
<div class="container-fluid">
    <!-- Sub-Navigation -->
    @include('admin.reports.partials.sub-navbar')

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Reports & Analytics</h1>
            <p class="mb-0 text-muted">Comprehensive academic performance insights and reporting tools</p>
        </div>
        <div>
            <a href="{{ route('admin.marksheets.index') }}" class="btn btn-primary">
                <i class="fas fa-file-alt"></i> Generate Marksheets
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Students</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_students']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Exams</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_exams']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Published Results</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['published_results']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Marks Entered</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_marks_entered']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Marksheet Generation -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-certificate"></i> Quick Marksheet Generation
            </h6>
            <a href="{{ route('admin.marksheets.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-cog"></i> Advanced Options
            </a>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">
                <i class="fas fa-info-circle text-primary"></i>
                Generate marksheets quickly for published exams. Select an exam and class to generate bulk marksheets instantly.
            </p>

            @if($publishedExams->count() > 0)
                <div class="row">
                    @foreach($publishedExams->take(6) as $exam)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border-left-success h-100">
                                <div class="card-body">
                                    <h6 class="card-title text-success">{{ $exam->name }}</h6>
                                    <p class="card-text text-muted small">
                                        <i class="fas fa-calendar"></i> {{ $exam->academicYear->name ?? 'N/A' }}<br>
                                        <i class="fas fa-graduation-cap"></i> {{ $exam->class->name ?? 'All Classes' }}<br>
                                        <i class="fas fa-book"></i> {{ $exam->subject->name ?? 'All Subjects' }}
                                    </p>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.marksheets.create', ['exam' => $exam->id]) }}"
                                           class="btn btn-success btn-sm btn-block">
                                            <i class="fas fa-certificate"></i> Generate Marksheets
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($publishedExams->count() > 6)
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.marksheets.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus"></i> View All {{ $publishedExams->count() }} Published Exams
                        </a>
                    </div>
                @endif
            @else
                <div class="text-center py-4">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Published Exams</h5>
                    <p class="text-muted">Publish exam results to generate marksheets.</p>
                    <a href="{{ route('admin.exams.index') }}" class="btn btn-primary">
                        <i class="fas fa-clipboard-list"></i> Manage Exams
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Performance Overview -->
    @if(!empty($performanceData))
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Current Academic Year Performance Overview</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Overall Pass Rate -->
                <div class="col-md-4 text-center mb-3">
                    <div class="h2 mb-2 text-success">{{ $performanceData['pass_rate'] }}%</div>
                    <div class="text-muted">Overall Pass Rate</div>
                    <small class="text-muted">{{ number_format($performanceData['total_marks']) }} total marks</small>
                </div>

                <!-- Grade Distribution -->
                <div class="col-md-4 mb-3">
                    <h6 class="font-weight-bold text-gray-800 mb-3">Grade Distribution</h6>
                    @if(!empty($performanceData['grade_distribution']))
                        @foreach($performanceData['grade_distribution'] as $grade => $count)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="font-weight-bold">Grade {{ $grade }}</span>
                                <span class="badge bg-primary">{{ $count }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No grade data available</p>
                    @endif
                </div>

                <!-- Top Classes -->
                <div class="col-md-4 mb-3">
                    <h6 class="font-weight-bold text-gray-800 mb-3">Top Performing Classes</h6>
                    @if(!empty($performanceData['top_classes']))
                        @foreach($performanceData['top_classes']->take(3) as $className => $classData)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="font-weight-bold">{{ $className }}</span>
                                <span class="badge bg-success">{{ number_format($classData['average_percentage'], 1) }}%</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No class data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Report Categories -->
    <div class="row mb-4">
        <!-- Academic Reports -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3">
                            <div class="bg-primary rounded p-2">
                                <i class="fas fa-chart-bar fa-lg text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">Academic Reports</h5>
                            <p class="card-text text-muted small">Performance analysis and academic insights</p>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.reports.academic') }}" class="btn btn-outline-primary">
                            <i class="fas fa-graduation-cap me-2"></i>Academic Performance Report
                        </a>
                        <a href="{{ route('admin.reports.class-performance') }}" class="btn btn-outline-primary">
                            <i class="fas fa-chalkboard me-2"></i>Class Performance Analysis
                        </a>
                        <a href="#" class="btn btn-outline-primary" onclick="alert('Subject Analysis coming soon!')">
                            <i class="fas fa-book me-2"></i>Subject-wise Analysis
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Reports -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3">
                            <div class="bg-success rounded p-2">
                                <i class="fas fa-user-graduate fa-lg text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">Student Reports</h5>
                            <p class="card-text text-muted small">Individual progress and transcripts</p>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.reports.student-progress') }}" class="btn btn-outline-success">
                            <i class="fas fa-chart-line me-2"></i>Student Progress Report
                        </a>
                        <a href="{{ route('admin.marksheets.index') }}" class="btn btn-outline-success">
                            <i class="fas fa-file-alt me-2"></i>Generate Marksheets
                        </a>
                        <button class="btn btn-outline-success" disabled>
                            <i class="fas fa-certificate me-2"></i>Transcript Generation (Coming Soon)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Reports -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3">
                            <div class="bg-info rounded p-2">
                                <i class="fas fa-cogs fa-lg text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">Custom Reports</h5>
                            <p class="card-text text-muted small">Build your own reports with filters</p>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.reports.custom') }}" class="btn btn-outline-info">
                            <i class="fas fa-tools me-2"></i>Custom Report Builder
                        </a>
                        <button class="btn btn-outline-info" disabled>
                            <i class="fas fa-clock me-2"></i>Scheduled Reports (Coming Soon)
                        </button>
                        <button class="btn btn-outline-info" disabled>
                            <i class="fas fa-file-code me-2"></i>Report Templates (Coming Soon)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent Exam Activity</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Exam</th>
                            <th>Academic Year</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentExams ?? [] as $exam)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $exam->name }}</div>
                                    <small class="text-muted">{{ $exam->getTypeLabel() ?? 'Exam' }}</small>
                                </td>
                                <td>{{ $exam->academicYear->name ?? 'N/A' }}</td>
                                <td>{{ $exam->class->name ?? 'All Classes' }}</td>
                                <td>{{ $exam->subject->name ?? 'All Subjects' }}</td>
                                <td>
                                    @php
                                        $statusClass = match($exam->status ?? 'ongoing') {
                                            'published' => 'success',
                                            'ongoing' => 'primary',
                                            'completed' => 'info',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">{{ ucfirst($exam->status ?? 'Ongoing') }}</span>
                                </td>
                                <td>
                                    @if(($exam->status ?? '') === 'published')
                                        <a href="{{ route('admin.marksheets.create', ['exam_id' => $exam->id]) }}"
                                           class="btn btn-sm btn-primary">
                                            Generate Marksheets
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.exams.show', $exam) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-center">
                                        <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                                        <h5 class="text-gray-600">No recent exams</h5>
                                        <p class="text-muted">There are no recent exam activities to display.</p>
                                        <a href="{{ route('admin.exams.index') }}" class="btn btn-primary">
                                            View All Exams
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* Ensure text visibility */
.text-gray-800, .text-gray-600 {
    color: #2d3748 !important;
}

.text-muted {
    color: #6c757d !important;
}

.text-primary {
    color: #4e73df !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

/* Ensure all text in cards is visible */
.card-body, .card-header, .table {
    color: #2d3748 !important;
}

/* Table text */
.table td, .table th {
    color: #2d3748 !important;
}

.fw-bold {
    color: #2d3748 !important;
}

</style>
@endsection

@extends('layouts.admin')

@section('title', 'Class Performance Analysis')
@section('page-title', 'Class Performance Analysis')

@section('content')
<div class="container-fluid">
    <!-- Sub-Navigation -->
    @include('admin.reports.partials.sub-navbar')

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Class Performance Analysis</h1>
            <p class="mb-0 text-muted">Detailed performance analysis for specific class and exam combinations</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>

    <!-- Selection Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Select Class and Exam</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.class-performance') }}">
                <div class="row">
                    <!-- Class Selection -->
                    <div class="col-md-6 mb-3">
                        <label for="class_id" class="form-label">Class</label>
                        <select name="class_id" id="class_id" required class="form-control">
                            <option value="">Select a Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} ({{ $class->level->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Exam Selection -->
                    <div class="col-md-6 mb-3">
                        <label for="exam_id" class="form-label">Exam</label>
                        <select name="exam_id" id="exam_id" required class="form-control">
                            <option value="">Select an Exam</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                    {{ $exam->name }} ({{ $exam->academicYear->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-chart-bar"></i> Analyze Performance
                    </button>
                    <a href="{{ route('admin.reports.class-performance') }}" class="btn btn-secondary">
                        Clear Selection
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Instructions -->
    <div class="alert alert-info">
        <div class="d-flex align-items-start">
            <i class="fas fa-info-circle text-info mt-1 me-3"></i>
            <div>
                <h6 class="alert-heading">How to Use Class Performance Analysis</h6>
                <p class="mb-0">
                    Select a specific class and exam to view detailed performance analytics including completion rates,
                    grade distributions, subject-wise averages, and individual student performance metrics.
                </p>
            </div>
        </div>
    </div>

    <!-- No Selection Message -->
    @if(!request('class_id') || !request('exam_id'))
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-chart-bar fa-4x text-gray-300 mb-4"></i>
                <h5 class="text-gray-800 mb-3">Class Performance Analysis</h5>
                <p class="text-muted mb-4">
                    Select a class and exam from the form above to generate a comprehensive performance analysis report.
                </p>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span class="text-muted">Completion Rates</span>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="fas fa-chart-pie text-primary me-2"></i>
                            <span class="text-muted">Grade Distribution</span>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="fas fa-chart-line text-info me-2"></i>
                            <span class="text-muted">Performance Trends</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Stats for Available Data -->
    @if($classes->count() > 0 && $exams->count() > 0)
        <div class="row mt-4">
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Available Classes</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $classes->count() }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-school fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Published Exams</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $exams->count() }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Analysis Ready</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $classes->count() * $exams->count() }}</div>
                                <div class="text-xs text-muted">Possible combinations</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
/* Ensure text visibility */
.text-gray-800, h1, h2, h3, h4, h5, h6 {
    color: #2d3748 !important;
}

.text-muted {
    color: #6c757d !important;
}

.text-primary {
    color: #4e73df !important;
}

.text-success {
    color: #1cc88a !important;
}

.text-info {
    color: #36b9cc !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

/* Ensure all text in cards is visible */
.card-body, .card-header, .form-label, label {
    color: #2d3748 !important;
}

.text-xs {
    color: #6c757d !important;
}
</style>
@endsection

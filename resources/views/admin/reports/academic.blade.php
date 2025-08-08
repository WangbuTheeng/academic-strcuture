@extends('layouts.admin')

@section('title', 'Academic Performance Report')
@section('page-title', 'Academic Performance Report')

@section('content')
<div class="container-fluid">
    <!-- Sub-Navigation -->
    @include('admin.reports.partials.sub-navbar')

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Academic Performance Report</h1>
            <p class="mb-0 text-muted">Comprehensive analysis of academic performance across different parameters</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Report Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.academic') }}">
                <div class="row">
                    <!-- Academic Year Filter -->
                    <div class="col-md-4 mb-3">
                        <label for="academic_year" class="form-label">Academic Year</label>
                        <select name="academic_year" id="academic_year" class="form-control">
                            <option value="">All Academic Years</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ $selectedYear == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Class Filter -->
                    <div class="col-md-4 mb-3">
                        <label for="class" class="form-label">Class</label>
                        <select name="class" id="class" class="form-control">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $selectedClass == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} ({{ $class->level->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Subject Filter -->
                    <div class="col-md-4 mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <select name="subject" id="subject" class="form-control">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ $selectedSubject == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }} ({{ $subject->department->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-chart-bar"></i> Generate Report
                    </button>
                    <a href="{{ route('admin.reports.academic') }}" class="btn btn-secondary">
                        Clear Filters
                    </a>
                    @if(!empty($data))
                        <button type="button" onclick="window.print()" class="btn btn-success">
                            <i class="fas fa-print"></i> Print Report
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if(!empty($data))
        <!-- Report Results -->
        <!-- Summary Statistics -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Students</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($data['total_students']) }}</div>
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
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Marks Entries</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($data['total_marks']) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Average Percentage</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($data['average_percentage'], 1) }}%</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-percentage fa-2x text-gray-300"></i>
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
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pass Rate</div>
                                @php
                                    $passRate = $data['total_marks'] > 0 ? ($data['pass_count'] / $data['total_marks']) * 100 : 0;
                                @endphp
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($passRate, 1) }}%</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pass/Fail Analysis -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Pass/Fail Distribution</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-success font-weight-bold">Passed</span>
                                <span class="text-muted">{{ number_format($data['pass_count']) }}</span>
                            </div>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ $data['total_marks'] > 0 ? ($data['pass_count'] / $data['total_marks']) * 100 : 0 }}%"
                                     aria-valuenow="{{ $data['pass_count'] }}"
                                     aria-valuemin="0"
                                     aria-valuemax="{{ $data['total_marks'] }}">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-danger font-weight-bold">Failed</span>
                                <span class="text-muted">{{ number_format($data['fail_count']) }}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-danger" role="progressbar"
                                     style="width: {{ $data['total_marks'] > 0 ? ($data['fail_count'] / $data['total_marks']) * 100 : 0 }}%"
                                     aria-valuenow="{{ $data['fail_count'] }}"
                                     aria-valuemin="0"
                                     aria-valuemax="{{ $data['total_marks'] }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grade Distribution -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Grade Distribution</h6>
                    </div>
                    <div class="card-body">
                        @if(!empty($data['grade_distribution']))
                            @foreach($data['grade_distribution'] as $grade => $count)
                                    <span class="font-weight-bold">Grade {{ $grade }}</span>
                                    <span class="text-muted">{{ $count }}</span>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-primary" role="progressbar"
                                         style="width: {{ $data['total_marks'] > 0 ? ($count / $data['total_marks']) * 100 : 0 }}%">
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No grade data available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Subject Performance -->
        @if(!empty($data['subject_performance']))
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Subject-wise Performance</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Total Marks</th>
                                <th>Average Percentage</th>
                                <th>Performance</th>
                            </tr>
                        <tbody>
                            @foreach($data['subject_performance'] as $subjectName => $performance)
                                <tr>
                                    <td class="font-weight-bold">{{ $subjectName }}</td>
                                    <td>{{ $performance['count'] }}</td>
                                    <td>{{ number_format($performance['average'], 1) }}%</td>
                                    <td>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary" role="progressbar"
                                                 style="width: {{ $performance['average'] }}%">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    @else
        <!-- No Data Message -->
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-chart-bar fa-3x text-gray-300 mb-4"></i>
                <h5 class="text-gray-800 mb-3">No Data Available</h5>
                <p class="text-muted mb-4">Please select filters above to generate an academic performance report.</p>
                <p class="text-muted small">Choose an academic year, class, or subject to see detailed performance analytics.</p>
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

    .text-warning {
        color: #f6c23e !important;
    }

    .text-info {
        color: #36b9cc !important;
    }

    .text-danger {
        color: #e74a3b !important;
    }

    .text-gray-300 {
        color: #dddfeb !important;
    }

    /* Ensure all text in cards is visible */
    .card-body, .card-header, .table {
        color: #2d3748 !important;
    }

    /* Form labels and text */
    .form-label, label {
        color: #2d3748 !important;
    }

    /* Table text */
    .table td, .table th {
        color: #2d3748 !important;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        body {
            margin: 0;
            padding: 0;
        }

        .card {
            box-shadow: none !important;
        }

        /* Ensure print text is black */
        * {
            color: #000 !important;
        }
    }
</style>
@endsection

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
            <h1 class="h3 mb-0 text-gray-800">{{ $class->name }} Performance Analysis</h1>
            <p class="mb-0 text-muted">{{ $exam->name }} - {{ $exam->academicYear->name ?? 'N/A' }}</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.class-performance') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Selection
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Students</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $analytics['total_students'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pass Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($analytics['pass_rate'] ?? 0, 1) }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Average Score</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($analytics['average_score'] ?? 0, 1) }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Highest Score</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($analytics['highest_score'] ?? 0, 1) }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trophy fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade Distribution -->
    @if(isset($analytics['grade_distribution']))
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Grade Distribution</h6>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($analytics['grade_distribution'] as $grade => $count)
                <div class="col-md-2 mb-3">
                    <div class="text-center">
                        <div class="h4 font-weight-bold text-primary">{{ $count }}</div>
                        <div class="text-muted">Grade {{ $grade }}</div>
                        <div class="progress mt-2">
                            <div class="progress-bar" style="width: {{ ($count / $analytics['total_students']) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Subject-wise Performance -->
    @if(isset($analytics['subject_performance']))
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Subject-wise Performance</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Students</th>
                            <th>Average</th>
                            <th>Pass Rate</th>
                            <th>Highest</th>
                            <th>Lowest</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($analytics['subject_performance'] as $subject)
                        <tr>
                            <td>{{ $subject['name'] }}</td>
                            <td>{{ $subject['student_count'] }}</td>
                            <td>{{ number_format($subject['average'], 1) }}%</td>
                            <td>
                                <span class="badge badge-{{ $subject['pass_rate'] >= 80 ? 'success' : ($subject['pass_rate'] >= 60 ? 'warning' : 'danger') }}">
                                    {{ number_format($subject['pass_rate'], 1) }}%
                                </span>
                            </td>
                            <td>{{ number_format($subject['highest'], 1) }}%</td>
                            <td>{{ number_format($subject['lowest'], 1) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Student Performance List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Individual Student Performance</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="studentPerformanceTable">
                    <thead>
                        <tr>
                            <th>Roll No</th>
                            <th>Student Name</th>
                            <th>Total Marks</th>
                            <th>Percentage</th>
                            <th>Grade</th>
                            <th>Result</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($marks->groupBy('student_id') as $studentId => $studentMarks)
                        @php
                            $student = $studentMarks->first()->student;
                            $totalMarks = $studentMarks->sum('total_marks');
                            $maxMarks = $studentMarks->sum(function($mark) { return $mark->exam->max_marks; });
                            $percentage = $maxMarks > 0 ? ($totalMarks / $maxMarks) * 100 : 0;
                            $overallResult = $studentMarks->contains('result', 'Fail') ? 'Fail' : 'Pass';
                        @endphp
                        <tr>
                            <td>{{ $student->currentEnrollment->roll_no ?? 'N/A' }}</td>
                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td>{{ number_format($totalMarks, 1) }}/{{ number_format($maxMarks, 0) }}</td>
                            <td>{{ number_format($percentage, 1) }}%</td>
                            <td>
                                <span class="badge badge-primary">{{ $studentMarks->first()->grade ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $overallResult === 'Pass' ? 'success' : 'danger' }}">
                                    {{ $overallResult }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.marksheets.create', ['exam_id' => $exam->id, 'student_id' => $student->id]) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-file-alt"></i> Marksheet
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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

.text-warning {
    color: #f6c23e !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

/* Ensure all text in cards and tables is visible */
.card-body, .card-header, .table {
    color: #2d3748 !important;
}

.table td, .table th {
    color: #2d3748 !important;
}

.text-xs {
    color: #6c757d !important;
}

@media print {
    .btn, .card-header, .no-print {
        display: none !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    /* Ensure print text is black */
    * {
        color: #000 !important;
    }
}
</style>
@endsection

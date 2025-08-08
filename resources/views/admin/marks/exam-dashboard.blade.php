@extends('layouts.admin')

@section('title', 'Mark Entry Dashboard - ' . $exam->name)
@section('page-title', 'Mark Entry Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.marks.index') }}">Mark Entry</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $exam->name }}</li>
        </ol>
    </nav>

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Mark Entry Dashboard</h1>
            <p class="mb-0 text-muted">{{ $exam->name }} - {{ $exam->academicYear->name ?? 'N/A' }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.marks.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Marks
            </a>
            <a href="{{ route('admin.marks.show', $exam) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View Progress
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Exam Information Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ $exam->name }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Academic Year:</strong> {{ $exam->academicYear->name ?? 'N/A' }}
                </div>
                <div class="col-md-3">
                    <strong>Class:</strong> {{ $exam->class->name ?? 'N/A' }}
                </div>
                <div class="col-md-3">
                    <strong>Max Marks:</strong> {{ $exam->max_marks }}
                </div>
                <div class="col-md-3">
                    <strong>Status:</strong> 
                    <span class="badge badge-{{ $exam->status === 'ongoing' ? 'success' : ($exam->status === 'completed' ? 'primary' : 'warning') }}">
                        {{ ucfirst($exam->status) }}
                    </span>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3">
                    <strong>Start Date:</strong> {{ $exam->start_date?->format('M d, Y') ?? 'Not Set' }}
                </div>
                <div class="col-md-3">
                    <strong>End Date:</strong> {{ $exam->end_date?->format('M d, Y') ?? 'Not Set' }}
                </div>
                <div class="col-md-3">
                    <strong>Theory Max:</strong> {{ $exam->theory_max }}
                </div>
                <div class="col-md-3">
                    <strong>Practical Max:</strong> {{ $exam->practical_max }}
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Progress Summary -->
    <div class="row mb-4">
        @php
            $totalSubjects = count($subjectStatuses);
            $completedSubjects = collect($subjectStatuses)->where('is_complete', true)->count();
            $inProgressSubjects = collect($subjectStatuses)->where('marks_entered', '>', 0)->where('is_complete', false)->count();
            $notStartedSubjects = collect($subjectStatuses)->where('marks_entered', 0)->count();
            $overallProgress = $totalSubjects > 0 ? round(($completedSubjects / $totalSubjects) * 100, 1) : 0;
        @endphp

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Subjects</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSubjects }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedSubjects }}</div>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">In Progress</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inProgressSubjects }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Overall Progress</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $overallProgress }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $overallProgress }}%" aria-valuenow="{{ $overallProgress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subjects for Mark Entry -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Subjects - Mark Entry Status</h6>
            <small class="text-muted">Click on a subject to enter or edit marks for that subject.</small>
        </div>
        <div class="card-body">
            @if(count($subjectStatuses) > 0)
                <div class="row">
                    @foreach($subjectStatuses as $status)
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="card border-left-{{ $status['is_complete'] ? 'success' : 'warning' }} shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-{{ $status['is_complete'] ? 'success' : 'warning' }} text-uppercase mb-1">
                                                {{ $status['subject']->name }}
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $status['marks_entered'] }}/{{ $status['total_students'] }}
                                            </div>
                                            <div class="text-xs text-gray-600">
                                                Students with marks entered
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="progress" style="width: 60px; height: 60px;">
                                                <div class="progress-bar bg-{{ $status['is_complete'] ? 'success' : 'warning' }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $status['completion_percentage'] }}%"
                                                     aria-valuenow="{{ $status['completion_percentage'] }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            <div class="text-center mt-1">
                                                <small class="text-muted">{{ $status['completion_percentage'] }}%</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <div class="btn-group w-100" role="group">
                                            <a href="{{ route('admin.marks.create', ['exam_id' => $exam->id, 'subject_id' => $status['subject']->id]) }}" 
                                               class="btn btn-{{ $status['marks_entered'] > 0 ? 'warning' : 'primary' }} btn-sm">
                                                <i class="fas fa-{{ $status['marks_entered'] > 0 ? 'edit' : 'plus' }}"></i>
                                                {{ $status['marks_entered'] > 0 ? 'Edit Marks' : 'Enter Marks' }}
                                            </a>
                                            
                                            @if($status['marks_entered'] > 0)
                                                <a href="{{ route('admin.marks.show', $exam) }}?subject_id={{ $status['subject']->id }}" 
                                                   class="btn btn-info btn-sm" title="View Marks">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($status['is_complete'])
                                        <div class="mt-2">
                                            <span class="badge badge-success w-100">
                                                <i class="fas fa-check"></i> Complete
                                            </span>
                                        </div>
                                    @elseif($status['marks_entered'] > 0)
                                        <div class="mt-2">
                                            <span class="badge badge-warning w-100">
                                                <i class="fas fa-clock"></i> In Progress
                                            </span>
                                        </div>
                                    @else
                                        <div class="mt-2">
                                            <span class="badge badge-secondary w-100">
                                                <i class="fas fa-hourglass-start"></i> Not Started
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-book fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No Subjects Found</h5>
                    <p class="text-muted">No subjects are available for this exam's class.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <a href="{{ route('admin.marks.show', $exam) }}" class="btn btn-info btn-block">
                        <i class="fas fa-chart-bar"></i> View Progress Report
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-info-circle"></i> Exam Details
                    </a>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-warning btn-block" onclick="alert('Feature coming soon!')">
                        <i class="fas fa-file-export"></i> Export Marks
                    </button>
                </div>
                <div class="col-md-3">
                    @php
                        $draftMarks = $exam->marks()->where('status', 'draft')->count();
                        $submittedMarks = $exam->marks()->where('status', 'submitted')->count();
                        $approvedMarks = $exam->marks()->where('status', 'approved')->count();
                    @endphp

                    @if($draftMarks > 0)
                        <button class="btn btn-warning btn-block" onclick="submitAllMarks()">
                            <i class="fas fa-paper-plane"></i> Submit All Marks ({{ $draftMarks }})
                        </button>
                    @elseif($submittedMarks > 0)
                        <button class="btn btn-success btn-block" onclick="approveAllMarks()">
                            <i class="fas fa-check-double"></i> Approve All Marks ({{ $submittedMarks }})
                        </button>
                    @elseif($approvedMarks > 0)
                        <a href="{{ route('admin.marksheets.create', ['exam' => $exam->id]) }}" class="btn btn-primary btn-block">
                            <i class="fas fa-certificate"></i> Generate Marksheets
                        </a>
                    @else
                        <button class="btn btn-secondary btn-block" disabled>
                            <i class="fas fa-hourglass-start"></i> No Marks to Submit
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Add any JavaScript functionality here if needed

    // Auto-refresh every 30 seconds to update progress
    setTimeout(function() {
        location.reload();
    }, 30000);
});

function submitAllMarks() {
    // Check if all subjects have marks entered
    const totalSubjects = {{ count($subjectStatuses) }};
    const completedSubjects = {{ collect($subjectStatuses)->where('marks_entered', '>', 0)->count() }};

    if (completedSubjects < totalSubjects) {
        alert('Please enter marks for all subjects before submitting.');
        return;
    }

    if (confirm('Are you sure you want to submit all marks for this exam?\n\nOnce submitted, marks cannot be edited without approval.\n\nThis action will submit marks for all subjects and students.')) {
        // Create and submit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.marks.submit") }}';

        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        // Add exam_id
        const examId = document.createElement('input');
        examId.type = 'hidden';
        examId.name = 'exam_id';
        examId.value = '{{ $exam->id }}';
        form.appendChild(examId);

        document.body.appendChild(form);
        form.submit();
    }
}

function approveAllMarks() {
    if (confirm('Are you sure you want to approve all submitted marks for this exam?\n\nThis will make the marks final and allow marksheet generation.')) {
        // Get all submitted mark IDs
        fetch('{{ route("admin.marks.get-submitted", $exam) }}')
            .then(response => response.json())
            .then(data => {
                if (data.mark_ids && data.mark_ids.length > 0) {
                    // Create and submit form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("admin.marks.approve") }}';

                    // Add CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    // Add mark IDs
                    data.mark_ids.forEach(markId => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'mark_ids[]';
                        input.value = markId;
                        form.appendChild(input);
                    });

                    document.body.appendChild(form);
                    form.submit();
                } else {
                    alert('No submitted marks found to approve.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error fetching marks to approve.');
            });
    }
}
</script>
@endpush
@endsection

@extends('layouts.admin')

@section('title', $exam->name)
@section('page-title', 'Exam Details')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $exam->name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.exams.index') }}">Examinations</a></li>
                    <li class="breadcrumb-item active">{{ $exam->name }}</li>
                </ol>
            </nav>
            <div class="text-muted">
                @if($exam->program)
                    <span class="badge bg-primary me-2">{{ $exam->program->name }}</span>
                @endif
                @if($exam->class)
                    <span class="badge bg-info me-2">{{ $exam->class->name }}</span>
                @endif
                @if($exam->subject)
                    <span class="badge bg-success">{{ $exam->subject->name }}</span>
                @else
                    <span class="badge bg-secondary">All Subjects</span>
                @endif
            </div>
        </div>
        <div>
            @if($exam->is_editable)
                <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit"></i> Edit Exam
                </a>
            @endif
            <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Exams
            </a>
        </div>
    </div>

    <!-- Exam Details Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Exam Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">Exam Type</label>
                    <p class="mb-0">{{ $exam->getTypeLabel() }}</p>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">Status</label>
                    <br>
                    @php
                        $badgeClass = match($exam->status) {
                            'draft' => 'bg-secondary',
                            'scheduled' => 'bg-info',
                            'ongoing' => 'bg-warning',
                            'submitted' => 'bg-primary',
                            'approved' => 'bg-success',
                            'published' => 'bg-dark',
                            'locked' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ ucfirst($exam->status) }}</span>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">Academic Year</label>
                    <p class="mb-0">{{ $exam->academicYear->name }}</p>
                </div>

                @if($exam->program)
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">Program</label>
                    <p class="mb-0">{{ $exam->program->name }}</p>
                </div>
                @endif

                @if($exam->class)
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">Class</label>
                    <p class="mb-0">{{ $exam->class->name }}</p>
                </div>
                @endif

                @if($exam->subject)
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">Subject</label>
                    <p class="mb-0">{{ $exam->subject->name }}</p>
                </div>
                @else
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">Subject Coverage</label>
                    <p class="mb-0">
                        <span class="badge bg-secondary">All Subjects</span>
                        @if($exam->class)
                            <small class="text-muted d-block">For {{ $exam->class->name }}</small>
                        @endif
                    </p>
                </div>
                @endif

                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">Start Date</label>
                    <p class="mb-0">{{ $exam->start_date?->format('M d, Y') ?? 'Not Set' }}</p>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">End Date</label>
                    <p class="mb-0">{{ $exam->end_date?->format('M d, Y') ?? 'Not Set' }}</p>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">Submission Deadline</label>
                    <p class="mb-0">{{ $exam->submission_deadline->format('M d, Y H:i') }}</p>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">Maximum Marks</label>
                    <p class="mb-0">{{ $exam->max_marks }}</p>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">Created By</label>
                    <p class="mb-0">{{ $exam->creator->name }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Marking Scheme Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Marking Scheme</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title">Theory Marks</h6>
                            <h3 class="mb-0">{{ $exam->theory_max }}</h3>
                        </div>
                    </div>
                </div>

                @if($exam->has_practical)
                <div class="col-md-4 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title">Practical Marks</h6>
                            <h3 class="mb-0">{{ $exam->practical_max }}</h3>
                        </div>
                    </div>
                </div>
                @endif

                @if($exam->has_assessment)
                <div class="col-md-4 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title">Assessment Marks</h6>
                            <h3 class="mb-0">{{ $exam->assess_max }}</h3>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Exam Scope Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Exam Scope & Coverage</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="card-title text-muted">Program Coverage</h6>
                            <h4 class="mb-0">
                                @if($exam->program)
                                    1 Program
                                @else
                                    All Programs
                                @endif
                            </h4>
                            @if($exam->program)
                                <small class="text-muted">{{ $exam->program->name }}</small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="card-title text-muted">Class Coverage</h6>
                            <h4 class="mb-0">
                                @if($exam->class)
                                    1 Class
                                @else
                                    All Classes
                                @endif
                            </h4>
                            @if($exam->class)
                                <small class="text-muted">{{ $exam->class->name }}</small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="card-title text-muted">Subject Coverage</h6>
                            <h4 class="mb-0">
                                @if($exam->subject)
                                    1 Subject
                                @else
                                    @php
                                        $subjectCount = 0;
                                        if ($exam->class_id) {
                                            $subjectCount = \App\Models\Subject::whereHas('classes', function($query) use ($exam) {
                                                $query->where('class_id', $exam->class_id);
                                            })->count();
                                        } else {
                                            $subjectCount = \App\Models\Subject::where('is_active', true)->count();
                                        }
                                    @endphp
                                    {{ $subjectCount }} Subjects
                                @endif
                            </h4>
                            @if($exam->subject)
                                <small class="text-muted">{{ $exam->subject->name }}</small>
                            @else
                                <small class="text-muted">Multiple subjects</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Exam Statistics</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h6 class="card-title text-muted">Total Students</h6>
                            <h4 class="mb-0">{{ $statistics['total_students'] }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title">Submitted Marks</h6>
                            <h4 class="mb-0">{{ $statistics['submitted_marks'] }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title">Pass Count</h6>
                            <h4 class="mb-0">{{ $statistics['pass_count'] }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title">Fail Count</h6>
                            <h4 class="mb-0">{{ $statistics['fail_count'] }}</h4>
                        </div>
                    </div>
                </div>

                @if($statistics['average_marks'])
                <div class="col-md-3 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title">Average Marks</h6>
                            <h4 class="mb-0">{{ number_format($statistics['average_marks'], 2) }}</h4>
                        </div>
                    </div>
                </div>
                @endif

                @if($statistics['highest_marks'])
                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title">Highest Marks</h6>
                            <h4 class="mb-0">{{ $statistics['highest_marks'] }}</h4>
                        </div>
                    </div>
                </div>
                @endif

                @if($statistics['lowest_marks'])
                <div class="col-md-3 mb-3">
                    <div class="card bg-secondary text-white">
                        <div class="card-body text-center">
                            <h6 class="card-title">Lowest Marks</h6>
                            <h4 class="mb-0">{{ $statistics['lowest_marks'] }}</h4>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Status Change Card -->
    @if(auth()->user()->can('manage-exams'))
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Status Management</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.exams.change-status', $exam) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Change Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="draft" {{ $exam->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="scheduled" {{ $exam->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="ongoing" {{ $exam->status === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="submitted" {{ $exam->status === 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="approved" {{ $exam->status === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="published" {{ $exam->status === 'published' ? 'selected' : '' }}>Published</option>
                            @if(auth()->user()->hasRole('admin'))
                            <option value="locked" {{ $exam->status === 'locked' ? 'selected' : '' }}>Locked</option>
                            @endif
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="reason" class="form-label">Reason (Optional)</label>
                        <input type="text" name="reason" id="reason" class="form-control" placeholder="Reason for status change">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    Update Status
                </button>
            </form>
        </div>
    </div>
    @endif

    <!-- Recent Marks -->
    @if($exam->marks->count() > 0)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Recent Marks (Latest 10)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Total Marks</th>
                            <th>Percentage</th>
                            <th>Grade</th>
                            <th>Result</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exam->marks->take(10) as $mark)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $mark->student->name }}</div>
                                <small class="text-muted">{{ $mark->student->roll_number }}</small>
                            </td>
                            <td>{{ $mark->total_marks }}</td>
                            <td>{{ number_format($mark->percentage, 2) }}%</td>
                            <td>{{ $mark->grade }}</td>
                            <td>
                                <span class="badge {{ $mark->result === 'Pass' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $mark->result }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusBadgeClass = match($mark->status) {
                                        'draft' => 'bg-secondary',
                                        'pending' => 'bg-warning',
                                        'submitted' => 'bg-primary',
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $statusBadgeClass }}">
                                    {{ ucfirst($mark->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($exam->marks->count() > 10)
            <div class="mt-3">
                <a href="{{ route('admin.marks.index', ['exam' => $exam->id]) }}" class="btn btn-outline-primary btn-sm">
                    View all {{ $exam->marks->count() }} marks →
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection

@extends('layouts.admin')

@section('title', 'View Marks - ' . $exam->name)
@section('page-title', 'View Marks')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">View Marks</h1>
            <p class="mb-0 text-muted">{{ $exam->name }} - {{ $exam->academicYear?->name ?? 'No Academic Year' }}</p>
        </div>
        <div>
            @if($exam->can_enter_marks)
                <a href="{{ route('admin.marks.create', ['exam_id' => $exam->id]) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Enter Marks
                </a>
            @endif
            <a href="{{ route('admin.marks.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Mark Entry
            </a>
        </div>
    </div>

    <!-- Exam Information Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Exam Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Exam:</strong> {{ $exam->name }}
                </div>
                <div class="col-md-3">
                    <strong>Academic Year:</strong> {{ $exam->academicYear?->name ?? 'No Academic Year' }}
                </div>
                <div class="col-md-3">
                    <strong>Class:</strong> {{ $exam->class ? $exam->class->name : 'All Classes' }}
                </div>
                <div class="col-md-3">
                    <strong>Subject:</strong> {{ $exam->subject ? $exam->subject->name : 'Multiple Subjects' }}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-3">
                    <strong>Status:</strong> 
                    <span class="badge badge-{{ $exam->status_color }}">{{ ucfirst($exam->status) }}</span>
                </div>
                <div class="col-md-3">
                    <strong>Max Marks:</strong> {{ $exam->max_marks }}
                </div>
                <div class="col-md-3">
                    <strong>Start Date:</strong> {{ $exam->start_date?->format('M d, Y') ?? 'Not Set' }}
                </div>
                <div class="col-md-3">
                    <strong>End Date:</strong> {{ $exam->end_date?->format('M d, Y') ?? 'Not Set' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Mark Entry Statistics</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    <div class="text-center">
                        <h4 class="text-primary">{{ $statistics['total_marks_entered'] }}</h4>
                        <small class="text-muted">Total Marks Entered</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="text-center">
                        <h4 class="text-info">{{ $statistics['subjects_covered'] }}</h4>
                        <small class="text-muted">Subjects Covered</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="text-center">
                        <h4 class="text-success">{{ number_format($statistics['average_percentage'], 2) }}%</h4>
                        <small class="text-muted">Average Percentage</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="text-center">
                        <h4 class="text-warning">{{ number_format($statistics['highest_percentage'], 2) }}%</h4>
                        <small class="text-muted">Highest Percentage</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="text-center">
                        <h4 class="text-success">{{ $statistics['pass_count'] }}</h4>
                        <small class="text-muted">Pass Count</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="text-center">
                        <h4 class="text-danger">{{ $statistics['fail_count'] }}</h4>
                        <small class="text-muted">Fail Count</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Marks by Subject -->
    @foreach($marksBySubject as $subjectId => $marks)
        @php
            $subject = $marks->first()->subject;
            $subjectStats = [
                'total_students' => $marks->count(),
                'average_percentage' => $marks->avg('percentage'),
                'highest_marks' => $marks->max('total'),
                'lowest_marks' => $marks->min('total'),
                'pass_count' => $marks->where('result', 'Pass')->count(),
                'fail_count' => $marks->where('result', 'Fail')->count(),
            ];
        @endphp
        
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ $subject->name }} ({{ $subject->code }})
                </h6>
                <div>
                    <span class="badge badge-info">{{ $subjectStats['total_students'] }} Students</span>
                    <span class="badge badge-success">{{ $subjectStats['pass_count'] }} Pass</span>
                    <span class="badge badge-danger">{{ $subjectStats['fail_count'] }} Fail</span>
                </div>
            </div>
            <div class="card-body">
                <!-- Subject Statistics -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <small class="text-muted">Average Percentage:</small>
                        <strong>{{ number_format($subjectStats['average_percentage'], 2) }}%</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Highest Marks:</small>
                        <strong>{{ $subjectStats['highest_marks'] }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Lowest Marks:</small>
                        <strong>{{ $subjectStats['lowest_marks'] }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Pass Rate:</small>
                        <strong>{{ $subjectStats['total_students'] > 0 ? number_format(($subjectStats['pass_count'] / $subjectStats['total_students']) * 100, 1) : 0 }}%</strong>
                    </div>
                </div>

                <!-- Marks Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Roll No.</th>
                                <th>Student Name</th>
                                <th>Class</th>
                                @if($exam->assess_max)
                                    <th>Assessment</th>
                                @endif
                                @if($exam->theory_max)
                                    <th>Theory</th>
                                @endif
                                @if($exam->practical_max)
                                    <th>Practical</th>
                                @endif
                                <th>Total</th>
                                <th>Percentage</th>
                                <th>Grade</th>
                                <th>Result</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($marks->sortBy('student.roll_number') as $mark)
                                <tr>
                                    <td>{{ $mark->student->roll_number }}</td>
                                    <td>{{ $mark->student->full_name }}</td>
                                    <td>{{ $mark->student->currentEnrollment->class->name ?? 'N/A' }}</td>
                                    @if($exam->assess_max)
                                        <td>{{ $mark->assess_marks ?? '-' }}</td>
                                    @endif
                                    @if($exam->theory_max)
                                        <td>{{ $mark->theory_marks ?? '-' }}</td>
                                    @endif
                                    @if($exam->practical_max)
                                        <td>{{ $mark->practical_marks ?? '-' }}</td>
                                    @endif
                                    <td><strong>{{ $mark->total }}</strong></td>
                                    <td>{{ number_format($mark->percentage, 2) }}%</td>
                                    <td>
                                        <span class="badge badge-{{ $mark->grade == 'A+' || $mark->grade == 'A' ? 'success' : ($mark->grade == 'F' ? 'danger' : 'warning') }}">
                                            {{ $mark->grade }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $mark->result == 'Pass' ? 'success' : 'danger' }}">
                                            {{ $mark->result }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $mark->status == 'approved' ? 'success' : ($mark->status == 'submitted' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($mark->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($mark->status == 'draft' && $exam->can_enter_marks)
                                            <a href="{{ route('admin.marks.edit', $mark) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($mark->remarks)
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    data-bs-toggle="tooltip" 
                                                    title="{{ $mark->remarks }}">
                                                <i class="fas fa-comment"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach

    @if($marksBySubject->count() == 0)
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No marks have been entered for this exam yet.</h5>
                @if($exam->can_enter_marks)
                    <p class="text-muted">Click the "Enter Marks" button above to start entering marks.</p>
                    <a href="{{ route('admin.marks.create', ['exam_id' => $exam->id]) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Enter Marks
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
@endsection

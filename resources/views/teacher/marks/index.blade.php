@extends('layouts.teacher')

@section('title', 'Mark Entry')
@section('page-title', 'Mark Entry')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Mark Entry</h1>
            <p class="mb-0 text-muted">Enter marks for your assigned subjects</p>
        </div>
        <div>
            <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- My Assigned Subjects -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">My Assigned Subjects</h6>
                </div>
                <div class="card-body">
                    @if($assignedSubjects->count() > 0)
                        <div class="row">
                            @foreach($assignedSubjects as $assignment)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-left-primary h-100">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary">{{ $assignment->subject->name }}</h6>
                                            <p class="card-text">
                                                <strong>Code:</strong> {{ $assignment->subject->code }}<br>
                                                <strong>Class:</strong> {{ $assignment->class->name }}<br>
                                                <strong>Level:</strong> {{ $assignment->class->level->name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-book fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-muted">No Subjects Assigned</h5>
                            <p class="text-muted">You haven't been assigned to any subjects yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Exams Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Available Exams ({{ $exams->total() }})</h6>
        </div>
        <div class="card-body">
            @if($exams->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Exam Name</th>
                                <th>Subject</th>
                                <th>Class</th>
                                <th>Exam Date</th>
                                <th>Type</th>
                                <th>Mark Entry Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exams as $exam)
                                <tr>
                                    <td>
                                        <strong>{{ $exam->name }}</strong>
                                        <br><small class="text-muted">{{ $exam->academicYear->name }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $exam->subject->name }}</strong>
                                        <br><small class="text-muted">{{ $exam->subject->code }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $exam->class->name }}</strong>
                                        <br><small class="text-muted">{{ $exam->class->level->name }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $exam->start_date->format('M d, Y') }}</strong>
                                        @if($exam->end_date && $exam->end_date != $exam->start_date)
                                            <br><small class="text-muted">to {{ $exam->end_date->format('M d, Y') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $exam->exam_type }}</span>
                                    </td>
                                    <td>
                                        @if($exam->can_enter_marks)
                                            <span class="badge bg-success">
                                                <i class="fas fa-unlock"></i> Open
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-lock"></i> Closed
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($exam->can_enter_marks)
                                            <a href="{{ route('teacher.marks.create', $exam) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Enter Marks
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                <i class="fas fa-lock"></i> Closed
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $exams->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-calendar-alt fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-muted">No Exams Available</h5>
                    <p class="text-muted">There are no exams available for your assigned subjects.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

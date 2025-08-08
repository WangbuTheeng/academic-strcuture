@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- School Header Card -->
    <div class="card shadow-lg border-0 mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-body text-white">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    @if($currentSchool && $currentSchool->logo_path)
                        <img src="{{ asset('storage/' . $currentSchool->logo_path) }}"
                             alt="{{ $currentSchool->name }} Logo"
                             class="img-fluid rounded-circle bg-white p-2"
                             style="max-height: 80px; max-width: 80px;">
                    @else
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-7">
                    <h2 class="h3 mb-1 font-weight-bold">
                        {{ $currentSchool->name ?? ($instituteSettings->institution_name ?? 'Academic Management System') }}
                    </h2>
                    <p class="mb-1 opacity-90">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        {{ $currentSchool->address ?? ($instituteSettings->institution_address ?? 'School Address') }}
                    </p>
                    @if($currentSchool && $currentSchool->phone)
                        <p class="mb-0 opacity-90">
                            <i class="fas fa-phone me-2"></i>{{ $currentSchool->phone }}
                            @if($currentSchool->email)
                                <span class="ms-3"><i class="fas fa-envelope me-2"></i>{{ $currentSchool->email }}</span>
                            @endif
                        </p>
                    @endif
                </div>
                <div class="col-md-3 text-end">
                    <div class="text-white-50 small">Academic Year</div>
                    <div class="h5 mb-2">{{ $currentAcademicYear->name ?? 'Not Set' }}</div>
                    <a href="{{ route('teacher.profile') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-user me-1"></i> My Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle bg-primary text-white me-3">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div>
                            <h4 class="mb-1">Welcome back, {{ $teacher->name }}!</h4>
                            <p class="text-muted mb-0">Ready to make a difference in your students' lives today?</p>
                        </div>
                        <div class="ms-auto">
                            <div class="text-muted small">{{ now()->format('l, F j, Y') }}</div>
                            <div class="text-primary font-weight-bold">{{ now()->format('g:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-gradient text-white rounded-3 me-3">
                            <i class="fas fa-book"></i>
                        </div>
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Assigned Subjects</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $markStats['total_subjects'] }}</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('teacher.marks.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-gradient text-white rounded-3 me-3">
                            <i class="fas fa-chalkboard"></i>
                        </div>
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Classes Teaching</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $markStats['total_classes'] }}</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('teacher.marks.index') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-users me-1"></i> View Classes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-gradient text-white rounded-3 me-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Submitted Marks</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $markStats['submitted_marks'] }}</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-info bg-gradient">Completed</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-gradient text-white rounded-3 me-3">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">Pending Marks</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $markStats['pending_marks'] }}</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        @if($markStats['pending_marks'] > 0)
                            <a href="{{ route('teacher.marks.index') }}" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-edit me-1"></i> Complete Now
                            </a>
                        @else
                            <span class="badge bg-success bg-gradient">All Done!</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Activity -->
    <div class="row mb-4">
        <!-- Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt text-primary me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('teacher.marks.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Enter Marks
                        </a>
                        <a href="{{ route('teacher.marks.results') }}" class="btn btn-outline-success">
                            <i class="fas fa-chart-line me-2"></i>View Results
                        </a>
                        <a href="{{ route('teacher.profile') }}" class="btn btn-outline-info">
                            <i class="fas fa-user me-2"></i>Update Profile
                        </a>
                        <a href="#" class="btn btn-outline-secondary">
                            <i class="fas fa-calendar me-2"></i>Class Schedule
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Subjects -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-book text-success me-2"></i>My Subjects & Classes
                    </h5>
                </div>
                <div class="card-body">
                    @if($assignedSubjects->count() > 0)
                        <div class="row">
                            @foreach($assignedSubjects->take(6) as $assignment)
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                        <div class="subject-icon bg-primary text-white rounded me-3">
                                            {{ substr($assignment->subject->code, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $assignment->subject->name }}</div>
                                            <div class="text-muted small">
                                                {{ $assignment->class->name }} - {{ $assignment->class->level->name ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($assignedSubjects->count() > 6)
                            <div class="text-center mt-3">
                                <a href="{{ route('teacher.marks.index') }}" class="btn btn-sm btn-outline-primary">
                                    View All {{ $assignedSubjects->count() }} Subjects
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No subjects assigned yet</h6>
                            <p class="text-muted small">Contact your administrator to get subject assignments.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Exams & Active Exams -->
    <div class="row">
        <!-- Upcoming Exams -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt text-info me-2"></i>Upcoming Exams
                    </h5>
                </div>
                <div class="card-body">
                    @if($upcomingExams->count() > 0)
                        @foreach($upcomingExams as $exam)
                            <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                                <div class="exam-date bg-info text-white rounded text-center me-3" style="min-width: 60px;">
                                    <div class="small">{{ $exam->start_date->format('M') }}</div>
                                    <div class="fw-bold">{{ $exam->start_date->format('d') }}</div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $exam->name }}</div>
                                    <div class="text-muted small">
                                        {{ $exam->subject->name }} - {{ $exam->class->name }}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-info">{{ $exam->exam_type }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No upcoming exams</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Active Exams (Mark Entry Available) -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit text-warning me-2"></i>Mark Entry Available
                    </h5>
                </div>
                <div class="card-body">
                    @if($activeExams->count() > 0)
                        @foreach($activeExams as $exam)
                            <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                                <div class="exam-status bg-warning text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $exam->name }}</div>
                                    <div class="text-muted small">
                                        {{ $exam->subject->name }} - {{ $exam->class->name }}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('teacher.marks.create', $exam) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit me-1"></i>Enter Marks
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No marks to enter</h6>
                            <p class="text-muted small">All current exams are completed or not yet open for mark entry.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-lift {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .avatar-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .subject-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        font-weight: bold;
    }

    .exam-date {
        padding: 8px;
        line-height: 1.2;
    }

    .card {
        border-radius: 12px;
    }

    .btn {
        border-radius: 8px;
    }

    .badge {
        border-radius: 6px;
    }
</style>
@endpush
@endsection

@extends('layouts.admin')

@section('title', 'Enrollment Details')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container-fluid px-4">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">
                <i class="fas fa-user-graduate text-primary me-2"></i>
                Enrollment Details
            </h1>
            <nav aria-label="breadcrumb" class="mt-2">
                <ol class="breadcrumb breadcrumb-modern">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.enrollments.index') }}">Enrollments</a></li>
                    <li class="breadcrumb-item active">{{ $enrollment->student->full_name }}</li>
                </ol>
            </nav>
            <p class="text-muted mb-0">Complete enrollment information and academic details</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.enrollments.edit', $enrollment) }}" class="btn btn-warning btn-modern">
                <i class="fas fa-edit me-1"></i> Edit Enrollment
            </a>
            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary btn-modern">
                <i class="fas fa-arrow-left me-1"></i> Back to Enrollments
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Student Information Card -->
            <div class="card card-modern shadow-lg border-0 mb-4">
                <div class="card-header bg-primary text-white py-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-white bg-opacity-20 me-3">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <h5 class="m-0 fw-bold" style="color: white;">Student Information</h5>
                            <small style="color: white;">Personal and contact details</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Student Photo and Name -->
                            <div class="info-item mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if($enrollment->student->photo)
                                            <img class="rounded-circle border border-2 border-primary"
                                                 src="{{ Storage::url($enrollment->student->photo) }}"
                                                 alt="{{ $enrollment->student->full_name }}"
                                                 style="width: 80px; height: 80px; object-fit: cover;"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="rounded-circle bg-primary d-none align-items-center justify-content-center"
                                                 style="width: 80px; height: 80px;">
                                                <span class="text-white fw-bold" style="font-size: 1.5rem;">
                                                    {{ strtoupper(substr($enrollment->student->first_name, 0, 1) . substr($enrollment->student->last_name, 0, 1)) }}
                                                </span>
                                            </div>
                                        @else
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center"
                                                 style="width: 80px; height: 80px;">
                                                <span class="text-white fw-bold" style="font-size: 1.5rem;">
                                                    {{ strtoupper(substr($enrollment->student->first_name, 0, 1) . substr($enrollment->student->last_name, 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <label class="info-label">
                                            <i class="fas fa-user text-primary me-2"></i>
                                            Full Name
                                        </label>
                                        <div class="info-value">{{ $enrollment->student->full_name ?? 'Student record missing' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="info-item mb-4">
                                <label class="info-label">
                                    <i class="fas fa-id-card text-info me-2"></i>
                                    Admission Number
                                </label>
                                <div class="info-value">{{ $enrollment->student->admission_number ?? 'N/A' }}</div>
                            </div>
                            
                            <div class="info-item mb-4">
                                <label class="info-label">
                                    <i class="fas fa-envelope text-success me-2"></i>
                                    Email
                                </label>
                                <div class="info-value">{{ $enrollment->student->email ?? 'Not provided' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-4">
                                <label class="info-label">
                                    <i class="fas fa-phone text-warning me-2"></i>
                                    Phone
                                </label>
                                <div class="info-value">{{ $enrollment->student->phone ?? 'Not provided' }}</div>
                            </div>
                            
                            <div class="info-item mb-4">
                                <label class="info-label">
                                    <i class="fas fa-calendar text-secondary me-2"></i>
                                    Date of Birth
                                </label>
                                <div class="info-value">
                                    {{ $enrollment->student->date_of_birth ? $enrollment->student->date_of_birth->format('F j, Y') : 'Not provided' }}
                                </div>
                            </div>
                            
                            <div class="info-item mb-4">
                                <label class="info-label">
                                    <i class="fas fa-venus-mars text-danger me-2"></i>
                                    Gender
                                </label>
                                <div class="info-value">{{ ucfirst($enrollment->student->gender ?? 'Not specified') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Information Card -->
            <div class="card card-modern shadow-lg border-0 mb-4">
                <div class="card-header bg-success text-white py-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-white bg-opacity-20 me-3">
                            <i class="fas fa-graduation-cap text-white"></i>
                        </div>
                        <div>
                            <h5 class="m-0 fw-bold" style="color: white;">Academic Information</h5>
                            <small style="color: white;">Program and class details</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-4">
                                <label class="info-label">
                                    <i class="fas fa-book text-success me-2"></i>
                                    Program
                                </label>
                                <div class="info-value">{{ $enrollment->program->name }}</div>
                                <div class="info-meta">{{ $enrollment->program->department->name }}</div>
                            </div>
                            
                            <div class="info-item mb-4">
                                <label class="info-label">
                                    <i class="fas fa-school text-warning me-2"></i>
                                    Class
                                </label>
                                <div class="info-value">{{ $enrollment->class->name }}</div>
                                <div class="info-meta">{{ $enrollment->class->level->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-4">
                                <label class="info-label">
                                    <i class="fas fa-calendar-alt text-info me-2"></i>
                                    Academic Year
                                </label>
                                <div class="info-value">{{ $enrollment->academicYear->name }}</div>
                                @if($enrollment->academicYear->is_current)
                                    <div class="info-meta">
                                        <span class="badge bg-success">Current Year</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="info-item mb-4">
                                <label class="info-label">
                                    <i class="fas fa-calendar text-secondary me-2"></i>
                                    Enrollment Date
                                </label>
                                <div class="info-value">{{ $enrollment->enrollment_date->format('F j, Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrollment Details Card -->
            <div class="card card-modern shadow-lg border-0 mb-4">
                <div class="card-header bg-info text-white py-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-white bg-opacity-20 me-3">
                            <i class="fas fa-clipboard-list text-white"></i>
                        </div>
                        <div>
                            <h5 class="m-0 fw-bold" style="color: white;">Enrollment Details</h5>
                            <small style="color: white;">Roll number and status information</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-item mb-4">
                                <label class="info-label">
                                    <i class="fas fa-id-card text-primary me-2"></i>
                                    Roll Number
                                </label>
                                <div class="info-value">{{ $enrollment->roll_no }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item mb-4">
                                <label class="info-label">
                                    <i class="fas fa-layer-group text-secondary me-2"></i>
                                    Section
                                </label>
                                <div class="info-value">{{ $enrollment->section ?? 'Not assigned' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-item mb-4">
                                <label class="info-label">
                                    <i class="fas fa-toggle-on text-success me-2"></i>
                                    Status
                                </label>
                                <div class="info-value">
                                    <span class="badge bg-{{ $enrollment->status == 'active' ? 'success' : ($enrollment->status == 'graduated' ? 'primary' : 'secondary') }} px-3 py-2">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assigned Subjects Card -->
            @if($enrollment->studentSubjects->count() > 0)
            <div class="card card-modern shadow-lg border-0 mb-4">
                <div class="card-header bg-warning text-white py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-white bg-opacity-20 me-3">
                                <i class="fas fa-book-open text-white"></i>
                            </div>
                            <div>
                                <h5 class="m-0 fw-bold" style="color: white;">Assigned Subjects</h5>
                                <small style="color: white;">
                                    {{ $enrollment->studentSubjects->count() }} subjects assigned
                                    ({{ $enrollment->studentSubjects->where('status', 'active')->count() }} active)
                                </small>
                            </div>
                        </div>
                        <a href="{{ route('admin.student-subjects.index', $enrollment) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-cog me-1"></i> Manage
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 fw-bold">Subject</th>
                                    <th class="border-0 fw-bold">Code</th>
                                    <th class="border-0 fw-bold">Type</th>
                                    <th class="border-0 fw-bold">Credit Hours</th>
                                    <th class="border-0 fw-bold">Date Added</th>
                                    <th class="border-0 fw-bold">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enrollment->studentSubjects as $studentSubject)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $studentSubject->subject->name }}</div>
                                        <small class="text-muted">{{ $studentSubject->subject->department->name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $studentSubject->subject->code }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($studentSubject->subject->subject_type) }}</span>
                                    </td>
                                    <td>{{ $studentSubject->subject->credit_hours }}</td>
                                    <td>{{ $studentSubject->date_added->format('M j, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $studentSubject->status == 'active' ? 'success' : 'warning' }}">
                                            {{ ucfirst($studentSubject->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats Card -->
            <div class="card card-modern shadow border-0 mb-4">
                <div class="card-header bg-light py-3">
                    <h6 class="m-0 fw-bold text-dark">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        Quick Stats
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Assigned Subjects</span>
                            <span class="fw-bold text-primary">{{ $enrollment->studentSubjects->count() }}</span>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Active Subjects</span>
                            <span class="fw-bold text-success">{{ $enrollment->studentSubjects->where('status', 'active')->count() }}</span>
                        </div>
                    </div>
                    <div class="stat-item mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Credit Hours</span>
                            <span class="fw-bold text-info">{{ $enrollment->studentSubjects->sum(function($ss) { return $ss->subject->credit_hours; }) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card card-modern shadow border-0 mb-4">
                <div class="card-header bg-light py-3">
                    <h6 class="m-0 fw-bold text-dark">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.enrollments.edit', $enrollment) }}" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit Enrollment
                        </a>
                        <a href="{{ route('admin.student-subjects.index', $enrollment) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-book me-1"></i> Manage Subjects
                        </a>
                        <a href="{{ route('admin.marks.index', ['student_id' => $enrollment->student->id]) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-chart-line me-1"></i> View Marks
                        </a>
                        <a href="{{ route('admin.students.show', $enrollment->student) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-user me-1"></i> Student Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="card card-modern shadow border-0">
                <div class="card-header bg-light py-3">
                    <h6 class="m-0 fw-bold text-dark">
                        <i class="fas fa-history text-secondary me-2"></i>
                        Timeline
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">Enrollment Created</div>
                                <div class="timeline-date">{{ $enrollment->created_at->format('M j, Y g:i A') }}</div>
                            </div>
                        </div>
                        @if($enrollment->updated_at != $enrollment->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">Last Updated</div>
                                <div class="timeline-date">{{ $enrollment->updated_at->format('M j, Y g:i A') }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Modern Card Styling */
.card-modern {
    border: none;
    border-radius: 16px;
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.card-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 35px rgba(0, 0, 0, 0.12);
}


/* Modern Breadcrumb */
.breadcrumb-modern {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    padding: 8px 16px;
    margin: 0;
}

.breadcrumb-modern .breadcrumb-item a {
    color: #6c757d;
    text-decoration: none;
    font-weight: 500;
}

.breadcrumb-modern .breadcrumb-item a:hover {
    color: #495057;
}

/* Modern Buttons */
.btn-modern {
    border-radius: 10px;
    font-weight: 600;
    padding: 10px 20px;
    transition: all 0.3s ease;
}

.btn-modern:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

/* Icon Circle */
.icon-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

/* Info Items */
.info-item {
    padding: 16px;
    background: #f8f9fa;
    border-radius: 12px;
    border-left: 4px solid #e5e7eb;
    transition: all 0.3s ease;
}

.info-item:hover {
    background: #f1f3f4;
    border-left-color: #4f46e5;
}

.info-label {
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
}

.info-value {
    font-size: 15px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 2px;
}

.info-meta {
    font-size: 12px;
    color: #9ca3af;
}

/* Stats */
.stat-item {
    padding: 12px 0;
    border-bottom: 1px solid #e5e7eb;
}

.stat-item:last-child {
    border-bottom: none;
}

/* Timeline */
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e5e7eb;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -26px;
    top: 4px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e5e7eb;
}

.timeline-title {
    font-weight: 600;
    color: #1f2937;
    font-size: 14px;
}

.timeline-date {
    font-size: 12px;
    color: #6b7280;
}

/* Table Styling */
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

/* Responsive Design */
@media (max-width: 768px) {
    .card-modern {
        border-radius: 12px;
        margin-bottom: 1rem;
    }
    
    .icon-circle {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    
    .info-item {
        padding: 12px;
    }
}
</style>
@endpush
@endsection

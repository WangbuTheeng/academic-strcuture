@extends('layouts.admin')

@section('title', $student->full_name)

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $student->full_name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
                    <li class="breadcrumb-item active">{{ $student->full_name }}</li>
                </ol>
            </nav>
            <p class="text-muted">Student ID: {{ $student->admission_number }}</p>
        </div>
        <div>
            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit"></i> Edit Student
            </a>
            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Students
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Student Photo and Basic Info -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    @if($student->photo)
                        <img class="rounded-circle border border-4 border-primary shadow-lg"
                             src="{{ Storage::url($student->photo) }}"
                             alt="{{ $student->full_name }}"
                             style="width: 180px; height: 180px; object-fit: cover;"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="rounded-circle bg-gradient-primary d-none align-items-center justify-content-center mx-auto border border-4 border-primary shadow-lg"
                             style="width: 180px; height: 180px;">
                            <span class="text-white fw-bold" style="font-size: 3.5rem;">
                                {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                            </span>
                        </div>
                    @else
                        <div class="rounded-circle bg-gradient-primary d-flex align-items-center justify-content-center mx-auto border border-4 border-primary shadow-lg"
                             style="width: 180px; height: 180px;">
                            <span class="text-white fw-bold" style="font-size: 3.5rem;">
                                {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <h3 class="mt-3 h4 text-gray-900">{{ $student->full_name }}</h3>
                    <p class="text-muted">{{ $student->admission_number }}</p>
                    <div class="mt-3">
                        <span class="badge
                            @if($student->status === 'active') badge-success
                            @elseif($student->status === 'inactive') badge-danger
                            @elseif($student->status === 'graduated') badge-primary
                            @elseif($student->status === 'transferred') badge-warning
                            @else badge-secondary
                            @endif">
                            {{ ucfirst($student->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit Student
                        </a>
                        @if($student->enrollments->count() > 0 && $student->currentEnrollment)
                            <a href="{{ route('admin.student-subjects.index', $student->currentEnrollment) }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-book"></i> Manage Subjects
                            </a>
                        @endif
                        <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Delete Student
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Details -->
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>First Name:</strong><br>
                            <span class="text-muted">{{ $student->first_name }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Last Name:</strong><br>
                            <span class="text-muted">{{ $student->last_name }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Date of Birth:</strong><br>
                            <span class="text-muted">{{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Gender:</strong><br>
                            <span class="text-muted">{{ ucfirst($student->gender) }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Phone:</strong><br>
                            <span class="text-muted">{{ $student->phone ?: 'N/A' }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Email:</strong><br>
                            <span class="text-muted">{{ $student->email ?: 'N/A' }}</span>
                        </div>
                        <div class="col-md-12 mb-3">
                            <strong>Address:</strong><br>
                            <span class="text-muted">{{ $student->address ?: 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Academic Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Admission Number:</strong><br>
                            <span class="text-muted">{{ $student->admission_number }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Admission Date:</strong><br>
                            <span class="text-muted">{{ $student->admission_date ? $student->admission_date->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="col-md-6 mb-3 font-black ">
                            <strong>Status:</strong><br>
                            <span class="badge 
                                @if($student->status === 'active') badge-success
                                @elseif($student->status === 'inactive') badge-danger
                                @elseif($student->status === 'graduated') badge-primary
                                @elseif($student->status === 'transferred') badge-warning
                                @else badge-secondary
                                @endif">
                                {{ ucfirst($student->status) }}
                            </span>
                        </div>
                        @if($student->currentEnrollment)
                        <div class="col-md-6 mb-3">
                            <strong>Current Class:</strong><br>
                            <span class="text-muted">{{ $student->currentEnrollment->class->name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Current Program:</strong><br>
                            <span class="text-muted">{{ $student->currentEnrollment->program->name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Roll Number:</strong><br>
                            <span class="text-muted">{{ $student->currentEnrollment->roll_number ?? 'N/A' }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Guardian Information -->
            @if($student->guardian_name || $student->guardian_phone || $student->guardian_email)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Guardian Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Guardian Name:</strong><br>
                            <span class="text-muted">{{ $student->guardian_name ?: 'N/A' }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Guardian Phone:</strong><br>
                            <span class="text-muted">{{ $student->guardian_phone ?: 'N/A' }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Guardian Email:</strong><br>
                            <span class="text-muted">{{ $student->guardian_email ?: 'N/A' }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Relationship:</strong><br>
                            <span class="text-muted">{{ $student->guardian_relationship ?: 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this student? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('admin.students.destroy', $student) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(45deg, #4f46e5, #7c3aed);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.2);
}

.rounded-circle {
    transition: all 0.3s ease;
}

.rounded-circle:hover {
    transform: scale(1.05);
}

.badge {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
}
</style>
@endpush

<script>
function confirmDelete() {
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endsection
��
 
 

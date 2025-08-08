@extends('layouts.admin')

@section('title', 'Bulk Student Enrollment')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-dark">Bulk Student Enrollment</h1>
            <p class="mb-0 text-muted">Enroll multiple students at once for {{ $currentAcademicYear->name }}</p>
        </div>
        <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Enrollments
        </a>
    </div>

    @if($students->count() == 0)
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>No students available for enrollment.</strong> 
            All active students are already enrolled in the current academic year ({{ $currentAcademicYear->name }}).
        </div>
    @else
        <form method="POST" action="{{ route('admin.enrollments.bulk-store') }}" id="bulkEnrollmentForm">
            @csrf
            
            <div class="row">
                <!-- Student Selection -->
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    Select Students ({{ $students->count() }} available)
                                </h6>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                        <i class="fas fa-check-square me-1"></i> Select All
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectNone()">
                                        <i class="fas fa-square me-1"></i> Select None
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                            <div class="row">
                                @foreach($students as $student)
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input student-checkbox" type="checkbox" 
                                               name="student_ids[]" value="{{ $student->id }}" 
                                               id="student_{{ $student->id }}" onchange="updateSelection()">
                                        <label class="form-check-label w-100" for="student_{{ $student->id }}">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    @if($student->photo)
                                                        <img class="rounded-circle border border-2 border-primary"
                                                             src="{{ Storage::url($student->photo) }}"
                                                             alt="{{ $student->full_name }}"
                                                             style="width: 40px; height: 40px; object-fit: cover;"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <div class="rounded-circle bg-primary d-none align-items-center justify-content-center"
                                                             style="width: 40px; height: 40px;">
                                                            <span class="text-white fw-bold">
                                                                {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center"
                                                             style="width: 40px; height: 40px;">
                                                            <span class="text-white fw-bold">
                                                                {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $student->full_name }}</div>
                                                    <small class="text-muted">{{ $student->admission_number }}</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="text-muted">
                                <span id="selectedCount">0</span> students selected
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enrollment Details -->
                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Enrollment Details</h6>
                        </div>
                        <div class="card-body">
                            <!-- Academic Year (Fixed) -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Academic Year</label>
                                <input type="hidden" name="academic_year_id" value="{{ $currentAcademicYear->id }}">
                                <div class="form-control-plaintext bg-light p-2 rounded">
                                    {{ $currentAcademicYear->name }}
                                    <span class="badge bg-success ms-2">Current</span>
                                </div>
                            </div>

                            <!-- Program -->
                            <div class="mb-3">
                                <label for="program_id" class="form-label fw-bold">Program <span class="text-danger">*</span></label>
                                <select class="form-select @error('program_id') is-invalid @enderror" 
                                        id="program_id" name="program_id" required>
                                    <option value="">Select Program</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                            {{ $program->name }} ({{ $program->department->name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('program_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Class -->
                            <div class="mb-3">
                                <label for="class_id" class="form-label fw-bold">Class <span class="text-danger">*</span></label>
                                <select class="form-select @error('class_id') is-invalid @enderror" 
                                        id="class_id" name="class_id" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }} ({{ $class->level->name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Enrollment Date -->
                            <div class="mb-3">
                                <label for="enrollment_date" class="form-label fw-bold">Enrollment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('enrollment_date') is-invalid @enderror" 
                                       id="enrollment_date" name="enrollment_date" 
                                       value="{{ old('enrollment_date', date('Y-m-d')) }}" required>
                                @error('enrollment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="mb-3">
                                <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Section -->
                            <div class="mb-3">
                                <label for="section" class="form-label fw-bold">Section</label>
                                <input type="text" class="form-control @error('section') is-invalid @enderror" 
                                       id="section" name="section" value="{{ old('section') }}" 
                                       placeholder="e.g., A, B, C" maxlength="10">
                                @error('section')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Auto Assign Subjects -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="auto_assign_subjects" name="auto_assign_subjects" 
                                           value="1" {{ old('auto_assign_subjects', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_assign_subjects">
                                        <strong>Auto-assign compulsory subjects</strong>
                                        <br><small class="text-muted">Automatically enroll students in compulsory subjects</small>
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                                    <i class="fas fa-users me-2"></i>
                                    Enroll Selected Students
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>

<script>
function selectAll() {
    document.querySelectorAll('.student-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    updateSelection();
}

function selectNone() {
    document.querySelectorAll('.student-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateSelection();
}

function updateSelection() {
    const checkboxes = document.querySelectorAll('.student-checkbox');
    const selectedCount = document.querySelectorAll('.student-checkbox:checked').length;
    
    document.getElementById('selectedCount').textContent = selectedCount;
    document.getElementById('submitBtn').disabled = selectedCount === 0;
    
    // Update submit button text
    const submitBtn = document.getElementById('submitBtn');
    if (selectedCount === 0) {
        submitBtn.innerHTML = '<i class="fas fa-users me-2"></i>Enroll Selected Students';
    } else {
        submitBtn.innerHTML = `<i class="fas fa-users me-2"></i>Enroll ${selectedCount} Student${selectedCount > 1 ? 's' : ''}`;
    }
}

// Form validation
document.getElementById('bulkEnrollmentForm').addEventListener('submit', function(e) {
    const selectedCount = document.querySelectorAll('.student-checkbox:checked').length;
    
    if (selectedCount === 0) {
        e.preventDefault();
        alert('Please select at least one student to enroll.');
        return false;
    }
    
    if (!confirm(`Are you sure you want to enroll ${selectedCount} student${selectedCount > 1 ? 's' : ''}?`)) {
        e.preventDefault();
        return false;
    }
});
</script>
@endsection

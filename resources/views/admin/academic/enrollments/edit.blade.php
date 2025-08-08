@extends('layouts.admin')

@section('title', 'Edit Student Enrollment')

@section('content')
<div class="container-fluid px-4">
    <!-- Modern Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800 fw-bold">
                <i class="fas fa-user-edit text-primary me-2"></i>
                Edit Enrollment
            </h1>
            <p class="text-muted mb-0">Update student enrollment information</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="btn btn-outline-info">
                <i class="fas fa-eye me-1"></i> View Details
            </a>
            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Enrollments
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.enrollments.update', $enrollment) }}" id="enrollmentForm">
                        @csrf
                        @method('PUT')

                        <!-- Student Selection -->
                        <div class="mb-4">
                            <h6 class="text-primary fw-bold mb-3">
                                <i class="fas fa-user me-2"></i>Student Information
                            </h6>

                            <div class="mb-3">
                                <label for="student_id" class="form-label fw-semibold">
                                    Student <span class="text-danger">*</span>
                                </label>
                                <select name="student_id" id="student_id"
                                        class="form-select @error('student_id') is-invalid @enderror" required>
                                    <option value="">Choose a student...</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}"
                                                {{ old('student_id', $enrollment->student_id) == $student->id ? 'selected' : '' }}>
                                            {{ $student->full_name }} ({{ $student->admission_number }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Academic Information -->
                        <div class="mb-4">
                            <h6 class="text-success fw-bold mb-3">
                                <i class="fas fa-graduation-cap me-2"></i>Academic Information
                            </h6>

                            <div class="row">
                                <!-- Academic Year -->
                                <div class="col-md-6 mb-3">
                                    <label for="academic_year_id" class="form-label fw-semibold">
                                        Academic Year <span class="text-danger">*</span>
                                    </label>
                                    <select name="academic_year_id" id="academic_year_id"
                                            class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                        <option value="">Select Academic Year</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}"
                                                    {{ old('academic_year_id', $enrollment->academic_year_id) == $year->id ? 'selected' : '' }}>
                                                {{ $year->name }}
                                                @if($year->is_current)
                                                    <span class="badge bg-success ms-1">Current</span>
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_year_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Program -->
                                <div class="col-md-6 mb-3">
                                    <label for="program_id" class="form-label fw-semibold">
                                        Program <span class="text-danger">*</span>
                                    </label>
                                    <select name="program_id" id="program_id"
                                            class="form-select @error('program_id') is-invalid @enderror" required>
                                        <option value="">Select Program</option>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}"
                                                    {{ old('program_id', $enrollment->program_id) == $program->id ? 'selected' : '' }}>
                                                {{ $program->name }} ({{ $program->department->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('program_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Class & Enrollment Details -->
                        <div class="mb-4">
                            <h6 class="text-warning fw-bold mb-3">
                                <i class="fas fa-chalkboard-teacher me-2"></i>Class & Enrollment Details
                            </h6>

                            <div class="row">
                                <!-- Class -->
                                <div class="col-md-6 mb-3">
                                    <label for="class_id" class="form-label fw-semibold">
                                        Class <span class="text-danger">*</span>
                                    </label>
                                    <select name="class_id" id="class_id"
                                            class="form-select @error('class_id') is-invalid @enderror" required>
                                        <option value="">Select Class</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}"
                                                    {{ old('class_id', $enrollment->class_id) == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }} ({{ $class->level->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Enrollment Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="enrollment_date" class="form-label fw-semibold">
                                        Enrollment Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="enrollment_date" id="enrollment_date"
                                           value="{{ old('enrollment_date', $enrollment->enrollment_date->format('Y-m-d')) }}"
                                           class="form-control @error('enrollment_date') is-invalid @enderror" required>
                                    @error('enrollment_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Roll Number -->
                                <div class="col-md-4 mb-3">
                                    <label for="roll_number" class="form-label fw-semibold">
                                        Roll Number
                                    </label>
                                    <div class="input-group">
                                        <input type="text" name="roll_number" id="roll_number"
                                               value="{{ old('roll_number', $enrollment->roll_no) }}"
                                               class="form-control @error('roll_number') is-invalid @enderror"
                                               placeholder="Auto-generated">
                                        <button type="button" class="btn btn-outline-primary" id="suggest-roll-btn">
                                            <i class="fas fa-magic"></i>
                                        </button>
                                    </div>
                                    @error('roll_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Current: {{ $enrollment->roll_no }}</small>
                                </div>

                                <!-- Section -->
                                <div class="col-md-4 mb-3">
                                    <label for="section" class="form-label fw-semibold">
                                        Section
                                    </label>
                                    <input type="text" name="section" id="section"
                                           value="{{ old('section', $enrollment->section) }}"
                                           class="form-control @error('section') is-invalid @enderror"
                                           placeholder="e.g., A, B, C">
                                    @error('section')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="col-md-4 mb-3">
                                    <label for="status" class="form-label fw-semibold">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select name="status" id="status"
                                            class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status', $enrollment->status) == 'active' ? 'selected' : '' }}>
                                            Active
                                        </option>
                                        <option value="inactive" {{ old('status', $enrollment->status) == 'inactive' ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                        <option value="graduated" {{ old('status', $enrollment->status) == 'graduated' ? 'selected' : '' }}>
                                            Graduated
                                        </option>
                                        <option value="transferred" {{ old('status', $enrollment->status) == 'transferred' ? 'selected' : '' }}>
                                            Transferred
                                        </option>
                                        <option value="dropped" {{ old('status', $enrollment->status) == 'dropped' ? 'selected' : '' }}>
                                            Dropped
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top">
                            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Enrollment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Current Details Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-info-circle text-info me-2"></i>Current Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-user text-primary me-2"></i>
                            <strong class="text-dark">Student</strong>
                        </div>
                        <div class="ps-4">
                            <div class="fw-semibold">{{ $enrollment->student->full_name }}</div>
                            <small class="text-muted">{{ $enrollment->student->admission_number }}</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-book text-success me-2"></i>
                            <strong class="text-dark">Program</strong>
                        </div>
                        <div class="ps-4">
                            <div class="fw-semibold">{{ $enrollment->program->name }}</div>
                            <small class="text-muted">{{ $enrollment->program->department->name }}</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-school text-warning me-2"></i>
                            <strong class="text-dark">Class</strong>
                        </div>
                        <div class="ps-4">
                            <div class="fw-semibold">{{ $enrollment->class->name }}</div>
                            <small class="text-muted">{{ $enrollment->class->level->name }}</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-calendar-alt text-info me-2"></i>
                            <strong class="text-dark">Academic Year</strong>
                        </div>
                        <div class="ps-4">
                            <div class="fw-semibold">{{ $enrollment->academicYear->name }}</div>
                            @if($enrollment->academicYear->is_current)
                                <span class="badge bg-success">Current Year</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-id-card text-primary me-2"></i>
                            <strong class="text-dark">Roll Number</strong>
                        </div>
                        <div class="ps-4">
                            <div class="fw-semibold">{{ $enrollment->roll_no }}</div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-toggle-on text-success me-2"></i>
                            <strong class="text-dark">Status</strong>
                        </div>
                        <div class="ps-4">
                            <span class="badge bg-{{ $enrollment->status == 'active' ? 'success' : ($enrollment->status == 'graduated' ? 'primary' : 'secondary') }}">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-bolt text-warning me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-eye me-1"></i> View Full Details
                        </a>
                        <a href="{{ route('admin.student-subjects.index', $enrollment) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-book me-1"></i> Manage Subjects
                        </a>
                        <a href="{{ route('admin.marks.index', ['student_id' => $enrollment->student->id]) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-chart-line me-1"></i> View Marks
                        </a>
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

/* Form Styling */
.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.form-select, .form-control {
    border-radius: 10px;
    border: 2px solid #e5e7eb;
    padding: 12px 16px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-select:focus, .form-control:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.15);
    transform: translateY(-1px);
}

/* Section Styling */
.form-section {
    position: relative;
}

.section-header {
    position: relative;
}

.section-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.section-divider {
    height: 2px;
    background: linear-gradient(90deg, #e5e7eb 0%, transparent 100%);
    border: none;
    margin: 0;
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

/* Detail Items */
.detail-item {
    padding: 16px;
    background: #f8f9fa;
    border-radius: 12px;
    border-left: 4px solid #e5e7eb;
    transition: all 0.3s ease;
}

.detail-item:hover {
    background: #f1f3f4;
    border-left-color: #4f46e5;
}

.detail-label {
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
}

.detail-value {
    font-size: 15px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 2px;
}

.detail-meta {
    font-size: 12px;
    color: #9ca3af;
}

/* Form Actions */
.form-actions {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 12px;
    margin: 0 -1.5rem -1.5rem -1.5rem;
    padding: 1.5rem;
}

/* Button Styling */
.btn-primary {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    border: none;
    border-radius: 10px;
    font-weight: 600;
    padding: 12px 24px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(79, 70, 229, 0.3);
}

.btn-secondary {
    background: #6b7280;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    padding: 12px 24px;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: #4b5563;
    transform: translateY(-1px);
}

/* Input Group Styling */
.input-group .form-control {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.input-group .btn {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    border-left: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    .card-modern {
        border-radius: 12px;
        margin-bottom: 1rem;
    }

    .section-icon {
        width: 35px;
        height: 35px;
        font-size: 16px;
    }

    .icon-circle {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }

    .detail-item {
        padding: 12px;
    }
}
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Roll number suggestion functionality
        const suggestRollBtn = document.getElementById('suggest-roll-btn');
        const rollNumberInput = document.getElementById('roll_number');
        const classSelect = document.getElementById('class_id');
        const academicYearSelect = document.getElementById('academic_year_id');
        const rollNumberStatus = document.getElementById('roll-number-status');
        const currentEnrollmentId = {{ $enrollment->id }};

        // Function to get next available roll number
        function suggestNextRollNumber() {
            const classId = classSelect.value;
            const academicYearId = academicYearSelect.value;

            if (!classId || !academicYearId) {
                rollNumberStatus.innerHTML = '<small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Please select class and academic year first</small>';
                return;
            }

            suggestRollBtn.disabled = true;
            suggestRollBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';

            fetch(`{{ route('admin.enrollments.next-roll-number') }}?class_id=${classId}&academic_year_id=${academicYearId}&exclude_id=${currentEnrollmentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.roll_number) {
                        rollNumberInput.value = data.roll_number;
                        rollNumberStatus.innerHTML = '<small class="text-success"><i class="fas fa-check"></i> Suggested next available roll number</small>';
                    } else {
                        rollNumberStatus.innerHTML = '<small class="text-danger"><i class="fas fa-times"></i> Could not generate roll number</small>';
                    }
                })
                .catch(error => {
                    console.error('Error getting roll number:', error);
                    rollNumberStatus.innerHTML = '<small class="text-danger"><i class="fas fa-times"></i> Error getting roll number</small>';
                })
                .finally(() => {
                    suggestRollBtn.disabled = false;
                    suggestRollBtn.innerHTML = '<i class="fas fa-magic"></i> Suggest Next';
                });
        }

        // Event listeners
        suggestRollBtn.addEventListener('click', suggestNextRollNumber);
    });
</script>
@endpush

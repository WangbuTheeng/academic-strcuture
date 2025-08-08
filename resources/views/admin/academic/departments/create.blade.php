@extends('layouts.admin')

@section('title', 'Create New Department')

@section('content')
<div class="container-fluid">
     <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-dark">Create New Department</h1>
            <p class="mb-0 text-muted">Add a new department to a faculty</p>
        </div>
        <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Back to Departments
        </a>
    </div>

    <!-- Create Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Department Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.departments.store') }}">
                        @csrf

                        <!-- Faculty Selection -->
                        <div class="mb-3">
                            <label for="faculty_id" class="form-label">
                                Faculty <span class="text-danger">*</span>
                            </label>
                            <select name="faculty_id" id="faculty_id"
                                    class="form-select @error('faculty_id') is-invalid @enderror" required>
                                <option value="">Select Faculty</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->name }} ({{ $faculty->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('faculty_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select the faculty this department belongs to</div>
                        </div>

                        <!-- Department Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Department Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g., Computer Science Department" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Enter the full name of the department</div>
                        </div>

                        <!-- Department Code -->
                        <div class="mb-3">
                            <label for="code" class="form-label">
                                Department Code
                            </label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}"
                                   class="form-control @error('code') is-invalid @enderror"
                                   placeholder="e.g., CS" maxlength="10">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional short code for the department (max 10 characters)</div>
                        </div>

                        <!-- Information Box -->
                        <div class="alert alert-info">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-info-circle text-info"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-info">Department Information</h6>
                                    <ul class="mb-0 text-info">
                                        <li>Departments are organizational units within faculties</li>
                                        <li>Each department can offer multiple programs and subjects</li>
                                        <li>Department codes help with quick identification and reporting</li>
                                        <li>Examples: Computer Science, Mathematics, Physics, Business Administration</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Card -->
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-dark">Preview</h6>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3">
                                        <div class="avatar-title bg-primary rounded-circle" id="preview-initials">
                                            --
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" id="preview-name">
                                            Department Name
                                        </div>
                                        <div class="text-muted">
                                            Faculty: <span id="preview-faculty">Select Faculty</span>
                                            • Code: <span id="preview-code">--</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Create Department
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="row justify-content-center mt-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Next Steps</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <span class="badge bg-primary rounded-circle">1</span>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark">Create Department</h6>
                                    <p class="text-muted mb-0">Complete this form to create the department</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <span class="badge bg-secondary rounded-circle">2</span>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark">Add Programs</h6>
                                    <p class="text-muted mb-0">Create academic programs within this department</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <span class="badge bg-secondary rounded-circle">3</span>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark">Define Subjects</h6>
                                    <p class="text-muted mb-0">Add subjects and courses offered by the department</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <span class="badge bg-secondary rounded-circle">4</span>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark">Assign Classes</h6>
                                    <p class="text-muted mb-0">Link classes to this department for organization</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.form-label {
    font-weight: 600;
    color: #374151;
}

.form-text {
    font-size: 0.875rem;
    color: #6b7280;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.text-danger {
    color: #dc3545 !important;
}

.avatar-sm {
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
}

.text-dark {
    color: #212529 !important;
}
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const facultySelect = document.getElementById('faculty_id');
        const nameInput = document.getElementById('name');
        const codeInput = document.getElementById('code');
        const previewName = document.getElementById('preview-name');
        const previewFaculty = document.getElementById('preview-faculty');
        const previewCode = document.getElementById('preview-code');
        const previewInitials = document.getElementById('preview-initials');

        function updatePreview() {
            const name = nameInput.value || 'Department Name';
            const code = codeInput.value || '--';
            const facultyText = facultySelect.options[facultySelect.selectedIndex]?.text || 'Select Faculty';

            previewName.textContent = name;
            previewFaculty.textContent = facultyText;
            previewCode.textContent = code;

            // Generate initials
            const words = name.split(' ');
            let initials = '';
            if (words.length >= 2) {
                initials = words[0].charAt(0) + words[1].charAt(0);
            } else if (words.length === 1 && words[0].length >= 2) {
                initials = words[0].charAt(0) + words[0].charAt(1);
            } else {
                initials = words[0].charAt(0) || '--';
            }

            previewInitials.textContent = initials.toUpperCase();
        }

        facultySelect.addEventListener('change', updatePreview);
        nameInput.addEventListener('input', updatePreview);
        codeInput.addEventListener('input', updatePreview);

        // Auto-generate code from name
        nameInput.addEventListener('input', function() {
            if (!codeInput.value) {
                const words = this.value.split(' ');
                let autoCode = '';

                if (words.length >= 2) {
                    // Take first letter of each word
                    autoCode = words.map(word => word.charAt(0)).join('').toUpperCase();
                } else if (words.length === 1 && words[0].length >= 2) {
                    // Take first 2-3 letters
                    autoCode = words[0].substring(0, Math.min(3, words[0].length)).toUpperCase();
                }

                if (autoCode.length <= 10) {
                    codeInput.value = autoCode;
                    updatePreview();
                }
            }
        });

        // Initial preview update
        updatePreview();
    });
</script>
@endpush
@endsection

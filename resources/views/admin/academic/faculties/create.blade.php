@extends('layouts.admin')

@section('title', 'Create New Faculty')
@section('page-title', 'Create New Faculty')

@section('content')
<div class="container-fluid">
    <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Create New Faculty</h1>
            <p class="mb-0 text-muted">Add a new faculty to the academic structure</p>
        </div>
        <div>
            <a href="{{ route('admin.faculties.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Faculties
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Faculty Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.faculties.store') }}">
                        @csrf

                        <!-- Faculty Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Faculty Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g., Faculty of Science">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Enter the full name of the faculty</div>
                        </div>

                        <!-- Faculty Code -->
                        <div class="mb-3">
                            <label for="code" class="form-label">
                                Faculty Code <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                   class="form-control @error('code') is-invalid @enderror"
                                   placeholder="e.g., FOS"
                                   maxlength="10">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Short code for the faculty (max 10 characters)</div>
                        </div>

                        <!-- Information Box -->
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>Faculty Information
                            </h6>
                            <ul class="mb-0">
                                <li>Faculties are the top-level academic divisions in your institution</li>
                                <li>Each faculty can contain multiple departments</li>
                                <li>Faculty codes should be unique and easily recognizable</li>
                                <li>Common examples: FOS (Faculty of Science), FOM (Faculty of Management)</li>
                            </ul>
                        </div>

                        <!-- Preview Card -->
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Preview</h6>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 40px; height: 40px;">
                                            <span class="text-white fw-bold" id="preview-initials">--</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold" id="preview-name">Faculty Name</div>
                                        <small class="text-muted">Code: <span id="preview-code">--</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('admin.faculties.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-1"></i>Create Faculty
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Next Steps</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 24px; height: 24px;">
                                        <span class="text-white fw-bold small">1</span>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold">Create Faculty</h6>
                                    <small class="text-muted">Complete this form to create the faculty</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 24px; height: 24px;">
                                        <span class="text-white fw-bold small">2</span>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold">Add Departments</h6>
                                    <small class="text-muted">Create departments within this faculty</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 24px; height: 24px;">
                                        <span class="text-white fw-bold small">3</span>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold">Configure Programs</h6>
                                    <small class="text-muted">Set up academic programs and subjects</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const codeInput = document.getElementById('code');
        const previewName = document.getElementById('preview-name');
        const previewCode = document.getElementById('preview-code');
        const previewInitials = document.getElementById('preview-initials');

        function updatePreview() {
            const name = nameInput.value || 'Faculty Name';
            const code = codeInput.value || '--';

            previewName.textContent = name;
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
                } else if (words.length === 1 && words[0].length >= 3) {
                    // Take first 3 letters
                    autoCode = words[0].substring(0, 3).toUpperCase();
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

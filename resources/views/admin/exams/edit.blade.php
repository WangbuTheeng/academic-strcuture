@extends('layouts.admin')

@section('title', 'Edit Exam')
@section('page-title', 'Edit Exam')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Exam</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.exams.index') }}">Examinations</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.exams.show', $exam) }}">{{ $exam->name }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <p class="text-muted">Update exam details and marking scheme</p>
        </div>
        <div>
            <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Exam
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Edit Exam Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.exams.update', $exam) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Exam Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $exam->name) }}" required
                               class="form-control @error('name') is-invalid @enderror">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="exam_type" class="form-label">Exam Type *</label>
                        <select name="exam_type" id="exam_type" required
                                class="form-select @error('exam_type') is-invalid @enderror"
                                onchange="toggleCustomExamType()">
                            <option value="">Select Exam Type</option>
                            <option value="assessment" {{ old('exam_type', $exam->exam_type) === 'assessment' ? 'selected' : '' }}>Assessment</option>
                            <option value="terminal" {{ old('exam_type', $exam->exam_type) === 'terminal' ? 'selected' : '' }}>Terminal Exam</option>
                            <option value="quiz" {{ old('exam_type', $exam->exam_type) === 'quiz' ? 'selected' : '' }}>Quiz</option>
                            <option value="project" {{ old('exam_type', $exam->exam_type) === 'project' ? 'selected' : '' }}>Project</option>
                            <option value="practical" {{ old('exam_type', $exam->exam_type) === 'practical' ? 'selected' : '' }}>Practical</option>
                            <option value="final" {{ old('exam_type', $exam->exam_type) === 'final' ? 'selected' : '' }}>Final Exam</option>
                            @php
                                $isCustomType = !in_array($exam->exam_type, ['assessment', 'terminal', 'quiz', 'project', 'practical', 'final']);
                            @endphp
                            <option value="custom" {{ old('exam_type') === 'custom' || $isCustomType ? 'selected' : '' }}>Custom (Write Your Own)</option>
                        </select>

                        <!-- Custom Exam Type Input -->
                        <div id="custom-exam-type-container" class="mt-2" style="display: {{ old('exam_type') === 'custom' || $isCustomType ? 'block' : 'none' }};">
                            <input type="text" name="custom_exam_type" id="custom_exam_type"
                                   class="form-control @error('custom_exam_type') is-invalid @enderror"
                                   placeholder="Enter custom exam type (e.g., Unit Test, Monthly Test, etc.)"
                                   value="{{ old('custom_exam_type', $isCustomType ? $exam->exam_type : '') }}"
                                   maxlength="50">
                            <small class="text-muted">Enter your custom exam type name (max 50 characters)</small>
                            @error('custom_exam_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @error('exam_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label for="academic_year_id" class="form-label">Academic Year *</label>
                        <select name="academic_year_id" id="academic_year_id" required
                                class="form-select @error('academic_year_id') is-invalid @enderror">
                            <option value="">Select Academic Year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id', $exam->academic_year_id) == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="exam_type" class="form-label">Exam Type</label>
                        <select name="exam_type" id="exam_type"
                                class="form-select @error('exam_type') is-invalid @enderror" required>
                            <option value="">Select Exam Type</option>
                            <option value="terminal" {{ old('exam_type', $exam->exam_type) == 'terminal' ? 'selected' : '' }}>Terminal Exam</option>
                            <option value="final" {{ old('exam_type', $exam->exam_type) == 'final' ? 'selected' : '' }}>Final Exam</option>
                            <option value="midterm" {{ old('exam_type', $exam->exam_type) == 'midterm' ? 'selected' : '' }}>Midterm Exam</option>
                            <option value="unit" {{ old('exam_type', $exam->exam_type) == 'unit' ? 'selected' : '' }}>Unit Test</option>
                            <option value="monthly" {{ old('exam_type', $exam->exam_type) == 'monthly' ? 'selected' : '' }}>Monthly Test</option>
                        </select>
                        @error('exam_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Class and Subject -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label for="class_id" class="form-label">Class</label>
                        <select name="class_id" id="class_id"
                                class="form-select @error('class_id') is-invalid @enderror">
                            <option value="">Select Class (Optional)</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id', $exam->class_id) == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} ({{ $class->level->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="subject_id" class="form-label">Subject</label>
                        <select name="subject_id" id="subject_id"
                                class="form-select @error('subject_id') is-invalid @enderror">
                            <option value="">Select Subject (Optional)</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $exam->subject_id) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }} ({{ $subject->department->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Marking Scheme -->
                <hr class="mb-4">
                <h5 class="mb-3">Marking Scheme</h5>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label for="max_marks" class="form-label">Maximum Marks *</label>
                        <input type="number" name="max_marks" id="max_marks" value="{{ old('max_marks', $exam->max_marks) }}"
                               min="1" max="1000" step="0.01" required
                               class="form-control @error('max_marks') is-invalid @enderror">
                        @error('max_marks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="theory_max" class="form-label">Theory Marks *</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="number" name="theory_max" id="theory_max" value="{{ old('theory_max', $exam->theory_max) }}"
                                       min="0" step="0.01" required
                                       class="form-control @error('theory_max') is-invalid @enderror"
                                       placeholder="Max marks">
                                @error('theory_max')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <input type="number" name="theory_pass_marks" id="theory_pass_marks" value="{{ old('theory_pass_marks', $exam->theory_pass_marks) }}"
                                       min="0" step="0.01"
                                       class="form-control @error('theory_pass_marks') is-invalid @enderror"
                                       placeholder="Pass marks">
                                @error('theory_pass_marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <small class="text-muted">Max marks / Minimum passing marks</small>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Practical Marks</label>
                        <div class="form-check mb-2">
                            <input type="checkbox" name="has_practical" id="has_practical" value="1"
                                   {{ old('has_practical', $exam->has_practical) ? 'checked' : '' }}
                                   class="form-check-input">
                            <label for="has_practical" class="form-check-label">Has Practical Component</label>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <input type="number" name="practical_max" id="practical_max" value="{{ old('practical_max', $exam->practical_max) }}"
                                       min="0" step="0.01" placeholder="Max marks"
                                       class="form-control @error('practical_max') is-invalid @enderror">
                                @error('practical_max')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <input type="number" name="practical_pass_marks" id="practical_pass_marks" value="{{ old('practical_pass_marks', $exam->practical_pass_marks) }}"
                                       min="0" step="0.01" placeholder="Pass marks"
                                       class="form-control @error('practical_pass_marks') is-invalid @enderror">
                                @error('practical_pass_marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <small class="text-muted">Max marks / Minimum passing marks</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Assessment Marks</label>
                        <div class="form-check mb-2">
                            <input type="checkbox" name="has_assessment" id="has_assessment" value="1"
                                   {{ old('has_assessment', $exam->has_assessment) ? 'checked' : '' }}
                                   class="form-check-input">
                            <label for="has_assessment" class="form-check-label">Has Assessment Component</label>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <input type="number" name="assess_max" id="assess_max" value="{{ old('assess_max', $exam->assess_max) }}"
                                       min="0" step="0.01" placeholder="Max marks"
                                       class="form-control @error('assess_max') is-invalid @enderror">
                                @error('assess_max')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <input type="number" name="assess_pass_marks" id="assess_pass_marks" value="{{ old('assess_pass_marks', $exam->assess_pass_marks) }}"
                                       min="0" step="0.01" placeholder="Pass marks"
                                       class="form-control @error('assess_pass_marks') is-invalid @enderror">
                                @error('assess_pass_marks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <small class="text-muted">Max marks / Minimum passing marks</small>
                    </div>
                </div>

                <!-- Dates -->
                <hr class="mb-4">
                <h5 class="mb-3">Schedule</h5>

                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label for="start_date" class="form-label">Start Date *</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $exam->start_date->format('Y-m-d')) }}" required
                               class="form-control @error('start_date') is-invalid @enderror">
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="end_date" class="form-label">End Date *</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $exam->end_date->format('Y-m-d')) }}" required
                               class="form-control @error('end_date') is-invalid @enderror">
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="submission_deadline" class="form-label">Submission Deadline *</label>
                        <input type="datetime-local" name="submission_deadline" id="submission_deadline"
                               value="{{ old('submission_deadline', $exam->submission_deadline->format('Y-m-d\TH:i')) }}" required
                               class="form-control @error('submission_deadline') is-invalid @enderror">
                        @error('submission_deadline')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Grading Scale -->
                <hr class="mb-4">
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label for="grading_scale_id" class="form-label">Grading Scale</label>
                        <select name="grading_scale_id" id="grading_scale_id"
                                class="form-select @error('grading_scale_id') is-invalid @enderror">
                            <option value="">Select Grading Scale (Optional)</option>
                            @foreach($gradingScales as $scale)
                                <option value="{{ $scale->id }}" {{ old('grading_scale_id', $exam->grading_scale_id) == $scale->id ? 'selected' : '' }}>
                                    {{ $scale->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('grading_scale_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <hr class="mb-4">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Update Exam
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Auto-calculate total marks
        function calculateTotal() {
            const theory = parseFloat(document.getElementById('theory_max').value) || 0;
            const practical = parseFloat(document.getElementById('practical_max').value) || 0;
            const assessment = parseFloat(document.getElementById('assess_max').value) || 0;
            const total = theory + practical + assessment;
            document.getElementById('max_marks').value = total;
        }

        // Add event listeners
        document.getElementById('theory_max').addEventListener('input', calculateTotal);
        document.getElementById('practical_max').addEventListener('input', calculateTotal);
        document.getElementById('assess_max').addEventListener('input', calculateTotal);

        // Toggle practical and assessment fields
        document.getElementById('has_practical').addEventListener('change', function() {
            const practicalField = document.getElementById('practical_max');
            if (this.checked) {
                practicalField.removeAttribute('disabled');
                practicalField.focus();
            } else {
                practicalField.setAttribute('disabled', 'disabled');
                practicalField.value = '';
                calculateTotal();
            }
        });

        document.getElementById('has_assessment').addEventListener('change', function() {
            const assessmentField = document.getElementById('assess_max');
            if (this.checked) {
                assessmentField.removeAttribute('disabled');
                assessmentField.focus();
            } else {
                assessmentField.setAttribute('disabled', 'disabled');
                assessmentField.value = '';
                calculateTotal();
            }
        });

        function toggleCustomExamType() {
            const select = document.getElementById('exam_type');
            const container = document.getElementById('custom-exam-type-container');
            const customInput = document.getElementById('custom_exam_type');

            if (select.value === 'custom') {
                container.style.display = 'block';
                customInput.required = true;
                customInput.focus();
            } else {
                container.style.display = 'none';
                customInput.required = false;
                customInput.value = '';
            }
        }

        // Initialize disabled state
        if (!document.getElementById('has_practical').checked) {
            document.getElementById('practical_max').setAttribute('disabled', 'disabled');
        }
        if (!document.getElementById('has_assessment').checked) {
            document.getElementById('assess_max').setAttribute('disabled', 'disabled');
        }

        // Initialize custom exam type visibility
        toggleCustomExamType();
    </script>
@endpush

@endsection

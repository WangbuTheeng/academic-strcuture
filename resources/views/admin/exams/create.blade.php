@extends('layouts.admin')

@section('title', 'Create New Exam')

@section('content')
<div class="container-fluid px-4">
    <!-- Modern Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800 fw-bold">
                <i class="fas fa-plus-circle text-primary me-2"></i>Create New Exam
            </h1>
            <p class="text-muted mb-0">Set up a new examination with flexible marking scheme</p>
        </div>
        <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Exams
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.exams.store') }}">
                        @csrf

                        <!-- Basic Information -->
                        <div class="mb-4">
                            <h6 class="text-primary fw-bold mb-3">
                                <i class="fas fa-info-circle me-2"></i>Basic Information
                            </h6>

                            <!-- Exam Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">
                                    Exam Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="e.g., Terminal Examination 2081">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <!-- Exam Type -->
                                <div class="col-md-6 mb-3">
                                    <label for="exam_type" class="form-label fw-semibold">
                                        Exam Type <span class="text-danger">*</span>
                                    </label>
                                    <select name="exam_type" id="exam_type" required
                                            class="form-select @error('exam_type') is-invalid @enderror"
                                            onchange="toggleCustomExamType()">
                                        <option value="">Select Exam Type</option>
                                        <option value="assessment" {{ old('exam_type') == 'assessment' ? 'selected' : '' }}>Assessment</option>
                                        <option value="terminal" {{ old('exam_type') == 'terminal' ? 'selected' : '' }}>Terminal Exam</option>
                                        <option value="quiz" {{ old('exam_type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                        <option value="project" {{ old('exam_type') == 'project' ? 'selected' : '' }}>Project</option>
                                        <option value="practical" {{ old('exam_type') == 'practical' ? 'selected' : '' }}>Practical</option>
                                        <option value="final" {{ old('exam_type') == 'final' ? 'selected' : '' }}>Final Exam</option>
                                        <option value="custom" {{ old('exam_type') == 'custom' ? 'selected' : '' }}>Custom (Write Your Own)</option>
                                    </select>

                                    <!-- Custom Exam Type Input -->
                                    <div id="custom-exam-type-container" class="mt-2" style="display: none;">
                                        <input type="text" name="custom_exam_type" id="custom_exam_type"
                                               class="form-control @error('custom_exam_type') is-invalid @enderror"
                                               placeholder="Enter custom exam type (e.g., Unit Test, Monthly Test, etc.)"
                                               value="{{ old('custom_exam_type') }}"
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

                                <!-- Academic Year -->
                                <div class="col-md-6 mb-3">
                                    <label for="academic_year_id" class="form-label fw-semibold">
                                        Academic Year <span class="text-danger">*</span>
                                    </label>
                                    <select name="academic_year_id" id="academic_year_id" required
                                            class="form-select @error('academic_year_id') is-invalid @enderror">
                                        <option value="">Select Academic Year</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                                {{ $year->name }}
                                                @if($year->is_current)
                                                    (Current)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_year_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Scope Selection -->
                        <div class="mb-4">
                            <h6 class="text-success fw-bold mb-3">
                                <i class="fas fa-bullseye me-2"></i>Exam Scope
                            </h6>

                            <div class="row">

                                <!-- Class -->
                                <div class="col-md-6 mb-3">
                                    <label for="class_id" class="form-label fw-semibold">
                                        Class
                                    </label>
                                    <select name="class_id" id="class_id"
                                            class="form-select @error('class_id') is-invalid @enderror">
                                        <option value="">All Classes</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }} ({{ $class->level->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Subject -->
                                <div class="col-md-6 mb-3">
                                    <label for="subject_id" class="form-label fw-semibold">
                                        Subject
                                    </label>
                                    <select name="subject_id" id="subject_id"
                                            class="form-select @error('subject_id') is-invalid @enderror">
                                        <option value="">All Subjects</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }} ({{ $subject->department->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Marking Scheme -->
                        <div class="mb-4">
                            <h6 class="text-warning fw-bold mb-3">
                                <i class="fas fa-calculator me-2"></i>Marking Scheme
                            </h6>

                            <div class="row">
                                <!-- Maximum Marks -->
                                <div class="col-md-6 mb-3">
                                    <label for="max_marks" class="form-label fw-semibold">
                                        Maximum Marks <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="max_marks" id="max_marks" value="{{ old('max_marks', 100) }}"
                                           min="1" max="1000" step="1" required
                                           class="form-control @error('max_marks') is-invalid @enderror"
                                           onchange="updateMarkDistribution()">
                                    @error('max_marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Theory Marks -->
                                <div class="col-md-6 mb-3">
                                    <label for="theory_max" class="form-label fw-semibold">
                                        Theory Marks <span class="text-danger">*</span>
                                    </label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="number" name="theory_max" id="theory_max" value="{{ old('theory_max', 100) }}"
                                                   min="0" step="1" required
                                                   class="form-control @error('theory_max') is-invalid @enderror"
                                                   onchange="updateMarkDistribution(); updatePassMarks('theory')"
                                                   placeholder="Max marks">
                                            @error('theory_max')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <input type="number" name="theory_pass_marks" id="theory_pass_marks" value="{{ old('theory_pass_marks', 32) }}"
                                                   min="0" step="1"
                                                   class="form-control @error('theory_pass_marks') is-invalid @enderror"
                                                   placeholder="Pass marks">
                                            @error('theory_pass_marks')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <small class="text-muted">Max marks / Minimum passing marks</small>
                                </div>

                                <!-- Practical Marks -->
                                <div class="col-md-6 mb-3">
                                    <label for="practical_max" class="form-label fw-semibold">
                                        Practical Marks
                                    </label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="number" name="practical_max" id="practical_max" value="{{ old('practical_max', 0) }}"
                                                   min="0" step="1"
                                                   class="form-control @error('practical_max') is-invalid @enderror"
                                                   onchange="updateMarkDistribution(); updatePassMarks('practical')"
                                                   placeholder="Max marks">
                                            @error('practical_max')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <input type="number" name="practical_pass_marks" id="practical_pass_marks" value="{{ old('practical_pass_marks', 0) }}"
                                                   min="0" step="1"
                                                   class="form-control @error('practical_pass_marks') is-invalid @enderror"
                                                   placeholder="Pass marks">
                                            @error('practical_pass_marks')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <small class="text-muted">Max marks / Minimum passing marks</small>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="has_practical" value="1" {{ old('has_practical') ? 'checked' : '' }}
                                               class="form-check-input" id="has_practical"
                                               onchange="togglePractical()">
                                        <label class="form-check-label" for="has_practical">
                                            Has Practical Component
                                        </label>
                                    </div>
                                </div>

                                <!-- Assessment Marks -->
                                <div class="col-md-6 mb-3">
                                    <label for="assess_max" class="form-label fw-semibold">
                                        Assessment Marks
                                    </label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="number" name="assess_max" id="assess_max" value="{{ old('assess_max', 0) }}"
                                                   min="0" step="1"
                                                   class="form-control @error('assess_max') is-invalid @enderror"
                                                   onchange="updateMarkDistribution(); updatePassMarks('assess')"
                                                   placeholder="Max marks">
                                            @error('assess_max')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <input type="number" name="assess_pass_marks" id="assess_pass_marks" value="{{ old('assess_pass_marks', 0) }}"
                                                   min="0" step="1"
                                                   class="form-control @error('assess_pass_marks') is-invalid @enderror"
                                                   placeholder="Pass marks">
                                            @error('assess_pass_marks')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <small class="text-muted">Max marks / Minimum passing marks</small>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="has_assessment" value="1" {{ old('has_assessment') ? 'checked' : '' }}
                                               class="form-check-input" id="has_assessment"
                                               onchange="toggleAssessment()">
                                        <label class="form-check-label" for="has_assessment">
                                            Has Assessment Component
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Mark Distribution Summary -->
                            <div class="alert alert-light border">
                                <h6 class="fw-bold mb-2">Mark Distribution Summary</h6>
                                <div id="mark-summary" class="small">
                                    <div>Theory: <span id="theory-display">100</span> marks</div>
                                    <div>Practical: <span id="practical-display">0</span> marks</div>
                                    <div>Assessment: <span id="assessment-display">0</span> marks</div>
                                    <div class="fw-bold mt-2">Total: <span id="total-display">100</span> marks</div>
                                </div>
                                <div id="mark-validation" class="mt-2 small"></div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Schedule -->
                        <div class="mb-4">
                            <h6 class="text-info fw-bold mb-3">
                                <i class="fas fa-calendar me-2"></i>Schedule
                            </h6>

                            <div class="row">
                                <!-- Start Date -->
                                <div class="col-md-4 mb-3">
                                    <label for="start_date" class="form-label fw-semibold">
                                        Start Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                                           class="form-control @error('start_date') is-invalid @enderror">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- End Date -->
                                <div class="col-md-4 mb-3">
                                    <label for="end_date" class="form-label fw-semibold">
                                        End Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required
                                           class="form-control @error('end_date') is-invalid @enderror">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Submission Deadline -->
                                <div class="col-md-4 mb-3">
                                    <label for="submission_deadline" class="form-label fw-semibold">
                                        Submission Deadline <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" name="submission_deadline" id="submission_deadline" value="{{ old('submission_deadline') }}" required
                                           class="form-control @error('submission_deadline') is-invalid @enderror">
                                    @error('submission_deadline')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top">
                            <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Create Exam
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Tips Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-lightbulb text-warning me-2"></i>Quick Tips
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="mb-3">
                            <strong class="text-primary">📝 Exam Types:</strong>
                            <ul class="mt-1 mb-0 ps-3">
                                <li><strong>Assessment:</strong> Internal evaluations</li>
                                <li><strong>Terminal:</strong> Mid-term exams</li>
                                <li><strong>Final:</strong> End-of-term exams</li>
                                <li><strong>Quiz:</strong> Short tests</li>
                                <li><strong>Practical:</strong> Lab/hands-on exams</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <strong class="text-success">🎯 Scope Selection:</strong>
                            <ul class="mt-1 mb-0 ps-3">
                                <li>Leave fields empty for "All"</li>
                                <li>Select specific class/subject for targeted exams</li>
                            </ul>
                        </div>

                        <div class="mb-0">
                            <strong class="text-warning">⚖️ Mark Distribution:</strong>
                            <ul class="mt-1 mb-0 ps-3">
                                <li>Total components must equal maximum marks</li>
                                <li>Use checkboxes to enable practical/assessment</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Exams Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-clock text-info me-2"></i>Recent Exams
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small text-muted">
                        <p class="mb-2">📊 <strong>Terminal Exam 2081</strong><br>
                        <span class="text-muted">Created 2 days ago</span></p>

                        <p class="mb-2">📝 <strong>Assessment - Math</strong><br>
                        <span class="text-muted">Created 1 week ago</span></p>

                        <p class="mb-0">🧪 <strong>Practical - Science</strong><br>
                        <span class="text-muted">Created 2 weeks ago</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function updateMarkDistribution() {
        const maxMarks = parseFloat(document.getElementById('max_marks').value) || 0;
        const theoryMarks = parseFloat(document.getElementById('theory_max').value) || 0;
        const practicalMarks = parseFloat(document.getElementById('practical_max').value) || 0;
        const assessMarks = parseFloat(document.getElementById('assess_max').value) || 0;

        const total = theoryMarks + practicalMarks + assessMarks;

        // Update display
        document.getElementById('theory-display').textContent = theoryMarks;
        document.getElementById('practical-display').textContent = practicalMarks;
        document.getElementById('assessment-display').textContent = assessMarks;
        document.getElementById('total-display').textContent = total;

        // Validation
        const validationDiv = document.getElementById('mark-validation');
        if (total !== maxMarks) {
            validationDiv.innerHTML = `<span class="text-danger">⚠️ Total component marks (${total}) must equal maximum marks (${maxMarks})</span>`;
        } else {
            validationDiv.innerHTML = `<span class="text-success">✓ Mark distribution is valid</span>`;
        }
    }

    function updatePassMarks(component) {
        const maxInput = document.getElementById(component + '_max');
        const passInput = document.getElementById(component + '_pass_marks');
        const maxValue = parseFloat(maxInput.value) || 0;

        // Auto-set pass marks to 32% of max marks if not already set
        if (maxValue > 0 && (!passInput.value || parseFloat(passInput.value) === 0)) {
            const suggestedPass = Math.round(maxValue * 0.32);
            passInput.value = suggestedPass;
        }
    }

    function togglePractical() {
        const checkbox = document.querySelector('input[name="has_practical"]');
        const input = document.getElementById('practical_max');

        if (!checkbox.checked) {
            input.value = 0;
            updateMarkDistribution();
        }
    }

    function toggleAssessment() {
        const checkbox = document.querySelector('input[name="has_assessment"]');
        const input = document.getElementById('assess_max');

        if (!checkbox.checked) {
            input.value = 0;
            updateMarkDistribution();
        }
    }

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

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateMarkDistribution();
        toggleCustomExamType(); // Initialize custom exam type visibility
    });
</script>
@endpush

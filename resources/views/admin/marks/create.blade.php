@extends('layouts.admin')

@section('title', 'Enter Marks')
@section('page-title', 'Enter Marks')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.marks.index') }}">Mark Entry</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.marks.exam-dashboard', $exam) }}">{{ $exam->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                @if(request('subject_id'))
                    {{ \App\Models\Subject::find(request('subject_id'))->name ?? 'Subject' }}
                @else
                    Enter Marks
                @endif
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Enter Marks</h1>
            <p class="mb-0 text-muted">
                {{ $exam->name }} - {{ $exam->academicYear?->name ?? 'No Academic Year' }}
                @if(request('subject_id'))
                    <span class="badge badge-primary ml-2">{{ \App\Models\Subject::find(request('subject_id'))->name ?? 'Subject' }}</span>
                @endif
            </p>
        </div>
        <a href="{{ route('admin.marks.exam-dashboard', $exam) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Exam Information Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Exam Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Exam:</strong> {{ $exam->name }}
                </div>
                <div class="col-md-3">
                    <strong>Academic Year:</strong> {{ $exam->academicYear?->name ?? 'No Academic Year' }}
                </div>
                <div class="col-md-3">
                    <strong>Class:</strong> {{ $exam->class ? $exam->class->name : 'All Classes' }}
                </div>
                <div class="col-md-3">
                    <strong>Subject:</strong> {{ $exam->subject ? $exam->subject->name : 'Multiple Subjects' }}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-3">
                    <strong>Max Marks:</strong> {{ $exam->max_marks }}
                </div>
                <div class="col-md-3">
                    <strong>Theory Max:</strong> {{ $exam->theory_max ?? 'N/A' }}
                </div>
                <div class="col-md-3">
                    <strong>Practical Max:</strong> {{ $exam->practical_max ?? 'N/A' }}
                </div>
                <div class="col-md-3">
                    <strong>Assessment Max:</strong> {{ $exam->assess_max ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Subject Selection (if multiple subjects) -->
    @if($subjects->count() > 1)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Quick Subject Navigation</h6>
            <small class="text-muted">Click on a subject to enter marks for that subject</small>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($subjects as $subject)
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.marks.create', ['exam_id' => $exam->id, 'subject_id' => $subject->id]) }}"
                           class="btn btn-{{ $subjectId == $subject->id ? 'primary' : 'outline-primary' }} btn-block">
                            <i class="fas fa-book"></i> {{ $subject->name }}
                            @if($subjectId == $subject->id)
                                <i class="fas fa-check ml-1"></i>
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Traditional dropdown for backup -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('admin.marks.create') }}">
                        <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                        @if($classId)
                            <input type="hidden" name="class_id" value="{{ $classId }}">
                        @endif
                        <select name="subject_id" class="form-select" onchange="this.form.submit()">
                            <option value="">Or select from dropdown...</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }} ({{ $subject->code }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Mark Entry Form -->
    @if($subjectId || $subjects->count() == 1)
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Enter Marks - {{ $subjects->first()->name ?? $subjects->where('id', $subjectId)->first()->name }}
            </h6>
            <span class="badge badge-info">{{ $students->count() }} Students</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.marks.store') }}" id="marksForm">
                @csrf
                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                <input type="hidden" name="subject_id" value="{{ $subjectId ?: $subjects->first()->id }}">

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Roll No.</th>
                                <th>Student Name</th>
                                <th>Class</th>
                                @if($exam->assess_max)
                                    <th>Assessment<br><small>(Max: {{ $exam->assess_max }})</small></th>
                                @endif
                                @if($exam->theory_max)
                                    <th>Theory<br><small>(Max: {{ $exam->theory_max }})</small></th>
                                @endif
                                @if($exam->practical_max)
                                    <th>Practical<br><small>(Max: {{ $exam->practical_max }})</small></th>
                                @endif
                                <th>Total</th>
                                <th>Percentage</th>
                                <th>Grade</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php
                                    $markKey = $student->id . '_' . ($subjectId ?: $subjects->first()->id);
                                    $existingMark = $existingMarks->get($markKey);
                                @endphp
                                <tr>
                                    <td>{{ $student->roll_number }}</td>
                                    <td>{{ $student->full_name }}</td>
                                    <td>{{ $student->currentEnrollment->class->name ?? 'N/A' }}</td>
                                    
                                    <input type="hidden" name="marks[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                                    
                                    @if($exam->assess_max)
                                        <td>
                                            <input type="number"
                                                   name="marks[{{ $loop->index }}][assess_marks]"
                                                   class="form-control assess-marks mark-input"
                                                   min="0"
                                                   max="{{ $exam->assess_max }}"
                                                   step="0.01"
                                                   value="{{ $existingMark->assess_marks ?? '' }}"
                                                   data-row="{{ $loop->index }}"
                                                   placeholder="0.00">
                                        </td>
                                    @endif
                                    
                                    @if($exam->theory_max)
                                        <td>
                                            <input type="number"
                                                   name="marks[{{ $loop->index }}][theory_marks]"
                                                   class="form-control theory-marks mark-input"
                                                   min="0"
                                                   max="{{ $exam->theory_max }}"
                                                   step="0.01"
                                                   value="{{ $existingMark->theory_marks ?? '' }}"
                                                   data-row="{{ $loop->index }}"
                                                   placeholder="0.00">
                                        </td>
                                    @endif
                                    
                                    @if($exam->practical_max)
                                        <td>
                                            <input type="number"
                                                   name="marks[{{ $loop->index }}][practical_marks]"
                                                   class="form-control practical-marks mark-input"
                                                   min="0"
                                                   max="{{ $exam->practical_max }}"
                                                   step="0.01"
                                                   value="{{ $existingMark->practical_marks ?? '' }}"
                                                   data-row="{{ $loop->index }}"
                                                   placeholder="0.00">
                                        </td>
                                    @endif
                                    
                                    <td>
                                        <input type="text" 
                                               class="form-control total-marks" 
                                               readonly 
                                               data-row="{{ $loop->index }}"
                                               value="{{ $existingMark->total ?? '' }}">
                                    </td>
                                    
                                    <td>
                                        <input type="text" 
                                               class="form-control percentage" 
                                               readonly 
                                               data-row="{{ $loop->index }}"
                                               value="{{ $existingMark->percentage ?? '' }}">
                                    </td>
                                    
                                    <td>
                                        <input type="text" 
                                               class="form-control grade" 
                                               readonly 
                                               data-row="{{ $loop->index }}"
                                               value="{{ $existingMark->grade ?? '' }}">
                                    </td>
                                    
                                    <td>
                                        <input type="text" 
                                               name="marks[{{ $loop->index }}][remarks]" 
                                               class="form-control" 
                                               placeholder="Optional remarks..."
                                               value="{{ $existingMark->remarks ?? '' }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Marks
                        </button>
                        <button type="button" class="btn btn-success ms-2" onclick="submitForApproval()">
                            <i class="fas fa-check"></i> Save & Submit for Approval
                        </button>
                        <a href="{{ route('admin.marks.index') }}" class="btn btn-secondary ms-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if(!$subjectId && $subjects->count() > 1)
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Please select a subject to enter marks.
    </div>
    @endif

    @if($students->count() == 0)
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> No students found for this exam. Please check the exam configuration.
    </div>
    @endif
</div>

@push('styles')
<style>
.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.mark-input:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate totals, percentages, and grades
    function calculateMarks(row) {
        const assessMarks = parseFloat(document.querySelector(`input[data-row="${row}"].assess-marks`)?.value || 0);
        const theoryMarks = parseFloat(document.querySelector(`input[data-row="${row}"].theory-marks`)?.value || 0);
        const practicalMarks = parseFloat(document.querySelector(`input[data-row="${row}"].practical-marks`)?.value || 0);
        
        const total = assessMarks + theoryMarks + practicalMarks;
        const maxMarks = {{ $exam->max_marks }};
        const percentage = maxMarks > 0 ? (total / maxMarks * 100).toFixed(2) : 0;
        
        // Update total and percentage
        document.querySelector(`input[data-row="${row}"].total-marks`).value = total.toFixed(2);
        document.querySelector(`input[data-row="${row}"].percentage`).value = percentage;
        
        // Calculate grade (simple A-F grading)
        let grade = 'F';
        if (percentage >= 90) grade = 'A+';
        else if (percentage >= 80) grade = 'A';
        else if (percentage >= 70) grade = 'B+';
        else if (percentage >= 60) grade = 'B';
        else if (percentage >= 50) grade = 'C+';
        else if (percentage >= 40) grade = 'C';
        else if (percentage >= 32) grade = 'D';
        
        document.querySelector(`input[data-row="${row}"].grade`).value = grade;
    }
    
    // Add event listeners to all mark input fields
    document.querySelectorAll('.assess-marks, .theory-marks, .practical-marks').forEach(input => {
        input.addEventListener('input', function() {
            const row = this.getAttribute('data-row');
            validateMarks(this);
            calculateMarks(row);
        });

        input.addEventListener('blur', function() {
            validateMarks(this);
        });

        // Calculate on page load for existing marks
        const row = input.getAttribute('data-row');
        calculateMarks(row);
    });

    // Validation function
    function validateMarks(input) {
        const value = parseFloat(input.value);
        const max = parseFloat(input.getAttribute('max'));
        const min = parseFloat(input.getAttribute('min'));

        // Remove previous error styling
        input.classList.remove('is-invalid');
        const existingError = input.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }

        let errorMessage = '';

        if (isNaN(value)) {
            return; // Empty is allowed
        }

        if (value < min) {
            errorMessage = `Marks cannot be less than ${min}`;
        } else if (value > max) {
            errorMessage = `Marks cannot exceed ${max}`;
        }

        if (errorMessage) {
            input.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = errorMessage;
            input.parentNode.appendChild(errorDiv);
        }
    }

    // Form validation before submit
    document.getElementById('marksForm').addEventListener('submit', function(e) {
        let hasErrors = false;

        document.querySelectorAll('.assess-marks, .theory-marks, .practical-marks').forEach(input => {
            validateMarks(input);
            if (input.classList.contains('is-invalid')) {
                hasErrors = true;
            }
        });

        if (hasErrors) {
            e.preventDefault();
            alert('Please fix the validation errors before submitting.');
            return false;
        }
    });
});

function submitForApproval() {
    if (confirm('Are you sure you want to submit these marks for approval? You will not be able to edit them after submission.')) {
        const form = document.getElementById('marksForm');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'submit_for_approval';
        input.value = '1';
        form.appendChild(input);
        form.submit();
    }
}
</script>
@endpush
@endsection

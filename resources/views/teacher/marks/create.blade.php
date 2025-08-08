@extends('layouts.teacher')

@section('title', 'Enter Marks - ' . $exam->name)
@section('page-title', 'Enter Marks')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Enter Marks</h1>
            <p class="mb-0 text-muted">{{ $exam->name }} - {{ $exam->subject->name }}</p>
        </div>
        <div>
            <a href="{{ route('teacher.marks.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Mark Entry
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Exam Information -->
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
                    <strong>Subject:</strong> {{ $exam->subject->name }}
                </div>
                <div class="col-md-3">
                    <strong>Class:</strong> {{ $exam->class->name }}
                </div>
                <div class="col-md-3">
                    <strong>Max Marks:</strong> {{ $exam->max_marks }}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <strong>Mark Distribution:</strong>
                    @if($exam->theory_max > 0)
                        Theory: {{ $exam->theory_max }}@if($exam->theory_pass_marks > 0) (Pass: {{ $exam->theory_pass_marks }})@endif
                    @endif
                    @if($exam->has_practical && $exam->practical_max > 0)
                        | Practical: {{ $exam->practical_max }}@if($exam->practical_pass_marks > 0) (Pass: {{ $exam->practical_pass_marks }})@endif
                    @endif
                    @if($exam->has_assessment && $exam->assess_max > 0)
                        | Assessment: {{ $exam->assess_max }}@if($exam->assess_pass_marks > 0) (Pass: {{ $exam->assess_pass_marks }})@endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Mark Entry Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Enter Marks - {{ $exam->subject->name }}
            </h6>
            <span class="badge badge-info">{{ $students->count() }} Students</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('teacher.marks.store', $exam) }}" id="marksForm">
                @csrf

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Roll No.</th>
                                <th>Student Name</th>
                                <th>Class</th>
                                
                                @if($exam->has_assessment && $exam->assess_max > 0)
                                    <th>Assessment<br><small>(Max: {{ $exam->assess_max }}@if($exam->assess_pass_marks > 0), Pass: {{ $exam->assess_pass_marks }}@endif)</small></th>
                                @endif
                                @if($exam->theory_max > 0)
                                    <th>Theory<br><small>(Max: {{ $exam->theory_max }}@if($exam->theory_pass_marks > 0), Pass: {{ $exam->theory_pass_marks }}@endif)</small></th>
                                @endif
                                @if($exam->has_practical && $exam->practical_max > 0)
                                    <th>Practical<br><small>(Max: {{ $exam->practical_max }}@if($exam->practical_pass_marks > 0), Pass: {{ $exam->practical_pass_marks }}@endif)</small></th>
                                @endif
                                <th>Total</th>
                                <th>Percentage</th>
                                <th>Grade</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $enrollment)
                                @php
                                    $student = $enrollment->student;
                                    $existingMark = $existingMarks->get($student->id);
                                @endphp
                                <tr>
                                    <td>{{ $student->roll_number }}</td>
                                    <td>
                                        <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                        <input type="hidden" name="marks[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                                    </td>
                                    <td>{{ $exam->class->name }}</td>
                                    
                                    @if($exam->has_assessment && $exam->assess_max > 0)
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

                                    @if($exam->theory_max > 0)
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

                                    @if($exam->has_practical && $exam->practical_max > 0)
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
                                               class="form-control remarks" 
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
                        @if($existingMarks->count() > 0)
                            <button type="button" class="btn btn-success" onclick="submitForApproval()">
                                <i class="fas fa-check"></i> Save & Submit for Approval
                            </button>
                        @endif
                        <a href="{{ route('teacher.marks.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate totals and percentages
    document.querySelectorAll('.mark-input').forEach(function(input) {
        input.addEventListener('input', function() {
            calculateRowTotals(this.dataset.row);
        });
    });

    function calculateRowTotals(row) {
        const assessMarks = parseFloat(document.querySelector(`[data-row="${row}"].assess-marks`)?.value || 0);
        const theoryMarks = parseFloat(document.querySelector(`[data-row="${row}"].theory-marks`)?.value || 0);
        const practicalMarks = parseFloat(document.querySelector(`[data-row="${row}"].practical-marks`)?.value || 0);
        
        const total = assessMarks + theoryMarks + practicalMarks;
        const maxMarks = {{ $exam->max_marks }};
        const percentage = maxMarks > 0 ? (total / maxMarks) * 100 : 0;
        
        // Update total and percentage
        document.querySelector(`[data-row="${row}"].total-marks`).value = total.toFixed(2);
        document.querySelector(`[data-row="${row}"].percentage`).value = percentage.toFixed(2) + '%';
        
        // Calculate grade (simplified)
        let grade = 'F';
        if (percentage >= 90) grade = 'A+';
        else if (percentage >= 80) grade = 'A';
        else if (percentage >= 70) grade = 'B+';
        else if (percentage >= 60) grade = 'B';
        else if (percentage >= 50) grade = 'C+';
        else if (percentage >= 40) grade = 'C';
        else if (percentage >= 32) grade = 'D';
        
        document.querySelector(`[data-row="${row}"].grade`).value = grade;
    }

    // Calculate existing totals on page load
    document.querySelectorAll('.mark-input').forEach(function(input) {
        if (input.value) {
            calculateRowTotals(input.dataset.row);
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
@endsection

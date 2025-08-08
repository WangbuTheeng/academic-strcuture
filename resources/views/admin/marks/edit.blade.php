@extends('layouts.admin')

@section('title', 'Edit Mark')
@section('page-title', 'Edit Mark')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Mark</h1>
            <p class="mb-0 text-muted">{{ $mark->exam->name }} - {{ $mark->subject->name }}</p>
        </div>
        <div>
            <a href="{{ route('admin.marks.show', $mark->exam) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Marks
            </a>
        </div>
    </div>

    <!-- Student and Exam Information Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Student & Exam Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Student:</strong> {{ $mark->student->full_name }}
                </div>
                <div class="col-md-3">
                    <strong>Roll No:</strong> {{ $mark->student->roll_number }}
                </div>
                <div class="col-md-3">
                    <strong>Class:</strong> {{ $mark->student->currentEnrollment->class->name ?? 'N/A' }}
                </div>
                <div class="col-md-3">
                    <strong>Subject:</strong> {{ $mark->subject->name }}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-3">
                    <strong>Exam:</strong> {{ $mark->exam->name }}
                </div>
                <div class="col-md-3">
                    <strong>Max Marks:</strong> {{ $mark->exam->max_marks }}
                </div>
                <div class="col-md-3">
                    <strong>Current Status:</strong> 
                    <span class="badge badge-{{ $mark->status == 'draft' ? 'secondary' : ($mark->status == 'submitted' ? 'warning' : 'success') }}">
                        {{ ucfirst($mark->status) }}
                    </span>
                </div>
                <div class="col-md-3">
                    <strong>Current Total:</strong> {{ $mark->total ?? 0 }}
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Mark Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Marks</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.marks.update', $mark) }}" id="editMarkForm">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Assessment Marks -->
                    @if($mark->exam->assess_max)
                    <div class="col-md-4 mb-3">
                        <label for="assess_marks" class="form-label">
                            Assessment Marks <small class="text-muted">(Max: {{ $mark->exam->assess_max }})</small>
                        </label>
                        <input type="number" 
                               name="assess_marks" 
                               id="assess_marks"
                               class="form-control @error('assess_marks') is-invalid @enderror" 
                               min="0" 
                               max="{{ $mark->exam->assess_max }}" 
                               step="0.01"
                               value="{{ old('assess_marks', $mark->assess_marks) }}"
                               onchange="calculateTotal()">
                        @error('assess_marks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <!-- Theory Marks -->
                    @if($mark->exam->theory_max)
                    <div class="col-md-4 mb-3">
                        <label for="theory_marks" class="form-label">
                            Theory Marks <small class="text-muted">(Max: {{ $mark->exam->theory_max }})</small>
                        </label>
                        <input type="number" 
                               name="theory_marks" 
                               id="theory_marks"
                               class="form-control @error('theory_marks') is-invalid @enderror" 
                               min="0" 
                               max="{{ $mark->exam->theory_max }}" 
                               step="0.01"
                               value="{{ old('theory_marks', $mark->theory_marks) }}"
                               onchange="calculateTotal()">
                        @error('theory_marks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <!-- Practical Marks -->
                    @if($mark->exam->practical_max)
                    <div class="col-md-4 mb-3">
                        <label for="practical_marks" class="form-label">
                            Practical Marks <small class="text-muted">(Max: {{ $mark->exam->practical_max }})</small>
                        </label>
                        <input type="number" 
                               name="practical_marks" 
                               id="practical_marks"
                               class="form-control @error('practical_marks') is-invalid @enderror" 
                               min="0" 
                               max="{{ $mark->exam->practical_max }}" 
                               step="0.01"
                               value="{{ old('practical_marks', $mark->practical_marks) }}"
                               onchange="calculateTotal()">
                        @error('practical_marks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                </div>

                <!-- Calculated Fields -->
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="total_display" class="form-label">Total Marks</label>
                        <input type="text" 
                               id="total_display"
                               class="form-control" 
                               readonly 
                               value="{{ $mark->total ?? 0 }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="percentage_display" class="form-label">Percentage</label>
                        <input type="text" 
                               id="percentage_display"
                               class="form-control" 
                               readonly 
                               value="{{ number_format($mark->percentage ?? 0, 2) }}%">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="grade_display" class="form-label">Grade</label>
                        <input type="text" 
                               id="grade_display"
                               class="form-control" 
                               readonly 
                               value="{{ $mark->grade ?? 'F' }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="result_display" class="form-label">Result</label>
                        <input type="text" 
                               id="result_display"
                               class="form-control" 
                               readonly 
                               value="{{ $mark->result ?? 'Fail' }}">
                    </div>
                </div>

                <!-- Remarks -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="remarks" class="form-label">Remarks <small class="text-muted">(Optional)</small></label>
                        <textarea name="remarks" 
                                  id="remarks"
                                  class="form-control @error('remarks') is-invalid @enderror" 
                                  rows="3"
                                  placeholder="Enter any remarks or comments about this mark...">{{ old('remarks', $mark->remarks) }}</textarea>
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Mark
                        </button>
                        <a href="{{ route('admin.marks.show', $mark->exam) }}" class="btn btn-secondary ms-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        @if($mark->status == 'draft')
                        <button type="button" class="btn btn-danger ms-2" onclick="deleteMark()">
                            <i class="fas fa-trash"></i> Delete Mark
                        </button>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Delete Form (hidden) -->
            <form id="deleteForm" method="POST" action="{{ route('admin.marks.destroy', $mark) }}" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <!-- Mark History (if available) -->
    @if($mark->updated_at != $mark->created_at)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Mark History</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Created:</strong> {{ $mark->created_at?->format('M d, Y h:i A') ?? 'Unknown' }}
                    @if($mark->created_by)
                        by {{ $mark->creator->name ?? 'Unknown' }}
                    @endif
                </div>
                <div class="col-md-6">
                    <strong>Last Updated:</strong> {{ $mark->updated_at?->format('M d, Y h:i A') ?? 'Unknown' }}
                </div>
            </div>
            @if($mark->submitted_at)
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Submitted:</strong> {{ $mark->submitted_at?->format('M d, Y h:i A') ?? 'Unknown' }}
                </div>
                @if($mark->approved_at)
                <div class="col-md-6">
                    <strong>Approved:</strong> {{ $mark->approved_at?->format('M d, Y h:i A') ?? 'Unknown' }}
                    @if($mark->approved_by)
                        by {{ $mark->approver->name ?? 'Unknown' }}
                    @endif
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function calculateTotal() {
    const assessMarks = parseFloat(document.getElementById('assess_marks')?.value || 0);
    const theoryMarks = parseFloat(document.getElementById('theory_marks')?.value || 0);
    const practicalMarks = parseFloat(document.getElementById('practical_marks')?.value || 0);
    
    const total = assessMarks + theoryMarks + practicalMarks;
    const maxMarks = {{ $mark->exam->max_marks }};
    const percentage = maxMarks > 0 ? (total / maxMarks * 100) : 0;
    
    // Update display fields
    document.getElementById('total_display').value = total.toFixed(2);
    document.getElementById('percentage_display').value = percentage.toFixed(2) + '%';
    
    // Calculate grade
    let grade = 'F';
    if (percentage >= 90) grade = 'A+';
    else if (percentage >= 80) grade = 'A';
    else if (percentage >= 70) grade = 'B+';
    else if (percentage >= 60) grade = 'B';
    else if (percentage >= 50) grade = 'C+';
    else if (percentage >= 40) grade = 'C';
    else if (percentage >= 32) grade = 'D';
    
    document.getElementById('grade_display').value = grade;
    document.getElementById('result_display').value = percentage >= 32 ? 'Pass' : 'Fail';
}

function deleteMark() {
    if (confirm('Are you sure you want to delete this mark? This action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}

// Calculate on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
});
</script>
@endpush
@endsection

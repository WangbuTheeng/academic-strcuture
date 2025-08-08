@extends('layouts.admin')

@section('title', 'Assign Teacher to Subject')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title">Assign Teacher to Subject(s)</h3>
                        <div class="mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="assignment_mode" id="single_mode" value="single" checked>
                                <label class="form-check-label" for="single_mode">Single Subject</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="assignment_mode" id="bulk_mode" value="bulk">
                                <label class="form-check-label" for="bulk_mode">Multiple Subjects</label>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('admin.teacher-subjects.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Assignments
                    </a>
                </div>

                <!-- Single Assignment Form -->
                <form id="single-form" action="{{ route('admin.teacher-subjects.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Single Assignment Mode:</strong> Assign one teacher to one subject in one class.
                        </div>
                        <div class="row">
                            <!-- Teacher Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id" class="required">Teacher</label>
                                    <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                        <option value="">Select Teacher</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('user_id') == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->name }} ({{ $teacher->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Subject Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subject_id" class="required">Subject</label>
                                    <select name="subject_id" id="subject_id" class="form-control @error('subject_id') is-invalid @enderror" required>
                                        <option value="">Select Subject</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }} ({{ $subject->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Class Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="class_id" class="required">Class</label>
                                    <select name="class_id" id="class_id" class="form-control @error('class_id') is-invalid @enderror" required>
                                        <option value="">Select Class</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }} ({{ $class->level->name ?? 'No Level' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Academic Year Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="academic_year_id" class="required">Academic Year</label>
                                    <select name="academic_year_id" id="academic_year_id" class="form-control @error('academic_year_id') is-invalid @enderror" required>
                                        <option value="">Select Academic Year</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                                {{ $year->name }} 
                                                @if($year->is_current)
                                                    <span class="badge badge-success">Current</span>
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

                        <div class="row">
                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label for="is_active" class="form-check-label">Active Assignment</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($errors->has('error'))
                            <div class="alert alert-danger">
                                {{ $errors->first('error') }}
                            </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Assignment
                        </button>
                        <a href="{{ route('admin.teacher-subjects.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>

                <!-- Bulk Assignment Form -->
                <form id="bulk-form" action="{{ route('admin.teacher-subjects.bulk-assign-subjects') }}" method="POST" style="display: none;">
                    @csrf
                    <div class="card-body">
                        <div class="alert alert-success">
                            <i class="fas fa-bolt me-2"></i>
                            <strong>Bulk Assignment Mode:</strong> Assign one teacher to multiple subjects in one class.
                        </div>

                        <div class="row">
                            <!-- Teacher Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bulk_user_id" class="required">Teacher</label>
                                    <select name="user_id" id="bulk_user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                        <option value="">Select Teacher</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('user_id') == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->name }} ({{ $teacher->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Class Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bulk_class_id" class="required">Class</label>
                                    <select name="class_id" id="bulk_class_id" class="form-control @error('class_id') is-invalid @enderror" required>
                                        <option value="">Select Class</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }} ({{ $class->level->name ?? 'No Level' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Multiple Subject Selection -->
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="bulk_subject_ids" class="required">Subjects</label>
                                    <select name="subject_ids[]" id="bulk_subject_ids" class="form-control @error('subject_ids') is-invalid @enderror" multiple required>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ in_array($subject->id, old('subject_ids', [])) ? 'selected' : '' }}>
                                                {{ $subject->name }} ({{ $subject->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple subjects</small>
                                    @error('subject_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Academic Year Selection -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="bulk_academic_year_id" class="required">Academic Year</label>
                                    <select name="academic_year_id" id="bulk_academic_year_id" class="form-control @error('academic_year_id') is-invalid @enderror" required>
                                        <option value="">Select Academic Year</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                                {{ $year->name }}
                                                @if($year->is_current)
                                                    <span class="badge badge-success">Current</span>
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

                        <div class="row">
                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_active" id="bulk_is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label for="bulk_is_active" class="form-check-label">Active Assignment</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($errors->has('error'))
                            <div class="alert alert-danger">
                                {{ $errors->first('error') }}
                            </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-bolt"></i> Bulk Assign Subjects
                        </button>
                        <a href="{{ route('admin.teacher-subjects.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.required::after {
    content: " *";
    color: red;
}

.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.form-check-label {
    font-weight: 500;
}

.alert {
    border-radius: 8px;
    border: none;
}

.alert-info {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    color: #1565c0;
}

.alert-success {
    background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
    color: #2e7d32;
}

.card {
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn {
    border-radius: 6px;
    font-weight: 500;
}

.btn-success {
    background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
    border: none;
}

.btn-success:hover {
    background: linear-gradient(135deg, #45a049 0%, #3d8b40 100%);
    transform: translateY(-1px);
}

.select2-container--bootstrap4 .select2-selection {
    border-radius: 6px;
}

#bulk_subject_ids + .mt-2 .btn {
    margin-right: 8px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize select2 for better UX
    function initializeSelect2() {
        if (typeof $.fn.select2 !== 'undefined') {
            // Single form selects
            $('#user_id, #subject_id, #class_id, #academic_year_id').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Bulk form selects
            $('#bulk_user_id, #bulk_class_id, #bulk_academic_year_id').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Multiple subject selection with search
            $('#bulk_subject_ids').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Select subjects...',
                allowClear: true,
                closeOnSelect: false
            });
        }
    }

    // Initialize on page load
    initializeSelect2();

    // Handle mode switching
    $('input[name="assignment_mode"]').change(function() {
        const mode = $(this).val();

        if (mode === 'single') {
            $('#single-form').show();
            $('#bulk-form').hide();
        } else {
            $('#single-form').hide();
            $('#bulk-form').show();
            // Re-initialize select2 for bulk form when shown
            setTimeout(initializeSelect2, 100);
        }
    });

    // Add select all/none functionality for bulk subjects
    if ($('#bulk_subject_ids').length) {
        // Add buttons after the select
        $('#bulk_subject_ids').after(`
            <div class="mt-2">
                <button type="button" class="btn btn-sm btn-outline-primary" id="select-all-subjects">
                    <i class="fas fa-check-double"></i> Select All
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="clear-all-subjects">
                    <i class="fas fa-times"></i> Clear All
                </button>
            </div>
        `);

        // Select all subjects
        $(document).on('click', '#select-all-subjects', function() {
            $('#bulk_subject_ids option').prop('selected', true);
            $('#bulk_subject_ids').trigger('change');
        });

        // Clear all subjects
        $(document).on('click', '#clear-all-subjects', function() {
            $('#bulk_subject_ids').val(null).trigger('change');
        });
    }

    // Form validation
    $('#bulk-form').submit(function(e) {
        const selectedSubjects = $('#bulk_subject_ids').val();
        if (!selectedSubjects || selectedSubjects.length === 0) {
            e.preventDefault();
            alert('Please select at least one subject.');
            return false;
        }

        // Confirm bulk assignment
        const teacherName = $('#bulk_user_id option:selected').text();
        const className = $('#bulk_class_id option:selected').text();
        const subjectCount = selectedSubjects.length;

        const confirmMessage = `Are you sure you want to assign ${teacherName} to ${subjectCount} subject(s) in ${className}?`;

        if (!confirm(confirmMessage)) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush
@endsection

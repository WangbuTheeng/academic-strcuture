@extends('layouts.admin')

@section('title', 'Generate Marksheets - ' . $exam->name)
@section('page-title', 'Generate Marksheets')

@push('styles')
<style>
.template-option {
    display: flex;
    align-items: center;
    padding: 15px;
    border: 2px solid #e3e6f0;
    border-radius: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.template-option:hover {
    border-color: #4e73df;
    background-color: #f8f9fc;
}

.custom-control-input:checked ~ .custom-control-label .template-option {
    border-color: #4e73df;
    background-color: #f8f9fc;
}

.template-preview {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 20px;
}

.modern-preview { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.classic-preview { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
.minimal-preview { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }

.student-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.student-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.student-card.selected {
    border-color: #4e73df;
    background-color: #f8f9fc;
}

.marks-summary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
}

.action-buttons .btn {
    margin: 5px;
    min-width: 120px;
}

.class-selection-card {
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid #e3e6f0;
}

.class-selection-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: #4e73df;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-certificate text-primary"></i> Generate Marksheets
            </h1>
            <p class="mb-0 text-muted">Create professional marksheets for {{ $exam->name }}</p>
        </div>
        <div>
            <a href="{{ route('admin.marksheets.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Marksheets
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Exam Information Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle"></i> Exam Information
            </h6>
            <span class="badge badge-{{ $exam->status_color }}">{{ ucfirst($exam->status) }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">Exam Name</label>
                    <p class="mb-0 font-weight-bold">{{ $exam->name }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">Academic Year</label>
                    <p class="mb-0">{{ $exam->academicYear->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">Subject</label>
                    <p class="mb-0">{{ $exam->subject->name ?? 'All Subjects' }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">Class</label>
                    <p class="mb-0">{{ $exam->class->name ?? 'All Classes' }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">Exam Type</label>
                    <p class="mb-0">{{ $exam->getTypeLabel() }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted">Max Marks</label>
                    <p class="mb-0">{{ $exam->max_marks }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Class Selection (if exam is for all classes) -->
    @if(!$exam->class_id && $availableClasses->count() > 1)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users"></i> Select Class
            </h6>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">
                <i class="fas fa-info-circle text-primary"></i>
                This exam covers multiple classes. Select a specific class to generate marksheets for, or proceed to see all students.
            </p>
            <div class="row">
                @foreach($availableClasses as $class)
                    @php
                        $classStudentCount = $studentsWithMarks->filter(function($student) use ($class) {
                            return $student->currentEnrollment && $student->currentEnrollment->class_id == $class->id;
                        })->count();
                    @endphp
                    <div class="col-md-4 mb-3">
                        <div class="card class-selection-card h-100" data-class-id="{{ $class->id }}">
                            <div class="card-body text-center">
                                <i class="fas fa-graduation-cap fa-2x text-primary mb-2"></i>
                                <h6 class="card-title">{{ $class->name }}</h6>
                                <p class="card-text text-muted">{{ $class->level->name ?? 'N/A' }}</p>
                                <span class="badge badge-info">{{ $classStudentCount }} Students</span>
                                <div class="mt-3">
                                    <a href="{{ route('admin.marksheets.create', ['exam' => $exam->id, 'class_id' => $class->id]) }}"
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-certificate"></i> Generate for {{ $class->name }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="col-md-4 mb-3">
                    <div class="card class-selection-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x text-success mb-2"></i>
                            <h6 class="card-title">All Classes</h6>
                            <p class="card-text text-muted">Generate for all students</p>
                            <span class="badge badge-success">{{ $studentsWithMarks->count() }} Students</span>
                            <div class="mt-3">
                                <a href="{{ route('admin.marksheets.create', ['exam' => $exam->id]) }}"
                                   class="btn btn-success btn-sm">
                                    <i class="fas fa-certificate"></i> Generate for All
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Template Selection and Generation Options -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-palette"></i> Marksheet Generation Options
            </h6>
        </div>
        <div class="card-body">
            <form id="bulkGenerateForm" method="POST" action="{{ route('admin.marksheets.bulk-generate') }}">
                @csrf
                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                @if(isset($classId))
                    <input type="hidden" name="class_id" value="{{ $classId }}">
                @endif
                <input type="hidden" name="grading_scale_id" value="" class="grading-scale-input">

                <div class="row">
                    <!-- Template Selection -->
                    <div class="col-md-6 mb-4">
                        <h6 class="font-weight-bold text-dark mb-3">
                            <i class="fas fa-file-alt text-primary"></i> Select Template
                        </h6>
                        <div class="template-selection">
                            @foreach($availableTemplates as $index => $template)
                                <div class="custom-control custom-radio mb-3">
                                    <input type="radio"
                                           id="template-{{ $template['id'] }}"
                                           name="template"
                                           value="{{ $template['id'] }}"
                                           class="custom-control-input"
                                           {{ $index === 0 ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="template-{{ $template['id'] }}">
                                        <div class="template-option">
                                            <div class="template-preview {{ $template['type'] }}-preview">
                                                <i class="{{ $template['icon'] }}"></i>
                                                @if($template['type'] === 'custom')
                                                    <span class="badge badge-success badge-sm">Custom</span>
                                                @endif
                                            </div>
                                            <div class="template-info">
                                                <strong>{{ $template['name'] }}</strong>
                                                <small class="text-muted d-block">{{ $template['description'] }}</small>
                                                @if($template['type'] === 'custom')
                                                    <small class="text-primary d-block">
                                                        <i class="fas fa-palette"></i> Custom Template
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach

                            @if($availableTemplates->where('type', 'custom')->isEmpty())
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>No custom templates found.</strong>
                                    <a href="{{ route('admin.marksheets.customize.create') }}" class="alert-link">Create a custom template</a> to see it here.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Grading Scale Selection -->
                    <div class="col-md-6 mb-4">
                        <h6 class="font-weight-bold text-dark mb-3">
                            <i class="fas fa-chart-line text-primary"></i> Grading Scale
                        </h6>
                        <div class="form-group">
                            <label for="grading_scale_id" class="form-label">Select Grading Scale</label>
                            <select class="form-control" id="grading_scale_id" name="grading_scale_id">
                                <option value="">Use Exam Default ({{ $exam->gradingScale->name ?? 'None' }})</option>
                                @foreach($gradingScales as $scale)
                                    <option value="{{ $scale->id }}"
                                            {{ $exam->grading_scale_id == $scale->id ? 'selected' : '' }}>
                                        {{ $scale->name }}
                                        @if($scale->is_default)
                                            <span class="text-primary">(Default)</span>
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Override the exam's default grading scale for this marksheet generation.
                                <a href="{{ route('admin.grading-scales.index') }}" target="_blank" class="text-primary">
                                    Manage grading scales
                                </a>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Bulk Actions -->
                    <div class="col-md-6 mb-4">
                        <h6 class="font-weight-bold text-dark mb-3">
                            <i class="fas fa-cogs text-primary"></i> Generation Actions
                        </h6>
                        <div class="action-buttons">
                            <button type="button" id="selectAllBtn" class="btn btn-primary btn-block mb-2">
                                <i class="fas fa-check-circle"></i> Select All Students
                            </button>
                            <button type="button" id="deselectAllBtn" class="btn btn-secondary btn-block mb-2">
                                <i class="fas fa-times-circle"></i> Deselect All
                            </button>
                            <button type="submit" id="generateBulkBtn" class="btn btn-success btn-block mb-2" disabled>
                                <i class="fas fa-download"></i> Generate Bulk Marksheets (PDF)
                            </button>
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Select students below to enable bulk generation
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Students List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users"></i> Students with Approved Marks
            </h6>
            <span class="badge badge-info">{{ $studentsWithMarks->count() }} Students</span>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">
                <i class="fas fa-info-circle text-primary"></i>
                Select students to generate individual or bulk marksheets. Only students with approved marks are shown.
            </p>

            @if($studentsWithMarks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th width="50">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" id="selectAllCheckbox" class="custom-control-input">
                                        <label class="custom-control-label" for="selectAllCheckbox"></label>
                                    </div>
                                </th>
                                <th>Student Details</th>
                                <th>Class & Program</th>
                                <th>Marks Summary</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentsWithMarks as $student)
                                @php
                                    $studentMarks = $student->marks;
                                    $totalMarks = $studentMarks->sum('total');
                                    $maxMarks = $studentMarks->count() * $exam->max_marks;
                                    $percentage = $maxMarks > 0 ? ($totalMarks / $maxMarks) * 100 : 0;
                                    $overallResult = $studentMarks->contains('result', 'Fail') ? 'Fail' : 'Pass';
                                @endphp
                                <tr class="student-row">
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                                   id="student_{{ $student->id }}" class="custom-control-input student-checkbox">
                                            <label class="custom-control-label" for="student_{{ $student->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary text-white mr-3">
                                                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $student->full_name }}</div>
                                                <small class="text-muted">Roll: {{ $student->currentEnrollment->roll_no ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="font-weight-medium">{{ $student->currentEnrollment->class->name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $student->currentEnrollment->program->name ?? 'N/A' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="marks-summary p-2 text-center">
                                            <div class="font-weight-bold">{{ number_format($totalMarks, 1) }}/{{ number_format($maxMarks, 1) }}</div>
                                            <small>{{ number_format($percentage, 1) }}%</small>
                                            <div class="mt-1">
                                                <span class="badge badge-{{ $overallResult === 'Pass' ? 'success' : 'danger' }}">
                                                    {{ $overallResult }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical btn-group-sm">
                                            <!-- Preview Button -->
                                            <form method="POST" action="{{ route('admin.marksheets.preview') }}" target="_blank" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                                                <input type="hidden" name="template" value="modern" class="template-input">
                                                <input type="hidden" name="grading_scale_id" value="" class="grading-scale-input">
                                                <button type="submit" class="btn btn-outline-primary btn-sm mb-1">
                                                    <i class="fas fa-eye"></i> Preview
                                                </button>
                                            </form>

                                            <!-- Generate Individual PDF -->
                                            <form method="POST" action="{{ route('admin.marksheets.generate') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                                                <input type="hidden" name="template" value="modern" class="template-input">
                                                <input type="hidden" name="format" value="pdf">
                                                <input type="hidden" name="grading_scale_id" value="" class="grading-scale-input">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-download"></i> PDF
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No students with approved marks</h5>
                    <p class="text-muted">There are no students with approved marks for this exam yet.</p>
                    <a href="{{ route('admin.marks.index') }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Go to Mark Entry
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Template selection handling
    $('input[name="template"]').change(function() {
        if (this.checked) {
            $('.template-input').val(this.value);
        }
    });

    // Grading scale selection handling
    $('#grading_scale_id').change(function() {
        $('.grading-scale-input').val(this.value);
    });

    // Select all functionality
    $('#selectAllCheckbox').change(function() {
        $('.student-checkbox').prop('checked', this.checked);
        updateBulkButton();
    });

    $('#selectAllBtn').click(function() {
        $('.student-checkbox').prop('checked', true);
        $('#selectAllCheckbox').prop('checked', true);
        updateBulkButton();
    });

    $('#deselectAllBtn').click(function() {
        $('.student-checkbox').prop('checked', false);
        $('#selectAllCheckbox').prop('checked', false);
        updateBulkButton();
    });

    // Individual checkbox handling
    $('.student-checkbox').change(function() {
        updateBulkButton();
        updateSelectAllCheckbox();
    });

    function updateBulkButton() {
        const checkedCount = $('.student-checkbox:checked').length;
        $('#generateBulkBtn').prop('disabled', checkedCount === 0);

        if (checkedCount > 0) {
            $('#generateBulkBtn').html('<i class="fas fa-download"></i> Generate Bulk Marksheets (' + checkedCount + ')');
        } else {
            $('#generateBulkBtn').html('<i class="fas fa-download"></i> Generate Bulk Marksheets (PDF)');
        }
    }

    function updateSelectAllCheckbox() {
        const totalCheckboxes = $('.student-checkbox').length;
        const checkedCheckboxes = $('.student-checkbox:checked').length;

        $('#selectAllCheckbox').prop('checked', totalCheckboxes === checkedCheckboxes);
    }

    // Form submission handling
    $('#bulkGenerateForm').submit(function(e) {
        const checkedCount = $('.student-checkbox:checked').length;
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Please select at least one student to generate marksheets.');
            return false;
        }

        // Show loading state
        $('#generateBulkBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');
    });

    // Student row hover effects
    $('.student-row').hover(
        function() { $(this).addClass('table-active'); },
        function() { $(this).removeClass('table-active'); }
    );

    // Initialize
    updateBulkButton();
});
</script>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

.custom-preview {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    position: relative;
}

.custom-preview .badge {
    position: absolute;
    top: -5px;
    right: -5px;
    font-size: 8px;
}

.template-option {
    transition: all 0.3s ease;
}

.custom-control-input:checked ~ .custom-control-label .template-option {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>
@endpush

@endsection

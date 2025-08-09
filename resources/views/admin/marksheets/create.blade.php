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

/* Professional Template Cards */
.template-card {
    border: 2px solid #e3e6f0;
    border-radius: 12px;
    padding: 15px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
    position: relative;
}

.template-card:hover {
    border-color: #4e73df;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(78, 115, 223, 0.15);
}

.template-card.selected {
    border-color: #4e73df;
    background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
    box-shadow: 0 4px 15px rgba(78, 115, 223, 0.2);
}

.template-preview-mini {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 18px;
}

.template-name {
    font-weight: 600;
    font-size: 13px;
    color: #5a5c69;
    margin-bottom: 5px;
}

.template-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 3px;
    justify-content: center;
}

.template-badges .badge {
    font-size: 9px;
    padding: 2px 6px;
}

/* Generation Actions */
.generation-actions {
    background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #e3e6f0;
}

.btn-toolbar .btn-group .btn {
    border-radius: 6px;
    font-weight: 500;
    padding: 8px 16px;
    font-size: 14px;
}

.btn-toolbar .btn-group:not(:last-child) {
    margin-right: 10px;
}

/* Compact Student Table */
.student-table-compact {
    font-size: 14px;
}

.student-table-compact .student-avatar {
    width: 32px;
    height: 32px;
    font-size: 12px;
}

.student-table-compact .btn-sm {
    padding: 4px 8px;
    font-size: 12px;
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
                                       class="btn btn-primary btn-sm btn-block mb-2">
                                        <i class="fas fa-certificate"></i> Generate for {{ $class->name }}
                                    </a>
                                    <form method="POST" action="{{ route('admin.marksheets.bulk-generate-class') }}" class="d-inline w-100">
                                        @csrf
                                        <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                                        <input type="hidden" name="class_id" value="{{ $class->id }}">
                                        <input type="hidden" name="template" value="{{ $availableTemplates->where('type', 'custom')->first()['id'] ?? 'modern' }}">
                                        <button type="submit" class="btn btn-warning btn-sm btn-block"
                                                onclick="return confirm('Generate marksheets for all {{ $classStudentCount }} students in {{ $class->name }}?')"
                                                {{ $classStudentCount == 0 ? 'disabled' : '' }}>
                                            <i class="fas fa-bolt"></i> Quick Generate All ({{ $classStudentCount }})
                                        </button>
                                    </form>
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
                                   class="btn btn-success btn-sm btn-block mb-2">
                                    <i class="fas fa-certificate"></i> Generate for All
                                </a>
                                <form method="POST" action="{{ route('admin.marksheets.bulk-generate') }}" class="d-inline w-100">
                                    @csrf
                                    <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                                    <input type="hidden" name="template" value="{{ $availableTemplates->where('type', 'custom')->first()['id'] ?? 'modern' }}">
                                    @foreach($studentsWithMarks->pluck('id') as $studentId)
                                        <input type="hidden" name="student_ids[]" value="{{ $studentId }}">
                                    @endforeach
                                    <button type="submit" class="btn btn-warning btn-sm btn-block"
                                            onclick="return confirm('Generate marksheets for ALL {{ $studentsWithMarks->count() }} students across all classes?')"
                                            {{ $studentsWithMarks->count() == 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-bolt"></i> Quick Generate All ({{ $studentsWithMarks->count() }})
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Professional Generation Interface -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-certificate"></i> Professional Marksheet Generation
            </h6>
        </div>
        <div class="card-body">
            <!-- Compact Template & Settings Row -->
            <div class="row mb-4">
                <!-- Template Selection - Compact Cards -->
                <div class="col-md-8">
                    <label class="form-label font-weight-bold text-dark mb-3">
                        <i class="fas fa-palette text-primary"></i> Template Selection
                    </label>
                    <div class="row">
                        @foreach($availableTemplates as $index => $template)
                            <div class="col-md-6 col-lg-3 mb-3">
                                @php
                                    $isFirstCustom = $template['type'] === 'custom' && $availableTemplates->where('type', 'custom')->first()['id'] === $template['id'];
                                    $isFirstOverall = $index === 0 && $availableTemplates->where('type', 'custom')->isEmpty();
                                @endphp
                                <div class="template-card {{ $isFirstCustom || $isFirstOverall ? 'selected' : '' }}" data-template="{{ $template['id'] }}">
                                    <div class="template-preview-mini {{ $template['type'] }}-preview">
                                        <i class="{{ $template['icon'] }}"></i>
                                    </div>
                                    <div class="template-name">{{ $template['name'] }}</div>
                                    @if($template['type'] === 'custom')
                                        <div class="template-badges">
                                            <span class="badge badge-success badge-xs">Custom</span>
                                            @if(isset($template['is_global']) && $template['is_global'])
                                                <span class="badge badge-info badge-xs">Global</span>
                                            @else
                                                <span class="badge badge-primary badge-xs">School</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Grading Scale - Compact -->
                <div class="col-md-4">
                    <label class="form-label font-weight-bold text-dark mb-3">
                        <i class="fas fa-chart-line text-primary"></i> Grading Scale
                    </label>
                    <select class="form-control" id="grading_scale_id" name="grading_scale_id">
                        <option value="">Exam Default</option>
                        @foreach($gradingScales as $scale)
                            <option value="{{ $scale->id }}" {{ $exam->grading_scale_id == $scale->id ? 'selected' : '' }}>
                                {{ $scale->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Action Buttons Row -->
            <div class="generation-actions">
                <div class="row">
                    <div class="col-md-12">
                        <div class="btn-toolbar justify-content-between" role="toolbar">
                            <!-- Left Side - Selection Actions -->
                            <div class="btn-group" role="group">
                                <button type="button" id="selectAllBtn" class="btn btn-outline-primary">
                                    <i class="fas fa-check-circle"></i> Select All
                                </button>
                                <button type="button" id="deselectAllBtn" class="btn btn-outline-secondary">
                                    <i class="fas fa-times-circle"></i> Clear
                                </button>
                            </div>

                            <!-- Center - Preview Actions -->
                            <div class="btn-group" role="group">
                                <button type="button" id="previewBulkBtn" class="btn btn-info" disabled>
                                    <i class="fas fa-eye"></i> Preview Selected
                                </button>
                                <button type="button" id="previewAllBtn" class="btn btn-outline-info">
                                    <i class="fas fa-search"></i> Preview All Class
                                </button>
                            </div>

                            <!-- Right Side - Generation Actions -->
                            <div class="btn-group" role="group">
                                <button type="button" id="generateSelectedBtn" class="btn btn-success" disabled>
                                    <i class="fas fa-download"></i> Download Selected
                                </button>
                                <button type="button" id="generateAllBtn" class="btn btn-warning">
                                    <i class="fas fa-bolt"></i> Download All Class
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden Forms -->
            <form id="bulkGenerateForm" method="POST" action="{{ route('admin.marksheets.bulk-generate') }}" style="display: none;">
                @csrf
                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                @if(isset($classId))
                    <input type="hidden" name="class_id" value="{{ $classId }}">
                @endif
                <input type="hidden" name="template" value="{{ $availableTemplates->where('type', 'custom')->first()['id'] ?? $availableTemplates->first()['id'] }}" class="template-input">
                <input type="hidden" name="grading_scale_id" value="" class="grading-scale-input">
            </form>

            <form id="bulkPreviewForm" method="POST" action="{{ route('admin.marksheets.bulk-preview') }}" target="_blank" style="display: none;">
                @csrf
                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                @if(isset($classId))
                    <input type="hidden" name="class_id" value="{{ $classId }}">
                @endif
                <input type="hidden" name="template" value="{{ $availableTemplates->where('type', 'custom')->first()['id'] ?? $availableTemplates->first()['id'] }}" class="template-input">
                <input type="hidden" name="grading_scale_id" value="" class="grading-scale-input">
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
                    <table class="table table-hover student-table-compact">
                        <thead class="thead-light">
                            <tr>
                                <th width="40">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" id="selectAllCheckbox" class="custom-control-input">
                                        <label class="custom-control-label" for="selectAllCheckbox"></label>
                                    </div>
                                </th>
                                <th>Student</th>
                                <th>Class</th>
                                <th>Marks</th>
                                <th width="120">Actions</th>
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
                                            <div class="student-avatar bg-primary text-white mr-2">
                                                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-weight-bold" style="font-size: 13px;">{{ $student->full_name }}</div>
                                                <small class="text-muted">{{ $student->currentEnrollment->roll_no ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-weight-medium" style="font-size: 13px;">{{ $student->currentEnrollment->class->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $student->currentEnrollment->program->name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <div class="font-weight-bold" style="font-size: 13px;">{{ number_format($totalMarks, 1) }}/{{ number_format($maxMarks, 1) }}</div>
                                            <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                                            <span class="badge badge-{{ $overallResult === 'Pass' ? 'success' : 'danger' }} badge-sm ml-1">
                                                {{ $overallResult }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <!-- Preview Button -->
                                            <form method="POST" action="{{ route('admin.marksheets.preview') }}" target="_blank" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                                                <input type="hidden" name="template" value="{{ $availableTemplates->where('type', 'custom')->first()['id'] ?? 'modern' }}" class="template-input">
                                                <input type="hidden" name="grading_scale_id" value="" class="grading-scale-input">
                                                <button type="submit" class="btn btn-outline-info btn-sm" title="Preview">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </form>

                                            <!-- Generate Individual PDF -->
                                            <form method="POST" action="{{ route('admin.marksheets.generate') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                                                <input type="hidden" name="template" value="{{ $availableTemplates->where('type', 'custom')->first()['id'] ?? 'modern' }}" class="template-input">
                                                <input type="hidden" name="format" value="pdf">
                                                <input type="hidden" name="grading_scale_id" value="" class="grading-scale-input">
                                                <button type="submit" class="btn btn-success btn-sm" title="Download PDF">
                                                    <i class="fas fa-download"></i>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let selectedStudents = [];
    let selectedTemplate = '{{ $availableTemplates->where("type", "custom")->first()["id"] ?? $availableTemplates->first()["id"] }}';

    // Template card selection
    $('.template-card').click(function() {
        $('.template-card').removeClass('selected');
        $(this).addClass('selected');
        selectedTemplate = $(this).data('template');
        $('.template-input').val(selectedTemplate);
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
        $('.student-checkbox').prop('checked', true).trigger('change');
    });

    $('#deselectAllBtn').click(function() {
        $('.student-checkbox').prop('checked', false).trigger('change');
    });

    // Generate all class marksheets
    $('#generateAllBtn').click(function(e) {
        e.preventDefault();

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Download All Class Marksheets?',
                text: 'This will generate marksheets for all students in this class.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Download All',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    generateAllMarksheets();
                }
            });
        } else {
            if (confirm('Download All Class Marksheets? This will generate marksheets for all students in this class.')) {
                generateAllMarksheets();
            }
        }
    });

    function generateAllMarksheets() {
        // Add all student IDs to form
        $('#bulkGenerateForm').find('input[name="student_ids[]"]').remove();
        $('.student-checkbox').each(function() {
            $('#bulkGenerateForm').append(`<input type="hidden" name="student_ids[]" value="${$(this).val()}">`);
        });

        // Show loading indicator
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Generating Marksheets...',
                text: 'Please wait while we generate the marksheets.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        console.log('Submitting bulk generate form...');
        console.log('Form action:', $('#bulkGenerateForm').attr('action'));
        console.log('Form data:', $('#bulkGenerateForm').serialize());

        // Check if there are any student IDs
        const studentIds = $('#bulkGenerateForm').find('input[name="student_ids[]"]');
        console.log('Student IDs count:', studentIds.length);

        if (studentIds.length === 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Error', 'No students selected for marksheet generation.', 'error');
            } else {
                alert('No students selected for marksheet generation.');
            }
            return;
        }

        $('#bulkGenerateForm').submit();
    });

    // Individual checkbox handling
    $('.student-checkbox').change(function() {
        const studentId = $(this).val();
        const isChecked = $(this).is(':checked');

        if (isChecked) {
            if (!selectedStudents.includes(studentId)) {
                selectedStudents.push(studentId);
            }
        } else {
            selectedStudents = selectedStudents.filter(id => id !== studentId);
        }

        updateActionButtons();
        updateSelectAllCheckbox();
    });

    function updateActionButtons() {
        const hasSelection = selectedStudents.length > 0;
        $('#generateSelectedBtn, #previewBulkBtn').prop('disabled', !hasSelection);

        if (hasSelection) {
            $('#generateSelectedBtn').html(`<i class="fas fa-download"></i> Download Selected (${selectedStudents.length})`);
            $('#previewBulkBtn').html(`<i class="fas fa-eye"></i> Preview Selected (${selectedStudents.length})`);
        } else {
            $('#generateSelectedBtn').html('<i class="fas fa-download"></i> Download Selected');
            $('#previewBulkBtn').html('<i class="fas fa-eye"></i> Preview Selected');
        }
    }

    function updateSelectAllCheckbox() {
        const totalCheckboxes = $('.student-checkbox').length;
        const checkedCheckboxes = $('.student-checkbox:checked').length;

        $('#selectAllCheckbox').prop('checked', totalCheckboxes === checkedCheckboxes);
    }

    // Preview selected students
    $('#previewBulkBtn').click(function(e) {
        e.preventDefault();
        if (selectedStudents.length === 0) return;

        // Add selected student IDs to preview form
        $('#bulkPreviewForm').find('input[name="student_ids[]"]').remove();
        selectedStudents.forEach(studentId => {
            $('#bulkPreviewForm').append(`<input type="hidden" name="student_ids[]" value="${studentId}">`);
        });

        $('#bulkPreviewForm').submit();
    });

    // Preview all students in class
    $('#previewAllBtn').click(function(e) {
        e.preventDefault();

        // Check if class ID exists
        @if(!$classId)
            alert('No class ID available. Please select a class-specific exam.');
            return;
        @endif

        // Create form for class preview
        const form = $('<form>', {
            method: 'POST',
            action: '{{ route("admin.marksheets.class-preview") }}',
            target: '_blank'
        });

        // Add CSRF token
        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: '{{ csrf_token() }}'
        }));

        // Add exam ID
        form.append($('<input>', {
            type: 'hidden',
            name: 'exam_id',
            value: '{{ $exam->id }}'
        }));

        // Add class ID
        form.append($('<input>', {
            type: 'hidden',
            name: 'class_id',
            value: '{{ $classId }}'
        }));

        // Add template
        form.append($('<input>', {
            type: 'hidden',
            name: 'template',
            value: selectedTemplate
        }));

        // Add grading scale
        form.append($('<input>', {
            type: 'hidden',
            name: 'grading_scale_id',
            value: $('#grading_scale_id').val()
        }));

        // Submit form
        $('body').append(form);
        form.submit();
        form.remove();
    });

    // Generate selected students
    $('#generateSelectedBtn').click(function(e) {
        e.preventDefault();
        if (selectedStudents.length === 0) return;

        generateSelectedMarksheets();
    });

    function generateSelectedMarksheets() {
        // Add selected student IDs to form
        $('#bulkGenerateForm').find('input[name="student_ids[]"]').remove();
        selectedStudents.forEach(studentId => {
            $('#bulkGenerateForm').append(`<input type="hidden" name="student_ids[]" value="${studentId}">`);
        });

        // Show loading indicator
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Generating Marksheets...',
                text: `Please wait while we generate marksheets for ${selectedStudents.length} students.`,
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        console.log('Submitting bulk generate form for selected students...');
        console.log('Selected students:', selectedStudents);
        console.log('Form action:', $('#bulkGenerateForm').attr('action'));
        console.log('Form data:', $('#bulkGenerateForm').serialize());

        $('#bulkGenerateForm').submit();
    }

    // Student row hover effects
    $('.student-row').hover(
        function() { $(this).addClass('table-active'); },
        function() { $(this).removeClass('table-active'); }
    );

    // Handle form submission errors
    $('#bulkGenerateForm').on('submit', function(e) {
        console.log('Form is being submitted...');

        // Add a timeout to detect if the form submission failed
        setTimeout(function() {
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
        }, 10000); // Close loading after 10 seconds if no response
    });

    // Initialize
    updateActionButtons();
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

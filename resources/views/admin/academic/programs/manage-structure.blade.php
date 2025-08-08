@extends('layouts.admin')

@section('title', 'Manage Program Structure - ' . $program->name)

@section('content')
<div class="container-fluid">
    <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-dark">Manage Program Structure</h1>
            <p class="mb-0 text-muted">{{ $program->name }} - Assign classes and subjects</p>
        </div>
        <a href="{{ route('admin.programs.show', $program) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Back to Program
        </a>
    </div>

    <div class="row">
        <!-- Program Classes -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Program Classes</h6>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
                        <i class="fas fa-plus me-1"></i> Add Class
                    </button>
                </div>
                <div class="card-body">
                    @if($program->classes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Level</th>
                                        <th>Department</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($program->classes as $class)
                                        <tr>
                                            <td>{{ $class->name }}</td>
                                            <td>{{ $class->level->name }}</td>
                                            <td>{{ $class->department->name ?? 'N/A' }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.programs.remove-class', [$program, $class]) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('Remove this class from program?')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chalkboard fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No classes assigned to this program</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
                                <i class="fas fa-plus me-1"></i> Add First Class
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Program Subjects -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Program Subjects</h6>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                        <i class="fas fa-plus me-1"></i> Add Subject
                    </button>
                </div>
                <div class="card-body">
                    @if($program->subjects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Code</th>
                                        <th>Credits</th>
                                        <th>Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($program->subjects as $subject)
                                        <tr>
                                            <td>{{ $subject->name }}</td>
                                            <td>{{ $subject->code }}</td>
                                            <td>{{ $subject->pivot->credit_hours ?? $subject->credit_hours }}</td>
                                            <td>
                                                <span class="badge badge-{{ $subject->pivot->is_compulsory ? 'primary' : 'secondary' }}">
                                                    {{ $subject->pivot->is_compulsory ? 'Compulsory' : 'Optional' }}
                                                </span>
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.programs.remove-subject', [$program, $subject]) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('Remove this subject from program?')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No subjects assigned to this program</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                                <i class="fas fa-plus me-1"></i> Add First Subject
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Class-Subject Assignment -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Class-Subject Assignments</h6>
                </div>
                <div class="card-body">
                    @if($program->classes->count() > 0)
                        @foreach($program->classes as $class)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">{{ $class->name }} - Subjects</h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" data-bs-target="#assignSubjectModal"
                                            data-class-id="{{ $class->id }}" data-class-name="{{ $class->name }}">
                                        <i class="fas fa-plus me-1"></i> Assign Subject
                                    </button>
                                </div>
                                
                                @if($class->subjects->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Subject</th>
                                                    <th>Code</th>
                                                    <th>Credits</th>
                                                    <th>Type</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($class->subjects as $subject)
                                                    <tr>
                                                        <td>{{ $subject->name }}</td>
                                                        <td>{{ $subject->code }}</td>
                                                        <td>{{ $subject->pivot->credit_hours }}</td>
                                                        <td>
                                                            <span class="badge badge-{{ $subject->pivot->is_compulsory ? 'primary' : 'secondary' }}">
                                                                {{ $subject->pivot->is_compulsory ? 'Compulsory' : 'Optional' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <form method="POST" action="{{ route('admin.classes.remove-subject', [$class, $subject]) }}" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                        onclick="return confirm('Remove this subject from class?')">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No subjects assigned to this class yet.
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Please add classes to this program first before assigning subjects.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Class Modal -->
<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.programs.add-class', $program) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Class to Program</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="class_id" class="form-label">Select Class</label>
                        <select name="class_id" id="class_id" class="form-select" required>
                            <option value="">Choose a class...</option>
                            @foreach($availableClasses as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->name }} ({{ $class->level->name ?? 'No Level' }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Select a class to add to this program</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.programs.add-subject', $program) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Subject to Program</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Select Subject</label>
                        <select name="subject_id" id="subject_id" class="form-select" required>
                            <option value="">Choose a subject...</option>
                            @foreach($availableSubjects as $subject)
                                <option value="{{ $subject->id }}">
                                    {{ $subject->name }} ({{ $subject->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="credit_hours" class="form-label">Credit Hours</label>
                            <input type="number" name="credit_hours" id="credit_hours" 
                                   class="form-control" step="0.5" min="0" max="10" value="3">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="is_compulsory" class="form-label">Subject Type</label>
                            <select name="is_compulsory" id="is_compulsory" class="form-select">
                                <option value="1">Compulsory</option>
                                <option value="0">Optional</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Subject to Class Modal -->
<div class="modal fade" id="assignSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="assignSubjectForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign Subject to <span id="className"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assign_subject_id" class="form-label">Select Subject</label>
                        <select name="subject_id" id="assign_subject_id" class="form-select" required>
                            <option value="">Choose a subject...</option>
                            @foreach($program->subjects as $subject)
                                <option value="{{ $subject->id }}">
                                    {{ $subject->name }} ({{ $subject->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="assign_credit_hours" class="form-label">Credit Hours</label>
                            <input type="number" name="credit_hours" id="assign_credit_hours"
                                   class="form-control" step="0.5" min="0" max="10" value="3">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="assign_is_compulsory" class="form-label">Subject Type</label>
                            <select name="is_compulsory" id="assign_is_compulsory" class="form-select">
                                <option value="1">Compulsory</option>
                                <option value="0">Optional</option>
                            </select>
                        </div>
                    </div>

                    @if($program->program_type === 'yearly')
                        <div class="mb-3">
                            <label for="assign_year_no" class="form-label">Year Number</label>
                            <select name="year_no" id="assign_year_no" class="form-select">
                                <option value="">Select Year</option>
                                @for($i = 1; $i <= $program->duration_years; $i++)
                                    <option value="{{ $i }}">Year {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    @else
                        <div class="mb-3">
                            <label for="assign_semester_id" class="form-label">Semester</label>
                            <select name="semester_id" id="assign_semester_id" class="form-select">
                                <option value="">Select Semester</option>
                                @foreach($semesters as $semester)
                                    <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const assignSubjectModal = document.getElementById('assignSubjectModal');

    assignSubjectModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const classId = button.getAttribute('data-class-id');
        const className = button.getAttribute('data-class-name');

        // Update modal title
        document.getElementById('className').textContent = className;

        // Update form action
        const form = document.getElementById('assignSubjectForm');
        form.action = `/admin/classes/${classId}/add-subject`;
    });
});
</script>
@endpush
@endsection

@extends('layouts.admin')

@section('title', 'Class Details - ' . $class->name)
@section('page-title', 'Class Details')

@section('content')
<div class="container-fluid">
    <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')
    
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $class->name }}</h1>
            <p class="mb-0 text-muted">Class Code: {{ $class->code }}</p>
        </div>
        <div>
            <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Class
            </a>
            <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Classes
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Class Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Class Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Class Name:</td>
                                    <td>{{ $class->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Class Code:</td>
                                    <td><span class="badge bg-primary">{{ $class->code }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Educational Level:</td>
                                    <td>{{ $class->level->name ?? 'No Level Assigned' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Department:</td>
                                    <td>{{ $class->department->name ?? 'Not Assigned' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Faculty:</td>
                                    <td>{{ $class->department->faculty->name ?? 'Not Assigned' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        @if($class->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created:</td>
                                    <td>{{ $class->created_at->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Last Updated:</td>
                                    <td>{{ $class->updated_at->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students List -->
            @if($class->enrollments->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Enrolled Students ({{ $class->enrollments->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Admission No.</th>
                                    <th>Program</th>
                                    <th>Academic Year</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($class->enrollments as $enrollment)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="rounded-circle bg-info d-flex align-items-center justify-content-center" 
                                                     style="width: 35px; height: 35px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $enrollment->student->name }}</div>
                                                <small class="text-muted">{{ $enrollment->student->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $enrollment->student->admission_number }}</td>
                                    <td>{{ $enrollment->program->name ?? 'Not Assigned' }}</td>
                                    <td>{{ $enrollment->academicYear->name ?? 'Not Assigned' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $enrollment->status === 'active' ? 'success' : 'warning' }}">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Programs List -->
            @if($class->programs && $class->programs->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Associated Programs ({{ $class->programs->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($class->programs as $program)
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-primary">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $program->name }}</div>
                                            <small class="text-muted">{{ $program->duration_years }} years • {{ ucfirst($program->degree_type) }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @else
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">Associated Programs</h6>
                </div>
                <div class="card-body text-center py-4">
                    <i class="fas fa-graduation-cap fa-3x text-gray-300 mb-3"></i>
                    <h6 class="text-gray-600">No Programs Found</h6>
                    <p class="text-muted">This class doesn't have any associated programs in the same department.</p>
                    @if($class->department)
                        <a href="{{ route('admin.programs.create', ['department' => $class->department->id]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add Program to {{ $class->department->name }}
                        </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Subjects List -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">Assigned Subjects ({{ $class->subjects->count() }})</h6>
                    @if($availableSubjects->count() > 0)
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                        <i class="fas fa-plus"></i> Add Subject
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    @if($class->subjects && $class->subjects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Subject Name</th>
                                        <th>Subject Code</th>
                                        <th>Credit Hours</th>
                                        <th>Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($class->subjects as $subject)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $subject->name }}</div>
                                            <small class="text-muted">{{ $subject->department->name ?? 'No Department' }}</small>
                                        </td>
                                        <td><span class="badge bg-info">{{ $subject->code }}</span></td>
                                        <td>{{ $subject->pivot->credit_hours ?? $subject->credit_hours }}</td>
                                        <td>
                                            <span class="badge bg-{{ $subject->pivot->is_compulsory ? 'primary' : 'secondary' }}">
                                                {{ $subject->pivot->is_compulsory ? 'Compulsory' : 'Elective' }}
                                            </span>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.classes.remove-subject', [$class, $subject]) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to remove this subject from the class?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> Remove
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
                            <p class="text-muted">No subjects assigned to this class yet.</p>
                            @if($availableSubjects->count() > 0)
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                                <i class="fas fa-plus"></i> Add First Subject
                            </button>
                            @else
                            <p class="text-muted">No subjects available to assign.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Class Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border-end">
                                <div class="h4 mb-0 text-primary">{{ $class->enrollments->count() }}</div>
                                <small class="text-muted">Students</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h4 mb-0 text-success">{{ $class->programs->count() }}</div>
                            <small class="text-muted">Programs</small>
                        </div>
                        <div class="col-6">
                            <div class="border-end">
                                <div class="h4 mb-0 text-info">{{ $class->enrollments->where('status', 'active')->count() }}</div>
                                <small class="text-muted">Active</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-0 text-warning">{{ $class->enrollments->where('status', '!=', 'active')->count() }}</div>
                            <small class="text-muted">Inactive</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit Class
                        </a>
                        <a href="{{ route('admin.students.index', ['class' => $class->id]) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-users"></i> View Students
                        </a>
                        <a href="{{ route('admin.programs.index', ['class' => $class->id]) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-graduation-cap"></i> Manage Programs
                        </a>
                        @if($class->is_active)
                            <button class="btn btn-warning btn-sm" onclick="toggleStatus(false)">
                                <i class="fas fa-pause"></i> Deactivate Class
                            </button>
                        @else
                            <button class="btn btn-success btn-sm" onclick="toggleStatus(true)">
                                <i class="fas fa-play"></i> Activate Class
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleStatus(activate) {
        const action = activate ? 'activate' : 'deactivate';
        const message = `Are you sure you want to ${action} this class?`;
        
        if (confirm(message)) {
            // Create a form to submit the status change
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.classes.update", $class) }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'PATCH';
            
            const statusField = document.createElement('input');
            statusField.type = 'hidden';
            statusField.name = 'is_active';
            statusField.value = activate ? '1' : '0';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            form.appendChild(statusField);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSubjectModalLabel">Add Subject to {{ $class->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.classes.add-subject', $class) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Select Subject <span class="text-danger">*</span></label>
                        <select class="form-select" id="subject_id" name="subject_id" required>
                            <option value="">Choose a subject...</option>
                            @foreach($availableSubjects as $subject)
                                <option value="{{ $subject->id }}" data-credits="{{ $subject->credit_hours }}">
                                    {{ $subject->name }} ({{ $subject->code }}) - {{ $subject->department->name ?? 'No Dept' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="credit_hours" class="form-label">Credit Hours</label>
                                <input type="number" class="form-control" id="credit_hours" name="credit_hours"
                                       min="0" max="10" step="0.5" placeholder="Auto-filled from subject">
                                <small class="text-muted">Leave empty to use subject's default credit hours</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="is_compulsory" class="form-label">Subject Type</label>
                                <select class="form-select" id="is_compulsory" name="is_compulsory">
                                    <option value="1">Compulsory</option>
                                    <option value="0">Elective</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-fill credit hours when subject is selected
    document.getElementById('subject_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const credits = selectedOption.dataset.credits;
        const creditHoursInput = document.getElementById('credit_hours');

        if (credits) {
            creditHoursInput.placeholder = `Default: ${credits} credits`;
        } else {
            creditHoursInput.placeholder = 'Enter credit hours';
        }
    });
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Subject Assignments')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-dark">Subject Assignments</h1>
            <p class="mb-0 text-muted">
                Manage subject assignments for {{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('admin.student-subjects.bulk-assign', $enrollment) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Assign all program and class subjects to this student?')">
                    <i class="fas fa-plus-circle me-1"></i>
                    Assign All Program Subjects
                </button>
            </form>
            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Enrollments
            </a>
        </div>
    </div>

    <!-- Auto Enrollment Info -->
    @if($assignedSubjects->count() > 0)
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Automatic Enrollment:</strong> Compulsory subjects are automatically assigned when a student is enrolled in a program.
        Use the "Assign All Program Subjects" button to add remaining elective subjects.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Student Information Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Student Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg me-3">
                            @if($enrollment->student->photo)
                                <img class="rounded-circle border border-2 border-primary"
                                     src="{{ Storage::url($enrollment->student->photo) }}"
                                     alt="{{ $enrollment->student->full_name }}"
                                     style="width: 60px; height: 60px; object-fit: cover;"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="avatar-title bg-primary rounded-circle d-none align-items-center justify-content-center"
                                     style="width: 60px; height: 60px;">
                                    <span class="text-white fw-bold">
                                        {{ strtoupper(substr($enrollment->student->first_name, 0, 1) . substr($enrollment->student->last_name, 0, 1)) }}
                                    </span>
                                </div>
                            @else
                                <div class="avatar-title bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 60px; height: 60px;">
                                    <span class="text-white fw-bold">
                                        {{ strtoupper(substr($enrollment->student->first_name, 0, 1) . substr($enrollment->student->last_name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h5 class="text-dark mb-1">{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</h5>
                            <p class="text-muted mb-0">{{ $enrollment->student->admission_number }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Program</label>
                    <p class="text-dark mb-0">{{ $enrollment->program->name }}</p>
                    <small class="text-muted">{{ $enrollment->program->department->name ?? 'N/A' }}</small>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Class</label>
                    <p class="text-dark mb-0">{{ $enrollment->class->name }}</p>
                    @if($enrollment->roll_number)
                        <small class="text-muted">Roll: {{ $enrollment->roll_number }}</small>
                    @endif
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Academic Year</label>
                    <p class="text-dark mb-0">{{ $enrollment->academicYear->name }}</p>
                    <small class="text-muted">Status: <span class="badge bg-success">{{ ucfirst($enrollment->status) }}</span></small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Assigned Subjects -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Assigned Subjects ({{ $assignedSubjects->count() }})</h6>
                </div>
                <div class="card-body">
                    @if($assignedSubjects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Subject Name</th>
                                        <th>Code</th>
                                        <th>Credit Hours</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Date Added</th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignedSubjects as $assignment)
                                        <tr>
                                            <td class="fw-bold text-dark">{{ $assignment->subject->name }}</td>
                                            <td><span class="badge bg-secondary">{{ $assignment->subject->code }}</span></td>
                                            <td>{{ $assignment->subject->credit_hours }}</td>
                                            <td><span class="badge bg-info">{{ ucfirst($assignment->subject->subject_type) }}</span></td>
                                            <td>
                                                @if($assignment->status === 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-warning">Dropped</span>
                                                @endif
                                            </td>
                                            <td>{{ $assignment->date_added->format('M d, Y') }}</td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @if($assignment->status === 'active')
                                                        <form method="POST" action="{{ route('admin.student-subjects.update-status', [$enrollment, $assignment]) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="dropped">
                                                            <button type="submit" class="btn btn-sm btn-warning" title="Drop Subject" 
                                                                    onclick="return confirm('Drop this subject for the student?')">
                                                                <i class="fas fa-minus-circle"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('admin.student-subjects.update-status', [$enrollment, $assignment]) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="active">
                                                            <button type="submit" class="btn btn-sm btn-success" title="Reactivate Subject">
                                                                <i class="fas fa-plus-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form method="POST" action="{{ route('admin.student-subjects.destroy', [$enrollment, $assignment]) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Remove Assignment" 
                                                                onclick="return confirm('Remove this subject assignment permanently?')">
                                                            <i class="fas fa-trash"></i>
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
                            <i class="fas fa-book fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">No subjects assigned</h5>
                            <p class="text-muted mb-4">This student has no subject assignments yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Available Subjects -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Available Subjects</h6>
                </div>
                <div class="card-body">
                    @if($availableSubjects->count() > 0)
                        <form method="POST" action="{{ route('admin.student-subjects.store', $enrollment) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Select subjects to assign:</label>
                                <div class="max-height-300 overflow-auto">
                                    @foreach($availableSubjects as $subject)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="subjects[]" 
                                                   value="{{ $subject->id }}" id="subject_{{ $subject->id }}">
                                            <label class="form-check-label" for="subject_{{ $subject->id }}">
                                                <div class="fw-bold text-dark">{{ $subject->name }}</div>
                                                <small class="text-muted">
                                                    {{ $subject->code }} • {{ $subject->credit_hours }} credits • {{ ucfirst($subject->subject_type) }}
                                                </small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" onclick="selectAllSubjects()" class="btn btn-sm btn-outline-primary">
                                    Select All
                                </button>
                                <button type="button" onclick="deselectAllSubjects()" class="btn btn-sm btn-outline-secondary">
                                    Deselect All
                                </button>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-plus me-1"></i>
                                    Assign Selected Subjects
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                            <h6 class="text-success">All subjects assigned!</h6>
                            <p class="text-muted mb-0">This student has been assigned all available subjects for their program.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="h4 mb-0 font-weight-bold text-primary">{{ $assignedSubjects->where('status', 'active')->count() }}</div>
                            <div class="small text-muted">Active Subjects</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h4 mb-0 font-weight-bold text-warning">{{ $assignedSubjects->where('status', 'dropped')->count() }}</div>
                            <div class="small text-muted">Dropped Subjects</div>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-0 font-weight-bold text-info">{{ $assignedSubjects->sum('subject.credit_hours') }}</div>
                            <div class="small text-muted">Total Credits</div>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-0 font-weight-bold text-success">{{ $availableSubjects->count() }}</div>
                            <div class="small text-muted">Available</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.avatar-lg {
    width: 4rem;
    height: 4rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    font-weight: 600;
}

.max-height-300 {
    max-height: 300px;
}

.text-dark {
    color: #212529 !important;
}

.form-label {
    font-weight: 600;
    color: #374151;
}
</style>
@endpush

@push('scripts')
<script>
    function selectAllSubjects() {
        document.querySelectorAll('input[name="subjects[]"]').forEach(checkbox => {
            checkbox.checked = true;
        });
    }

    function deselectAllSubjects() {
        document.querySelectorAll('input[name="subjects[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    }
</script>
@endpush
@endsection

@extends('layouts.admin')

@section('title', 'Examination Management')
@section('page-title', 'Examinations')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Examination Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Examinations</li>
                </ol>
            </nav>
            <p class="text-muted">Manage exams, assessments, and evaluation schedules</p>
        </div>
        <div>
            <button onclick="toggleBulkActions()" class="btn btn-secondary me-2">
                <i class="fas fa-tasks"></i> Bulk Actions
            </button>
            <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Exam
            </a>
        </div>
    </div>
    <!-- Search and Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter Exams</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.exams.index') }}">
                <div class="row">
                    <!-- Search Input -->
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Search Exams</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Search by name, subject, or class..."
                                   class="form-control">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Exam Type Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="exam_type" class="form-label">Type</label>
                        <select name="exam_type" id="exam_type" class="form-select">
                            <option value="">All Types</option>
                            @foreach($examTypes as $type)
                                <option value="{{ $type }}" {{ request('exam_type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Academic Year Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="academic_year" class="form-label">Academic Year</label>
                        <select name="academic_year" id="academic_year" class="form-select">
                            <option value="">All Years</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ request('academic_year') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Button -->
                    <div class="col-md-2 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions Bar (Hidden by default) -->
    <div id="bulk-actions-bar" class="alert alert-warning d-none mb-4">
        <form method="POST" action="{{ route('admin.exams.bulk-action') }}" onsubmit="return confirmBulkAction()">
            @csrf
            <div class="d-flex flex-wrap align-items-center gap-3">
                <span class="fw-bold">
                    <span id="selected-count">0</span> exams selected
                </span>
                <select name="action" class="form-select form-select-sm" style="width: auto;">
                    <option value="">Choose Action</option>
                    <option value="change_status">Change Status</option>
                    <option value="delete">Delete</option>
                </select>
                <select name="status" class="form-select form-select-sm d-none" style="width: auto;" id="status-select">
                    @foreach($statuses as $status)
                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-warning btn-sm">
                    Apply
                </button>
                <button type="button" onclick="clearSelection()" class="btn btn-outline-warning btn-sm">
                    Clear Selection
                </button>
            </div>
            <input type="hidden" name="exams" id="selected-exams" value="">
        </form>
    </div>

    <!-- Exams Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Examinations List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="select-all" class="form-check-input" onchange="toggleAllExams()">
                            </th>
                            <th>Exam Details</th>
                            <th>Subject & Class</th>
                            <th>Schedule</th>
                            <th>Marks</th>
                            <th>Status</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $exam)
                            <tr>
                                <td>
                                    <input type="checkbox" class="exam-checkbox form-check-input"
                                           value="{{ $exam->id }}" onchange="updateSelection()">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-clipboard-list text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $exam->name }}</div>
                                            <small class="text-muted">{{ $exam->getTypeLabel() }} • {{ $exam->academicYear->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $exam->subject->name ?? 'All Subjects' }}</div>
                                    <small class="text-muted">
                                        @if($exam->exam_scope === 'level')
                                            <i class="fas fa-layer-group me-1"></i>{{ $exam->level->name ?? 'Level' }} - {{ $exam->class->name ?? 'All Classes' }}
                                        @elseif($exam->exam_scope === 'school')
                                            <i class="fas fa-school me-1"></i>School-wide - {{ $exam->class->name ?? 'All Classes' }}
                                        @else
                                            <i class="fas fa-chalkboard-teacher me-1"></i>{{ $exam->class->name ?? 'All Classes' }}
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <div>{{ $exam->start_date?->format('M d, Y') ?? 'Not Set' }}</div>
                                    <small class="text-muted">to {{ $exam->end_date?->format('M d, Y') ?? 'Not Set' }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $exam->max_marks }} marks</div>
                                    <small class="text-muted">
                                        Theory: {{ $exam->theory_max }}
                                        @if($exam->has_practical) • Practical: {{ $exam->practical_max }} @endif
                                        @if($exam->has_assessment) • Assessment: {{ $exam->assess_max }} @endif
                                    </small>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($exam->status) {
                                            'draft' => 'bg-secondary',
                                            'scheduled' => 'bg-info',
                                            'ongoing' => 'bg-warning',
                                            'submitted' => 'bg-primary',
                                            'approved' => 'bg-success',
                                            'published' => 'bg-dark',
                                            'locked' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ ucfirst($exam->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.exams.show', $exam) }}"
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($exam->is_editable)
                                            <a href="{{ route('admin.exams.edit', $exam) }}"
                                               class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($exam->can_enter_marks)
                                            <a href="#" class="btn btn-sm btn-success" title="Enter Marks">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        @endif
                                        @if($exam->is_editable)
                                            <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}"
                                                  class="d-inline" onsubmit="return confirm('Are you sure you want to delete this exam?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-center">
                                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No exams found</h5>
                                        <p class="text-muted mb-3">Get started by creating your first exam.</p>
                                        <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Create Exam
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($exams->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-center">
                    {{ $exams->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    let selectedExams = [];

    function toggleBulkActions() {
        const bulkBar = document.getElementById('bulk-actions-bar');
        bulkBar.classList.toggle('d-none');
    }

    function toggleAllExams() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.exam-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });

        updateSelection();
    }

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.exam-checkbox:checked');
        selectedExams = Array.from(checkboxes).map(cb => cb.value);

        document.getElementById('selected-count').textContent = selectedExams.length;
        document.getElementById('selected-exams').value = selectedExams.join(',');

        // Show/hide bulk actions bar
        const bulkBar = document.getElementById('bulk-actions-bar');
        if (selectedExams.length > 0) {
            bulkBar.classList.remove('d-none');
        } else {
            bulkBar.classList.add('d-none');
        }

        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.exam-checkbox');
        const selectAll = document.getElementById('select-all');
        selectAll.checked = selectedExams.length === allCheckboxes.length;
        selectAll.indeterminate = selectedExams.length > 0 && selectedExams.length < allCheckboxes.length;
    }

    function clearSelection() {
        document.querySelectorAll('.exam-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateSelection();
    }

    function confirmBulkAction() {
        const action = document.querySelector('select[name="action"]').value;
        if (!action) {
            alert('Please select an action.');
            return false;
        }

        if (selectedExams.length === 0) {
            alert('Please select at least one exam.');
            return false;
        }

        return confirm(`Are you sure you want to ${action.replace('_', ' ')} ${selectedExams.length} exam(s)?`);
    }

    // Show/hide status select based on action
    document.querySelector('select[name="action"]').addEventListener('change', function() {
        const statusSelect = document.getElementById('status-select');
        if (this.value === 'change_status') {
            statusSelect.classList.remove('d-none');
        } else {
            statusSelect.classList.add('d-none');
        }
    });
</script>
@endpush

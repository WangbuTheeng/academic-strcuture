@extends('layouts.admin')

@section('title', 'Mark Entry System')
@section('page-title', 'Mark Entry System')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Mark Entry System</h1>
            <p class="mb-0 text-muted">Enter and manage examination marks for students</p>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter Exams</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.marks.index') }}">
                <div class="row">
                    <!-- Search Input -->
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Search Exams</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Search by exam name..."
                                   class="form-control">
                        </div>
                    </div>

                    <!-- Class Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="class" class="form-label">Class</label>
                        <select name="class" id="class" class="form-select">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} ({{ $class->level->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Subject Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <select name="subject" id="subject" class="form-select">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }} ({{ $subject->department->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Academic Year Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="academic_year" class="form-label">Academic Year</label>
                        <select name="academic_year" id="academic_year" class="form-select">
                            <option value="">Current Year</option>
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
                        <a href="{{ route('admin.marks.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Available Exams Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Available Exams for Mark Entry</h6>
            <small class="text-muted">Select an exam to enter marks. Only exams with "Ongoing" status are available for mark entry.</small>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>Exam Details</th>
                            <th>Subject & Class</th>
                            <th>Schedule</th>
                            <th>Marking Scheme</th>
                            <th>Progress</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $exam)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-edit text-white"></i>
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
                                    <small class="text-muted">{{ $exam->class->name ?? 'All Classes' }}</small>
                                </td>
                                <td>
                                    <div>{{ $exam->start_date?->format('M d, Y') ?? 'Not Set' }}</div>
                                    <small class="text-muted">to {{ $exam->end_date?->format('M d, Y') ?? 'Not Set' }}</small>
                                    <div class="text-danger small">Deadline: {{ $exam->submission_deadline?->format('M d, Y H:i') ?? 'Not Set' }}</div>
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
                                        $totalMarks = $exam->marks()->count();
                                        $submittedMarks = $exam->marks()->where('status', '!=', 'draft')->count();
                                        $progressPercentage = $totalMarks > 0 ? ($submittedMarks / $totalMarks) * 100 : 0;
                                    @endphp
                                    <div>{{ $submittedMarks }}/{{ $totalMarks }} entered</div>
                                    <div class="progress mt-1" style="height: 8px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progressPercentage }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ number_format($progressPercentage, 1) }}% complete</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.marks.exam-dashboard', $exam) }}"
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> Enter Marks
                                        </a>

                                        <a href="{{ route('admin.marks.show', $exam) }}"
                                           class="btn btn-info btn-sm" title="View Progress">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-center">
                                        <i class="fas fa-edit fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No exams available for mark entry</h5>
                                        <p class="text-muted mb-3">There are currently no ongoing exams that require mark entry.</p>
                                        <a href="{{ route('admin.exams.index') }}" class="btn btn-primary">
                                            <i class="fas fa-clipboard-list"></i> View All Exams
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

    <!-- Quick Stats -->
    @if($exams->count() > 0)
    <div class="row mt-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Available Exams</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $exams->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Marks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $exams->sum(function($exam) { return $exam->marks()->where('status', 'draft')->count(); }) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-edit fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Submitted Marks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $exams->sum(function($exam) { return $exam->marks()->where('status', 'submitted')->count(); }) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Approved Marks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $exams->sum(function($exam) { return $exam->marks()->where('status', 'approved')->count(); }) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

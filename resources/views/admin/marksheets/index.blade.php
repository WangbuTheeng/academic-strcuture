@extends('layouts.admin')

@section('title', 'Marksheet Generation')
@section('page-title', 'Marksheet Generation')

@push('styles')
<style>
.exam-card {
    transition: all 0.3s ease;
    border: 1px solid #e3e6f0;
}

.exam-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-color: #4e73df;
}

.status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.workflow-step {
    padding: 10px 15px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    margin: 0 5px;
}

.workflow-step.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.workflow-step.inactive {
    background: #f8f9fc;
    color: #6c757d;
    border: 1px solid #e3e6f0;
}

.help-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-certificate text-primary"></i> Marksheet Generation
            </h1>
            <p class="mb-0 text-muted">Generate professional marksheets for published exam results</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-chart-bar"></i> All Reports
            </a>
        </div>
    </div>

    <!-- Workflow Guide -->
    <div class="card shadow mb-4 help-card">
        <div class="card-body text-center">
            <h6 class="text-white mb-3">
                <i class="fas fa-info-circle"></i> Marksheet Generation Workflow
            </h6>
            <div class="d-flex justify-content-center align-items-center flex-wrap">
                <span class="workflow-step inactive">Draft</span>
                <i class="fas fa-arrow-right text-white mx-2"></i>
                <span class="workflow-step inactive">Ongoing</span>
                <i class="fas fa-arrow-right text-white mx-2"></i>
                <span class="workflow-step inactive">Submitted</span>
                <i class="fas fa-arrow-right text-white mx-2"></i>
                <span class="workflow-step inactive">Approved</span>
                <i class="fas fa-arrow-right text-white mx-2"></i>
                <span class="workflow-step active">Published</span>
                <i class="fas fa-arrow-right text-white mx-2"></i>
                <span class="workflow-step inactive">Locked</span>
            </div>
            <small class="text-white-50 mt-2 d-block">
                Only <strong>Published</strong> exams with approved marks can generate marksheets
            </small>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-search"></i> Search Exams
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.marksheets.index') }}">
                <div class="row">
                    <!-- Search Input -->
                    <div class="col-md-6 mb-3">
                        <label for="search" class="form-label">Search Exams</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Search by exam name..." class="form-control">
                        </div>
                    </div>

                    <!-- Academic Year Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="academic_year" class="form-label">Academic Year</label>
                        <select name="academic_year" id="academic_year" class="form-control">
                            <option value="">All Years</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ request('academic_year') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Class Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="class" class="form-label">Class</label>
                        <select name="class" id="class" class="form-control">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} ({{ $class->level->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="{{ route('admin.marksheets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </div>
                </div>
            </form>
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

    <!-- Published Exams Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list-alt"></i> Published Exams Available for Marksheet Generation
            </h6>
            <span class="badge badge-info">{{ $exams->count() }} Exams</span>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">
                <i class="fas fa-info-circle text-primary"></i>
                Select an exam to generate marksheets. Only published or locked exams are available for marksheet generation.
            </p>

            @if($exams->count() > 0)
                <div class="row">
                    @foreach($exams as $exam)
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="card exam-card h-100">
                                <div class="card-header bg-gradient-primary text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-file-alt"></i> {{ $exam->name }}
                                        </h6>
                                        <span class="badge badge-light status-badge">
                                            {{ ucfirst($exam->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <small class="text-muted">Subject & Class</small>
                                        <div class="font-weight-bold">{{ $exam->subject->name ?? 'All Subjects' }}</div>
                                        <div class="text-muted">{{ $exam->class->name ?? 'All Classes' }}</div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">Academic Year</small>
                                        <div class="font-weight-bold">{{ $exam->academicYear->name ?? 'N/A' }}</div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">Students with Approved Marks</small>
                                        <div class="font-weight-bold text-success">
                                            @if($exam->class)
                                                {{ $exam->class->students()->count() }} Students
                                            @else
                                                All Students
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <a href="{{ route('admin.marksheets.create', ['exam' => $exam->id]) }}"
                                       class="btn btn-primary btn-block">
                                        <i class="fas fa-certificate"></i> Generate Marksheets
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No published exams found</h5>
                    <p class="text-muted">There are currently no published exams available for marksheet generation.</p>
                    <div class="mt-3">
                        <a href="{{ route('admin.exams.index') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> View All Exams
                        </a>
                        <a href="{{ route('admin.marks.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-edit"></i> Mark Entry
                        </a>
                    </div>
                    <div class="mt-4">
                        <small class="text-muted">
                            <i class="fas fa-lightbulb text-warning"></i>
                            <strong>Tip:</strong> To generate marksheets, first ensure exams are published and marks are approved.
                        </small>
                    </div>
                </div>
            @endif

            <!-- Pagination -->
            @if($exams->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $exams->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Stats -->
    @if($exams->count() > 0)
    <div class="row mt-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Published Exams
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $exams->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Students with Results
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $exams->sum(function($exam) { return $exam->marks()->where('status', 'approved')->distinct('student_id')->count(); }) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Approved Marks
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $exams->sum(function($exam) { return $exam->marks()->where('status', 'approved')->count(); }) }}
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Academic Years
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $academicYears->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection

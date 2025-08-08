@extends('layouts.admin')

@section('title', 'Academic Year Details - ' . $academicYear->name)
@section('page-title', 'Academic Year Details')

@section('content')

 @include('admin.academic.partials.sub-navbar')

 
<div class="container-fluid">
    <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')
    
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Academic Year: {{ $academicYear->name }}</h1>
            <p class="mb-0 text-muted">View academic year details and statistics</p>
        </div>
        <div>
            <a href="{{ route('admin.academic-years.edit', $academicYear) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.academic-years.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Academic Year Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Academic Year Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Academic Year:</td>
                                    <td>{{ $academicYear->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Start Date:</td>
                                    <td>{{ $academicYear->start_date?->format('M d, Y') ?? 'Not Set' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">End Date:</td>
                                    <td>{{ $academicYear->end_date?->format('M d, Y') ?? 'Not Set' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Duration:</td>
                                    <td>{{ ($academicYear->start_date && $academicYear->end_date) ? $academicYear->start_date->diffInDays($academicYear->end_date) . ' days' : 'Not Available' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        @if($academicYear->is_current)
                                            <span class="badge bg-success">Current</span>
                                        @else
                                            <span class="badge bg-secondary">Not Current</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created:</td>
                                    <td>{{ $academicYear->created_at->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Last Updated:</td>
                                    <td>{{ $academicYear->updated_at->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Year Duration -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Academic Calendar</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Academic Year Duration</h6>
                            <p class="text-muted">
                                This academic year runs from
                                <strong>{{ $academicYear->start_date?->format('M d, Y') ?? 'Not Set' }}</strong>
                                to
                                <strong>{{ $academicYear->end_date?->format('M d, Y') ?? 'Not Set' }}</strong>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-info">Status</h6>
                            @if($academicYear->is_current)
                                <span class="badge bg-success fs-6">Current Academic Year</span>
                            @else
                                <span class="badge bg-secondary fs-6">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border-end">
                                <div class="h4 mb-0 text-primary">{{ $academicYear->exams->count() ?? 0 }}</div>
                                <small class="text-muted">Exams</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h4 mb-0 text-success">{{ $academicYear->enrollments->count() ?? 0 }}</div>
                            <small class="text-muted">Enrollments</small>
                        </div>
                        <div class="col-6">
                            <div class="border-end">
                                <div class="h4 mb-0 text-info">
                                    @if($academicYear->start_date && $academicYear->end_date)
                                        {{ $academicYear->start_date->diffInDays($academicYear->end_date) }}
                                    @else
                                        0
                                    @endif
                                </div>
                                <small class="text-muted">Days</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-0 text-warning">{{ $academicYear->semesters->where('is_active', true)->count() }}</div>
                            <small class="text-muted">Active Semesters</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$academicYear->is_current)
                            <form method="POST" action="{{ route('admin.academic-years.set-current', $academicYear) }}">
                                @csrf
                                <button type="submit" class="btn btn-success w-100" 
                                        onclick="return confirm('Are you sure you want to set this as the current academic year?')">
                                    <i class="fas fa-check-circle me-1"></i>Set as Current
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('admin.students.index', ['academic_year' => $academicYear->id]) }}"
                           class="btn btn-primary">
                            <i class="fas fa-users me-1"></i>View Students
                        </a>
                        
                        <a href="{{ route('admin.exams.create', ['academic_year' => $academicYear->id]) }}" 
                           class="btn btn-info">
                            <i class="fas fa-clipboard-list me-1"></i>Create Exam
                        </a>
                        
                        <a href="{{ route('admin.academic-years.edit', $academicYear) }}" 
                           class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Edit Details
                        </a>
                        
                        @if(!$academicYear->is_current && $academicYear->exams->count() == 0)
                            <form method="POST" action="{{ route('admin.academic-years.destroy', $academicYear) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100" 
                                        onclick="return confirm('Are you sure you want to delete this academic year?')">
                                    <i class="fas fa-trash me-1"></i>Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

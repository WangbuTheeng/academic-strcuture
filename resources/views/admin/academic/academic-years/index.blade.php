@extends('layouts.admin')

@section('title', 'Academic Years Management')

@section('content')
 @include('admin.academic.partials.sub-navbar')
 
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Academic Years Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.academic.index') }}">Academic Structure</a></li>
                    <li class="breadcrumb-item active">Academic Years</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Academic Year
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.academic-years.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="Search academic years..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="current" {{ request('status') == 'current' ? 'selected' : '' }}>Current</option>
                        <option value="past" {{ request('status') == 'past' ? 'selected' : '' }}>Past</option>
                        <option value="future" {{ request('status') == 'future' ? 'selected' : '' }}>Future</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Academic Years Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Academic Years List</h6>
        </div>
        <div class="card-body">
            @if($academicYears->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Enrollments</th>
                                <th>Semesters</th>
                                <th>Exams</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($academicYears as $academicYear)
                            <tr>
                                <td>
                                    <strong>{{ $academicYear->name }}</strong>
                                    @if($academicYear->is_current)
                                        <span class="badge badge-success badge-sm ml-1">Current</span>
                                    @endif
                                </td>
                                <td>{{ $academicYear->start_date?->format('M d, Y') ?? 'Not Set' }}</td>
                                <td>{{ $academicYear->end_date?->format('M d, Y') ?? 'Not Set' }}</td>
                                <td>
                                    @if($academicYear->is_current)
                                        <span class="badge badge-success">Current</span>
                                    @elseif($academicYear->end_date < now())
                                        <span class="badge badge-secondary">Past</span>
                                    @elseif($academicYear->start_date > now())
                                        <span class="badge badge-info">Future</span>
                                    @else
                                        <span class="badge badge-warning">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $academicYear->enrollments_count ?? 0 }}</td>
                                <td>{{ $academicYear->semesters_count ?? 0 }}</td>
                                <td>{{ $academicYear->exams_count ?? 0 }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.academic-years.show', $academicYear) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.academic-years.edit', $academicYear) }}" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!$academicYear->is_current)
                                            <form method="POST" action="{{ route('admin.academic-years.set-current', $academicYear) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Set as Current" onclick="return confirm('Set this as current academic year?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if(!$academicYear->is_current && $academicYear->enrollments_count == 0 && $academicYear->exams_count == 0)
                                            <form method="POST" action="{{ route('admin.academic-years.destroy', $academicYear) }}" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $academicYears->firstItem() }} to {{ $academicYears->lastItem() }} of {{ $academicYears->total() }} results
                    </div>
                    <div>
                        {{ $academicYears->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No academic years found.</p>
                    <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add First Academic Year
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

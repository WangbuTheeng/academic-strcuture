@extends('layouts.admin')

@section('title', 'Grading Scales')
@section('page-title', 'Grading Scales')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Grading Scales</h1>
            <p class="mb-0 text-muted">Manage grading scales and grade ranges for different academic levels</p>
        </div>
        <div>
            <a href="{{ route('admin.grading-scales.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Scale
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Grading Scales</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.grading-scales.index') }}">
                <div class="row">
                    <!-- Search -->
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               placeholder="Search by name or description..."
                               class="form-control">
                    </div>

                    <!-- Level Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="level" class="form-label">Level</label>
                        <select name="level" id="level" class="form-control">
                            <option value="">All Levels</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}" {{ request('level') == $level->id ? 'selected' : '' }}>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Program Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="program" class="form-label">Program</label>
                        <select name="program" id="program" class="form-control">
                            <option value="">All Programs</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ request('program') == $program->id ? 'selected' : '' }}>
                                    {{ $program->name }} ({{ $program->level->name ?? 'No Level' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="text-right">
                    <a href="{{ route('admin.grading-scales.index') }}" class="btn btn-secondary">
                        Clear Filters
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Grading Scales Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Grading Scales</h6>
        </div>
        <div class="card-body">
            @if($gradingScales->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Scope</th>
                                <th>Pass Mark</th>
                                <th>Grade Ranges</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gradingScales as $gradingScale)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <div class="icon-circle bg-primary">
                                                <i class="fas fa-chart-line text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">
                                                {{ $gradingScale->name }}
                                                @if($gradingScale->is_default)
                                                    <span class="badge badge-primary ml-2">Default</span>
                                                @endif
                                            </div>
                                            <div class="text-muted small">{{ $gradingScale->description }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        @if($gradingScale->program)
                                            {{ $gradingScale->program->name }}
                                        @elseif($gradingScale->level)
                                            {{ $gradingScale->level->name }}
                                        @else
                                            Global
                                        @endif
                                    </div>
                                    <div class="text-muted small">
                                        @if($gradingScale->program)
                                            Program Level
                                        @elseif($gradingScale->level)
                                            Level Wide
                                        @else
                                            Institution Wide
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $gradingScale->pass_mark }}%</div>
                                    <div class="text-muted small">Max: {{ $gradingScale->max_marks }}</div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap">
                                        @foreach($gradingScale->gradeRanges->take(5) as $range)
                                            <span class="badge badge-secondary mr-1 mb-1">
                                                {{ $range->grade }} ({{ $range->min_percentage }}-{{ $range->max_percentage }}%)
                                            </span>
                                        @endforeach
                                        @if($gradingScale->gradeRanges->count() > 5)
                                            <span class="badge badge-light">+{{ $gradingScale->gradeRanges->count() - 5 }} more</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($gradingScale->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.grading-scales.show', $gradingScale) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.grading-scales.edit', $gradingScale) }}" 
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!$gradingScale->is_default)
                                            <form method="POST" action="{{ route('admin.grading-scales.set-default', $gradingScale) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.grading-scales.toggle-status', $gradingScale) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $gradingScale->is_active ? 'warning' : 'info' }}">
                                                <i class="fas fa-{{ $gradingScale->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $gradingScales->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-chart-line fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No grading scales found</h5>
                    <p class="text-muted">Create your first grading scale to get started.</p>
                    <a href="{{ route('admin.grading-scales.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Grading Scale
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.icon-circle {
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection

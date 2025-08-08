@extends('layouts.admin')

@section('title', 'View Grading Scale')
@section('page-title', 'Grading Scale Details')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.grading-scales.index') }}">Grading Scales</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $gradingScale->name }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $gradingScale->name }}</h1>
            <p class="mb-0 text-muted">View grading scale details and grade ranges</p>
        </div>
        <div>
            <a href="{{ route('admin.grading-scales.edit', $gradingScale) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Scale
            </a>
            <a href="{{ route('admin.grading-scales.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Grading Scale Details -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Scale Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Name:</strong>
                            <p class="mb-0">{{ $gradingScale->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <p class="mb-0">
                                @if($gradingScale->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                                @if($gradingScale->is_default)
                                    <span class="badge badge-primary ml-2">Default</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($gradingScale->description)
                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Description:</strong>
                            <p class="mb-0">{{ $gradingScale->description }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Pass Mark:</strong>
                            <p class="mb-0">{{ $gradingScale->pass_mark }}%</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Maximum Marks:</strong>
                            <p class="mb-0">{{ $gradingScale->max_marks }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Scope:</strong>
                            <p class="mb-0">
                                @if($gradingScale->program)
                                    Program: {{ $gradingScale->program->name }}
                                @elseif($gradingScale->level)
                                    Level: {{ $gradingScale->level->name }}
                                @else
                                    Institution Wide
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <strong>Created By:</strong>
                            <p class="mb-0">{{ $gradingScale->creator->name ?? 'System' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Created On:</strong>
                            <p class="mb-0">{{ $gradingScale->created_at->format('M d, Y \a\t h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grade Ranges -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Grade Ranges</h6>
                </div>
                <div class="card-body">
                    @if($gradingScale->gradeRanges->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Grade</th>
                                        <th>Minimum %</th>
                                        <th>Maximum %</th>
                                        <th>GPA</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($gradingScale->gradeRanges->sortByDesc('min_percentage') as $range)
                                    <tr>
                                        <td>
                                            <span class="badge badge-primary">{{ $range->grade }}</span>
                                        </td>
                                        <td>{{ $range->min_percentage }}%</td>
                                        <td>{{ $range->max_percentage }}%</td>
                                        <td>{{ number_format($range->gpa, 2) }}</td>
                                        <td>{{ $range->description ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">No Grade Ranges Defined</h5>
                            <p class="text-muted">Add grade ranges to complete this grading scale.</p>
                            <a href="{{ route('admin.grading-scales.edit', $gradingScale) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Grade Ranges
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions & Statistics -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.grading-scales.edit', $gradingScale) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Scale
                        </a>
                        
                        @if(!$gradingScale->is_default)
                            <form method="POST" action="{{ route('admin.grading-scales.set-default', $gradingScale) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-star"></i> Set as Default
                                </button>
                            </form>
                        @endif
                        
                        <form method="POST" action="{{ route('admin.grading-scales.toggle-status', $gradingScale) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-{{ $gradingScale->is_active ? 'warning' : 'info' }} w-100">
                                <i class="fas fa-{{ $gradingScale->is_active ? 'pause' : 'play' }}"></i> 
                                {{ $gradingScale->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        
                        <button type="button" class="btn btn-secondary" onclick="duplicateScale()">
                            <i class="fas fa-copy"></i> Duplicate Scale
                        </button>
                    </div>
                </div>
            </div>

            <!-- Usage Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Usage Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <h4 class="text-primary">{{ $gradingScale->gradeRanges->count() }}</h4>
                            <small class="text-muted">Grade Ranges</small>
                        </div>
                        
                        <div class="mb-3">
                            <h4 class="text-success">{{ $gradingScale->pass_mark }}%</h4>
                            <small class="text-muted">Pass Mark</small>
                        </div>
                        
                        <div class="mb-3">
                            <h4 class="text-info">{{ $gradingScale->max_marks }}</h4>
                            <small class="text-muted">Maximum Marks</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grade Distribution Preview -->
            @if($gradingScale->gradeRanges->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Grade Distribution</h6>
                </div>
                <div class="card-body">
                    @foreach($gradingScale->gradeRanges->sortByDesc('min_percentage') as $range)
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge badge-primary">{{ $range->grade }}</span>
                            <small class="text-muted">{{ $range->min_percentage }}-{{ $range->max_percentage }}%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="width: {{ ($range->max_percentage - $range->min_percentage) }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function duplicateScale() {
    if (confirm('Are you sure you want to duplicate this grading scale?')) {
        // Create a form to submit the duplication request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.grading-scales.store") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add duplicate flag
        const duplicateFlag = document.createElement('input');
        duplicateFlag.type = 'hidden';
        duplicateFlag.name = 'duplicate_from';
        duplicateFlag.value = '{{ $gradingScale->id }}';
        form.appendChild(duplicateFlag);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection

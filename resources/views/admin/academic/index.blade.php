@extends('layouts.admin')

@section('title', 'Academic Structure Management')
@section('page-title', 'Academic Structure Management')

@section('content')
<div class="container-fluid">
    <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')

    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-dark font-weight-bold">Academic Structure Overview</h1>
            <p class="mb-0 text-muted">A comprehensive overview of your institution's academic framework.</p>
        </div>
        <div class="d-flex align-items-center">
            <button onclick="validateStructure()" class="btn btn-info-soft mr-2">
                <i class="fas fa-check-circle mr-1"></i> Validate Structure
            </button>
            <a href="{{ route('admin.data-export.index') }}" class="btn btn-success-soft">
                <i class="fas fa-download mr-1"></i> Export Structure
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted font-weight-bold">Educational Levels</h6>
                            <h2 class="font-weight-bolder">{{ $stats['total_levels'] }}</h2>
                        </div>
                        <div class="stat-icon bg-primary-soft">
                            <i class="fas fa-layer-group text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted font-weight-bold">Faculties</h6>
                            <h2 class="font-weight-bolder">{{ $stats['total_faculties'] }}</h2>
                        </div>
                        <div class="stat-icon bg-success-soft">
                            <i class="fas fa-university text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted font-weight-bold">Departments</h6>
                            <h2 class="font-weight-bolder">{{ $stats['total_departments'] }}</h2>
                        </div>
                        <div class="stat-icon bg-info-soft">
                            <i class="fas fa-building text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted font-weight-bold">Subjects</h6>
                            <h2 class="font-weight-bolder">{{ $stats['total_subjects'] }}</h2>
                        </div>
                        <div class="stat-icon bg-warning-soft">
                            <i class="fas fa-book text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row">
        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.faculties.create') }}" class="list-group-item list-group-item-action d-flex align-items-center quick-action-item">
                            <div class="quick-action-icon bg-purple-soft">
                                <i class="fas fa-plus-circle text-purple"></i>
                            </div>
                            <div class="ml-3">
                                <div class="font-weight-bold text-dark">Add Faculty</div>
                                <small class="text-muted">Create a new faculty</small>
                            </div>
                        </a>
                        <a href="{{ route('admin.departments.create') }}" class="list-group-item list-group-item-action d-flex align-items-center quick-action-item">
                            <div class="quick-action-icon bg-info-soft">
                                <i class="fas fa-plus-circle text-info"></i>
                            </div>
                            <div class="ml-3">
                                <div class="font-weight-bold text-dark">Add Department</div>
                                <small class="text-muted">Create a new department</small>
                            </div>
                        </a>
                        <a href="{{ route('admin.subjects.create') }}" class="list-group-item list-group-item-action d-flex align-items-center quick-action-item">
                            <div class="quick-action-icon bg-success-soft">
                                <i class="fas fa-plus-circle text-success"></i>
                            </div>
                            <div class="ml-3">
                                <div class="font-weight-bold text-dark">Add Subject</div>
                                <small class="text-muted">Create a new subject</small>
                            </div>
                        </a>
                        <a href="{{ route('admin.academic-years.create') }}" class="list-group-item list-group-item-action d-flex align-items-center quick-action-item">
                            <div class="quick-action-icon bg-warning-soft">
                                <i class="fas fa-calendar-plus text-warning"></i>
                            </div>
                            <div class="ml-3">
                                <div class="font-weight-bold text-dark">Setup Academic Year</div>
                                <small class="text-muted">Configure a new year</small>
                            </div>
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="list-group-item list-group-item-action d-flex align-items-center quick-action-item">
                            <div class="quick-action-icon bg-danger-soft">
                                <i class="fas fa-chart-bar text-danger"></i>
                            </div>
                            <div class="ml-3">
                                <div class="font-weight-bold text-dark">View Reports</div>
                                <small class="text-muted">Access academic reports</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Hierarchy Overview -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Academic Hierarchy</h6>
                    <div class="btn-group" role="group">
                        <button onclick="toggleView('tree')" id="tree-view-btn" class="btn btn-sm btn-primary">Tree View</button>
                        <button onclick="toggleView('list')" id="list-view-btn" class="btn btn-sm btn-outline-primary">List View</button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Tree View -->
                    <div id="tree-view">
                        @forelse($levels as $level)
                            <div class="hierarchy-level">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="font-weight-bold text-primary mb-0">
                                        <i class="fas fa-layer-group text-primary mr-2"></i>
                                        {{ $level->name }}
                                    </h5>
                                    <span class="badge badge-primary-soft">{{ $level->classes->count() }} classes</span>
                                </div>
                                @if($level->classes->count() > 0)
                                    <div class="ml-4 mt-2">
                                        @foreach($level->classes->groupBy('department.faculty.name') as $facultyName => $facultyClasses)
                                            <div class="hierarchy-faculty">
                                                <h6 class="font-weight-bold text-success mb-1">{{ $facultyName ?: 'No Faculty' }}</h6>
                                                @foreach($facultyClasses->groupBy('department.name') as $departmentName => $departmentClasses)
                                                    <div class="hierarchy-department">
                                                        <h6 class="font-weight-bold text-info mb-1">{{ $departmentName ?: 'No Department' }}</h6>
                                                        <div class="ml-3">
                                                            @foreach($departmentClasses as $class)
                                                                <span class="badge badge-secondary-soft mr-1 mb-1">{{ $class->name }}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted ml-4 mt-2 mb-0">No classes defined for this level.</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-center text-muted">No educational levels found.</p>
                        @endforelse
                    </div>

                    <!-- List View (Hidden by default) -->
                    <div id="list-view" style="display: none;">
                        @forelse($faculties as $faculty)
                            <div class="list-view-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="font-weight-bold mb-0 text-dark">{{ $faculty->name }}</h5>
                                        <small class="text-muted">{{ $faculty->code }}</small>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-weight-bold text-dark">{{ $faculty->departments_count }} Departments</div>
                                        <small class="text-muted">{{ $faculty->programs_count }} Programs</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted">No faculties found.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* General Styling */
    .font-weight-bolder { font-weight: 900 !important; }
    .btn-info-soft { background-color: #e6f7ff; border-color: #91d5ff; color: #1890ff; }
    .btn-success-soft { background-color: #f6ffed; border-color: #b7eb8f; color: #52c41a; }
    .badge-primary-soft { background-color: #e6f7ff; color: #1890ff; }
    .badge-secondary-soft { background-color: #f0f2f5; color: #595959; }
    .bg-purple-soft { background-color: #f9f0ff; }
    .text-purple { color: #722ed1; }
    .bg-danger-soft { background-color: #fff1f0; }

    /* Statistic Cards */
    .stat-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    }
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .bg-primary-soft { background-color: rgba(0, 123, 255, 0.1); }
    .bg-success-soft { background-color: rgba(40, 167, 69, 0.1); }
    .bg-info-soft { background-color: rgba(23, 162, 184, 0.1); }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }

    /* Quick Actions */
    .quick-action-item {
        border-radius: 8px;
        transition: background-color 0.2s ease;
    }
    .quick-action-item:hover {
        background-color: #f8f9fa;
    }
    .quick-action-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Academic Hierarchy */
    .hierarchy-level {
        background-color: #fff;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 1.25rem;
        margin-bottom: 1rem;
    }
    .hierarchy-faculty {
        border-left: 3px solid #28a745;
        padding-left: 1rem;
        margin-top: 1rem;
        margin-bottom: 1rem;
    }
    .hierarchy-department {
        border-left: 3px solid #17a2b8;
        padding-left: 1rem;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }

    /* List View */
    .list-view-item {
        background-color: #fff;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: box-shadow 0.2s ease;
    }
    .list-view-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
</style>
@endpush

@push('scripts')
<script>
function toggleView(viewType) {
    const treeView = document.getElementById('tree-view');
    const listView = document.getElementById('list-view');
    const treeBtn = document.getElementById('tree-view-btn');
    const listBtn = document.getElementById('list-view-btn');

    if (viewType === 'tree') {
        treeView.style.display = 'block';
        listView.style.display = 'none';
        treeBtn.classList.remove('btn-outline-primary');
        treeBtn.classList.add('btn-primary');
        listBtn.classList.remove('btn-primary');
        listBtn.classList.add('btn-outline-primary');
    } else {
        treeView.style.display = 'none';
        listView.style.display = 'block';
        listBtn.classList.remove('btn-outline-primary');
        listBtn.classList.add('btn-primary');
        treeBtn.classList.remove('btn-primary');
        treeBtn.classList.add('btn-outline-primary');
    }
}

async function validateStructure() {
    try {
        const response = await fetch('{{ route("admin.academic.validate") }}');
        const data = await response.json();

        if (data.valid) {
            // Using a more modern notification library like SweetAlert would be better
            alert('Academic structure is valid! No issues found.');
        } else {
            let message = `Found ${data.total_issues} issue(s):\n\n`;
            data.issues.forEach(issue => {
                message += `• ${issue}\n`;
            });
            alert(message);
        }
    } catch (error) {
        console.error('Validation Error:', error);
        alert('Error validating structure. Please check the console for details.');
    }
}
</script>
@endpush
@endsection

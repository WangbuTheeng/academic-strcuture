@extends('layouts.admin')

@section('title', 'Academic Structure Reports')
@section('page-title', 'Academic Structure Reports')

@section('content')
<div class="container-fluid">
    <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')
    
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Academic Structure Reports</h1>
            <p class="mb-0 text-muted">Comprehensive reports and analytics for academic structure</p>
        </div>
        <div>
            <button class="btn btn-success" onclick="exportReport()">
                <i class="fas fa-download"></i> Export Report
            </button>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Faculties</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_faculties'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-university fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Departments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_departments'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Classes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_classes'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Subjects</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_subjects'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Faculty Statistics -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Faculty Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Faculty</th>
                                    <th>Departments</th>
                                    <th>Programs</th>
                                    <th>Classes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($facultyStats as $faculty)
                                <tr>
                                    <td class="fw-bold">{{ $faculty->name }}</td>
                                    <td><span class="badge bg-primary">{{ $faculty->departments_count }}</span></td>
                                    <td><span class="badge bg-success">{{ $faculty->programs_count }}</span></td>
                                    <td><span class="badge bg-info">{{ $faculty->classes_count }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No faculties found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Statistics -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Department Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th>Programs</th>
                                    <th>Classes</th>
                                    <th>Subjects</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departmentStats->take(10) as $department)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $department->name }}</div>
                                        <small class="text-muted">{{ $department->faculty->name ?? 'No Faculty' }}</small>
                                    </td>
                                    <td><span class="badge bg-primary">{{ $department->programs_count }}</span></td>
                                    <td><span class="badge bg-success">{{ $department->classes_count }}</span></td>
                                    <td><span class="badge bg-info">{{ $department->subjects_count }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No departments found</td>
                                </tr>
                                @endforelse
                                @if($departmentStats->count() > 10)
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        <small>Showing top 10 departments. Total: {{ $departmentStats->count() }}</small>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Level Statistics -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Educational Level Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($levelStats as $level)
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-info">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-layer-group fa-2x text-info"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $level->name }}</div>
                                            <small class="text-muted">{{ $level->classes_count }} classes</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Academic Year Info -->
    @if($stats['current_academic_year'])
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-calendar-alt"></i> Current Academic Year
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Academic Year:</td>
                                    <td>{{ $stats['current_academic_year']->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Start Date:</td>
                                    <td>{{ $stats['current_academic_year']->start_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">End Date:</td>
                                    <td>{{ $stats['current_academic_year']->end_date->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        @if($stats['current_academic_year']->is_current)
                                            <span class="badge bg-success">Current</span>
                                        @else
                                            <span class="badge bg-warning">Not Current</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Active Semesters:</td>
                                    <td><span class="badge bg-info">{{ $stats['active_semesters'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Total Programs:</td>
                                    <td><span class="badge bg-primary">{{ $stats['total_programs'] }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function exportReport() {
        // This would implement report export functionality
        alert('Export functionality would be implemented here');
    }
</script>
@endpush

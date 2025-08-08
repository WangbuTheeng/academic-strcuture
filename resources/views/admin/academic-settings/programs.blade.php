@extends('layouts.admin')

@section('title', 'Academic Programs')
@section('page-title', 'Academic Programs')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Academic Programs</h1>
            <p class="mb-0 text-muted">Manage academic programs and specializations</p>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProgramModal">
                <i class="fas fa-plus"></i> Add Program
            </button>
        </div>
    </div>

    <!-- Programs Grid -->
    <div class="row">
        <!-- Example programs -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0">Science Stream</h6>
                </div>
                <div class="card-body">
                    <p class="card-text">Physics, Chemistry, Mathematics, Biology</p>
                    <div class="mb-2">
                        <small class="text-muted">Duration: 2 years</small>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Students: 150</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-outline-primary">Edit</button>
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0">Commerce Stream</h6>
                </div>
                <div class="card-body">
                    <p class="card-text">Accounting, Economics, Business Studies</p>
                    <div class="mb-2">
                        <small class="text-muted">Duration: 2 years</small>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Students: 120</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-outline-primary">Edit</button>
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0">Arts Stream</h6>
                </div>
                <div class="card-body">
                    <p class="card-text">History, Geography, Political Science, Literature</p>
                    <div class="mb-2">
                        <small class="text-muted">Duration: 2 years</small>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Students: 80</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-outline-primary">Edit</button>
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Coming Soon Message -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-graduation-cap fa-3x text-gray-300 mb-3"></i>
                    <h4 class="text-gray-600">Academic Programs Management</h4>
                    <p class="text-muted mb-4">
                        This feature is under development. You'll be able to create and manage academic programs, 
                        define subject combinations, set program requirements, and track student enrollments.
                    </p>
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="feature-preview p-3 border rounded">
                                        <i class="fas fa-plus-circle text-primary mb-2"></i>
                                        <h6>Create Programs</h6>
                                        <small class="text-muted">Add new programs</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="feature-preview p-3 border rounded">
                                        <i class="fas fa-book text-success mb-2"></i>
                                        <h6>Subject Mapping</h6>
                                        <small class="text-muted">Map subjects to programs</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="feature-preview p-3 border rounded">
                                        <i class="fas fa-users text-info mb-2"></i>
                                        <h6>Student Enrollment</h6>
                                        <small class="text-muted">Track enrollments</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="feature-preview p-3 border rounded">
                                        <i class="fas fa-chart-bar text-warning mb-2"></i>
                                        <h6>Program Analytics</h6>
                                        <small class="text-muted">Performance insights</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Program Modal -->
<div class="modal fade" id="addProgramModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Academic Program</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="programName" class="form-label">Program Name</label>
                                <input type="text" class="form-control" id="programName" placeholder="e.g., Science Stream">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="programCode" class="form-label">Program Code</label>
                                <input type="text" class="form-control" id="programCode" placeholder="SCI">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="programDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="programDescription" rows="3" placeholder="Brief description of this program"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="duration" class="form-label">Duration (Years)</label>
                                <input type="number" class="form-control" id="duration" placeholder="2">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="capacity" class="form-label">Student Capacity</label>
                                <input type="number" class="form-control" id="capacity" placeholder="100">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="subjects" class="form-label">Core Subjects</label>
                        <textarea class="form-control" id="subjects" rows="2" placeholder="List core subjects for this program"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="isActive">
                                <label class="form-check-label" for="isActive">
                                    Active Program
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="allowEnrollment">
                                <label class="form-check-label" for="allowEnrollment">
                                    Allow New Enrollments
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save Program</button>
            </div>
        </div>
    </div>
</div>

<style>
.feature-preview {
    text-align: center;
    transition: all 0.3s ease;
}

.feature-preview:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.feature-preview i {
    font-size: 1.5rem;
    display: block;
}
</style>
@endsection

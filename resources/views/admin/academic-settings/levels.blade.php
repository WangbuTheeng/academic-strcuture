@extends('layouts.admin')

@section('title', 'Academic Levels')
@section('page-title', 'Academic Levels')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Academic Levels</h1>
            <p class="mb-0 text-muted">Manage academic levels and class structures</p>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLevelModal">
                <i class="fas fa-plus"></i> Add Level
            </button>
        </div>
    </div>

    <!-- Academic Levels Grid -->
    <div class="row">
        <!-- Example levels - these would come from database -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0">Primary School</h6>
                </div>
                <div class="card-body">
                    <p class="card-text">Classes 1-5</p>
                    <div class="mb-2">
                        <small class="text-muted">Age Range: 6-11 years</small>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Total Classes: 5</small>
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
                    <h6 class="m-0">Middle School</h6>
                </div>
                <div class="card-body">
                    <p class="card-text">Classes 6-8</p>
                    <div class="mb-2">
                        <small class="text-muted">Age Range: 12-14 years</small>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Total Classes: 3</small>
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
                    <h6 class="m-0">High School</h6>
                </div>
                <div class="card-body">
                    <p class="card-text">Classes 9-12</p>
                    <div class="mb-2">
                        <small class="text-muted">Age Range: 15-18 years</small>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Total Classes: 4</small>
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
                    <i class="fas fa-layer-group fa-3x text-gray-300 mb-3"></i>
                    <h4 class="text-gray-600">Academic Levels Management</h4>
                    <p class="text-muted mb-4">
                        This feature is under development. You'll be able to create and manage academic levels, 
                        define class structures, set age ranges, and organize your institution's academic hierarchy.
                    </p>
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="feature-preview p-3 border rounded">
                                        <i class="fas fa-plus-circle text-primary mb-2"></i>
                                        <h6>Create Levels</h6>
                                        <small class="text-muted">Add new academic levels</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="feature-preview p-3 border rounded">
                                        <i class="fas fa-edit text-success mb-2"></i>
                                        <h6>Manage Classes</h6>
                                        <small class="text-muted">Organize class structures</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="feature-preview p-3 border rounded">
                                        <i class="fas fa-users text-info mb-2"></i>
                                        <h6>Student Assignment</h6>
                                        <small class="text-muted">Assign students to levels</small>
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

<!-- Add Level Modal -->
<div class="modal fade" id="addLevelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Academic Level</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="levelName" class="form-label">Level Name</label>
                        <input type="text" class="form-control" id="levelName" placeholder="e.g., Primary School">
                    </div>
                    <div class="mb-3">
                        <label for="levelDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="levelDescription" rows="3" placeholder="Brief description of this level"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="startClass" class="form-label">Start Class</label>
                                <input type="number" class="form-control" id="startClass" placeholder="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="endClass" class="form-label">End Class</label>
                                <input type="number" class="form-control" id="endClass" placeholder="5">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="minAge" class="form-label">Minimum Age</label>
                                <input type="number" class="form-control" id="minAge" placeholder="6">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="maxAge" class="form-label">Maximum Age</label>
                                <input type="number" class="form-control" id="maxAge" placeholder="11">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save Level</button>
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

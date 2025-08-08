@extends('layouts.admin')

@section('title', 'Subjects Management')
@section('page-title', 'Subjects Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Subjects Management</h1>
            <p class="mb-0 text-muted">Manage subjects, categories, and curriculum</p>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                <i class="fas fa-plus"></i> Add Subject
            </button>
        </div>
    </div>

    <!-- Subject Categories -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Subject Categories</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="category-card p-3 border rounded text-center">
                                <i class="fas fa-flask text-primary fa-2x mb-2"></i>
                                <h6>Science</h6>
                                <small class="text-muted">12 subjects</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="category-card p-3 border rounded text-center">
                                <i class="fas fa-calculator text-success fa-2x mb-2"></i>
                                <h6>Mathematics</h6>
                                <small class="text-muted">5 subjects</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="category-card p-3 border rounded text-center">
                                <i class="fas fa-language text-info fa-2x mb-2"></i>
                                <h6>Languages</h6>
                                <small class="text-muted">8 subjects</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="category-card p-3 border rounded text-center">
                                <i class="fas fa-globe text-warning fa-2x mb-2"></i>
                                <h6>Social Studies</h6>
                                <small class="text-muted">6 subjects</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subjects List -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">All Subjects</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Subject Name</th>
                                    <th>Code</th>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <th>Credits</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Example subjects -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-flask text-primary me-2"></i>
                                            <strong>Physics</strong>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-secondary">PHY</span></td>
                                    <td>Science</td>
                                    <td>Core</td>
                                    <td>4</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Edit</button>
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-atom text-success me-2"></i>
                                            <strong>Chemistry</strong>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-secondary">CHE</span></td>
                                    <td>Science</td>
                                    <td>Core</td>
                                    <td>4</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Edit</button>
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calculator text-info me-2"></i>
                                            <strong>Mathematics</strong>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-secondary">MAT</span></td>
                                    <td>Mathematics</td>
                                    <td>Core</td>
                                    <td>5</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Edit</button>
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-book text-warning me-2"></i>
                                            <strong>English Literature</strong>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-secondary">ENG</span></td>
                                    <td>Languages</td>
                                    <td>Core</td>
                                    <td>3</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">Edit</button>
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Coming Soon Message -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-book fa-3x text-gray-300 mb-3"></i>
                    <h4 class="text-gray-600">Subjects Management System</h4>
                    <p class="text-muted mb-4">
                        This feature is under development. You'll be able to create and manage subjects, 
                        organize them into categories, set credit hours, and define curriculum requirements.
                    </p>
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <div class="feature-preview p-3 border rounded">
                                        <i class="fas fa-plus-circle text-primary mb-2"></i>
                                        <h6>Add Subjects</h6>
                                        <small class="text-muted">Create new subjects</small>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <div class="feature-preview p-3 border rounded">
                                        <i class="fas fa-tags text-success mb-2"></i>
                                        <h6>Categories</h6>
                                        <small class="text-muted">Organize by category</small>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <div class="feature-preview p-3 border rounded">
                                        <i class="fas fa-star text-warning mb-2"></i>
                                        <h6>Credit Hours</h6>
                                        <small class="text-muted">Set credit values</small>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <div class="feature-preview p-3 border rounded">
                                        <i class="fas fa-link text-info mb-2"></i>
                                        <h6>Prerequisites</h6>
                                        <small class="text-muted">Define dependencies</small>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <div class="feature-preview p-3 border rounded">
                                        <i class="fas fa-users text-purple mb-2"></i>
                                        <h6>Teacher Assignment</h6>
                                        <small class="text-muted">Assign teachers</small>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <div class="feature-preview p-3 border rounded">
                                        <i class="fas fa-chart-line text-danger mb-2"></i>
                                        <h6>Analytics</h6>
                                        <small class="text-muted">Performance tracking</small>
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

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="subjectName" class="form-label">Subject Name</label>
                                <input type="text" class="form-control" id="subjectName" placeholder="e.g., Physics">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="subjectCode" class="form-label">Subject Code</label>
                                <input type="text" class="form-control" id="subjectCode" placeholder="PHY">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-control" id="category">
                                    <option value="">Select Category</option>
                                    <option value="science">Science</option>
                                    <option value="mathematics">Mathematics</option>
                                    <option value="languages">Languages</option>
                                    <option value="social">Social Studies</option>
                                    <option value="arts">Arts</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="subjectType" class="form-label">Subject Type</label>
                                <select class="form-control" id="subjectType">
                                    <option value="core">Core</option>
                                    <option value="elective">Elective</option>
                                    <option value="optional">Optional</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="credits" class="form-label">Credit Hours</label>
                                <input type="number" class="form-control" id="credits" placeholder="3">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="theoryHours" class="form-label">Theory Hours/Week</label>
                                <input type="number" class="form-control" id="theoryHours" placeholder="4">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="practicalHours" class="form-label">Practical Hours/Week</label>
                                <input type="number" class="form-control" id="practicalHours" placeholder="2">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" rows="3" placeholder="Brief description of the subject"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="hasTheory" checked>
                                <label class="form-check-label" for="hasTheory">
                                    Has Theory Component
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="hasPractical">
                                <label class="form-check-label" for="hasPractical">
                                    Has Practical Component
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save Subject</button>
            </div>
        </div>
    </div>
</div>

<style>
.category-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.category-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

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

.text-purple {
    color: #6f42c1 !important;
}
</style>
@endsection

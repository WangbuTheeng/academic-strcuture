@extends('layouts.admin')

@section('title', 'Marksheet Customization')
@section('page-title', 'Marksheet Customization')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Marksheet Customization</h1>
            <p class="mb-0 text-muted">Design and customize marksheet templates for your institution</p>
        </div>
        <div>
            <a href="{{ route('admin.marksheets.customize.advanced-editor') }}" class="btn btn-success me-2">
                <i class="fas fa-magic"></i> Advanced Editor
            </a>
            <a href="{{ route('admin.marksheets.customize.drag-drop-builder') }}" class="btn btn-warning me-2">
                <i class="fas fa-mouse-pointer"></i> Drag & Drop Builder
            </a>
            <a href="{{ route('admin.marksheets.customize.column-reorder') }}" class="btn btn-info me-2">
                <i class="fas fa-arrows-alt"></i> Column Reorder
            </a>
            <a href="{{ route('admin.marksheets.customize.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Template
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Templates</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $templates->count() }}</div>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Templates</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $templates->where('is_active', true)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Grading Scales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $gradingScales->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Default Template</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $templates->where('is_default', true)->first()?->name ?? 'None' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Showcase -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Template Design Tools</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Advanced Editor -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="feature-card h-100 p-4 border rounded">
                                <div class="text-center mb-3">
                                    <div class="feature-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-magic fa-2x"></i>
                                    </div>
                                </div>
                                <h5 class="text-center mb-3">Advanced Editor</h5>
                                <p class="text-muted text-center mb-3">Professional-grade template editor with canvas-based design, element properties, and real-time preview.</p>
                                <ul class="list-unstyled small text-muted mb-3">
                                    <li><i class="fas fa-check text-success me-2"></i>Visual drag-and-drop interface</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Element property inspector</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Layer management</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Advanced table editor</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Zoom and grid controls</li>
                                </ul>
                                <div class="text-center">
                                    <a href="{{ route('admin.marksheets.customize.advanced-editor') }}" class="btn btn-success">
                                        <i class="fas fa-magic"></i> Launch Editor
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Drag & Drop Builder -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="feature-card h-100 p-4 border rounded">
                                <div class="text-center mb-3">
                                    <div class="feature-icon bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-mouse-pointer fa-2x"></i>
                                    </div>
                                </div>
                                <h5 class="text-center mb-3">Drag & Drop Builder</h5>
                                <p class="text-muted text-center mb-3">Intuitive template builder with section-based design and easy customization options.</p>
                                <ul class="list-unstyled small text-muted mb-3">
                                    <li><i class="fas fa-check text-success me-2"></i>Section-based design</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Element library</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Table configuration</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Template settings</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Export/import templates</li>
                                </ul>
                                <div class="text-center">
                                    <a href="{{ route('admin.marksheets.customize.drag-drop-builder') }}" class="btn btn-warning">
                                        <i class="fas fa-mouse-pointer"></i> Open Builder
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Column Reorder -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="feature-card h-100 p-4 border rounded">
                                <div class="text-center mb-3">
                                    <div class="feature-icon bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-arrows-alt fa-2x"></i>
                                    </div>
                                </div>
                                <h5 class="text-center mb-3">Column Reorder</h5>
                                <p class="text-muted text-center mb-3">Simple drag-and-drop interface for reordering table columns and customizing layout.</p>
                                <ul class="list-unstyled small text-muted mb-3">
                                    <li><i class="fas fa-check text-success me-2"></i>Quick column reordering</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Live preview</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Column visibility toggle</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Simple interface</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Instant updates</li>
                                </ul>
                                <div class="text-center">
                                    <a href="{{ route('admin.marksheets.customize.column-reorder') }}" class="btn btn-info">
                                        <i class="fas fa-arrows-alt"></i> Reorder Columns
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="row">
        @forelse($templates as $template)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow h-100 {{ $template->is_default ? 'border-warning' : '' }}">
                    @if($template->is_default)
                        <div class="card-header bg-warning text-white py-2">
                            <i class="fas fa-star me-1"></i>Default Template
                        </div>
                    @endif
                    
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">{{ $template->name }}</h5>
                            <div class="d-flex flex-column align-items-end">
                                <span class="badge bg-{{ $template->is_active ? 'success' : 'secondary' }} mb-1">
                                    {{ $template->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @if($template->is_global)
                                    <span class="badge bg-info">
                                        <i class="fas fa-globe"></i> Global
                                    </span>
                                @else
                                    <span class="badge bg-primary">
                                        <i class="fas fa-school"></i> School
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <p class="card-text text-muted small">{{ $template->description ?? 'No description available' }}</p>
                        
                        <div class="mb-3">
                            <small class="text-muted">
                                <strong>Type:</strong> {{ $template->getTypeLabel() }}<br>
                                <strong>Grading Scale:</strong> {{ $template->gradingScale->name ?? 'N/A' }}<br>
                                <strong>Created:</strong> {{ $template->created_at->format('M d, Y') }}
                            </small>
                        </div>
                        
                        <!-- Template Preview Thumbnail -->
                        <div class="template-preview mb-3" style="height: 120px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; position: relative; overflow: hidden;">
                            <div class="preview-content p-2" style="font-size: 8px; line-height: 1.2;">
                                <div style="background: {{ $template->getSetting('header_color', '#2563eb') }}; color: white; padding: 2px; text-align: center; margin-bottom: 4px;">
                                    <strong>{{ ($instituteSettings && isset($instituteSettings->institution_name)) ? $instituteSettings->institution_name : 'School Name' }}</strong>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2px; margin-bottom: 4px;">
                                    <div><strong>Student:</strong> John Doe</div>
                                    <div><strong>Roll:</strong> 001</div>
                                    <div><strong>Class:</strong> 10</div>
                                    <div><strong>Exam:</strong> Final</div>
                                </div>
                                <table style="width: 100%; border-collapse: collapse; font-size: 6px;">
                                    <tr style="background: {{ $template->getSetting('header_color', '#2563eb') }}; color: white;">
                                        <th style="border: 1px solid #ddd; padding: 1px;">Subject</th>
                                        <th style="border: 1px solid #ddd; padding: 1px;">Marks</th>
                                        <th style="border: 1px solid #ddd; padding: 1px;">Grade</th>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd; padding: 1px;">Math</td>
                                        <td style="border: 1px solid #ddd; padding: 1px;">85</td>
                                        <td style="border: 1px solid #ddd; padding: 1px;">A</td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd; padding: 1px;">Science</td>
                                        <td style="border: 1px solid #ddd; padding: 1px;">78</td>
                                        <td style="border: 1px solid #ddd; padding: 1px;">B+</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('admin.marksheets.customize.preview', $template) }}" 
                               class="btn btn-outline-primary btn-sm" target="_blank">
                                <i class="fas fa-eye"></i> Preview
                            </a>
                            <a href="{{ route('admin.marksheets.customize.edit', $template) }}" 
                               class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-info btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    @if(!$template->is_default)
                                        <li>
                                            <form method="POST" action="{{ route('admin.marksheets.customize.set-default', $template) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fas fa-star me-1"></i>Set as Default
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                    <li>
                                        <form method="POST" action="{{ route('admin.marksheets.customize.duplicate', $template) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-copy me-1"></i>Duplicate
                                            </button>
                                        </form>
                                    </li>
                                    @if(!$template->is_default)
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.marksheets.customize.destroy', $template) }}" 
                                                  class="d-inline" onsubmit="return confirm('Are you sure you want to delete this template?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-file-alt fa-3x text-gray-300 mb-4"></i>
                        <h5 class="text-gray-800 mb-3">No Templates Found</h5>
                        <p class="text-muted mb-4">You haven't created any marksheet templates yet.</p>
                        <a href="{{ route('admin.marksheets.customize.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Create Your First Template
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Quick Start Guide -->
    <div class="card shadow mt-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info">Quick Start Guide</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center mb-3">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                        <i class="fas fa-plus text-white"></i>
                    </div>
                    <h6>1. Create Template</h6>
                    <p class="small text-muted">Start by creating a new marksheet template with your preferred design.</p>
                </div>
                <div class="col-md-3 text-center mb-3">
                    <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                        <i class="fas fa-palette text-white"></i>
                    </div>
                    <h6>2. Customize Design</h6>
                    <p class="small text-muted">Customize colors, fonts, layout, and add your institution's branding.</p>
                </div>
                <div class="col-md-3 text-center mb-3">
                    <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                        <i class="fas fa-eye text-white"></i>
                    </div>
                    <h6>3. Preview & Test</h6>
                    <p class="small text-muted">Preview your template with sample data to see how it will look.</p>
                </div>
                <div class="col-md-3 text-center mb-3">
                    <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                        <i class="fas fa-star text-white"></i>
                    </div>
                    <h6>4. Set as Default</h6>
                    <p class="small text-muted">Set your template as default to use it for generating marksheets.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Ensure text visibility */
.text-gray-800, h1, h2, h3, h4, h5, h6 {
    color: #2d3748 !important;
}

.text-muted {
    color: #6c757d !important;
}

.text-primary {
    color: #4e73df !important;
}

.text-success {
    color: #1cc88a !important;
}

.text-info {
    color: #36b9cc !important;
}

.text-warning {
    color: #f6c23e !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

/* Ensure all text in cards is visible */
.card-body, .card-header, .card-footer {
    color: #2d3748 !important;
}

.template-preview {
    transition: transform 0.2s ease;
}

.template-preview:hover {
    transform: scale(1.02);
}

.card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
}

.btn-group .dropdown-menu {
    min-width: 150px;
    z-index: 1050 !important;
}

/* Fix dropdown overlap issue */
.card {
    position: relative;
    z-index: 1;
}

.btn-group {
    position: relative;
    z-index: 10;
}

.dropdown-menu {
    z-index: 1050 !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection

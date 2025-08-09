@extends('layouts.admin')

@section('title', 'Create Marksheet Template')
@section('page-title', 'Create Marksheet Template')

@section('content') 
@include('admin.reports.partials.sub-navbar')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Create Marksheet Template</h1>
            <p class="mb-0 text-muted">Design a custom marksheet template for your institution</p>
        </div>
        <div>
            <a href="{{ route('admin.marksheets.customize.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Templates
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Template Form -->
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.marksheets.customize.store') }}" id="templateForm">
                @csrf
                
                <!-- Basic Information -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Template Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="template_type" class="form-label">Template Type <span class="text-danger">*</span></label>
                                <select class="form-control @error('template_type') is-invalid @enderror" 
                                        id="template_type" name="template_type" required onchange="loadTemplateDefaults()">
                                    <option value="">Select Template Type</option>
                                    <option value="modern" {{ old('template_type') == 'modern' ? 'selected' : '' }}>Modern Design</option>
                                    <option value="classic" {{ old('template_type') == 'classic' ? 'selected' : '' }}>Classic Design</option>
                                    <option value="minimal" {{ old('template_type') == 'minimal' ? 'selected' : '' }}>Minimal Design</option>
                                    <option value="custom" {{ old('template_type') == 'custom' ? 'selected' : '' }}>Custom Design</option>
                                </select>
                                @error('template_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="grading_scale_id" class="form-label">Grading Scale <span class="text-danger">*</span></label>
                                <select class="form-control @error('grading_scale_id') is-invalid @enderror" 
                                        id="grading_scale_id" name="grading_scale_id" required>
                                    <option value="">Select Grading Scale</option>
                                    @foreach($gradingScales as $scale)
                                        <option value="{{ $scale->id }}" {{ old('grading_scale_id') == $scale->id ? 'selected' : '' }}>
                                            {{ $scale->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('grading_scale_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="is_default" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        Set as Default Template
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" placeholder="Brief description of this template">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Design Settings -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Design Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="header_color" class="form-label">Header Color</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="header_color" name="settings[header_color]" value="{{ old('settings.header_color', '#2563eb') }}" onchange="updatePreview()">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="text_color" class="form-label">Text Color</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="text_color" name="settings[text_color]" value="{{ old('settings.text_color', '#1f2937') }}" onchange="updatePreview()">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="font_family" class="form-label">Font Family</label>
                                <select class="form-control" id="font_family" name="settings[font_family]" onchange="updatePreview()">
                                    <option value="Arial" {{ old('settings.font_family') == 'Arial' ? 'selected' : '' }}>Arial</option>
                                    <option value="Times" {{ old('settings.font_family') == 'Times' ? 'selected' : '' }}>Times New Roman</option>
                                    <option value="Helvetica" {{ old('settings.font_family') == 'Helvetica' ? 'selected' : '' }}>Helvetica</option>
                                    <option value="Georgia" {{ old('settings.font_family') == 'Georgia' ? 'selected' : '' }}>Georgia</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="font_size" class="form-label">Font Size</label>
                                <select class="form-control" id="font_size" name="settings[font_size]" onchange="updatePreview()">
                                    @for($i = 8; $i <= 16; $i++)
                                        <option value="{{ $i }}" {{ old('settings.font_size', 12) == $i ? 'selected' : '' }}>{{ $i }}px</option>
                                    @endfor
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="border_style" class="form-label">Border Style</label>
                                <select class="form-control" id="border_style" name="settings[border_style]" onchange="updatePreview()">
                                    <option value="solid" {{ old('settings.border_style') == 'solid' ? 'selected' : '' }}>Solid</option>
                                    <option value="dashed" {{ old('settings.border_style') == 'dashed' ? 'selected' : '' }}>Dashed</option>
                                    <option value="dotted" {{ old('settings.border_style') == 'dotted' ? 'selected' : '' }}>Dotted</option>
                                    <option value="none" {{ old('settings.border_style') == 'none' ? 'selected' : '' }}>None</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- School Information Settings -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">School Information Display</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-gray-800 mb-3">Header Elements</h6>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="show_school_logo" name="settings[show_school_logo]" value="1" {{ old('settings.show_school_logo', true) ? 'checked' : '' }} onchange="updatePreview()">
                                    <label class="form-check-label" for="show_school_logo">Show School Logo</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="show_school_name" name="settings[show_school_name]" value="1" {{ old('settings.show_school_name', true) ? 'checked' : '' }} onchange="updatePreview()">
                                    <label class="form-check-label" for="show_school_name">Show School Name</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="show_school_address" name="settings[show_school_address]" value="1" {{ old('settings.show_school_address', true) ? 'checked' : '' }} onchange="updatePreview()">
                                    <label class="form-check-label" for="show_school_address">Show School Address</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="show_contact_info" name="settings[show_contact_info]" value="1" {{ old('settings.show_contact_info', true) ? 'checked' : '' }} onchange="updatePreview()">
                                    <label class="form-check-label" for="show_contact_info">Show Contact Information</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-gray-800 mb-3">Logo & Positioning</h6>
                                <div class="mb-3">
                                    <label for="logo_position" class="form-label">Logo Position</label>
                                    <select class="form-control" id="logo_position" name="settings[logo_position]" onchange="updatePreview()">
                                        <option value="left" {{ old('settings.logo_position', 'left') == 'left' ? 'selected' : '' }}>Left</option>
                                        <option value="center" {{ old('settings.logo_position', 'left') == 'center' ? 'selected' : '' }}>Center</option>
                                        <option value="right" {{ old('settings.logo_position', 'left') == 'right' ? 'selected' : '' }}>Right</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="logo_size" class="form-label">Logo Size</label>
                                    <select class="form-control" id="logo_size" name="settings[logo_size]" onchange="updatePreview()">
                                        <option value="small" {{ old('settings.logo_size', 'medium') == 'small' ? 'selected' : '' }}>Small (60px)</option>
                                        <option value="medium" {{ old('settings.logo_size', 'medium') == 'medium' ? 'selected' : '' }}>Medium (80px)</option>
                                        <option value="large" {{ old('settings.logo_size', 'medium') == 'large' ? 'selected' : '' }}>Large (100px)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-gray-800 mb-3">School Name Styling</h6>
                                <div class="mb-3">
                                    <label for="school_name_size" class="form-label">School Name Size</label>
                                    <select class="form-control" id="school_name_size" name="settings[school_name_size]" onchange="updatePreview()">
                                        <option value="18" {{ old('settings.school_name_size', '24') == '18' ? 'selected' : '' }}>Small (18px)</option>
                                        <option value="24" {{ old('settings.school_name_size', '24') == '24' ? 'selected' : '' }}>Medium (24px)</option>
                                        <option value="30" {{ old('settings.school_name_size', '24') == '30' ? 'selected' : '' }}>Large (30px)</option>
                                        <option value="36" {{ old('settings.school_name_size', '24') == '36' ? 'selected' : '' }}>Extra Large (36px)</option>
                                    </select>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="school_name_bold" name="settings[school_name_bold]" value="1" {{ old('settings.school_name_bold', true) ? 'checked' : '' }} onchange="updatePreview()">
                                    <label class="form-check-label" for="school_name_bold">Bold School Name</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-gray-800 mb-3">Principal Information</h6>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="show_principal_name" name="settings[show_principal_name]" value="1" {{ old('settings.show_principal_name', true) ? 'checked' : '' }} onchange="updatePreview()">
                                    <label class="form-check-label" for="show_principal_name">Show Principal Name</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="show_principal_signature" name="settings[show_principal_signature]" value="1" {{ old('settings.show_principal_signature', false) ? 'checked' : '' }} onchange="updatePreview()">
                                    <label class="form-check-label" for="show_principal_signature">Show Principal Signature</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Settings -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Marks Display Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-gray-800 mb-3">Marks Breakdown</h6>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="show_theory_practical" name="settings[show_theory_practical]" value="1" {{ old('settings.show_theory_practical', true) ? 'checked' : '' }} onchange="updatePreview()">
                                    <label class="form-check-label" for="show_theory_practical">Show Theory/Practical Breakdown</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="show_assessment_marks" name="settings[show_assessment_marks]" value="1" {{ old('settings.show_assessment_marks', true) ? 'checked' : '' }} onchange="updatePreview()">
                                    <label class="form-check-label" for="show_assessment_marks">Show Assessment Marks</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="show_grade_points" name="settings[show_grade_points]" value="1" {{ old('settings.show_grade_points', true) ? 'checked' : '' }} onchange="updatePreview()">
                                    <label class="form-check-label" for="show_grade_points">Show Grade Points</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-gray-800 mb-3">Additional Information</h6>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="show_remarks" name="settings[show_remarks]" value="1" {{ old('settings.show_remarks', true) ? 'checked' : '' }} onchange="updatePreview()">
                                    <label class="form-check-label" for="show_remarks">Show Remarks</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="show_attendance" name="settings[show_attendance]" value="1" {{ old('settings.show_attendance', false) ? 'checked' : '' }} onchange="updatePreview()">
                                    <label class="form-check-label" for="show_attendance">Show Attendance</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="show_rank" name="settings[show_rank]" value="1" {{ old('settings.show_rank', false) ? 'checked' : '' }} onchange="updatePreview()">
                                    <label class="form-check-label" for="show_rank">Show Class Rank</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Smart Table Configuration -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-table"></i> Smart Table Configuration
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-lightbulb"></i>
                            <strong>Smart Detection:</strong> The marksheet will automatically show/hide columns based on your exam configuration.
                            For example, if an exam doesn't have practical marks, the practical column won't appear.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-gray-800 mb-3">Column Headers</h6>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="show_marks_in_headers" name="settings[show_marks_in_headers]" value="1" {{ old('settings.show_marks_in_headers', true) ? 'checked' : '' }} onchange="updatePreview()">
                                    <label class="form-check-label" for="show_marks_in_headers">Show Max Marks in Headers</label>
                                    <small class="form-text text-muted">e.g., "Theory(60)" instead of just "Theory"</small>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="auto_hide_empty_columns" name="settings[auto_hide_empty_columns]" value="1" {{ old('settings.auto_hide_empty_columns', true) ? 'checked' : '' }} onchange="updatePreview()">
                                    <label class="form-check-label" for="auto_hide_empty_columns">Auto-hide Empty Columns</label>
                                    <small class="form-text text-muted">Automatically hide columns with no data</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-gray-800 mb-3">Table Layout</h6>
                                <div class="mb-3">
                                    <label for="table_style" class="form-label">Table Style</label>
                                    <select class="form-control" id="table_style" name="settings[table_style]" onchange="updatePreview()">
                                        <option value="bordered" {{ old('settings.table_style', 'bordered') == 'bordered' ? 'selected' : '' }}>Bordered</option>
                                        <option value="striped" {{ old('settings.table_style', 'bordered') == 'striped' ? 'selected' : '' }}>Striped Rows</option>
                                        <option value="minimal" {{ old('settings.table_style', 'bordered') == 'minimal' ? 'selected' : '' }}>Minimal</option>
                                        <option value="modern" {{ old('settings.table_style', 'bordered') == 'modern' ? 'selected' : '' }}>Modern</option>
                                    </select>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="highlight_totals" name="settings[highlight_totals]" value="1" {{ old('settings.highlight_totals', true) ? 'checked' : '' }} onchange="updatePreview()">
                                    <label class="form-check-label" for="highlight_totals">Highlight Total Marks</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.marksheets.customize.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <div>
                                <button type="button" class="btn btn-info me-2" onclick="openLivePreview()">
                                    <i class="fas fa-eye"></i> Live Preview
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Template
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Live Preview Panel -->
        <div class="col-lg-4">
            <div class="card shadow sticky-top" style="top: 20px;">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Live Preview</h6>
                </div>
                <div class="card-body p-2">
                    <div id="livePreview" style="transform: scale(0.4); transform-origin: top left; width: 250%; height: 600px; overflow: hidden; border: 1px solid #dee2e6;">
                        <div class="preview-content">
                            <!-- Preview content will be loaded here -->
                            <div class="text-center py-5">
                                <i class="fas fa-eye fa-2x text-gray-300 mb-3"></i>
                                <p class="text-muted">Select template type to see preview</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-outline-info btn-sm w-100" onclick="openFullPreview()">
                        <i class="fas fa-expand"></i> Full Size Preview
                    </button>
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

.text-info {
    color: #36b9cc !important;
}

/* Ensure all text in cards is visible */
.card-body, .card-header, .form-label, label {
    color: #2d3748 !important;
}

.form-check-label {
    color: #2d3748 !important;
}

.form-text {
    color: #6c757d !important;
}

#livePreview {
    background: white;
    border-radius: 4px;
}

.sticky-top {
    position: sticky !important;
}
</style>

<script>
function loadTemplateDefaults() {
    const templateType = document.getElementById('template_type').value;
    if (!templateType) return;
    
    // Default settings for each template type
    const defaults = {
        modern: {
            header_color: '#2563eb',
            text_color: '#1f2937',
            font_family: 'Arial',
            font_size: 12,
            border_style: 'solid'
        },
        classic: {
            header_color: '#1f2937',
            text_color: '#1f2937',
            font_family: 'Times',
            font_size: 12,
            border_style: 'solid'
        },
        minimal: {
            header_color: '#6b7280',
            text_color: '#1f2937',
            font_family: 'Helvetica',
            font_size: 11,
            border_style: 'none'
        },
        custom: {
            header_color: '#2563eb',
            text_color: '#1f2937',
            font_family: 'Arial',
            font_size: 12,
            border_style: 'solid'
        }
    };
    
    const settings = defaults[templateType];
    if (settings) {
        document.getElementById('header_color').value = settings.header_color;
        document.getElementById('text_color').value = settings.text_color;
        document.getElementById('font_family').value = settings.font_family;
        document.getElementById('font_size').value = settings.font_size;
        document.getElementById('border_style').value = settings.border_style;
        
        updatePreview();
    }
}

function updatePreview() {
    // This would update the live preview
    // For now, just show a placeholder
    console.log('Updating preview...');
}

function openLivePreview() {
    // Open live preview in a new window
    const form = document.getElementById('templateForm');
    const formData = new FormData(form);
    
    // Convert to URL parameters for GET request
    const params = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        params.append(key, value);
    }
    
    window.open('{{ route("admin.marksheets.customize.live-preview") }}?' + params.toString(), 'livePreview', 'width=800,height=600,scrollbars=yes');
}

function openFullPreview() {
    openLivePreview();
}

// Initialize preview when page loads
document.addEventListener('DOMContentLoaded', function() {
    const templateType = document.getElementById('template_type').value;
    if (templateType) {
        loadTemplateDefaults();
    }
});
</script>
@endsection

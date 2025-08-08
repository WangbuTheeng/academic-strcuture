@extends('layouts.admin')

@section('title', 'Edit Grading Scale')
@section('page-title', 'Edit Grading Scale')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.grading-scales.index') }}">Grading Scales</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit {{ $gradingScale->name }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Grading Scale</h1>
            <p class="mb-0 text-muted">Modify grading scale settings and grade ranges</p>
        </div>
        <div>
            <a href="{{ route('admin.grading-scales.show', $gradingScale) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View Scale
            </a>
            <a href="{{ route('admin.grading-scales.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.grading-scales.update', $gradingScale) }}" id="grading-scale-form">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Basic Information -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Scale Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $gradingScale->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pass_mark" class="form-label">Pass Mark (%) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('pass_mark') is-invalid @enderror" 
                                           id="pass_mark" name="pass_mark" value="{{ old('pass_mark', $gradingScale->pass_mark) }}" 
                                           min="0" max="100" required>
                                    @error('pass_mark')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_marks" class="form-label">Maximum Marks <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('max_marks') is-invalid @enderror" 
                                           id="max_marks" name="max_marks" value="{{ old('max_marks', $gradingScale->max_marks) }}" 
                                           min="1" required>
                                    @error('max_marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="scope" class="form-label">Scope</label>
                                    <select class="form-control @error('scope') is-invalid @enderror" id="scope" name="scope">
                                        <option value="institution" {{ old('scope', $gradingScale->program_id ? 'program' : ($gradingScale->level_id ? 'level' : 'institution')) == 'institution' ? 'selected' : '' }}>
                                            Institution Wide
                                        </option>
                                        <option value="level" {{ old('scope', $gradingScale->program_id ? 'program' : ($gradingScale->level_id ? 'level' : 'institution')) == 'level' ? 'selected' : '' }}>
                                            Specific Level
                                        </option>
                                        <option value="program" {{ old('scope', $gradingScale->program_id ? 'program' : ($gradingScale->level_id ? 'level' : 'institution')) == 'program' ? 'selected' : '' }}>
                                            Specific Program
                                        </option>
                                    </select>
                                    @error('scope')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row" id="level-selection" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="level_id" class="form-label">Level</label>
                                    <select class="form-control @error('level_id') is-invalid @enderror" id="level_id" name="level_id">
                                        <option value="">Select Level</option>
                                        @foreach($levels as $level)
                                            <option value="{{ $level->id }}" {{ old('level_id', $gradingScale->level_id) == $level->id ? 'selected' : '' }}>
                                                {{ $level->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('level_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row" id="program-selection" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="program_id" class="form-label">Program</label>
                                    <select class="form-control @error('program_id') is-invalid @enderror" id="program_id" name="program_id">
                                        <option value="">Select Program</option>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}" {{ old('program_id', $gradingScale->program_id) == $program->id ? 'selected' : '' }}>
                                                {{ $program->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('program_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $gradingScale->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $gradingScale->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1" 
                                           {{ old('is_default', $gradingScale->is_default) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        Set as Default
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grade Ranges -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Grade Ranges</h6>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addGradeRange()">
                            <i class="fas fa-plus"></i> Add Range
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="grade-ranges-container">
                            @foreach($gradingScale->gradeRanges->sortByDesc('min_percentage') as $index => $range)
                                <div class="grade-range-item border rounded p-3 mb-3" data-index="{{ $index }}">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Grade</label>
                                                <input type="text" class="form-control" name="grade_ranges[{{ $index }}][grade]" 
                                                       value="{{ $range->grade }}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Min %</label>
                                                <input type="number" class="form-control" name="grade_ranges[{{ $index }}][min_percentage]" 
                                                       value="{{ $range->min_percentage }}" min="0" max="100" step="0.01" required>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Max %</label>
                                                <input type="number" class="form-control" name="grade_ranges[{{ $index }}][max_percentage]" 
                                                       value="{{ $range->max_percentage }}" min="0" max="100" step="0.01" required>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">GPA</label>
                                                <input type="number" class="form-control" name="grade_ranges[{{ $index }}][gpa]" 
                                                       value="{{ $range->gpa }}" min="0" max="10" step="0.01" required>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">Description</label>
                                                <input type="text" class="form-control" name="grade_ranges[{{ $index }}][description]" 
                                                       value="{{ $range->description }}">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="button" class="btn btn-danger btn-sm d-block" onclick="removeGradeRange(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <input type="hidden" name="grade_ranges[{{ $index }}][id]" value="{{ $range->id }}">
                                </div>
                            @endforeach
                        </div>
                        
                        @if($gradingScale->gradeRanges->count() == 0)
                            <div class="text-center py-4" id="no-ranges-message">
                                <i class="fas fa-chart-line fa-3x text-gray-300 mb-3"></i>
                                <h5 class="text-gray-600">No Grade Ranges</h5>
                                <p class="text-muted">Click "Add Range" to create grade ranges for this scale.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions & Preview -->
            <div class="col-lg-4">
                <!-- Actions -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Grading Scale
                            </button>
                            
                            <button type="button" class="btn btn-success" onclick="previewScale()">
                                <i class="fas fa-eye"></i> Preview Scale
                            </button>
                            
                            <button type="button" class="btn btn-warning" onclick="validateRanges()">
                                <i class="fas fa-check"></i> Validate Ranges
                            </button>
                            
                            <a href="{{ route('admin.grading-scales.show', $gradingScale) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Templates -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Quick Templates</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadTemplate('standard')">
                                Standard A-F Scale
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadTemplate('percentage')">
                                Percentage Based
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadTemplate('gpa')">
                                4.0 GPA Scale
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadTemplate('custom')">
                                Custom Scale
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let gradeRangeIndex = {{ $gradingScale->gradeRanges->count() }};

// Scope selection handling
document.getElementById('scope').addEventListener('change', function() {
    const scope = this.value;
    const levelSelection = document.getElementById('level-selection');
    const programSelection = document.getElementById('program-selection');
    
    levelSelection.style.display = scope === 'level' ? 'block' : 'none';
    programSelection.style.display = scope === 'program' ? 'block' : 'none';
    
    if (scope === 'institution') {
        document.getElementById('level_id').value = '';
        document.getElementById('program_id').value = '';
    }
});

// Initialize scope display
document.getElementById('scope').dispatchEvent(new Event('change'));

function addGradeRange() {
    const container = document.getElementById('grade-ranges-container');
    const noRangesMessage = document.getElementById('no-ranges-message');
    
    if (noRangesMessage) {
        noRangesMessage.style.display = 'none';
    }
    
    const gradeRangeHtml = `
        <div class="grade-range-item border rounded p-3 mb-3" data-index="${gradeRangeIndex}">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Grade</label>
                        <input type="text" class="form-control" name="grade_ranges[${gradeRangeIndex}][grade]" required>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Min %</label>
                        <input type="number" class="form-control" name="grade_ranges[${gradeRangeIndex}][min_percentage]" 
                               min="0" max="100" step="0.01" required>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Max %</label>
                        <input type="number" class="form-control" name="grade_ranges[${gradeRangeIndex}][max_percentage]" 
                               min="0" max="100" step="0.01" required>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">GPA</label>
                        <input type="number" class="form-control" name="grade_ranges[${gradeRangeIndex}][gpa]" 
                               min="0" max="10" step="0.01" required>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" name="grade_ranges[${gradeRangeIndex}][description]">
                    </div>
                </div>
                
                <div class="col-md-1">
                    <div class="form-group">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm d-block" onclick="removeGradeRange(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', gradeRangeHtml);
    gradeRangeIndex++;
}

function removeGradeRange(button) {
    const gradeRangeItem = button.closest('.grade-range-item');
    gradeRangeItem.remove();
    
    // Show no ranges message if no ranges left
    const container = document.getElementById('grade-ranges-container');
    const noRangesMessage = document.getElementById('no-ranges-message');
    
    if (container.children.length === 0 && noRangesMessage) {
        noRangesMessage.style.display = 'block';
    }
}

function validateRanges() {
    const ranges = document.querySelectorAll('.grade-range-item');
    let isValid = true;
    let errors = [];
    
    ranges.forEach((range, index) => {
        const minInput = range.querySelector('input[name*="[min_percentage]"]');
        const maxInput = range.querySelector('input[name*="[max_percentage]"]');
        const gradeInput = range.querySelector('input[name*="[grade]"]');
        
        const min = parseFloat(minInput.value);
        const max = parseFloat(maxInput.value);
        const grade = gradeInput.value.trim();
        
        if (min >= max) {
            errors.push(`Grade ${grade}: Minimum percentage must be less than maximum percentage`);
            isValid = false;
        }
        
        if (!grade) {
            errors.push(`Row ${index + 1}: Grade is required`);
            isValid = false;
        }
    });
    
    if (isValid) {
        alert('All grade ranges are valid!');
    } else {
        alert('Validation errors:\n' + errors.join('\n'));
    }
}

function previewScale() {
    // This would open a preview modal
    alert('Preview functionality would show a visual representation of the grading scale');
}

function loadTemplate(type) {
    if (!confirm('This will replace all existing grade ranges. Continue?')) {
        return;
    }
    
    // Clear existing ranges
    document.getElementById('grade-ranges-container').innerHTML = '';
    gradeRangeIndex = 0;
    
    let templates = {
        'standard': [
            { grade: 'A+', min: 97, max: 100, gpa: 4.0, description: 'Excellent' },
            { grade: 'A', min: 93, max: 96, gpa: 4.0, description: 'Excellent' },
            { grade: 'A-', min: 90, max: 92, gpa: 3.7, description: 'Very Good' },
            { grade: 'B+', min: 87, max: 89, gpa: 3.3, description: 'Good' },
            { grade: 'B', min: 83, max: 86, gpa: 3.0, description: 'Good' },
            { grade: 'B-', min: 80, max: 82, gpa: 2.7, description: 'Satisfactory' },
            { grade: 'C+', min: 77, max: 79, gpa: 2.3, description: 'Satisfactory' },
            { grade: 'C', min: 73, max: 76, gpa: 2.0, description: 'Satisfactory' },
            { grade: 'C-', min: 70, max: 72, gpa: 1.7, description: 'Below Average' },
            { grade: 'D', min: 60, max: 69, gpa: 1.0, description: 'Poor' },
            { grade: 'F', min: 0, max: 59, gpa: 0.0, description: 'Fail' }
        ],
        'percentage': [
            { grade: 'A', min: 90, max: 100, gpa: 4.0, description: 'Outstanding' },
            { grade: 'B', min: 80, max: 89, gpa: 3.0, description: 'Good' },
            { grade: 'C', min: 70, max: 79, gpa: 2.0, description: 'Satisfactory' },
            { grade: 'D', min: 60, max: 69, gpa: 1.0, description: 'Pass' },
            { grade: 'F', min: 0, max: 59, gpa: 0.0, description: 'Fail' }
        ],
        'gpa': [
            { grade: 'A', min: 90, max: 100, gpa: 4.0, description: 'Excellent' },
            { grade: 'B', min: 80, max: 89, gpa: 3.0, description: 'Good' },
            { grade: 'C', min: 70, max: 79, gpa: 2.0, description: 'Average' },
            { grade: 'D', min: 60, max: 69, gpa: 1.0, description: 'Below Average' },
            { grade: 'F', min: 0, max: 59, gpa: 0.0, description: 'Fail' }
        ]
    };
    
    if (templates[type]) {
        templates[type].forEach(template => {
            addGradeRange();
            const lastRange = document.querySelector('.grade-range-item:last-child');
            lastRange.querySelector('input[name*="[grade]"]').value = template.grade;
            lastRange.querySelector('input[name*="[min_percentage]"]').value = template.min;
            lastRange.querySelector('input[name*="[max_percentage]"]').value = template.max;
            lastRange.querySelector('input[name*="[gpa]"]').value = template.gpa;
            lastRange.querySelector('input[name*="[description]"]').value = template.description;
        });
    }
}
</script>
@endsection

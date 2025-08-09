@extends('layouts.admin')

@section('title', 'Bulk Generate Bills')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-layer-group text-primary me-2"></i>Bulk Generate Bills
            </h1>
            <p class="text-muted mb-0">Generate bills for multiple students at once</p>
        </div>
        <div>
            <a href="{{ route('admin.student-bills.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Bills
            </a>
        </div>
    </div>

    <!-- Bulk Generation Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs me-2"></i>Generation Settings
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.student-bills.process-bulk-generate') }}" id="bulkGenerateForm">
                        @csrf

                        <!-- Academic Year -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="academic_year_id" class="form-label fw-semibold">
                                    <i class="fas fa-calendar-alt me-1"></i>Academic Year
                                </label>
                                <select name="academic_year_id" id="academic_year_id" 
                                        class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                    <option value="">Select Academic Year</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" 
                                                {{ old('academic_year_id', $currentAcademicYear?->id) == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Bill Date -->
                            <div class="col-md-6">
                                <label for="bill_date" class="form-label fw-semibold">
                                    <i class="fas fa-calendar me-1"></i>Bill Date
                                </label>
                                <input type="date" name="bill_date" id="bill_date" 
                                       class="form-control @error('bill_date') is-invalid @enderror"
                                       value="{{ old('bill_date', date('Y-m-d')) }}" required>
                                @error('bill_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Due Date -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="due_date" class="form-label fw-semibold">
                                    <i class="fas fa-clock me-1"></i>Due Date
                                </label>
                                <input type="date" name="due_date" id="due_date" 
                                       class="form-control @error('due_date') is-invalid @enderror"
                                       value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Bill Title -->
                            <div class="col-md-6">
                                <label for="bill_title" class="form-label fw-semibold">
                                    <i class="fas fa-tag me-1"></i>Bill Title
                                </label>
                                <input type="text" name="bill_title" id="bill_title" 
                                       class="form-control @error('bill_title') is-invalid @enderror"
                                       value="{{ old('bill_title', 'Academic Fee Bill - ' . date('M Y')) }}" 
                                       placeholder="Enter bill title">
                                @error('bill_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Student Filters -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">
                                    <i class="fas fa-filter me-2"></i>Student Selection
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Level -->
                                    <div class="col-md-4 mb-3">
                                        <label for="level_id" class="form-label fw-semibold">Level</label>
                                        <select name="level_id" id="level_id" class="form-select">
                                            <option value="">All Levels</option>
                                            @foreach($levels as $level)
                                                <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>
                                                    {{ $level->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Program -->
                                    <div class="col-md-4 mb-3">
                                        <label for="program_id" class="form-label fw-semibold">Program</label>
                                        <select name="program_id" id="program_id" class="form-select">
                                            <option value="">All Programs</option>
                                            @foreach($programs as $program)
                                                <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                                    {{ $program->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Class -->
                                    <div class="col-md-4 mb-3">
                                        <label for="class_id" class="form-label fw-semibold">
                                            <i class="fas fa-chalkboard-teacher me-1"></i>Class
                                        </label>
                                        <select name="class_id" id="class_id" class="form-select">
                                            <option value="">All Classes</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                    {{ $class->name }} ({{ $class->level->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Student Count Preview -->
                                <div class="alert alert-info" id="studentCountPreview" style="display: none;">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span id="studentCountText">Loading student count...</span>
                                </div>
                            </div>
                        </div>

                        <!-- Fee Structures -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-warning">
                                    <i class="fas fa-money-bill-wave me-2"></i>Fee Structures
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($feeStructures->isEmpty())
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No active fee structures found. 
                                        <a href="{{ route('admin.fees.structures.create') }}" class="alert-link">Create one first</a>.
                                    </div>
                                @else
                                    <div class="row">
                                        @foreach($feeStructures->groupBy('fee_category') as $category => $structures)
                                            <div class="col-md-6 mb-3">
                                                <h6 class="text-primary">{{ $category }}</h6>
                                                @foreach($structures as $structure)
                                                    <div class="form-check">
                                                        <input class="form-check-input fee-structure-checkbox" 
                                                               type="checkbox" 
                                                               name="fee_structures[]" 
                                                               value="{{ $structure->id }}" 
                                                               id="fee_{{ $structure->id }}"
                                                               data-amount="{{ $structure->amount }}">
                                                        <label class="form-check-label" for="fee_{{ $structure->id }}">
                                                            {{ $structure->fee_name }}
                                                            <span class="text-success fw-bold">Rs. {{ number_format($structure->amount, 2) }}</span>
                                                            @if($structure->level)
                                                                <small class="text-muted">({{ $structure->level->name }})</small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Total Amount Preview -->
                                    <div class="alert alert-success mt-3" id="totalAmountPreview" style="display: none;">
                                        <i class="fas fa-calculator me-2"></i>
                                        Total Amount per Bill: <strong id="totalAmountText">Rs. 0.00</strong>
                                    </div>
                                @endif

                                @error('fee_structures')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Generation Options -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-info">
                                    <i class="fas fa-cogs me-2"></i>Generation Options
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="skip_existing" 
                                                   id="skip_existing" value="1" checked>
                                            <label class="form-check-label" for="skip_existing">
                                                Skip students who already have bills for this period
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="auto_print" 
                                                   id="auto_print" value="1">
                                            <label class="form-check-label" for="auto_print">
                                                <i class="fas fa-print me-1"></i>
                                                Generate printable bills after creation
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-info" id="previewBtn">
                                <i class="fas fa-eye me-2"></i>Preview Students
                            </button>
                            <div>
                                <button type="submit" class="btn btn-primary" id="generateBtn">
                                    <i class="fas fa-layer-group me-2"></i>Generate Bills
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle me-2"></i>Generation Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="fas fa-users fa-3x text-primary mb-2"></i>
                            <h4 id="summaryStudentCount">0</h4>
                            <p class="text-muted">Students Selected</p>
                        </div>
                        
                        <div class="mb-3">
                            <i class="fas fa-money-bill-wave fa-3x text-success mb-2"></i>
                            <h4 id="summaryTotalAmount">Rs. 0.00</h4>
                            <p class="text-muted">Amount per Bill</p>
                        </div>
                        
                        <div class="mb-3">
                            <i class="fas fa-calculator fa-3x text-warning mb-2"></i>
                            <h4 id="summaryGrandTotal">Rs. 0.00</h4>
                            <p class="text-muted">Total Collection Expected</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.fees.structures.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Create Fee Structure
                        </a>
                        <a href="{{ route('admin.student-bills.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-list me-2"></i>View All Bills
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Preview Modal -->
<div class="modal fade" id="studentPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-users me-2"></i>Students Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="studentPreviewContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading students...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const feeCheckboxes = document.querySelectorAll('.fee-structure-checkbox');
    const totalAmountPreview = document.getElementById('totalAmountPreview');
    const totalAmountText = document.getElementById('totalAmountText');
    const summaryTotalAmount = document.getElementById('summaryTotalAmount');
    const summaryGrandTotal = document.getElementById('summaryGrandTotal');
    const summaryStudentCount = document.getElementById('summaryStudentCount');
    
    // Update total amount when fee structures are selected
    function updateTotalAmount() {
        let total = 0;
        feeCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                total += parseFloat(checkbox.dataset.amount);
            }
        });
        
        const formattedTotal = 'Rs. ' + total.toLocaleString('en-IN', {minimumFractionDigits: 2});
        totalAmountText.textContent = formattedTotal;
        summaryTotalAmount.textContent = formattedTotal;
        
        if (total > 0) {
            totalAmountPreview.style.display = 'block';
        } else {
            totalAmountPreview.style.display = 'none';
        }
        
        updateGrandTotal();
    }
    
    // Update grand total (total amount × student count)
    function updateGrandTotal() {
        const totalAmount = parseFloat(summaryTotalAmount.textContent.replace(/[^\d.-]/g, ''));
        const studentCount = parseInt(summaryStudentCount.textContent);
        const grandTotal = totalAmount * studentCount;
        
        summaryGrandTotal.textContent = 'Rs. ' + grandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2});
    }
    
    // Add event listeners to fee checkboxes
    feeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateTotalAmount);
    });
    
    // Preview students functionality
    document.getElementById('previewBtn').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('bulkGenerateForm'));
        
        fetch('{{ route("admin.student-bills.preview-students") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                summaryStudentCount.textContent = data.students.length;
                updateGrandTotal();
                
                let html = '<div class="table-responsive"><table class="table table-sm">';
                html += '<thead><tr><th>Name</th><th>Admission No.</th><th>Class</th></tr></thead><tbody>';
                
                data.students.forEach(student => {
                    html += `<tr>
                        <td>${student.full_name}</td>
                        <td>${student.admission_number}</td>
                        <td>${student.class_name}</td>
                    </tr>`;
                });
                
                html += '</tbody></table></div>';
                
                if (data.students.length === 0) {
                    html = '<div class="alert alert-warning">No students found matching the selected criteria.</div>';
                }
                
                document.getElementById('studentPreviewContent').innerHTML = html;
                new bootstrap.Modal(document.getElementById('studentPreviewModal')).show();
            } else {
                alert('Error loading students: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading students');
        });
    });
    
    // Initialize total amount calculation
    updateTotalAmount();
});
</script>
@endpush

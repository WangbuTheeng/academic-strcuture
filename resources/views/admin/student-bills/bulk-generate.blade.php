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

                        <!-- Enhanced Features Info -->
                        <div class="alert alert-success border-0 mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-star text-warning me-2"></i>
                                <div>
                                    <strong>Enhanced Bulk Generation!</strong>
                                    <p class="mb-0 mt-1">
                                        • <strong>Editable Amounts:</strong> Modify fee structure amounts directly<br>
                                        • <strong>Custom Fees:</strong> Add manual fees with custom descriptions<br>
                                        • <strong>Quick Add:</strong> Use preset buttons for common fees like monthly fees
                                    </p>
                                </div>
                            </div>
                        </div>

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
                                                    <div class="form-check mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <input class="form-check-input fee-structure-checkbox me-2"
                                                                   type="checkbox"
                                                                   name="fee_structures[]"
                                                                   value="{{ $structure->id }}"
                                                                   id="fee_{{ $structure->id }}"
                                                                   data-amount="{{ $structure->amount }}"
                                                                   data-original-amount="{{ $structure->amount }}">
                                                            <div class="flex-grow-1">
                                                                <label class="form-check-label d-block" for="fee_{{ $structure->id }}">
                                                                    {{ $structure->fee_name }}
                                                                    @if($structure->level)
                                                                        <small class="text-muted">({{ $structure->level->name }})</small>
                                                                    @endif
                                                                </label>
                                                                <div class="d-flex align-items-center mt-1">
                                                                    <span class="text-muted me-2">Amount:</span>
                                                                    <input type="number"
                                                                           class="form-control form-control-sm fee-amount-input"
                                                                           style="width: 120px;"
                                                                           name="fee_amounts[{{ $structure->id }}]"
                                                                           value="{{ $structure->amount }}"
                                                                           step="0.01"
                                                                           min="0"
                                                                           data-structure-id="{{ $structure->id }}"
                                                                           placeholder="0.00">
                                                                    <button type="button"
                                                                            class="btn btn-sm btn-outline-secondary ms-2 reset-amount-btn"
                                                                            data-structure-id="{{ $structure->id }}"
                                                                            data-original-amount="{{ $structure->amount }}"
                                                                            title="Reset to original amount">
                                                                        <i class="fas fa-undo"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
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

                        <!-- Previous Dues Option -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-info">
                                    <i class="fas fa-history me-2"></i>Previous Outstanding Dues
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check">
                                    <input type="checkbox" name="include_previous_dues" id="include_previous_dues"
                                           class="form-check-input" value="1" {{ old('include_previous_dues') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="include_previous_dues">
                                        <strong>Include Previous Outstanding Dues</strong>
                                        <small class="text-muted d-block">
                                            Automatically add any unpaid amounts from previous bills to each new bill.
                                            This makes it easier for students to pay all their dues in one transaction.
                                        </small>
                                    </label>
                                </div>
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>How it works:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>System will check each student's unpaid bills</li>
                                        <li>Outstanding amounts will be added as "Previous Dues" line item</li>
                                        <li>Students can pay both new fees and old dues together</li>
                                        <li>Only applies to students who have outstanding balances</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Fee Items -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-info">
                                    <i class="fas fa-plus-circle me-2"></i>Custom Fee Items
                                </h6>
                                <button type="button" class="btn btn-sm btn-info" id="addCustomFeeBtn">
                                    <i class="fas fa-plus me-1"></i>Add Custom Fee
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Add custom fee items with manual descriptions and amounts. Perfect for monthly fees, special charges, or any custom billing needs.
                                </div>

                                <!-- Quick Add Buttons -->
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">Quick Add Common Fees:</h6>
                                    <div class="btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary me-2 mb-2 quick-add-fee"
                                                data-description="Monthly Fee - {{ date('F Y') }}"
                                                data-category="tuition"
                                                data-amount="12000">
                                            <i class="fas fa-calendar-alt me-1"></i>Monthly Fee
                                        </button>
                                        <button type="button" class="btn btn-outline-success me-2 mb-2 quick-add-fee"
                                                data-description="Examination Fee"
                                                data-category="examination"
                                                data-amount="1500">
                                            <i class="fas fa-file-alt me-1"></i>Exam Fee
                                        </button>
                                        <button type="button" class="btn btn-outline-warning me-2 mb-2 quick-add-fee"
                                                data-description="Library Fee"
                                                data-category="library"
                                                data-amount="500">
                                            <i class="fas fa-book me-1"></i>Library Fee
                                        </button>
                                        <button type="button" class="btn btn-outline-info me-2 mb-2 quick-add-fee"
                                                data-description="Transport Fee"
                                                data-category="transport"
                                                data-amount="2000">
                                            <i class="fas fa-bus me-1"></i>Transport Fee
                                        </button>
                                    </div>
                                </div>

                                <div id="customFeesContainer">
                                    <!-- Custom fee items will be added here dynamically -->
                                </div>

                                <!-- Custom Fees Total -->
                                <div class="alert alert-info mt-3" id="customFeesTotalPreview" style="display: none;">
                                    <i class="fas fa-calculator me-2"></i>
                                    Custom Fees Total: <strong id="customFeesTotalText">Rs. 0.00</strong>
                                </div>
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
                            <div>
                                <button type="button" class="btn btn-outline-info" id="previewBtn">
                                    <i class="fas fa-eye me-2"></i>Preview Students
                                </button>
                                <button type="button" class="btn btn-outline-warning ms-2" id="diagnoseBtn">
                                    <i class="fas fa-stethoscope me-2"></i>Diagnose Issues
                                </button>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary" id="generateBtn">
                                    <i class="fas fa-layer-group me-2"></i>Generate Bills
                                </button>
                                <button type="submit" name="auto_print" value="1" class="btn btn-success ms-2" id="generatePrintBtn">
                                    <i class="fas fa-print me-2"></i>Generate & Print
                                </button>
                            </div>
                        </div>

                        <!-- Validation Alert -->
                        <div id="validationAlert" class="alert alert-warning mt-3" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span id="validationMessage"></span>
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

                    <div class="alert alert-info mt-3 mb-0">
                        <h6><i class="fas fa-lightbulb me-2"></i>Quick Tips:</h6>
                        <ul class="mb-0 small">
                            <li>Select at least one fee structure or add custom fees</li>
                            <li>Use "Preview Students" to verify your selection</li>
                            <li>Previous dues are added automatically when enabled</li>
                            <li>System skips students with existing bills for same date</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Diagnostic Modal -->
<div class="modal fade" id="diagnosticModal" tabindex="-1" aria-labelledby="diagnosticModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="diagnosticModalLabel">
                    <i class="fas fa-stethoscope me-2"></i>Bulk Generation Diagnostics
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="diagnosticContent">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                        <p class="mt-2">Running diagnostics...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
    
    // Custom fee items management
    let customFeeCounter = 0;
    const customFeesContainer = document.getElementById('customFeesContainer');
    const customFeesTotalPreview = document.getElementById('customFeesTotalPreview');
    const customFeesTotalText = document.getElementById('customFeesTotalText');

    // Add custom fee item
    document.getElementById('addCustomFeeBtn').addEventListener('click', function() {
        customFeeCounter++;
        const customFeeHtml = `
            <div class="custom-fee-item border rounded p-3 mb-3" data-fee-id="${customFeeCounter}">
                <div class="row">
                    <div class="col-md-5">
                        <label class="form-label">Fee Description</label>
                        <input type="text" class="form-control" name="custom_fees[${customFeeCounter}][description]"
                               placeholder="e.g., Monthly Fee - January 2025" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <select class="form-control" name="custom_fees[${customFeeCounter}][category]">
                            <option value="tuition">Tuition</option>
                            <option value="examination">Examination</option>
                            <option value="library">Library</option>
                            <option value="transport">Transport</option>
                            <option value="hostel">Hostel</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Amount (Rs.)</label>
                        <input type="number" class="form-control custom-fee-amount"
                               name="custom_fees[${customFeeCounter}][amount]"
                               placeholder="0.00" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-custom-fee"
                                data-fee-id="${customFeeCounter}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        customFeesContainer.insertAdjacentHTML('beforeend', customFeeHtml);
        updateCustomFeesTotal();

        // Add event listeners to new inputs
        const newItem = customFeesContainer.querySelector(`[data-fee-id="${customFeeCounter}"]`);
        newItem.querySelector('.custom-fee-amount').addEventListener('input', updateCustomFeesTotal);
        newItem.querySelector('.remove-custom-fee').addEventListener('click', function() {
            newItem.remove();
            updateCustomFeesTotal();
        });
    });

    // Quick add fee functionality
    document.querySelectorAll('.quick-add-fee').forEach(button => {
        button.addEventListener('click', function() {
            const description = this.dataset.description;
            const category = this.dataset.category;
            const amount = this.dataset.amount;

            // Trigger add custom fee
            document.getElementById('addCustomFeeBtn').click();

            // Fill in the values for the newly added item
            setTimeout(() => {
                const lastItem = customFeesContainer.querySelector('.custom-fee-item:last-child');
                if (lastItem) {
                    lastItem.querySelector('input[name*="[description]"]').value = description;
                    lastItem.querySelector('select[name*="[category]"]').value = category;
                    lastItem.querySelector('input[name*="[amount]"]').value = amount;
                    updateCustomFeesTotal();
                }
            }, 100);
        });
    });

    // Update custom fees total
    function updateCustomFeesTotal() {
        let total = 0;
        document.querySelectorAll('.custom-fee-amount').forEach(input => {
            const value = parseFloat(input.value) || 0;
            total += value;
        });

        const formattedTotal = 'Rs. ' + total.toLocaleString('en-IN', {minimumFractionDigits: 2});
        customFeesTotalText.textContent = formattedTotal;

        if (total > 0) {
            customFeesTotalPreview.style.display = 'block';
        } else {
            customFeesTotalPreview.style.display = 'none';
        }

        updateTotalAmount();
    }

    // Update total amount when fee structures are selected
    function updateTotalAmount() {
        let structureTotal = 0;
        feeCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                structureTotal += parseFloat(checkbox.dataset.amount);
            }
        });

        let customTotal = 0;
        document.querySelectorAll('.custom-fee-amount').forEach(input => {
            const value = parseFloat(input.value) || 0;
            customTotal += value;
        });

        const total = structureTotal + customTotal;
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

    // Add event listeners to fee amount inputs
    document.querySelectorAll('.fee-amount-input').forEach(input => {
        input.addEventListener('input', function() {
            const structureId = this.dataset.structureId;
            const checkbox = document.getElementById(`fee_${structureId}`);
            const newAmount = parseFloat(this.value) || 0;

            // Update the checkbox data-amount
            checkbox.dataset.amount = newAmount;

            // Auto-check the checkbox if amount is entered and not already checked
            if (newAmount > 0 && !checkbox.checked) {
                checkbox.checked = true;
            }

            updateTotalAmount();
        });
    });

    // Add event listeners to reset amount buttons
    document.querySelectorAll('.reset-amount-btn').forEach(button => {
        button.addEventListener('click', function() {
            const structureId = this.dataset.structureId;
            const originalAmount = this.dataset.originalAmount;
            const input = document.querySelector(`input[data-structure-id="${structureId}"]`);
            const checkbox = document.getElementById(`fee_${structureId}`);

            // Reset to original amount
            input.value = originalAmount;
            checkbox.dataset.amount = originalAmount;

            updateTotalAmount();
        });
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
    
    // Form validation
    document.getElementById('bulkGenerateForm').addEventListener('submit', function(e) {
        const validationAlert = document.getElementById('validationAlert');
        const validationMessage = document.getElementById('validationMessage');

        // Check if at least one fee type is selected
        const feeStructures = document.querySelectorAll('input[name="fee_structures[]"]:checked');
        const customFeeItems = document.querySelectorAll('.custom-fee-item');
        let hasCustomFees = false;

        customFeeItems.forEach((item, index) => {
            const description = item.querySelector('input[name*="[description]"]');
            const amount = item.querySelector('input[name*="[amount]"]');

            if (description && amount && description.value.trim() && amount.value.trim()) {
                hasCustomFees = true;
            }
        });

        if (feeStructures.length === 0 && !hasCustomFees) {
            e.preventDefault();
            validationMessage.textContent = 'Please select at least one fee structure or add custom fees before generating bills.';
            validationAlert.style.display = 'block';
            validationAlert.scrollIntoView({ behavior: 'smooth' });
            return false;
        }

        // Show loading state
        const submitBtn = e.submitter || document.getElementById('generateBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating Bills...';
        submitBtn.disabled = true;

        // Hide validation alert if shown
        validationAlert.style.display = 'none';

        // Re-enable button after 30 seconds (in case of slow processing)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 30000);
    });

    // Diagnostic functionality
    document.getElementById('diagnoseBtn').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('bulkGenerateForm'));

        fetch('{{ route("admin.student-bills.bulk-diagnose") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayDiagnostics(data.diagnostics, data.recommendations);
                new bootstrap.Modal(document.getElementById('diagnosticModal')).show();
            } else {
                alert('Error running diagnostics: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error running diagnostics');
        });
    });

    function displayDiagnostics(diagnostics, recommendations) {
        let html = '<div class="row">';

        // Students section
        html += '<div class="col-md-6">';
        html += '<h6><i class="fas fa-users text-primary me-2"></i>Students</h6>';
        html += '<ul class="list-unstyled">';
        html += `<li>Total in school: <strong>${diagnostics.students.total_in_school}</strong></li>`;
        html += `<li>Active students: <strong>${diagnostics.students.active_in_school}</strong></li>`;
        html += `<li>With enrollments: <strong>${diagnostics.students.with_enrollments}</strong></li>`;
        html += `<li>Matching criteria: <strong class="${diagnostics.students.matching_criteria > 0 ? 'text-success' : 'text-danger'}">${diagnostics.students.matching_criteria}</strong></li>`;
        html += '</ul>';
        html += '</div>';

        // Fee structures section
        html += '<div class="col-md-6">';
        html += '<h6><i class="fas fa-money-bill text-success me-2"></i>Fee Structures</h6>';
        html += '<ul class="list-unstyled">';
        html += `<li>Available: <strong>${diagnostics.fee_structures.total}</strong></li>`;
        html += '</ul>';
        html += '</div>';

        html += '</div>';

        // Recommendations
        if (recommendations.length > 0) {
            html += '<hr><h6><i class="fas fa-lightbulb text-warning me-2"></i>Recommendations</h6>';
            html += '<ul>';
            recommendations.forEach(rec => {
                html += `<li>${rec}</li>`;
            });
            html += '</ul>';
        }

        document.getElementById('diagnosticContent').innerHTML = html;
    }

    // Initialize total amount calculation
    updateTotalAmount();
});
</script>
@endpush

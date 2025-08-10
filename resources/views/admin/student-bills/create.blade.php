@extends('layouts.admin')

@section('title', 'Create Student Bill')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus text-primary me-2"></i>Create Student Bill
            </h1>
            <p class="text-muted mb-0">Generate a new bill for student fees</p>
        </div>
        <div>
            <a href="{{ route('admin.fees.bills.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Bills
            </a>
        </div>
    </div>

    <!-- Create Bill Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-invoice me-2"></i>Bill Information
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.fees.bills.store') }}" method="POST">
                        @csrf
                        
                        <!-- Student Selection -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="student_id" class="form-label">Student <span class="text-danger">*</span></label>
                                <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                                    <option value="">Select Student</option>
                                    @if(isset($students))
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                                {{ $student->full_name }} ({{ $student->admission_number }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                <select name="academic_year_id" id="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                    <option value="">Select Academic Year</option>
                                    @if(isset($academicYears))
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                                {{ $year->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('academic_year_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Bill Details -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="bill_date" class="form-label">Bill Date <span class="text-danger">*</span></label>
                                <input type="date" name="bill_date" id="bill_date" 
                                       class="form-control @error('bill_date') is-invalid @enderror" 
                                       value="{{ old('bill_date', date('Y-m-d')) }}" 
                                       max="{{ date('Y-m-d') }}" required>
                                @error('bill_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                <input type="date" name="due_date" id="due_date" 
                                       class="form-control @error('due_date') is-invalid @enderror" 
                                       value="{{ old('due_date') }}" 
                                       min="{{ date('Y-m-d') }}" required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" rows="3"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Optional bill description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Include Previous Dues Option -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="include_previous_dues" id="include_previous_dues"
                                       class="form-check-input" value="1" {{ old('include_previous_dues') ? 'checked' : '' }}>
                                <label class="form-check-label" for="include_previous_dues">
                                    <strong>Include Previous Outstanding Dues</strong>
                                    <small class="text-muted d-block">Add any unpaid amounts from previous bills to this new bill</small>
                                </label>
                            </div>
                            <div id="previous-dues-info" class="mt-2" style="display: none;">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span id="previous-dues-text">Select a student to see previous dues</span>
                                </div>
                            </div>
                        </div>

                        <!-- Fee Items Selection -->
                        <h6 class="text-primary mb-3">Fee Items</h6>

                        <!-- Fee Structure Selection -->
                        <div class="mb-3">
                            <label class="form-label">Predefined Fee Structures</label>
                            @if(isset($feeStructures) && $feeStructures->count() > 0)
                                <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                                    @foreach($feeStructures as $structure)
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="fee_structure_ids[]" value="{{ $structure->id }}"
                                                   class="form-check-input fee-structure-checkbox"
                                                   id="fee_{{ $structure->id }}"
                                                   data-amount="{{ $structure->amount }}"
                                                   data-name="{{ $structure->fee_name }}"
                                                   {{ in_array($structure->id, old('fee_structure_ids', [])) ? 'checked' : '' }}>
                                            <label for="fee_{{ $structure->id }}" class="form-check-label w-100">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $structure->fee_name }}</strong>
                                                        <br><small class="text-muted">{{ $structure->fee_category_label }} - {{ $structure->billing_frequency_label }}</small>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="fw-bold text-success">Rs. {{ number_format($structure->amount, 2) }}</span>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>No fee structures available.</strong>
                                    You can <a href="{{ route('admin.fees.structures.create') }}" target="_blank">create fee structures</a>
                                    or add custom fees below.
                                </div>
                            @endif
                        </div>

                        <!-- Custom Fee Items -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label">Custom Fee Items</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCustomFeeItem()">
                                    <i class="fas fa-plus me-1"></i>Add Custom Fee
                                </button>
                            </div>
                            <div id="custom-fee-items">
                                <!-- Custom fee items will be added here -->
                            </div>
                        </div>

                        <!-- Bill Summary -->
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title text-primary">Bill Summary</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between">
                                            <span>Selected Items:</span>
                                            <span id="selected-count">0</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between">
                                            <strong>Total Amount:</strong>
                                            <strong class="text-success" id="total-amount">Rs. 0.00</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.fees.bills.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Bill
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Student Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-user me-2"></i>Student Information
                    </h6>
                </div>
                <div class="card-body" id="student-info">
                    <p class="text-muted">Select a student to view information</p>
                </div>
            </div>

            <!-- Bill Guidelines -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-info-circle me-2"></i>Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small">
                        <li><i class="fas fa-check text-success me-2"></i>Select appropriate fee structures for the student</li>
                        <li><i class="fas fa-check text-success me-2"></i>Set a reasonable due date</li>
                        <li><i class="fas fa-check text-success me-2"></i>Add description for clarity</li>
                        <li><i class="fas fa-check text-success me-2"></i>Verify total amount before creating</li>
                        <li><i class="fas fa-check text-success me-2"></i>Bill number will be auto-generated</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Load student information when student is selected
    document.getElementById('student_id').addEventListener('change', function() {
        const studentId = this.value;
        const studentInfo = document.getElementById('student-info');
        const previousDuesInfo = document.getElementById('previous-dues-info');
        const previousDuesText = document.getElementById('previous-dues-text');

        if (studentId) {
            // Show loading state
            studentInfo.innerHTML = `
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin"></i> Loading student information...
                </div>
            `;

            // Fetch student information and previous dues via AJAX
            fetch(`/admin/students/${studentId}/bill-info`)
                .then(response => response.json())
                .then(data => {
                    // Update student information
                    studentInfo.innerHTML = `
                        <div class="mb-2">
                            <label class="form-label text-muted">Name</label>
                            <div class="fw-bold">${data.student.full_name}</div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label text-muted">Admission Number</label>
                            <div>${data.student.admission_number}</div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label text-muted">Class</label>
                            <div>${data.student.current_class || 'Not Enrolled'}</div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label text-muted">Status</label>
                            <div><span class="badge bg-success">Active</span></div>
                        </div>
                    `;

                    // Update previous dues information
                    if (data.previous_dues.total_amount > 0) {
                        previousDuesText.innerHTML = `
                            <strong>Outstanding Amount: ${data.previous_dues.formatted_amount}</strong><br>
                            <small>From ${data.previous_dues.bill_count} unpaid bill(s)</small>
                        `;
                        previousDuesInfo.style.display = 'block';

                        // Update the checkbox label to show the amount
                        const checkbox = document.getElementById('include_previous_dues');
                        const label = checkbox.nextElementSibling;
                        label.innerHTML = `
                            <strong>Include Previous Outstanding Dues (${data.previous_dues.formatted_amount})</strong>
                            <small class="text-muted d-block">Add any unpaid amounts from previous bills to this new bill</small>
                        `;
                        checkbox.disabled = false;
                    } else {
                        previousDuesText.innerHTML = 'No outstanding dues found for this student.';
                        previousDuesInfo.style.display = 'block';

                        // Reset checkbox label and disable it
                        const checkbox = document.getElementById('include_previous_dues');
                        const label = checkbox.nextElementSibling;
                        label.innerHTML = `
                            <strong>Include Previous Outstanding Dues</strong>
                            <small class="text-muted d-block">No outstanding dues for this student</small>
                        `;
                        checkbox.disabled = true;
                        checkbox.checked = false;
                    }
                })
                .catch(error => {
                    console.error('Error fetching student information:', error);
                    studentInfo.innerHTML = '<p class="text-danger">Error loading student information</p>';
                    previousDuesInfo.style.display = 'none';
                });
        } else {
            studentInfo.innerHTML = '<p class="text-muted">Select a student to view information</p>';
            previousDuesInfo.style.display = 'none';

            // Reset checkbox
            const checkbox = document.getElementById('include_previous_dues');
            checkbox.disabled = false;
            checkbox.checked = false;
        }
    });

    let customFeeCounter = 0;

    // Add custom fee item
    function addCustomFeeItem() {
        customFeeCounter++;
        const customFeesContainer = document.getElementById('custom-fee-items');

        const feeItemHtml = `
            <div class="card mb-2 custom-fee-item" id="custom-fee-${customFeeCounter}">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-md-5">
                            <label class="form-label">Fee Name</label>
                            <input type="text" name="custom_fees[${customFeeCounter}][name]"
                                   class="form-control" placeholder="Enter fee name" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Amount (Rs.)</label>
                            <input type="number" name="custom_fees[${customFeeCounter}][amount]"
                                   class="form-control custom-fee-amount" step="0.01" min="0.01"
                                   placeholder="0.00" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <select name="custom_fees[${customFeeCounter}][category]" class="form-select">
                                <option value="tuition">Tuition Fee</option>
                                <option value="admission">Admission Fee</option>
                                <option value="examination">Examination Fee</option>
                                <option value="library">Library Fee</option>
                                <option value="laboratory">Laboratory Fee</option>
                                <option value="transport">Transport Fee</option>
                                <option value="hostel">Hostel Fee</option>
                                <option value="miscellaneous">Miscellaneous</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger btn-sm"
                                    onclick="removeCustomFeeItem(${customFeeCounter})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        customFeesContainer.insertAdjacentHTML('beforeend', feeItemHtml);
        updateBillSummary();
    }

    // Remove custom fee item
    function removeCustomFeeItem(id) {
        document.getElementById(`custom-fee-${id}`).remove();
        updateBillSummary();
    }

    // Calculate total amount when fee structures or custom fees are selected
    function updateBillSummary() {
        const structureCheckboxes = document.querySelectorAll('.fee-structure-checkbox:checked');
        const customAmountInputs = document.querySelectorAll('.custom-fee-amount');

        let totalAmount = 0;
        let selectedCount = 0;

        // Add predefined fee structures
        structureCheckboxes.forEach(checkbox => {
            totalAmount += parseFloat(checkbox.dataset.amount || 0);
            selectedCount++;
        });

        // Add custom fees
        customAmountInputs.forEach(input => {
            const amount = parseFloat(input.value || 0);
            if (amount > 0) {
                totalAmount += amount;
                selectedCount++;
            }
        });

        document.getElementById('selected-count').textContent = selectedCount;
        document.getElementById('total-amount').textContent = `Rs. ${totalAmount.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
    }

    // Add event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Fee structure checkboxes
        const checkboxes = document.querySelectorAll('.fee-structure-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBillSummary);
        });

        // Custom fee amount inputs (using event delegation)
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('custom-fee-amount')) {
                updateBillSummary();
            }
        });

        // Initial calculation
        updateBillSummary();
    });

    // Set default due date (30 days from bill date)
    document.getElementById('bill_date').addEventListener('change', function() {
        const billDate = new Date(this.value);
        const dueDate = new Date(billDate);
        dueDate.setDate(dueDate.getDate() + 30);
        
        document.getElementById('due_date').value = dueDate.toISOString().split('T')[0];
    });
</script>
@endpush

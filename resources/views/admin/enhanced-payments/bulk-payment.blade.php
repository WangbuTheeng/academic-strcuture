@extends('layouts.admin')

@section('title', 'Bulk Payment Processing')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-layer-group text-primary me-2"></i>Bulk Payment Processing
            </h1>
            <p class="text-muted mb-0">Process payments for multiple students at once</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.enhanced-payments.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <form action="{{ route('admin.enhanced-payments.process-bulk-payment') }}" method="POST" id="bulkPaymentForm">
        @csrf
        <div class="row">
            <!-- Student Selection -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Student Selection</h6>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label for="academic_year_id" class="form-label">Academic Year</label>
                                <select class="form-select" id="academic_year_id" name="academic_year_id">
                                    <option value="">All Years</option>
                                    @foreach(\App\Models\AcademicYear::orderBy('name')->get() as $year)
                                        <option value="{{ $year->id }}" {{ $year->is_current ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="level_id" class="form-label">Level</label>
                                <select class="form-select" id="level_id" name="level_id">
                                    <option value="">All Levels</option>
                                    @foreach(\App\Models\Level::orderBy('name')->get() as $level)
                                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="program_id" class="form-label">Program</label>
                                <select class="form-select" id="program_id" name="program_id">
                                    <option value="">All Programs</option>
                                    @foreach(\App\Models\Program::orderBy('name')->get() as $program)
                                        <option value="{{ $program->id }}">{{ $program->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="class_id" class="form-label">Class</label>
                                <select class="form-select" id="class_id" name="class_id">
                                    <option value="">All Classes</option>
                                    @foreach(\App\Models\SchoolClass::orderBy('name')->get() as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-info" id="loadStudents">
                                    <i class="fas fa-search me-2"></i>Load Students
                                </button>
                                <button type="button" class="btn btn-success" id="selectAll">
                                    <i class="fas fa-check-square me-2"></i>Select All
                                </button>
                                <button type="button" class="btn btn-warning" id="clearAll">
                                    <i class="fas fa-square me-2"></i>Clear All
                                </button>
                            </div>
                        </div>

                        <!-- Students List -->
                        <div id="studentsContainer">
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <p>Click "Load Students" to see available students</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Payment Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="payment_date" class="form-label">Payment Date</label>
                            <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                   value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="">Select Method</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="online">Online Payment</option>
                                <option value="cheque">Cheque</option>
                                <option value="card">Card</option>
                                <option value="mobile_wallet">Mobile Wallet</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="reference_number" class="form-label">Reference Number</label>
                            <input type="text" class="form-control" id="reference_number" name="reference_number" 
                                   placeholder="Optional reference">
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3" 
                                      placeholder="Optional remarks"></textarea>
                        </div>

                        <div class="alert alert-info">
                            <strong>Selected Students:</strong> <span id="selectedCount">0</span><br>
                            <strong>Total Amount:</strong> NRs. <span id="totalAmount">0.00</span>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block" id="processPayment" disabled>
                            <i class="fas fa-credit-card me-2"></i>Process Bulk Payment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadStudentsBtn = document.getElementById('loadStudents');
    const selectAllBtn = document.getElementById('selectAll');
    const clearAllBtn = document.getElementById('clearAll');
    const studentsContainer = document.getElementById('studentsContainer');
    const selectedCountSpan = document.getElementById('selectedCount');
    const totalAmountSpan = document.getElementById('totalAmount');
    const processPaymentBtn = document.getElementById('processPayment');

    loadStudentsBtn.addEventListener('click', loadStudents);
    selectAllBtn.addEventListener('click', selectAll);
    clearAllBtn.addEventListener('click', clearAll);

    function loadStudents() {
        const filters = {
            academic_year_id: document.getElementById('academic_year_id').value,
            level_id: document.getElementById('level_id').value,
            program_id: document.getElementById('program_id').value,
            class_id: document.getElementById('class_id').value,
        };

        studentsContainer.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading students...</div>';

        // Simulate API call - replace with actual endpoint
        setTimeout(() => {
            studentsContainer.innerHTML = `
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="selectAllCheckbox">
                                </th>
                                <th>Student</th>
                                <th>Class</th>
                                <th>Due Amount</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTableBody">
                            <!-- Students will be loaded here -->
                        </tbody>
                    </table>
                </div>
            `;
            updateCounts();
        }, 1000);
    }

    function selectAll() {
        const checkboxes = studentsContainer.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = true);
        updateCounts();
    }

    function clearAll() {
        const checkboxes = studentsContainer.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = false);
        updateCounts();
    }

    function updateCounts() {
        const checkedBoxes = studentsContainer.querySelectorAll('input[type="checkbox"]:checked');
        const count = checkedBoxes.length;
        
        selectedCountSpan.textContent = count;
        processPaymentBtn.disabled = count === 0;
        
        // Calculate total amount (placeholder)
        totalAmountSpan.textContent = (count * 5000).toFixed(2);
    }
});
</script>
@endsection

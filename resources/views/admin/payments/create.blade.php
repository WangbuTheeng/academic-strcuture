@extends('layouts.admin')

@section('title', 'Record Payment')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus text-primary me-2"></i>Record Payment
            </h1>
            <p class="text-muted mb-0">Record a new payment for student bills</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Payments
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.fees.payments.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <!-- Payment Information -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Payment Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="student_id">Student <span class="text-danger">*</span></label>
                                    <select name="student_id" id="student_id" class="form-control" required>
                                        <option value="">Select Student</option>
                                        @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->full_name }} ({{ $student->admission_number }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Hidden input for selected bill -->
                                <input type="hidden" name="bill_id" id="bill_id" required>
                                <div class="form-group">
                                    <label>Selected Bill <span class="text-danger">*</span></label>
                                    <div id="selectedBillDisplay" class="form-control" style="background: #f8f9fa; min-height: 38px; display: flex; align-items: center;">
                                        <span class="text-muted">Select a student first to see available bills</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount">Amount <span class="text-danger">*</span></label>
                                    <input type="number" name="amount" id="amount" class="form-control"
                                           step="0.01" min="0.01" required>
                                    <small class="form-text text-muted">Maximum: Rs. <span id="maxAmount">0.00</span></small>

                                    <!-- Quick Amount Buttons -->
                                    <div id="quickAmountButtons" class="mt-2" style="display: none;">
                                        <small class="text-muted d-block mb-1">Quick amounts:</small>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-secondary" onclick="setQuickAmount(25)">25%</button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="setQuickAmount(50)">50%</button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="setQuickAmount(75)">75%</button>
                                            <button type="button" class="btn btn-outline-success" onclick="setQuickAmount(100)">100%</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_date">Payment Date <span class="text-danger">*</span></label>
                                    <input type="date" name="payment_date" id="payment_date" class="form-control" 
                                           value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                                    <select name="payment_method" id="payment_method" class="form-control" required>
                                        <option value="">Select Method</option>
                                        @foreach($paymentMethods as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reference_number">Reference Number</label>
                                    <input type="text" name="reference_number" id="reference_number" class="form-control">
                                    <small class="form-text text-muted">Transaction ID, Check number, etc.</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Remarks</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Bills -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Available Bills</h6>
                        <span id="billCount" class="badge badge-secondary">0</span>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <!-- Loading State -->
                        <div id="billsLoading" style="display: none;" class="text-center py-4">
                            <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                            <p class="text-muted">Loading bills...</p>
                        </div>

                        <!-- No Student Selected -->
                        <div id="noBillsMessage" class="text-center text-muted py-4">
                            <i class="fas fa-user-graduate fa-3x mb-3"></i>
                            <p>Select a student to view available bills</p>
                        </div>

                        <!-- No Bills Found -->
                        <div id="noBillsFound" style="display: none;" class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                            <p>No pending bills found for this student</p>
                            <small>All bills are fully paid!</small>
                        </div>

                        <!-- Bills Container -->
                        <div id="billsContainer">
                            <!-- Bill cards will be dynamically inserted here -->
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Payment Summary</h6>
                    </div>
                    <div class="card-body">
                        <div id="paymentSummary" style="display: none;">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h6 class="text-primary">Bill Total</h6>
                                    <h5 id="summaryBillTotal">Rs. 0.00</h5>
                                </div>
                                <div class="col-4">
                                    <h6 class="text-success">Paying Now</h6>
                                    <h5 id="summaryPayingNow">Rs. 0.00</h5>
                                </div>
                                <div class="col-4">
                                    <h6 class="text-warning">Remaining</h6>
                                    <h5 id="summaryRemaining">Rs. 0.00</h5>
                                </div>
                            </div>
                            <hr>
                            <div class="progress mb-2">
                                <div id="paymentProgress" class="progress-bar bg-success" style="width: 0%"></div>
                            </div>
                            <small class="text-muted">Payment Progress: <span id="paymentPercentage">0%</span></small>
                        </div>

                        <div id="noPaymentSummary" class="text-center text-muted">
                            <i class="fas fa-calculator fa-2x mb-3"></i>
                            <p>Select a bill and enter amount to see payment summary</p>
                        </div>
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block" id="submitBtn" disabled>
                            <i class="fas fa-save me-2"></i>Record Payment
                        </button>
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-block mt-2">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>

                        <!-- Validation Messages -->
                        <div id="validationMessages" class="mt-3" style="display: none;">
                            <div class="alert alert-warning alert-sm">
                                <ul id="validationList" class="mb-0 small"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Toast Notifications -->
<div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

<style>
/* Bill Card Styles */
.bill-card {
    border: 2px solid #e3e6f0;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.bill-card:hover {
    border-color: #4e73df;
    box-shadow: 0 4px 8px rgba(78, 115, 223, 0.15);
    transform: translateY(-2px);
}

.bill-card.selected {
    border-color: #4e73df;
    background: #f8f9ff;
    box-shadow: 0 4px 12px rgba(78, 115, 223, 0.25);
}

.bill-card-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 10px;
}

.bill-number {
    font-weight: bold;
    color: #2c3e50;
    font-size: 14px;
}

.bill-status {
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-partial {
    background: #d1ecf1;
    color: #0c5460;
}

.status-overdue {
    background: #f8d7da;
    color: #721c24;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

.bill-title {
    color: #495057;
    font-size: 13px;
    margin-bottom: 8px;
}

.bill-amount {
    font-size: 18px;
    font-weight: bold;
    color: #e74c3c;
    margin-bottom: 5px;
}

.bill-due-date {
    font-size: 12px;
    color: #6c757d;
}

.due-overdue {
    color: #dc3545;
    font-weight: bold;
}

.due-soon {
    color: #fd7e14;
    font-weight: bold;
}

.bill-card-footer {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
}

.quick-actions {
    display: flex;
    gap: 5px;
}

.quick-action-btn {
    padding: 2px 6px;
    font-size: 10px;
    border-radius: 3px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-pay-full {
    background: #28a745;
    color: white;
}

.btn-pay-partial {
    background: #17a2b8;
    color: white;
}

.quick-action-btn:hover {
    opacity: 0.8;
    transform: scale(1.05);
}

/* Enhanced form styling */
#selectedBillDisplay {
    transition: all 0.3s ease;
}

#selectedBillDisplay:has(.d-flex) {
    background: #e8f5e8 !important;
    border-color: #28a745;
}

/* Progress bar styling */
.progress {
    background-color: #f8f9fa;
}

/* Quick amount buttons */
.btn-group-sm .btn {
    font-size: 11px;
    padding: 2px 8px;
}

/* Loading animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.bill-card {
    animation: fadeIn 0.3s ease-out;
}

/* Enhanced hover effects */
.bill-card:hover .quick-actions {
    opacity: 1;
}

.quick-actions {
    opacity: 0.7;
    transition: opacity 0.2s ease;
}

/* Selected bill highlight */
#selectedBillDisplay .d-flex {
    padding: 8px 12px;
    background: rgba(40, 167, 69, 0.1);
    border-radius: 4px;
    border: 1px solid rgba(40, 167, 69, 0.3);
}

/* Toast notifications */
.toast-notification {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    padding: 12px 16px;
    margin-bottom: 10px;
    border-left: 4px solid #28a745;
    animation: slideIn 0.3s ease-out;
    max-width: 300px;
}

.toast-notification.error {
    border-left-color: #dc3545;
}

.toast-notification.warning {
    border-left-color: #ffc107;
}

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentSelect = document.getElementById('student_id');
    const billIdInput = document.getElementById('bill_id');
    const amountInput = document.getElementById('amount');
    const selectedBillDisplay = document.getElementById('selectedBillDisplay');
    const billsContainer = document.getElementById('billsContainer');
    const billCount = document.getElementById('billCount');

    // UI Elements
    const billsLoading = document.getElementById('billsLoading');
    const noBillsMessage = document.getElementById('noBillsMessage');
    const noBillsFound = document.getElementById('noBillsFound');

    // Setup CSRF token for fetch requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let selectedBill = null;
    let availableBills = [];

    // Function to load bills for a student
    function loadStudentBills(studentId) {
        if (!studentId) {
            showNoBillsMessage();
            clearSelectedBill();
            return;
        }

        // Show loading state
        showBillsLoading();

        fetch(`{{ route('admin.fees.student-bills-by-student') }}?student_id=${studentId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(bills => {
                availableBills = bills;

                if (bills.length === 0) {
                    showNoBillsFound();
                } else {
                    displayBillCards(bills);
                }
            })
            .catch(error => {
                console.error('Error loading bills:', error);
                showError('Error loading bills. Please refresh the page and try again.');
            });
    }

    // Function to display bill cards
    function displayBillCards(bills) {
        hideAllStates();
        billsContainer.style.display = 'block';
        billsContainer.innerHTML = '';
        billCount.textContent = bills.length;

        bills.forEach(bill => {
            const billCard = createBillCard(bill);
            billsContainer.appendChild(billCard);
        });
    }

    // Function to create a bill card
    function createBillCard(bill) {
        const card = document.createElement('div');
        card.className = 'bill-card';
        card.dataset.billId = bill.id;

        // Determine status and due date styling using enhanced data
        const statusClass = getStatusClass(bill.status);
        const dueDateClass = bill.is_overdue ? 'due-overdue' : (bill.days_until_due <= 7 ? 'due-soon' : '');
        const dueDateText = formatDueDate(bill);

        // Add progress bar if bill has payments
        const progressBar = bill.payment_progress > 0 ?
            `<div class="progress mb-2" style="height: 4px;">
                <div class="progress-bar bg-success" style="width: ${bill.payment_progress}%"></div>
            </div>` : '';

        card.innerHTML = `
            <div class="bill-card-header">
                <div class="bill-number">${bill.bill_number}</div>
                <span class="bill-status ${statusClass}">${getStatusText(bill.status)}</span>
            </div>
            <div class="bill-title">${bill.bill_title || 'Academic Fee Bill'}</div>
            ${progressBar}
            <div class="bill-amount">Rs. ${bill.balance_amount}</div>
            <div class="bill-due-date ${dueDateClass}">Due: ${dueDateText}</div>
            <div class="bill-card-footer">
                <div>
                    <small class="text-muted">Total: Rs. ${bill.total_amount}</small><br>
                    <small class="text-muted">Paid: Rs. ${bill.paid_amount}</small>
                    ${bill.payment_progress > 0 ? `<br><small class="text-success">${bill.payment_progress}% paid</small>` : ''}
                </div>
                <div class="quick-actions">
                    <button type="button" class="quick-action-btn btn-pay-full" onclick="selectBillWithAmount('${bill.id}', '${bill.balance_amount}')">
                        Pay Full
                    </button>
                    <button type="button" class="quick-action-btn btn-pay-partial" onclick="selectBillForPartial('${bill.id}')">
                        Partial
                    </button>
                </div>
            </div>
        `;

        // Add click handler for card selection
        card.addEventListener('click', function(e) {
            // Don't trigger if clicking on quick action buttons
            if (e.target.classList.contains('quick-action-btn')) {
                return;
            }
            selectBill(bill);
        });

        return card;
    }

    // Function to select a bill
    function selectBill(bill) {
        selectedBill = bill;
        billIdInput.value = bill.id;

        // Update selected bill display
        selectedBillDisplay.innerHTML = `
            <div class="d-flex justify-content-between align-items-center w-100">
                <div>
                    <strong>${bill.bill_number}</strong><br>
                    <small class="text-muted">Balance: Rs. ${bill.balance_amount}</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelectedBill()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        // Update visual selection
        document.querySelectorAll('.bill-card').forEach(card => {
            card.classList.remove('selected');
        });
        document.querySelector(`[data-bill-id="${bill.id}"]`).classList.add('selected');

        // Set amount to bill balance by default
        const balanceAmount = parseFloat(bill.balance_amount.replace(/,/g, ''));
        amountInput.value = balanceAmount;
        amountInput.max = balanceAmount;

        // Update max amount display if element exists
        const maxAmountElement = document.getElementById('maxAmount');
        if (maxAmountElement) {
            maxAmountElement.textContent = bill.balance_amount;
        }

        // Show quick amount buttons
        const quickAmountButtons = document.getElementById('quickAmountButtons');
        if (quickAmountButtons) {
            quickAmountButtons.style.display = 'block';
        }

        // Store current bill balance for quick amount calculations
        window.currentBillBalance = balanceAmount;
        window.currentBillTotal = parseFloat(bill.total_amount.replace(/,/g, ''));

        // Update payment summary
        updatePaymentSummary();

        // Show success toast
        showToast(`Selected bill ${bill.bill_number} - Rs. ${bill.balance_amount} due`, 'success');
    }

    // Load bills when student changes
    studentSelect.addEventListener('change', function() {
        loadStudentBills(this.value);
    });

    // Load bills on page load if student is pre-selected
    if (studentSelect.value) {
        setTimeout(() => {
            loadStudentBills(studentSelect.value);
        }, 100);
    }

    // Add real-time validation listeners
    amountInput.addEventListener('input', updatePaymentSummary);
    document.getElementById('payment_method').addEventListener('change', validateForm);
    document.getElementById('payment_date').addEventListener('change', validateForm);
    studentSelect.addEventListener('change', validateForm);

    // Utility functions
    function getStatusClass(status) {
        switch(status.toLowerCase()) {
            case 'overdue': return 'status-overdue';
            case 'partial': return 'status-partial';
            default: return 'status-pending';
        }
    }

    function getStatusText(status) {
        switch(status.toLowerCase()) {
            case 'overdue': return 'OVERDUE';
            case 'partial': return 'PARTIAL';
            case 'pending': return 'PENDING';
            default: return status.toUpperCase();
        }
    }

    function getDueDateClass(dueDate) {
        // Use the enhanced data if available
        if (typeof dueDate === 'object' && dueDate.due_date_status) {
            switch(dueDate.due_date_status) {
                case 'overdue': return 'due-overdue';
                case 'due_soon': return 'due-soon';
                default: return '';
            }
        }

        // Fallback to date calculation
        const today = new Date();
        const due = new Date(dueDate);
        const diffTime = due - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays < 0) return 'due-overdue';
        if (diffDays <= 7) return 'due-soon';
        return '';
    }

    function formatDueDate(dueDate) {
        // Use enhanced data if available
        if (typeof dueDate === 'object' && dueDate.due_date_formatted) {
            const days = dueDate.days_until_due;
            if (days < 0) {
                return `${dueDate.due_date_formatted} (${Math.abs(days)} days overdue)`;
            } else if (days === 0) {
                return `${dueDate.due_date_formatted} (Due today)`;
            } else if (days <= 7) {
                return `${dueDate.due_date_formatted} (${days} days left)`;
            }
            return dueDate.due_date_formatted;
        }

        // Fallback to date calculation
        const today = new Date();
        const due = new Date(dueDate);
        const diffTime = due - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        const formatted = due.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });

        if (diffDays < 0) {
            return `${formatted} (${Math.abs(diffDays)} days overdue)`;
        } else if (diffDays === 0) {
            return `${formatted} (Due today)`;
        } else if (diffDays <= 7) {
            return `${formatted} (${diffDays} days left)`;
        }
        return formatted;
    }

    // UI State functions
    function showBillsLoading() {
        hideAllStates();
        billsLoading.style.display = 'block';
    }

    function showNoBillsMessage() {
        hideAllStates();
        noBillsMessage.style.display = 'block';
        billCount.textContent = '0';
    }

    function showNoBillsFound() {
        hideAllStates();
        noBillsFound.style.display = 'block';
        billCount.textContent = '0';
    }

    function hideAllStates() {
        billsLoading.style.display = 'none';
        noBillsMessage.style.display = 'none';
        noBillsFound.style.display = 'none';
        billsContainer.style.display = 'none';
    }

    function showError(message) {
        hideAllStates();
        billsContainer.innerHTML = `
            <div class="text-center text-danger py-4">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <p>${message}</p>
            </div>
        `;
        billsContainer.style.display = 'block';
    }

    function clearSelectedBill() {
        selectedBill = null;
        billIdInput.value = '';
        selectedBillDisplay.innerHTML = '<span class="text-muted">No bill selected</span>';
        amountInput.value = '';
        amountInput.max = '';

        // Update max amount display if element exists
        const maxAmountElement = document.getElementById('maxAmount');
        if (maxAmountElement) {
            maxAmountElement.textContent = '0.00';
        }

        // Hide quick amount buttons
        const quickAmountButtons = document.getElementById('quickAmountButtons');
        if (quickAmountButtons) {
            quickAmountButtons.style.display = 'none';
        }

        // Clear current bill balance
        window.currentBillBalance = 0;
        window.currentBillTotal = 0;

        // Hide payment summary
        hidePaymentSummary();

        // Remove visual selection
        document.querySelectorAll('.bill-card').forEach(card => {
            card.classList.remove('selected');
        });

        // Update validation
        validateForm();
    }

    // Form submission handling
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('submit', function(e) {
        console.log('Form submission started');

        // Validate required fields
        const studentId = document.getElementById('student_id').value;
        const billId = document.getElementById('bill_id').value;
        const amount = document.getElementById('amount').value;
        const paymentDate = document.getElementById('payment_date').value;
        const paymentMethod = document.getElementById('payment_method').value;

        if (!studentId) {
            e.preventDefault();
            alert('Please select a student.');
            return false;
        }

        if (!billId) {
            e.preventDefault();
            alert('Please select a bill from the available bills.');
            return false;
        }

        if (!amount || parseFloat(amount) <= 0) {
            e.preventDefault();
            alert('Please enter a valid amount.');
            return false;
        }

        if (!paymentDate) {
            e.preventDefault();
            alert('Please select a payment date.');
            return false;
        }

        if (!paymentMethod) {
            e.preventDefault();
            alert('Please select a payment method.');
            return false;
        }

        // Check if amount exceeds maximum
        const maxAmount = parseFloat(amountInput.max);
        if (maxAmount && parseFloat(amount) > maxAmount) {
            e.preventDefault();
            alert(`Amount cannot exceed Rs. ${maxAmount.toFixed(2)}`);
            return false;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

        console.log('Form validation passed, submitting...');
        return true;
    });
});

// Global functions for quick actions (accessible from onclick handlers)
function selectBillWithAmount(billId, amount) {
    const bill = availableBills.find(b => b.id == billId);
    if (bill) {
        selectBill(bill);
        document.getElementById('amount').value = parseFloat(amount.toString().replace(/,/g, ''));
    }
}

function selectBillForPartial(billId) {
    const bill = availableBills.find(b => b.id == billId);
    if (bill) {
        selectBill(bill);
        document.getElementById('amount').value = '';
        document.getElementById('amount').focus();
    }
}

function clearSelectedBill() {
    const billIdInput = document.getElementById('bill_id');
    const selectedBillDisplay = document.getElementById('selectedBillDisplay');
    const amountInput = document.getElementById('amount');

    billIdInput.value = '';
    selectedBillDisplay.innerHTML = '<span class="text-muted">No bill selected</span>';
    amountInput.value = '';
    amountInput.max = '';

    // Update max amount display if element exists
    const maxAmountElement = document.getElementById('maxAmount');
    if (maxAmountElement) {
        maxAmountElement.textContent = '0.00';
    }

    // Hide quick amount buttons
    const quickAmountButtons = document.getElementById('quickAmountButtons');
    if (quickAmountButtons) {
        quickAmountButtons.style.display = 'none';
    }

    // Clear current bill balance
    window.currentBillBalance = 0;
    window.currentBillTotal = 0;

    // Hide payment summary
    hidePaymentSummary();

    // Remove visual selection
    document.querySelectorAll('.bill-card').forEach(card => {
        card.classList.remove('selected');
    });

    // Update validation
    validateForm();
}

// Toast notification function
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast-notification ${type}`;
    toast.innerHTML = `
        <div style="display: flex; justify-content: between; align-items: center;">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; font-size: 18px; margin-left: 10px; cursor: pointer;">&times;</button>
        </div>
    `;

    toastContainer.appendChild(toast);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// Quick amount function
function setQuickAmount(percentage) {
    const amountInput = document.getElementById('amount');
    if (window.currentBillBalance && window.currentBillBalance > 0) {
        const amount = (window.currentBillBalance * percentage / 100).toFixed(2);
        amountInput.value = amount;

        // Add visual feedback
        amountInput.style.background = '#e8f5e8';
        setTimeout(() => {
            amountInput.style.background = '';
        }, 1000);

        // Update payment summary
        updatePaymentSummary();
    }
}

// Payment summary functions
function updatePaymentSummary() {
    const amountInput = document.getElementById('amount');
    const payingAmount = parseFloat(amountInput.value) || 0;

    if (window.currentBillBalance > 0 && payingAmount > 0) {
        showPaymentSummary(payingAmount);
    } else {
        hidePaymentSummary();
    }

    // Update form validation
    validateForm();
}

function showPaymentSummary(payingAmount) {
    const billTotal = window.currentBillBalance;
    const remaining = Math.max(0, billTotal - payingAmount);
    const percentage = Math.min(100, (payingAmount / billTotal) * 100);

    document.getElementById('summaryBillTotal').textContent = `Rs. ${billTotal.toFixed(2)}`;
    document.getElementById('summaryPayingNow').textContent = `Rs. ${payingAmount.toFixed(2)}`;
    document.getElementById('summaryRemaining').textContent = `Rs. ${remaining.toFixed(2)}`;
    document.getElementById('paymentProgress').style.width = `${percentage}%`;
    document.getElementById('paymentPercentage').textContent = `${percentage.toFixed(1)}%`;

    document.getElementById('paymentSummary').style.display = 'block';
    document.getElementById('noPaymentSummary').style.display = 'none';
}

function hidePaymentSummary() {
    document.getElementById('paymentSummary').style.display = 'none';
    document.getElementById('noPaymentSummary').style.display = 'block';
}

// Real-time form validation
function validateForm() {
    const studentId = document.getElementById('student_id').value;
    const billId = document.getElementById('bill_id').value;
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const paymentMethod = document.getElementById('payment_method').value;
    const paymentDate = document.getElementById('payment_date').value;

    const errors = [];

    if (!studentId) errors.push('Please select a student');
    if (!billId) errors.push('Please select a bill');
    if (amount <= 0) errors.push('Please enter a valid amount');
    if (amount > window.currentBillBalance) errors.push(`Amount cannot exceed Rs. ${window.currentBillBalance.toFixed(2)}`);
    if (!paymentMethod) errors.push('Please select a payment method');
    if (!paymentDate) errors.push('Please select a payment date');

    const submitBtn = document.getElementById('submitBtn');
    const validationMessages = document.getElementById('validationMessages');
    const validationList = document.getElementById('validationList');

    if (errors.length > 0) {
        submitBtn.disabled = true;
        submitBtn.classList.remove('btn-primary');
        submitBtn.classList.add('btn-secondary');

        validationList.innerHTML = errors.map(error => `<li>${error}</li>`).join('');
        validationMessages.style.display = 'block';
    } else {
        submitBtn.disabled = false;
        submitBtn.classList.remove('btn-secondary');
        submitBtn.classList.add('btn-primary');
        validationMessages.style.display = 'none';
    }
}
</script>
@endsection

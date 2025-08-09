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
                                <div class="form-group">
                                    <label for="bill_id">Bill <span class="text-danger">*</span></label>
                                    <select name="bill_id" id="bill_id" class="form-control" required>
                                        <option value="">Select Bill</option>
                                        @if(isset($bills) && $bills->count() > 0)
                                        @foreach($bills as $bill)
                                        <option value="{{ $bill->id }}" {{ request('bill_id') == $bill->id ? 'selected' : '' }}>
                                            {{ $bill->bill_number }} - Rs. {{ number_format($bill->balance_amount, 2) }} (Due: {{ $bill->due_date->format('M d, Y') }})
                                        </option>
                                        @endforeach
                                        @endif
                                    </select>
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

            <!-- Bill Summary -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Bill Summary</h6>
                    </div>
                    <div class="card-body">
                        <div id="billSummary" style="display: none;">
                            <div class="text-center">
                                <h4 class="text-primary">Rs. <span id="billTotal">0.00</span></h4>
                                <p class="text-muted">Total Amount</p>
                                
                                <h4 class="text-success">Rs. <span id="billPaid">0.00</span></h4>
                                <p class="text-muted">Paid Amount</p>
                                
                                <h4 class="text-danger">Rs. <span id="billBalance">0.00</span></h4>
                                <p class="text-muted">Balance Amount</p>
                            </div>
                        </div>
                        
                        <div id="noBillSelected" class="text-center text-muted">
                            <i class="fas fa-file-invoice fa-3x mb-3"></i>
                            <p>Select a student and bill to view summary</p>
                        </div>
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block" id="submitBtn">
                            <i class="fas fa-save me-2"></i>Record Payment
                        </button>
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-block mt-2">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentSelect = document.getElementById('student_id');
    const billSelect = document.getElementById('bill_id');
    const amountInput = document.getElementById('amount');

    // Setup CSRF token for fetch requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Function to load bills for a student
    function loadStudentBills(studentId) {
        if (!studentId) {
            billSelect.innerHTML = '<option value="">Select Bill</option>';
            hideBillSummary();
            return;
        }

        // Show loading state
        billSelect.innerHTML = '<option value="">Loading bills...</option>';
        billSelect.disabled = true;

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
                billSelect.innerHTML = '<option value="">Select Bill</option>';
                billSelect.disabled = false;

                if (bills.length === 0) {
                    billSelect.innerHTML = '<option value="">No pending bills found</option>';
                    return;
                }

                bills.forEach(bill => {
                    const option = document.createElement('option');
                    option.value = bill.id;
                    option.textContent = `${bill.bill_number} - Rs. ${bill.balance_amount} (Due: ${bill.due_date})`;
                    billSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading bills:', error);
                billSelect.innerHTML = '<option value="">Error loading bills</option>';
                billSelect.disabled = false;

                // Show user-friendly error message
                alert('Error loading bills. Please refresh the page and try again.');
            });

        hideBillSummary();
    }

    // Load bills when student changes
    studentSelect.addEventListener('change', function() {
        loadStudentBills(this.value);
    });

    // Load bills on page load if student is pre-selected
    if (studentSelect.value) {
        // Add a small delay to ensure everything is loaded
        setTimeout(() => {
            loadStudentBills(studentSelect.value);
        }, 100);
    }
    
    // Load bill details when bill changes
    billSelect.addEventListener('change', function() {
        const billId = this.value;

        if (billId) {
            fetch(`{{ url('admin/fees/bills') }}/${billId}/details`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(bill => {
                    showBillSummary(bill);
                    amountInput.max = bill.balance_amount;
                    amountInput.value = bill.balance_amount;
                })
                .catch(error => {
                    console.error('Error loading bill details:', error);
                    hideBillSummary();
                });
        } else {
            hideBillSummary();
        }
    });
    
    function showBillSummary(bill) {
        document.getElementById('billTotal').textContent = parseFloat(bill.total_amount).toFixed(2);
        document.getElementById('billPaid').textContent = parseFloat(bill.paid_amount).toFixed(2);
        document.getElementById('billBalance').textContent = parseFloat(bill.balance_amount).toFixed(2);
        document.getElementById('maxAmount').textContent = parseFloat(bill.balance_amount).toFixed(2);
        
        document.getElementById('billSummary').style.display = 'block';
        document.getElementById('noBillSelected').style.display = 'none';
    }
    
    function hideBillSummary() {
        document.getElementById('billSummary').style.display = 'none';
        document.getElementById('noBillSelected').style.display = 'block';
        amountInput.max = '';
        amountInput.value = '';
        document.getElementById('maxAmount').textContent = '0.00';
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
            alert('Please select a bill.');
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
</script>
@endsection

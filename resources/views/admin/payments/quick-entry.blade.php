@extends('layouts.admin')

@section('title', 'Quick Payment Entry')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-bolt text-primary me-2"></i>Quick Payment Entry
            </h1>
            <p class="text-muted mb-0">Fast payment processing for students</p>
        </div>
        <div>
            <a href="{{ route('admin.fees.payments.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-2"></i>All Payments
            </a>
        </div>
    </div>

    <!-- Quick Payment Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-credit-card me-2"></i>Payment Information
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.fees.payments.process-quick-entry') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="student_id" class="form-label">Student <span class="text-danger">*</span></label>
                                <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                                    <option value="">Select Student</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->full_name }} ({{ $student->admission_number }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="bill_id" class="form-label">Bill <span class="text-danger">*</span></label>
                                <select name="bill_id" id="bill_id" class="form-select @error('bill_id') is-invalid @enderror" required>
                                    <option value="">Select Bill</option>
                                </select>
                                @error('bill_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount (Rs.) <span class="text-danger">*</span></label>
                                <input type="number" name="amount" id="amount" step="0.01" min="0.01" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount') }}" 
                                       placeholder="0.00" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                    <option value="">Select Method</option>
                                    @foreach($paymentMethods as $key => $method)
                                        <option value="{{ $key }}" {{ old('payment_method') == $key ? 'selected' : '' }}>
                                            {{ $method }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" id="payment_date" 
                                       class="form-control @error('payment_date') is-invalid @enderror" 
                                       value="{{ old('payment_date', date('Y-m-d')) }}" 
                                       max="{{ date('Y-m-d') }}" required>
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="reference_number" class="form-label">Reference Number</label>
                                <input type="text" name="reference_number" id="reference_number" 
                                       class="form-control @error('reference_number') is-invalid @enderror" 
                                       value="{{ old('reference_number') }}" 
                                       placeholder="Optional reference number">
                                @error('reference_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea name="remarks" id="remarks" rows="3" 
                                      class="form-control @error('remarks') is-invalid @enderror" 
                                      placeholder="Optional remarks">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.fees.payments.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Process Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle me-2"></i>Payment Methods
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">Available Methods:</h6>
                    <ul class="list-unstyled small">
                        <li><strong>Cash:</strong> Direct cash payment</li>
                        <li><strong>Bank Transfer:</strong> Electronic transfer</li>
                        <li><strong>Online:</strong> Online payment gateway</li>
                        <li><strong>Cheque:</strong> Bank cheque payment</li>
                        <li><strong>Card:</strong> Credit/Debit card</li>
                        <li><strong>Mobile Wallet:</strong> Digital wallet</li>
                    </ul>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Tip:</strong> Select a student first to see their pending bills.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Load bills when student is selected
    document.getElementById('student_id').addEventListener('change', function() {
        const studentId = this.value;
        const billSelect = document.getElementById('bill_id');
        
        // Clear existing options
        billSelect.innerHTML = '<option value="">Select Bill</option>';
        
        if (studentId) {
            fetch(`{{ route('admin.fees.student-bills-by-student') }}?student_id=${studentId}`)
                .then(response => response.json())
                .then(bills => {
                    bills.forEach(bill => {
                        const option = document.createElement('option');
                        option.value = bill.id;
                        option.textContent = `${bill.bill_number} - Rs. ${bill.balance_amount} (Due: ${bill.due_date})`;
                        billSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading bills:', error));
        }
    });

    // Set max amount based on selected bill
    document.getElementById('bill_id').addEventListener('change', function() {
        const billId = this.value;
        const amountInput = document.getElementById('amount');
        
        if (billId) {
            const billText = this.options[this.selectedIndex].text;
            const balanceMatch = billText.match(/Rs\. ([\d,]+\.?\d*)/);
            if (balanceMatch) {
                const balance = parseFloat(balanceMatch[1].replace(/,/g, ''));
                amountInput.max = balance;
                amountInput.value = balance;
            }
        }
    });
</script>
@endpush

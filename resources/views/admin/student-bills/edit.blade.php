@extends('layouts.admin')

@section('title', 'Edit Student Bill')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit text-primary me-2"></i>Edit Bill #{{ $bill->bill_number }}
            </h1>
            <p class="text-muted mb-0">Modify student bill details and items</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.student-bills.show', $bill) }}" class="btn btn-info">
                <i class="fas fa-eye me-2"></i>View Bill
            </a>
            <a href="{{ route('admin.student-bills.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Bills
            </a>
        </div>
    </div>

    <form action="{{ route('admin.student-bills.update', $bill) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Bill Information -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Bill Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="student_id">Student</label>
                                    <select name="student_id" id="student_id" class="form-control" required>
                                        @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ $bill->student_id == $student->id ? 'selected' : '' }}>
                                            {{ $student->full_name }} ({{ $student->admission_number }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="academic_year_id">Academic Year</label>
                                    <select name="academic_year_id" id="academic_year_id" class="form-control" required>
                                        @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ $bill->academic_year_id == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bill_date">Bill Date</label>
                                    <input type="date" name="bill_date" id="bill_date" class="form-control" 
                                           value="{{ $bill->bill_date->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="due_date">Due Date</label>
                                    <input type="date" name="due_date" id="due_date" class="form-control" 
                                           value="{{ $bill->due_date->format('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="3">{{ $bill->remarks }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Bill Items -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Bill Items</h6>
                        <button type="button" class="btn btn-sm btn-success" onclick="addCustomFee()">
                            <i class="fas fa-plus me-1"></i>Add Custom Fee
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="billItems">
                            @foreach($bill->billItems as $index => $item)
                            <div class="bill-item mb-3 p-3 border rounded">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Description</label>
                                        <input type="text" name="items[{{ $index }}][description]" 
                                               class="form-control" value="{{ $item->description }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Category</label>
                                        <input type="text" name="items[{{ $index }}][fee_category]" 
                                               class="form-control" value="{{ $item->fee_category }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Amount</label>
                                        <input type="number" name="items[{{ $index }}][amount]" 
                                               class="form-control item-amount" value="{{ $item->final_amount }}" 
                                               step="0.01" min="0" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-danger btn-block" onclick="removeItem(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="text-right mt-3">
                            <h5>Total Amount: Rs. <span id="totalAmount">{{ number_format($bill->total_amount, 2) }}</span></h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Current Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <h4 class="text-primary">Rs. {{ number_format($bill->total_amount, 2) }}</h4>
                            <p class="text-muted">Current Total</p>
                            
                            <h4 class="text-success">Rs. {{ number_format($bill->paid_amount, 2) }}</h4>
                            <p class="text-muted">Paid Amount</p>
                            
                            <h4 class="text-danger">Rs. {{ number_format($bill->balance_amount, 2) }}</h4>
                            <p class="text-muted">Balance Amount</p>
                        </div>
                        
                        @if($bill->paid_amount > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            This bill has payments. Changes may affect payment records.
                        </div>
                        @endif
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save me-2"></i>Update Bill
                        </button>
                        <a href="{{ route('admin.student-bills.show', $bill) }}" class="btn btn-secondary btn-block mt-2">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let itemIndex = {{ $bill->billItems->count() }};

function addCustomFee() {
    const billItems = document.getElementById('billItems');
    const newItem = document.createElement('div');
    newItem.className = 'bill-item mb-3 p-3 border rounded';
    newItem.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <label>Description</label>
                <input type="text" name="items[${itemIndex}][description]" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Category</label>
                <input type="text" name="items[${itemIndex}][fee_category]" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Amount</label>
                <input type="number" name="items[${itemIndex}][amount]" class="form-control item-amount" 
                       step="0.01" min="0" required>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-danger btn-block" onclick="removeItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    billItems.appendChild(newItem);
    itemIndex++;
    updateTotal();
}

function removeItem(button) {
    button.closest('.bill-item').remove();
    updateTotal();
}

function updateTotal() {
    const amounts = document.querySelectorAll('.item-amount');
    let total = 0;
    amounts.forEach(amount => {
        total += parseFloat(amount.value) || 0;
    });
    document.getElementById('totalAmount').textContent = total.toFixed(2);
}

// Update total when amounts change
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('item-amount')) {
        updateTotal();
    }
});
</script>
@endsection

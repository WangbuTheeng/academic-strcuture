@extends('layouts.admin')

@section('title', 'Student Bill Details')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-invoice text-primary me-2"></i>Bill #{{ $studentBill->bill_number }}
            </h1>
            <p class="text-muted mb-0">Student bill details and payment history</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.student-bills.preview', $studentBill) }}" class="btn btn-success" target="_blank">
                <i class="fas fa-file-pdf me-2"></i>View Bill
            </a>
            <a href="{{ route('admin.student-bills.edit', $studentBill) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit Bill
            </a>
            <a href="{{ route('admin.student-bills.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Bills
            </a>
        </div>
    </div>

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
                            <p><strong>Student:</strong> {{ $studentBill->student->full_name ?? 'N/A' }}</p>
                            <p><strong>Admission Number:</strong> {{ $studentBill->student->admission_number ?? 'N/A' }}</p>
                            <p><strong>Class:</strong> {{ $studentBill->student?->currentEnrollment?->class?->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Bill Date:</strong> {{ $studentBill->bill_date?->format('M d, Y') ?? 'N/A' }}</p>
                            <p><strong>Due Date:</strong> {{ $studentBill->due_date?->format('M d, Y') ?? 'N/A' }}</p>
                            <p><strong>Status:</strong>
                                <span class="badge badge-{{ $studentBill->status === 'paid' ? 'success' : ($studentBill->status === 'partial' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($studentBill->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bill Items -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bill Items</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($studentBill->billItems as $item)
                                <tr>
                                    <td>{{ $item->description }}</td>
                                    <td>{{ $item->fee_category }}</td>
                                    <td>NRs. {{ number_format($item->final_amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-info">
                                    <th colspan="2">Total Amount</th>
                                    <th>NRs. {{ number_format($studentBill->total_amount, 2) }}</th>
                                </tr>
                                <tr class="table-success">
                                    <th colspan="2">Paid Amount</th>
                                    <th>NRs. {{ number_format($studentBill->paid_amount, 2) }}</th>
                                </tr>
                                <tr class="table-warning">
                                    <th colspan="2">Balance Amount</th>
                                    <th>NRs. {{ number_format($studentBill->balance_amount, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Summary</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h4 class="text-primary">NRs. {{ number_format($studentBill->total_amount, 2) }}</h4>
                        <p class="text-muted">Total Amount</p>

                        <h4 class="text-success">NRs. {{ number_format($studentBill->paid_amount, 2) }}</h4>
                        <p class="text-muted">Paid Amount</p>

                        <h4 class="text-danger">NRs. {{ number_format($studentBill->balance_amount, 2) }}</h4>
                        <p class="text-muted">Balance Amount</p>
                    </div>

                    @if($studentBill->balance_amount > 0)
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.fees.payments.create', ['bill_id' => $studentBill->id]) }}" class="btn btn-success btn-block">
                            <i class="fas fa-plus me-2"></i>Add Payment
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment History -->
            @if($studentBill->payments->count() > 0)
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment History</h6>
                </div>
                <div class="card-body">
                    @foreach($studentBill->payments as $payment)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <small class="text-muted">{{ $payment->payment_date->format('M d, Y') }}</small><br>
                            <span class="badge badge-{{ $payment->status === 'verified' ? 'success' : 'warning' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                        <div class="text-right">
                            <strong>NRs. {{ number_format($payment->amount, 2) }}</strong><br>
                            <small class="text-muted">{{ $payment->payment_method }}</small>
                        </div>
                    </div>
                    <hr>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

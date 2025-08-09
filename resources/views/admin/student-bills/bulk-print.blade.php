<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Bills Print</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
            body { margin: 0; padding: 0; }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        
        .bill-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto 40px;
            border: 1px solid #ddd;
            padding: 20px;
            background: white;
        }
        
        .school-header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .school-logo {
            max-height: 60px;
            margin-bottom: 10px;
        }
        
        .school-name {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
            color: #333;
        }
        
        .school-address {
            font-size: 11px;
            color: #666;
            margin: 2px 0;
        }
        
        .bill-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0;
            color: #333;
            text-transform: uppercase;
        }
        
        .bill-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .bill-info-left, .bill-info-right {
            width: 48%;
        }
        
        .info-row {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        
        .student-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .fee-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .fee-table th,
        .fee-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .fee-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .fee-table .amount {
            text-align: right;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #e9ecef;
        }
        
        .payment-info {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .print-controls {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .btn {
            padding: 8px 16px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        
        .status-overdue {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="print-controls no-print">
        <h3>Bulk Bills Print Preview</h3>
        <p>{{ $bills->count() }} bills ready for printing</p>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print All Bills
        </button>
        <a href="{{ route('admin.student-bills.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Bills
        </a>
    </div>

    @foreach($bills as $index => $bill)
        <div class="bill-container">
            <!-- School Header -->
            <div class="school-header">
                @if(auth()->user()->school->logo)
                    <img src="{{ asset('storage/' . auth()->user()->school->logo) }}" alt="School Logo" class="school-logo">
                @endif
                <div class="school-name">{{ auth()->user()->school->name }}</div>
                @if(auth()->user()->school->address)
                    <div class="school-address">{{ auth()->user()->school->address }}</div>
                @endif
                @if(auth()->user()->school->phone)
                    <div class="school-address">Phone: {{ auth()->user()->school->phone }}</div>
                @endif
                @if(auth()->user()->school->email)
                    <div class="school-address">Email: {{ auth()->user()->school->email }}</div>
                @endif
            </div>

            <!-- Bill Title -->
            <div class="bill-title">Fee Bill</div>

            <!-- Bill Information -->
            <div class="bill-info">
                <div class="bill-info-left">
                    <div class="info-row">
                        <span class="info-label">Bill No:</span>
                        <span>{{ $bill->bill_number }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Bill Date:</span>
                        <span>{{ $bill->bill_date->format('d M, Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Due Date:</span>
                        <span>{{ $bill->due_date->format('d M, Y') }}</span>
                    </div>
                </div>
                <div class="bill-info-right">
                    <div class="info-row">
                        <span class="info-label">Academic Year:</span>
                        <span>{{ $bill->academicYear->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="status-badge status-{{ strtolower($bill->status) }}">
                            {{ ucfirst($bill->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Student Details -->
            <div class="student-details">
                <h4 style="margin: 0 0 10px 0;">Student Information</h4>
                <div style="display: flex; justify-content: space-between;">
                    <div>
                        <div class="info-row">
                            <span class="info-label">Name:</span>
                            <span>{{ $bill->student->full_name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Admission No:</span>
                            <span>{{ $bill->student->admission_number }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="info-row">
                            <span class="info-label">Class:</span>
                            <span>{{ $bill->class?->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Program:</span>
                            <span>{{ $bill->program?->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fee Details Table -->
            <table class="fee-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">S.N.</th>
                        <th>Fee Description</th>
                        <th style="width: 100px;">Category</th>
                        <th style="width: 120px;" class="amount">Amount (Rs.)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bill->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->fee_name }}</td>
                            <td>{{ $item->fee_category }}</td>
                            <td class="amount">{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                    
                    <!-- Subtotal -->
                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: bold;">Subtotal:</td>
                        <td class="amount" style="font-weight: bold;">{{ number_format($bill->subtotal, 2) }}</td>
                    </tr>
                    
                    <!-- Discount -->
                    @if($bill->discount_amount > 0)
                        <tr>
                            <td colspan="3" style="text-align: right;">Discount:</td>
                            <td class="amount">-{{ number_format($bill->discount_amount, 2) }}</td>
                        </tr>
                    @endif
                    
                    <!-- Tax -->
                    @if($bill->tax_amount > 0)
                        <tr>
                            <td colspan="3" style="text-align: right;">Tax:</td>
                            <td class="amount">{{ number_format($bill->tax_amount, 2) }}</td>
                        </tr>
                    @endif
                    
                    <!-- Total -->
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right; font-weight: bold;">Total Amount:</td>
                        <td class="amount" style="font-weight: bold;">{{ number_format($bill->total_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Payment Information -->
            <div class="payment-info">
                <h5 style="margin: 0 0 10px 0;">Payment Information</h5>
                <div style="display: flex; justify-content: space-between;">
                    <div>
                        <div class="info-row">
                            <span class="info-label">Amount Paid:</span>
                            <span>Rs. {{ number_format($bill->paid_amount, 2) }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="info-row">
                            <span class="info-label">Balance Due:</span>
                            <span style="font-weight: bold; color: #dc3545;">
                                Rs. {{ number_format($bill->total_amount - $bill->paid_amount, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>This is a computer-generated bill. Please keep this for your records.</p>
                <p>For any queries, please contact the school office.</p>
                <p>Generated on: {{ now()->format('d M, Y h:i A') }}</p>
            </div>
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <script>
        // Auto-print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>

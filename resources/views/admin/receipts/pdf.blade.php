<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $receipt->receipt_number ?? $receipt->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        /* Half A4 page sizing for PDF */
        .receipt-container {
            width: 210mm;
            height: 148mm;
            margin: 0 auto;
            padding: 15mm;
            background: white;
            position: relative;
        }

        /* Header with logo and institution details */
        .receipt-header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
        }

        .logo-section {
            width: 90px;
            height: 90px;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .logo-section img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 10px;
            border: 3px solid #e8e8e8;
            background: #f8f9fa;
            padding: 3px;
        }

        .logo-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: bold;
            border-radius: 10px;
            border: 3px solid #e8e8e8;
        }

        .institution-details {
            flex: 1;
            text-align: center;
        }

        .institution-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .institution-address {
            font-size: 12px;
            color: #666;
            margin-bottom: 2px;
        }

        .receipt-title {
            text-align: center;
            background: #34495e;
            color: white;
            padding: 8px;
            margin: 15px -15mm;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        /* Receipt content */
        .receipt-content {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .left-section, .right-section {
            flex: 1;
        }

        .info-group {
            margin-bottom: 15px;
        }

        .info-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 13px;
            color: #333;
            font-weight: 600;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }

        /* Payment details table */
        .payment-details {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }

        .payment-details th,
        .payment-details td {
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 12px;
        }

        .payment-details th {
            background: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }

        .amount-cell {
            text-align: right;
            font-weight: bold;
            color: #27ae60;
        }

        /* Footer */
        .receipt-footer {
            position: absolute;
            bottom: 15mm;
            left: 15mm;
            right: 15mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        .signature-section {
            text-align: center;
        }

        .signature-line {
            width: 150px;
            border-bottom: 1px solid #333;
            margin: 20px auto 5px;
        }

        .signature-label {
            font-size: 10px;
            color: #666;
        }

        .receipt-date {
            font-size: 10px;
            color: #666;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #27ae60;
            color: white;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <div class="logo-section">
                @if(isset($instituteSettings->institution_logo) && $instituteSettings->institution_logo)
                    <img src="{{ public_path('storage/' . $instituteSettings->institution_logo) }}" alt="Institution Logo">
                @else
                    <div class="logo-placeholder">
                        {{ substr($instituteSettings->institution_name ?? 'AMS', 0, 3) }}
                    </div>
                @endif
            </div>

            <div class="institution-details">
                <div class="institution-name">{{ $instituteSettings->institution_name ?? 'Academic Institution' }}</div>
                <div class="institution-address">{{ $instituteSettings->institution_address ?? 'Institution Address' }}</div>
                <div class="institution-address">Phone: {{ $instituteSettings->institution_phone ?? '+977-1-XXXXXXX' }}</div>
                <div class="institution-address">Email: {{ $instituteSettings->institution_email ?? 'info@institution.edu.np' }}</div>
            </div>
        </div>

        <div class="receipt-title">PAYMENT RECEIPT</div>

        <!-- Receipt Content -->
        <div class="receipt-content">
            <div class="left-section">
                <div class="info-group">
                    <div class="info-label">Receipt No:</div>
                    <div class="info-value">{{ $receipt->receipt_number ?? $receipt->id }}</div>
                </div>

                <div class="info-group">
                    <div class="info-label">Date:</div>
                    <div class="info-value">{{ $receipt->receipt_date->format('M d, Y') }}</div>
                </div>

                <div class="info-group">
                    <div class="info-label">Payment Method:</div>
                    <div class="info-value">{{ ucwords(str_replace('_', ' ', $receipt->payment_method)) }}</div>
                </div>

                @if($receipt->payment->reference_number)
                <div class="info-group">
                    <div class="info-label">Reference No:</div>
                    <div class="info-value">{{ $receipt->payment->reference_number }}</div>
                </div>
                @endif
            </div>

            <div class="right-section">
                <div class="info-group">
                    <div class="info-label">Student:</div>
                    <div class="info-value">{{ $receipt->payment->student->full_name ?? 'N/A' }}</div>
                </div>

                <div class="info-group">
                    <div class="info-label">Admission No:</div>
                    <div class="info-value">{{ $receipt->payment->student->admission_number ?? 'N/A' }}</div>
                </div>

                <div class="info-group">
                    <div class="info-label">Class:</div>
                    <div class="info-value">{{ $receipt->payment->student->currentEnrollment->class->name ?? 'N/A' }}</div>
                </div>

                <div class="info-group">
                    <div class="info-label">Bill Status:</div>
                    <div class="info-value">
                        <span class="status-badge">{{ ucfirst($receipt->payment->bill->status) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <table class="payment-details">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Payment for Bill {{ $receipt->payment->bill->bill_number }}</td>
                    <td class="amount-cell">NRs. {{ number_format($receipt->amount, 2) }}</td>
                </tr>
                <tr style="background: #f8f9fa; font-weight: bold;">
                    <td><strong>Total Paid Amount</strong></td>
                    <td class="amount-cell"><strong>NRs. {{ number_format($receipt->amount, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="receipt-footer">
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="signature-label">Received by: {{ $receipt->issuer->name ?? 'Administrator' }}</div>
            </div>

            <div class="receipt-date">
                <div>Date: {{ now()->format('M d, Y') }}</div>
                <div style="margin-top: 5px; font-size: 9px;">This is a computer generated receipt</div>
            </div>
        </div>
    </div>
</body>
</html>
            <span class="info-value">{{ $receipt->payment->reference_number }}</span>
        </div>
        @endif
    </div>

    <div class="info-section">
        <div class="info-title">Student Information</div>
        <div class="info-row">
            <span class="info-label">Name:</span>
            <span class="info-value">{{ $receipt->payment->student->full_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Admission No:</span>
            <span class="info-value">{{ $receipt->payment->student->admission_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Class:</span>
            <span class="info-value">{{ $receipt->payment->student->currentEnrollment->class->name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Program:</span>
            <span class="info-value">{{ $receipt->payment->student->currentEnrollment->program->name ?? 'N/A' }}</span>
        </div>
    </div>

<!-- Amount Section -->
<div class="total-section">
    <div class="total-row grand-total">
        <span>Amount Received:</span>
        <span>NRs. {{ number_format($receipt->amount, 2) }}</span>
    </div>
    <div style="text-align: center; margin-top: 10px; font-style: italic; color: #666;">
        ({{ ucwords(\App\Helpers\NumberHelper::numberToWords($receipt->amount ?? 0)) }} Only)
    </div>
</div>

<!-- Fee Details -->
<table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
    <thead>
        <tr style="background: #f8f9fa;">
            <th style="padding: 6px; border: 1px solid #ddd; text-align: left; font-size: 10px;">Fee Description</th>
            <th style="padding: 6px; border: 1px solid #ddd; text-align: left; font-size: 10px;">Category</th>
            <th style="padding: 6px; border: 1px solid #ddd; text-align: right; font-size: 10px;">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($receipt->payment->bill->billItems as $item)
        <tr>
            <td style="padding: 6px; border: 1px solid #ddd; font-size: 9px;">{{ $item->description }}</td>
            <td style="padding: 6px; border: 1px solid #ddd; font-size: 9px;">{{ ucfirst($item->fee_category ?? 'General') }}</td>
            <td style="padding: 6px; border: 1px solid #ddd; text-align: right; font-size: 9px;">NRs. {{ number_format($item->final_amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Payment Summary -->
<table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
    <tbody>
        <tr style="background: #f8f9fa;">
            <td style="padding: 6px; border: 1px solid #ddd; font-weight: bold; font-size: 9px;">Bill Total Amount</td>
            <td style="padding: 6px; border: 1px solid #ddd; text-align: right; font-weight: bold; font-size: 9px;">NRs. {{ number_format($receipt->payment->bill->total_amount, 2) }}</td>
        </tr>
        <tr style="background: #f8f9fa;">
            <td style="padding: 6px; border: 1px solid #ddd; font-weight: bold; font-size: 9px;">Total Amount Paid So Far</td>
            <td style="padding: 6px; border: 1px solid #ddd; text-align: right; font-weight: bold; font-size: 9px;">NRs. {{ number_format($receipt->payment->bill->paid_amount, 2) }}</td>
        </tr>
        <tr style="background: #f8f9fa;">
            <td style="padding: 6px; border: 1px solid #ddd; font-weight: bold; font-size: 9px;">Amount Paid (This Receipt)</td>
            <td style="padding: 6px; border: 1px solid #ddd; text-align: right; font-weight: bold; font-size: 9px;">NRs. {{ number_format($receipt->amount, 2) }}</td>
        </tr>
        @if($receipt->payment->bill->balance_amount > 0)
        <tr style="background: #f8f9fa;">
            <td style="padding: 6px; border: 1px solid #ddd; font-weight: bold; font-size: 9px;">Remaining Balance</td>
            <td style="padding: 6px; border: 1px solid #ddd; text-align: right; font-weight: bold; font-size: 9px;">NRs. {{ number_format($receipt->payment->bill->balance_amount, 2) }}</td>
        </tr>
        @else
        <tr style="background: #f8f9fa;">
            <td style="padding: 6px; border: 1px solid #ddd; font-weight: bold; font-size: 9px;">Bill Status</td>
            <td style="padding: 6px; border: 1px solid #ddd; text-align: right; font-weight: bold; font-size: 9px;">FULLY PAID</td>
        </tr>
        @endif
    </tbody>
</table>
</div>

@if($pendingBills->count() > 0)
<!-- Other Pending Bills -->
<div style="margin-top: 25px; padding: 15px; border: 2px solid #f39c12; border-radius: 8px; background: #fef9e7;">
    <div style="font-size: 16px; font-weight: bold; color: #e67e22; margin-bottom: 15px; text-align: center;">
        OTHER PENDING BILLS
    </div>
    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
        <thead>
            <tr style="background: #f39c12; color: white;">
                <th style="padding: 8px; text-align: left; border: 1px solid #e67e22;">Bill Number</th>
                <th style="padding: 8px; text-align: center; border: 1px solid #e67e22;">Due Date</th>
                <th style="padding: 8px; text-align: right; border: 1px solid #e67e22;">Amount Due</th>
                <th style="padding: 8px; text-align: center; border: 1px solid #e67e22;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendingBills as $bill)
            <tr>
                <td style="padding: 8px; border: 1px solid #e67e22;">{{ $bill->bill_number }}</td>
                <td style="padding: 8px; text-align: center; border: 1px solid #e67e22;">{{ $bill->due_date->format('M d, Y') }}</td>
                <td style="padding: 8px; text-align: right; border: 1px solid #e67e22;">NRs. {{ number_format($bill->balance_amount, 2) }}</td>
                <td style="padding: 8px; text-align: center; border: 1px solid #e67e22;">
                    <span style="background: {{ $bill->is_overdue ? '#e74c3c' : '#f39c12' }}; color: white; padding: 3px 8px; border-radius: 4px; font-size: 11px;">
                        {{ strtoupper($bill->status) }}
                    </span>
                </td>
            </tr>
            @endforeach
            <tr style="background: #f8f9fa; font-weight: bold;">
                <td colspan="2" style="padding: 10px; border: 1px solid #e67e22;">Total Outstanding</td>
                <td style="padding: 10px; text-align: right; border: 1px solid #e67e22;">NRs. {{ number_format($pendingBills->sum('balance_amount'), 2) }}</td>
                <td style="padding: 10px; border: 1px solid #e67e22;"></td>
            </tr>
        </tbody>
    </table>
</div>
@endif

@endsection

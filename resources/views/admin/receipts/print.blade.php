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
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            line-height: 1.4;
        }

        /* Receipt container */
        .receipt-container {
            width: 210mm;
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 1px solid #ddd;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Header */
        .receipt-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }



        .receipt-title {
            background: rgba(255,255,255,0.2);
            margin: 15px -20px -20px -20px;
            padding: 12px;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
            text-align: center;
        }

        /* Receipt content */
        .receipt-content {
            padding: 20px;
        }

        /* Info table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .info-table td.label {
            font-weight: bold;
            color: #333;
            width: 150px;
        }

        .info-table td.value {
            color: #666;
        }

        /* Payment table */
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border: 1px solid #ddd;
        }

        .payment-table th {
            background: #f8f9fa;
            padding: 10px 12px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
            color: #333;
            font-size: 13px;
        }

        .payment-table td {
            padding: 10px 12px;
            border: 1px solid #ddd;
            font-size: 13px;
        }

        .payment-table td.amount {
            text-align: right;
            font-weight: bold;
        }

        .payment-table tr.total-row {
            background: #f8f9fa;
        }

        .payment-table tr.total-row td {
            font-weight: bold;
            font-size: 16px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            background: #28a745;
            color: white;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }



        /* Footer */
        .receipt-footer {
            padding: 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f9fa;
        }

        .signature-section {
            text-align: left;
        }

        .signature-line {
            width: 200px;
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
            padding-bottom: 20px;
        }

        .signature-text {
            font-size: 12px;
            color: #666;
        }

        .footer-info {
            text-align: right;
            font-size: 12px;
            color: #666;
        }

        /* Print styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
            }

            .receipt-container {
                border: none;
                box-shadow: none;
                margin: 0;
                max-width: none;
                width: 100%;
            }

            .receipt-header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }
        }

        @page {
            size: A4;
            margin: 15mm;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                @if(isset($instituteSettings->institution_logo) && $instituteSettings->institution_logo)
                    <img src="{{ asset('storage/' . $instituteSettings->institution_logo) }}"
                         alt="Logo" style="width: 60px; height: 60px; margin-right: 15px; border-radius: 8px;">
                @endif
                <div>
                    <div style="font-size: 24px; font-weight: bold; margin-bottom: 5px;">
                        {{ $instituteSettings->institution_name ?? 'Test Academy' }}
                    </div>
                    <div style="font-size: 14px; opacity: 0.9;">
                        {{ $instituteSettings->institution_address ?? 'test address' }}
                    </div>
                    <div style="font-size: 14px; opacity: 0.9;">
                        Phone: {{ $instituteSettings->institution_phone ?? '45454' }} | Email: {{ $instituteSettings->institution_email ?? 'test12@gmail.com' }}
                    </div>
                </div>
            </div>
            <div class="receipt-title">PAYMENT RECEIPT</div>
        </div>
        
        <!-- Receipt Content -->
        <div class="receipt-content">
            <!-- Receipt Information -->
            <div style="margin-bottom: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <p><strong>Receipt No:</strong> {{ $receipt->receipt_number ?? 'REC-' . str_pad($receipt->id, 6, '0', STR_PAD_LEFT) }}</p>
                        <p><strong>Date:</strong> {{ $receipt->receipt_date->format('M d, Y') }}</p>
                        <p><strong>Payment Method:</strong> {{ ucwords(str_replace('_', ' ', $receipt->payment_method)) }}</p>
                    </div>
                    <div>
                        <p><strong>Student:</strong> {{ $receipt->payment->student->full_name }}</p>
                        <p><strong>Admission No:</strong> {{ $receipt->payment->student->admission_number }}</p>
                        <p><strong>Class:</strong> {{ $receipt->payment->student->currentEnrollment->class->name ?? 'Test Class' }}</p>
                    </div>
                </div>
            </div>

            <!-- Fee Details -->
            <table class="payment-table">
                <thead>
                    <tr>
                        <th>Fee Description</th>
                        <th>Category</th>
                        <th class="amount">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receipt->payment->bill->billItems as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td>
                            @if(stripos($item->description, 'previous') !== false || stripos($item->description, 'outstanding') !== false)
                                <span style="background: #ffc107; color: #856404; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: bold;">Previous Dues</span>
                            @else
                                <span style="background: #6c757d; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: bold;">{{ ucfirst($item->fee_category ?? 'General') }}</span>
                            @endif
                        </td>
                        <td class="amount">NRs. {{ number_format($item->final_amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Payment Summary -->
            <table class="payment-table" style="margin-top: 20px;">
                <tbody>
                    <tr style="background: #f8f9fa;">
                        <td style="font-weight: bold;">Bill Total Amount</td>
                        <td></td>
                        <td class="amount" style="font-weight: bold;">NRs. {{ number_format($receipt->payment->bill->total_amount, 2) }}</td>
                    </tr>
                    <tr style="background: #f8f9fa;">
                        <td style="font-weight: bold;">Total Amount Paid So Far</td>
                        <td></td>
                        <td class="amount" style="font-weight: bold;">NRs. {{ number_format($receipt->payment->bill->paid_amount, 2) }}</td>
                    </tr>
                    <tr style="background: #f8f9fa;">
                        <td style="font-weight: bold;">Amount Paid (This Receipt)</td>
                        <td></td>
                        <td class="amount" style="font-weight: bold;">NRs. {{ number_format($receipt->amount, 2) }}</td>
                    </tr>
                    @php
                        $remainingBalance = $receipt->payment->bill->balance_amount;
                    @endphp
                    @if($remainingBalance > 0)
                    <tr style="background: #f8f9fa;">
                        <td style="font-weight: bold;">Remaining Balance</td>
                        <td></td>
                        <td class="amount" style="font-weight: bold;">NRs. {{ number_format($remainingBalance, 2) }}</td>
                    </tr>
                    @else
                    <tr style="background: #f8f9fa;">
                        <td style="font-weight: bold;">Bill Status</td>
                        <td></td>
                        <td class="amount" style="font-weight: bold;">FULLY PAID</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if($pendingBills->count() > 0)
        <!-- Other Pending Bills -->
        <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-radius: 5px;">
            <h4 style="margin-bottom: 15px; color: #856404;">Other Pending Bills</h4>
            <p style="margin-bottom: 15px; font-size: 13px; color: #856404;">
                <strong>Note:</strong> Student has {{ $pendingBills->count() }} other pending bill(s)
            </p>

            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Bill #</th>
                        <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Description</th>
                        <th style="padding: 8px; text-align: center; border: 1px solid #ddd;">Due Date</th>
                        <th style="padding: 8px; text-align: right; border: 1px solid #ddd;">Amount Due</th>
                        <th style="padding: 8px; text-align: center; border: 1px solid #ddd;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingBills as $bill)
                    <tr>
                        <td style="padding: 6px; border: 1px solid #ddd;">{{ $bill->bill_number }}</td>
                        <td style="padding: 6px; border: 1px solid #ddd;">{{ $bill->bill_title ?? 'Academic Fee Bill' }}</td>
                        <td style="padding: 6px; text-align: center; border: 1px solid #ddd;">{{ $bill->due_date->format('M d, Y') }}</td>
                        <td style="padding: 6px; text-align: right; border: 1px solid #ddd;">NRs. {{ number_format($bill->balance_amount, 2) }}</td>
                        <td style="padding: 6px; text-align: center; border: 1px solid #ddd;">
                            @if($bill->due_date->isPast())
                                <span style="color: #dc3545; font-weight: bold;">OVERDUE</span>
                            @else
                                <span style="color: #ffc107; font-weight: bold;">{{ strtoupper($bill->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    <tr style="background: #f8f9fa; font-weight: bold;">
                        <td colspan="3" style="padding: 8px; border: 1px solid #ddd; text-align: right;">Total Outstanding</td>
                        <td style="padding: 8px; text-align: right; border: 1px solid #ddd;">NRs. {{ number_format($pendingBills->sum('balance_amount'), 2) }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <!-- Footer -->
        <div class="receipt-footer">
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="signature-text">Signature: _______________</div>
            </div>
            <div class="footer-info">
                <div>Received by: {{ $receipt->issuer->name ?? 'Test Administrator' }}</div>
                <div>Date: {{ $receipt->receipt_date->format('M d, Y') }}</div>
                <div style="font-size: 10px; margin-top: 5px;">This is a computer generated receipt</div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>

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
            position: relative;
        }

        .header-content {
            display: flex;
            align-items: center;
            position: relative;
        }

        .logo-section {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 70px;
            height: 70px;
        }

        .logo-section img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 10px;
            border: 3px solid rgba(255,255,255,0.4);
            background: rgba(255,255,255,0.1);
            padding: 2px;
        }

        .logo-placeholder {
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            border-radius: 10px;
            border: 3px solid rgba(255,255,255,0.4);
        }

        .institution-details {
            flex: 1;
            text-align: center;
        }

        .institution-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .institution-address {
            font-size: 14px;
            margin-bottom: 3px;
        }

        .receipt-title {
            background: rgba(255,255,255,0.2);
            margin: 15px -20px -20px -20px;
            padding: 10px;
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
        }

        .payment-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
            color: #333;
        }

        .payment-table td {
            padding: 12px;
            border: 1px solid #ddd;
            font-size: 14px;
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
            <div class="header-content">
                <div class="logo-section">
                    @if(isset($instituteSettings->institution_logo) && $instituteSettings->institution_logo)
                        <img src="{{ asset('storage/' . $instituteSettings->institution_logo) }}" alt="Institution Logo"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="logo-placeholder" style="display: none;">
                            {{ substr($instituteSettings->institution_name ?? 'AMS', 0, 3) }}
                        </div>
                    @else
                        <div class="logo-placeholder">
                            {{ substr($instituteSettings->institution_name ?? 'AMS', 0, 3) }}
                        </div>
                    @endif
                </div>

                <div class="institution-details">
                    <div class="institution-name">{{ $instituteSettings->institution_name ?? 'Test Academy' }}</div>
                    <div class="institution-address">{{ $instituteSettings->institution_address ?? 'test address' }}</div>
                    <div class="institution-address">Phone: {{ $instituteSettings->institution_phone ?? '45454' }} | Email: {{ $instituteSettings->institution_email ?? 'test12@gmail.com' }}</div>
                </div>
            </div>
            <div class="receipt-title">PAYMENT RECEIPT</div>
        </div>
        
        <!-- Receipt Content -->
        <div class="receipt-content">
            <!-- Receipt Information Table -->
            <table class="info-table">
                <tr>
                    <td class="label">Receipt No:</td>
                    <td class="value">{{ $receipt->receipt_number ?? $receipt->id }}</td>
                    <td class="label">Student:</td>
                    <td class="value">{{ $receipt->payment->student->full_name ?? 'Wangbu Tamang' }}</td>
                </tr>
                <tr>
                    <td class="label">Date:</td>
                    <td class="value">{{ $receipt->receipt_date->format('M d, Y') }}</td>
                    <td class="label">Admission No:</td>
                    <td class="value">{{ $receipt->payment->student->admission_number ?? 'ADM-2025-002' }}</td>
                </tr>
                <tr>
                    <td class="label">Payment Method:</td>
                    <td class="value">{{ ucwords(str_replace('_', ' ', $receipt->payment_method)) }}</td>
                    <td class="label">Class:</td>
                    <td class="value">{{ $receipt->payment->student->currentEnrollment->class->name ?? 'Test Class' }}</td>
                </tr>
            </table>

            <!-- Fee Details Section -->
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: left; font-size: 12px;">Fee Description</th>
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: left; font-size: 12px;">Category</th>
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: right; font-size: 12px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receipt->payment->bill->billItems as $item)
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; font-size: 11px;">{{ $item->description }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; font-size: 11px;">{{ ucfirst($item->fee_category ?? 'General') }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: right; font-size: 11px;">NRs. {{ number_format($item->final_amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Payment Summary Section -->
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <tbody>
                    <tr style="background: #f8f9fa;">
                        <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; font-size: 11px;">Bill Total Amount</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: right; font-weight: bold; font-size: 11px;">NRs. {{ number_format($receipt->payment->bill->total_amount, 2) }}</td>
                    </tr>
                    <tr style="background: #f8f9fa;">
                        <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; font-size: 11px;">Total Amount Paid So Far</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: right; font-weight: bold; font-size: 11px;">NRs. {{ number_format($receipt->payment->bill->paid_amount, 2) }}</td>
                    </tr>
                    <tr style="background: #f8f9fa;">
                        <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; font-size: 11px;">Amount Paid (This Receipt)</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: right; font-weight: bold; font-size: 11px;">NRs. {{ number_format($receipt->amount, 2) }}</td>
                    </tr>
                    @php
                        $remainingBalance = $receipt->payment->bill->balance_amount;
                    @endphp
                    @if($remainingBalance > 0)
                    <tr style="background: #f8f9fa;">
                        <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; font-size: 11px;">Remaining Balance</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: right; font-weight: bold; font-size: 11px;">NRs. {{ number_format($remainingBalance, 2) }}</td>
                    </tr>
                    @else
                    <tr style="background: #f8f9fa;">
                        <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; font-size: 11px;">Bill Status</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: right; font-weight: bold; font-size: 11px;">FULLY PAID</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if($pendingBills->count() > 0)
        <!-- Other Pending Bills -->
        <div style="margin-top: 20px; padding: 10px; border: 1px solid #ddd;">
            <div style="font-size: 12px; font-weight: bold; margin-bottom: 8px;">
                OTHER PENDING BILLS
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 4px; text-align: left; border: 1px solid #ddd;">Bill #</th>
                        <th style="padding: 4px; text-align: center; border: 1px solid #ddd;">Due Date</th>
                        <th style="padding: 4px; text-align: right; border: 1px solid #ddd;">Amount Due</th>
                        <th style="padding: 4px; text-align: center; border: 1px solid #ddd;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingBills as $bill)
                    <tr>
                        <td style="padding: 4px; border: 1px solid #ddd;">{{ $bill->bill_number }}</td>
                        <td style="padding: 4px; text-align: center; border: 1px solid #ddd;">{{ $bill->due_date->format('M d, Y') }}</td>
                        <td style="padding: 4px; text-align: right; border: 1px solid #ddd;">NRs. {{ number_format($bill->balance_amount, 2) }}</td>
                        <td style="padding: 4px; text-align: center; border: 1px solid #ddd;">{{ strtoupper($bill->status) }}</td>
                    </tr>
                    @endforeach
                    <tr style="background: #f8f9fa; font-weight: bold;">
                        <td colspan="2" style="padding: 6px; border: 1px solid #ddd;">Total Outstanding</td>
                        <td style="padding: 6px; text-align: right; border: 1px solid #ddd;">NRs. {{ number_format($pendingBills->sum('balance_amount'), 2) }}</td>
                        <td style="padding: 6px; border: 1px solid #ddd;"></td>
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

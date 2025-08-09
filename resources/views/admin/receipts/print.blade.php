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
            width: 60px;
            height: 60px;
        }

        .logo-section img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 8px;
            border: 2px solid rgba(255,255,255,0.3);
        }

        .logo-placeholder {
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
            border-radius: 8px;
            border: 2px solid rgba(255,255,255,0.3);
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

            <!-- Payment Details Table -->
            <table class="payment-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Payment for Bill #{{ $receipt->payment->bill->bill_number }}</td>
                        <td class="amount">NRs. {{ number_format($receipt->amount, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td><strong>Total Paid Amount</strong></td>
                        <td class="amount"><strong>NRs. {{ number_format($receipt->amount, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td>Bill Status</td>
                        <td class="amount">
                            <span class="status-badge">{{ strtoupper($receipt->payment->bill->status) }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

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

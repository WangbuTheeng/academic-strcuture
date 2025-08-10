<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Bill - {{ $bill->bill_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8f9fa;
            padding: 20px;
        }

        .bill-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0;
            background: white;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .bill-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        .logo-section {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
        }

        .logo-placeholder {
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            border: 3px solid rgba(255,255,255,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
        }

        .institute-info {
            text-align: center;
        }

        .institute-name {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .institute-details {
            font-size: 14px;
            opacity: 0.9;
            line-height: 1.4;
        }

        .bill-title {
            text-align: center;
            background: rgba(139, 92, 246, 0.8);
            color: white;
            margin: 0;
            padding: 12px;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 3px;
        }

        .bill-content {
            padding: 20px;
            background: white;
        }

        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px 0;
        }

        .info-item {
            text-align: left;
        }

        .info-label {
            font-size: 14px;
            color: #333;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 14px;
            color: #333;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #e0e0e0;
        }

        .payment-table th {
            padding: 12px;
            font-size: 14px;
            color: #333;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            font-weight: bold;
            background: #f8f9fa;
        }

        .payment-table td {
            padding: 12px;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #e0e0e0;
        }

        .payment-table .amount {
            text-align: right;
            font-weight: bold;
        }

        .summary-row {
            background: #f8f9fa;
            font-weight: bold;
        }

        .summary-row td {
            padding: 12px;
            font-size: 14px;
            border-bottom: 1px solid #e0e0e0;
        }

        .bill-footer {
            margin-top: 40px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 12px;
            color: #666;
        }

        .signature-section {
            text-align: left;
        }

        .signature-line {
            width: 150px;
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
            padding-bottom: 20px;
        }

        .footer-info {
            text-align: right;
        }

        @media print {
            body { background: white; padding: 0; }
            .bill-container {
                transform: none;
                box-shadow: none;
                border: none;
                margin: 0;
                width: 100%;
                height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="bill-container">
        <!-- Header Section -->
        <div class="bill-header">
            <!-- Logo Section -->
            <div class="logo-section">
                @if(isset($instituteSettings->institution_logo) && $instituteSettings->institution_logo)
                    <img src="{{ asset('storage/' . $instituteSettings->institution_logo) }}" alt="Institution Logo"
                         style="width: 100%; height: 100%; object-fit: contain; border-radius: 10px; border: 3px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.1); padding: 2px;"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                @endif
                <div class="logo-placeholder" @if(isset($instituteSettings->institution_logo) && $instituteSettings->institution_logo) style="display: none;" @endif>
                    Logo
                </div>
            </div>

            <!-- Institute Information -->
            <div class="institute-info">
                <div class="institute-name">{{ $instituteSettings->institution_name ?? 'Test Academy' }}</div>
                <div class="institute-details">
                    {{ $instituteSettings->institution_address ?? 'test address' }}<br>
                    Phone: {{ $instituteSettings->institution_phone ?? '45454' }} | Email: {{ $instituteSettings->institution_email ?? 'test12@gmail.com' }}
                </div>
            </div>
        </div>

        <!-- Bill Title -->
        <div class="bill-title">
            PAYMENT RECEIPT
        </div>

        <!-- Bill Content -->
        <div class="bill-content">
            <!-- Info Section -->
            <div class="info-section">
                <div class="info-item">
                    <div class="info-label">Receipt No:</div>
                    <div class="info-value">{{ $bill->bill_number }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Student:</div>
                    <div class="info-value">{{ $bill->student->full_name ?? 'N/A' }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Date:</div>
                    <div class="info-value">{{ $bill->bill_date->format('M d, Y') }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Admission No:</div>
                    <div class="info-value">{{ $bill->student->admission_number ?? 'N/A' }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Payment Method:</div>
                    <div class="info-value">Cash</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Class:</div>
                    <div class="info-value">{{ $bill->student->currentEnrollment->class->name ?? 'N/A' }}</div>
                </div>
            </div>

            <!-- Payment Details Table -->
            <table class="payment-table">
                <thead>
                    <tr>
                        <th>Fee Description</th>
                        <th>Category</th>
                        <th class="amount">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bill->billItems as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td>{{ ucfirst($item->fee_category ?? 'General') }}</td>
                        <td class="amount">NRs. {{ number_format($item->final_amount, 2) }}</td>
                    </tr>
                    @endforeach

                    <!-- Summary Rows -->
                    <tr class="summary-row">
                        <td colspan="2">Bill Total Amount</td>
                        <td class="amount">NRs. {{ number_format($bill->total_amount, 2) }}</td>
                    </tr>

                    <tr class="summary-row">
                        <td colspan="2">Total Amount Paid So Far</td>
                        <td class="amount">NRs. {{ number_format($bill->paid_amount, 2) }}</td>
                    </tr>

                    <tr class="summary-row">
                        <td colspan="2">Amount Paid (This Receipt)</td>
                        <td class="amount">NRs. 1,000.00</td>
                    </tr>

                    <tr class="summary-row">
                        <td colspan="2">Remaining Balance</td>
                        <td class="amount">NRs. {{ number_format($bill->total_amount - $bill->paid_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="bill-footer">
            <div class="signature-section">
                <div>Signature: ___________________</div>
            </div>
            <div class="footer-info">
                <div>Received by: Test Administrator</div>
                <div>Date: {{ $bill->bill_date->format('M d, Y') }}</div>
                <div style="font-size: 10px; margin-top: 5px; color: #999;">This is a computer generated receipt</div>
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

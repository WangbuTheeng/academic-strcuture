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
            width: 210mm;
            height: 148mm;
            margin: 0 auto;
            padding: 0;
            background: white;
            position: relative;
            border: 2px solid #e0e0e0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transform: scale(0.9);
            transform-origin: top center;
        }

        .bill-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 15px 20px;
            position: relative;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-section {
            width: 70px;
            height: 70px;
            flex-shrink: 0;
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
            flex: 1;
        }

        .institute-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .institute-details {
            font-size: 12px;
            opacity: 0.9;
            line-height: 1.4;
        }

        .bill-title {
            text-align: center;
            background: #e74c3c;
            color: white;
            margin: 0;
            padding: 8px;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .bill-content {
            padding: 15px 20px;
            background: white;
        }

        .info-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            border-left: 3px solid #3498db;
        }

        .info-card-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
        }

        .info-item {
            margin-bottom: 8px;
        }

        .info-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 13px;
            color: #333;
            font-weight: 600;
            background: white;
            padding: 5px 8px;
            border-radius: 4px;
            border: 1px solid #e0e0e0;
        }

        .status-badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending { background: #f39c12; color: white; }
        .status-paid { background: #27ae60; color: white; }
        .status-partial { background: #3498db; color: white; }
        .status-overdue { background: #e74c3c; color: white; }

        .payment-details {
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            margin: 0 20px;
        }

        .payment-header {
            background: #e74c3c;
            color: white;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
            margin: 0;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px;
            width: calc(100% - 30px);
        }

        .payment-table th {
            padding: 8px;
            font-size: 12px;
            color: #666;
            text-align: left;
            border-bottom: 2px solid #e0e0e0;
            font-weight: bold;
            background: #f8f9fa;
        }

        .payment-table td {
            padding: 8px 0;
            font-size: 13px;
            color: #333;
            border-bottom: 1px solid #e0e0e0;
        }

        .payment-table .amount {
            text-align: right;
            font-weight: bold;
        }

        .total-row {
            background: #27ae60;
            color: white;
            font-weight: bold;
        }

        .total-row td {
            padding: 10px 8px;
            font-size: 15px;
            border-radius: 4px;
            border: none;
        }

        .balance-row {
            background: #f39c12;
            color: white;
            font-weight: bold;
        }

        .balance-row td {
            padding: 8px;
            font-size: 13px;
            border-radius: 4px;
            border: none;
        }

        .bill-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: #2c3e50;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
        }

        .signature-section {
            text-align: left;
        }

        .signature-line {
            width: 120px;
            border-bottom: 1px solid rgba(255,255,255,0.5);
            margin-bottom: 5px;
            padding-bottom: 15px;
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
                    Phone: {{ $instituteSettings->institution_phone ?? '45454' }}<br>
                    Email: {{ $instituteSettings->institution_email ?? 'test12@gmail.com' }}
                </div>
            </div>
        </div>

        <!-- Bill Title -->
        <div class="bill-title">
            STUDENT FEE BILL
        </div>

        <!-- Bill Content -->
        <div class="bill-content">
            <!-- Info Cards -->
            <div class="info-cards">
                <!-- Left Card - Student Information -->
                <div class="info-card">
                    <div class="info-card-title">Student Information</div>

                    <div class="info-item">
                        <div class="info-label">Student Name:</div>
                        <div class="info-value">{{ $bill->student->full_name ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Admission No:</div>
                        <div class="info-value">{{ $bill->student->admission_number ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Class:</div>
                        <div class="info-value">{{ $bill->student->currentEnrollment->class->name ?? 'N/A' }}</div>
                    </div>
                </div>

                <!-- Right Card - Bill Information -->
                <div class="info-card">
                    <div class="info-card-title">Bill Information</div>

                    <div class="info-item">
                        <div class="info-label">Bill Number:</div>
                        <div class="info-value">{{ $bill->bill_number }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Bill Date:</div>
                        <div class="info-value">{{ $bill->bill_date->format('M d, Y') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Due Date:</div>
                        <div class="info-value">{{ $bill->due_date->format('M d, Y') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Status:</div>
                        <div class="info-value">
                            <span class="status-badge status-{{ $bill->status }}">
                                {{ ucfirst($bill->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="payment-details">
                <div class="payment-header">
                    Bill Details
                </div>

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

                        <!-- Subtotal -->
                        <tr style="background: #f8f9fa;">
                            <td colspan="2" style="font-weight: bold; color: #333; padding: 10px 8px;">
                                Total Bill Amount
                            </td>
                            <td class="amount" style="font-weight: bold; color: #333; padding: 10px 8px;">
                                NRs. {{ number_format($bill->total_amount, 2) }}
                            </td>
                        </tr>

                        @if($bill->paid_amount > 0)
                        <!-- Amount Paid -->
                        <tr class="total-row">
                            <td colspan="2">Amount Paid</td>
                            <td class="amount">NRs. {{ number_format($bill->paid_amount, 2) }}</td>
                        </tr>
                        @endif

                        @if($bill->balance_amount > 0)
                        <!-- Remaining Balance -->
                        <tr class="balance-row">
                            <td colspan="2">Balance Due</td>
                            <td class="amount">NRs. {{ number_format($bill->balance_amount, 2) }}</td>
                        </tr>
                        @else
                        <tr class="total-row">
                            <td colspan="2">Bill Status</td>
                            <td class="amount">FULLY PAID</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="bill-footer">
            <div class="signature-section">
                <div class="signature-line"></div>
                <div>Authorized Signature</div>
            </div>
            <div class="footer-info">
                <div>Date: {{ $bill->bill_date->format('M d, Y') }}</div>
                <div style="font-size: 10px; margin-top: 5px;">This is a computer generated bill</div>
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

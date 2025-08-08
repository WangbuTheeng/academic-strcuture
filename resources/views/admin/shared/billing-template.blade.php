<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $documentTitle ?? 'Billing Document' }}</title>
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
        }
        
        .document-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .document-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .document-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .school-info {
            position: relative;
            z-index: 1;
        }
        
        .school-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
        }
        
        .school-name {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .school-address {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        
        .document-title {
            background: rgba(255,255,255,0.2);
            padding: 15px 30px;
            margin: 20px -30px -30px -30px;
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 2px;
        }
        
        .document-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 30px;
            background: #f8f9fa;
        }
        
        .info-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .info-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
        }
        
        .info-value {
            color: #333;
            font-weight: 500;
        }
        
        .document-items {
            padding: 0 30px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .items-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .items-table tr:hover {
            background: #f8f9fa;
        }
        
        .amount-cell {
            text-align: right;
            font-weight: 600;
            color: #667eea;
        }
        
        .total-section {
            background: #f8f9fa;
            padding: 30px;
            margin-top: 20px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 1.1rem;
        }
        
        .total-row.grand-total {
            border-top: 2px solid #667eea;
            margin-top: 15px;
            padding-top: 15px;
            font-size: 1.3rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .total-row.balance {
            background: #fff3cd;
            padding: 15px;
            margin: 10px -30px;
            border-left: 4px solid #ffc107;
        }
        
        .total-row.paid {
            background: #d4edda;
            padding: 15px;
            margin: 10px -30px;
            border-left: 4px solid #28a745;
        }
        
        .document-footer {
            background: #333;
            color: white;
            padding: 20px 30px;
            text-align: center;
        }
        
        .payment-info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 20px;
            margin: 20px 30px;
            border-radius: 5px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        
        .status-partial {
            background: #ffeaa7;
            color: #6c5ce7;
        }
        
        .status-overdue {
            background: #f8d7da;
            color: #721c24;
        }
        
        @media print {
            body {
                background: white;
            }
            .document-container {
                box-shadow: none;
                margin: 0;
            }
        }
        
        @media (max-width: 768px) {
            .document-info {
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 20px;
            }
            
            .school-name {
                font-size: 2rem;
            }
            
            .document-items {
                padding: 0 20px;
            }
            
            .total-section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="document-container">
        <!-- Header -->
        <div class="document-header">
            <div class="school-info">
                @if(isset($instituteSettings->logo_url) && $instituteSettings->logo_url)
                    <div class="school-logo">
                        <img src="{{ asset('storage/' . $instituteSettings->logo_url) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                @else
                    <div class="school-logo">{{ substr($instituteSettings->institution_name ?? 'AMS', 0, 3) }}</div>
                @endif
                <div class="school-name">{{ $instituteSettings->institution_name ?? 'Academic Institution' }}</div>
                <div class="school-address">{{ $instituteSettings->institution_address ?? 'Institution Address' }}</div>
                <div class="school-address">Phone: {{ $instituteSettings->institution_phone ?? '+977-1-XXXXXXX' }}</div>
                <div class="school-address">Email: {{ $instituteSettings->institution_email ?? 'info@institution.edu.np' }}</div>
            </div>
            <div class="document-title">{{ $documentType ?? 'BILLING DOCUMENT' }}</div>
        </div>

        @yield('document-content')

        <!-- Footer -->
        <div class="document-footer">
            <p>This is a computer-generated document. For any queries, please contact the accounts office.</p>
            <p>Generated on {{ now()->format('M d, Y \a\t g:i A') }}</p>
            @if(isset($footerText))
                <p>{{ $footerText }}</p>
            @endif
        </div>
    </div>
</body>
</html>

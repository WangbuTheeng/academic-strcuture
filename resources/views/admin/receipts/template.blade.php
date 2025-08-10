@extends('layouts.admin')

@section('title', 'Receipt Template Preview')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-receipt text-primary me-2"></i>Receipt Template Preview
            </h1>
            <p class="text-muted mb-0">Preview and customize receipt template design</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.fees.receipts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Receipts
            </a>
            <button type="button" class="btn btn-primary" onclick="printPreview()">
                <i class="fas fa-print me-2"></i>Print Preview
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Receipt Template Preview</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This is a preview of how receipts will appear when printed. The template is designed for half A4 page size.
                    </div>

                    @if(!isset($instituteSettings->institution_logo) || !$instituteSettings->institution_logo)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>No logo uploaded:</strong> To display your institution's logo in receipts, please upload a logo in
                        <a href="{{ route('admin.institute-settings.index') }}" class="alert-link">Institute Settings</a>.
                        Currently showing institution initials as placeholder.
                    </div>
                    @endif
                    
                    <!-- Receipt Preview -->
                    <div id="receipt-preview" style="transform: scale(0.75); transform-origin: top left; margin-bottom: -120px;">
                        <div style="width: 210mm; height: 148mm; margin: 0 auto; padding: 0; background: white; position: relative; border: 2px solid #e0e0e0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">

                            <!-- Header Section -->
                            <div style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white; padding: 15px 20px; position: relative;">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <!-- Logo Section -->
                                    <div style="width: 70px; height: 70px; flex-shrink: 0;">
                                        @if(isset($instituteSettings->institution_logo) && $instituteSettings->institution_logo)
                                            <img src="{{ asset('storage/' . $instituteSettings->institution_logo) }}" alt="Institution Logo"
                                                 style="width: 100%; height: 100%; object-fit: contain; border-radius: 10px; border: 3px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.1); padding: 2px;"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div style="width: 100%; height: 100%; background: rgba(255,255,255,0.2); color: white; display: none; align-items: center; justify-content: center; font-size: 22px; font-weight: bold; border-radius: 10px; border: 3px solid rgba(255,255,255,0.4);">
                                                {{ substr($instituteSettings->institution_name ?? 'AMS', 0, 3) }}
                                            </div>
                                        @else
                                            <div style="width: 100%; height: 100%; background: rgba(255,255,255,0.2); color: white; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: bold; border-radius: 10px; border: 3px solid rgba(255,255,255,0.4);">
                                                {{ substr($instituteSettings->institution_name ?? 'AMS', 0, 3) }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Institution Details -->
                                    <div style="flex: 1; text-align: center;">
                                        <div style="font-size: 22px; font-weight: bold; margin-bottom: 5px; letter-spacing: 0.5px;">
                                            {{ $instituteSettings->institution_name ?? 'Academic Institution' }}
                                        </div>
                                        <div style="font-size: 12px; opacity: 0.9; margin-bottom: 2px;">
                                            {{ $instituteSettings->institution_address ?? 'Institution Address' }}
                                        </div>
                                        <div style="font-size: 12px; opacity: 0.9;">
                                            {{ $instituteSettings->institution_phone ?? '+977-1-XXXXXXX' }} | {{ $instituteSettings->institution_email ?? 'info@institution.edu.np' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Receipt Title -->
                            <div style="text-align: center; background: #e74c3c; color: white; margin: 0; padding: 8px; font-size: 16px; font-weight: bold; letter-spacing: 2px;">
                                PAYMENT RECEIPT
                            </div>

                            <!-- Receipt Content -->
                            <div style="padding: 15px 20px; background: white;">
                                <!-- Receipt Info Cards -->
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                    <!-- Left Card -->
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #e0e0e0; border-left: 3px solid #3498db;">
                                        <div style="font-size: 14px; font-weight: bold; color: #2c3e50; margin-bottom: 10px; border-bottom: 1px solid #e0e0e0; padding-bottom: 5px;">
                                            Receipt Details
                                        </div>

                                        <div style="margin-bottom: 8px;">
                                            <div style="font-size: 10px; color: #666; text-transform: uppercase; font-weight: bold; margin-bottom: 2px;">Receipt No:</div>
                                            <div style="font-size: 13px; color: #333; font-weight: 600; background: white; padding: 5px 8px; border-radius: 4px; border: 1px solid #e0e0e0;">{{ $sampleReceipt->receipt_number }}</div>
                                        </div>

                                        <div style="margin-bottom: 8px;">
                                            <div style="font-size: 10px; color: #666; text-transform: uppercase; font-weight: bold; margin-bottom: 2px;">Date:</div>
                                            <div style="font-size: 13px; color: #333; font-weight: 600; background: white; padding: 5px 8px; border-radius: 4px; border: 1px solid #e0e0e0;">{{ $sampleReceipt->receipt_date->format('M d, Y') }}</div>
                                        </div>

                                        <div style="margin-bottom: 8px;">
                                            <div style="font-size: 10px; color: #666; text-transform: uppercase; font-weight: bold; margin-bottom: 2px;">Payment Method:</div>
                                            <div style="font-size: 13px; color: #333; font-weight: 600; background: white; padding: 5px 8px; border-radius: 4px; border: 1px solid #e0e0e0;">{{ ucwords(str_replace('_', ' ', $sampleReceipt->payment_method)) }}</div>
                                        </div>
                                    </div>

                                    <!-- Right Card -->
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #e0e0e0; border-left: 3px solid #27ae60;">
                                        <div style="font-size: 14px; font-weight: bold; color: #2c3e50; margin-bottom: 10px; border-bottom: 1px solid #e0e0e0; padding-bottom: 5px;">
                                            Student Details
                                        </div>

                                        <div style="margin-bottom: 8px;">
                                            <div style="font-size: 10px; color: #666; text-transform: uppercase; font-weight: bold; margin-bottom: 2px;">Student Name:</div>
                                            <div style="font-size: 13px; color: #333; font-weight: 600; background: white; padding: 5px 8px; border-radius: 4px; border: 1px solid #e0e0e0;">{{ $sampleReceipt->payment->student->full_name }}</div>
                                        </div>

                                        <div style="margin-bottom: 8px;">
                                            <div style="font-size: 10px; color: #666; text-transform: uppercase; font-weight: bold; margin-bottom: 2px;">Admission No:</div>
                                            <div style="font-size: 13px; color: #333; font-weight: 600; background: white; padding: 5px 8px; border-radius: 4px; border: 1px solid #e0e0e0;">{{ $sampleReceipt->payment->student->admission_number }}</div>
                                        </div>

                                        <div style="margin-bottom: 8px;">
                                            <div style="font-size: 10px; color: #666; text-transform: uppercase; font-weight: bold; margin-bottom: 2px;">Class:</div>
                                            <div style="font-size: 13px; color: #333; font-weight: 600; background: white; padding: 5px 8px; border-radius: 4px; border: 1px solid #e0e0e0;">{{ $sampleReceipt->payment->student->class->name }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Details -->
                                <div style="background: #f8f9fa; border-radius: 8px; border: 1px solid #e0e0e0; margin: 0 20px;">
                                    <div style="background: #e74c3c; color: white; padding: 10px 15px; font-size: 14px; font-weight: bold; margin: 0;">
                                        Payment Details
                                    </div>

                                    <div style="padding: 15px;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <thead>
                                                <tr style="background: #f8f9fa;">
                                                    <th style="padding: 8px; font-size: 12px; color: #666; text-align: left; border-bottom: 2px solid #e0e0e0; font-weight: bold;">Fee Description</th>
                                                    <th style="padding: 8px; font-size: 12px; color: #666; text-align: right; border-bottom: 2px solid #e0e0e0; font-weight: bold;">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($sampleReceipt->payment->bill->billItems as $item)
                                                <tr>
                                                    <td style="padding: 8px 0; font-size: 13px; color: #333; border-bottom: 1px solid #e0e0e0; font-weight: 500;">
                                                        {{ $item->description }}
                                                    </td>
                                                    <td style="padding: 8px 0; text-align: right; font-size: 13px; color: #333; border-bottom: 1px solid #e0e0e0;">
                                                        NRs. {{ number_format($item->final_amount, 2) }}
                                                    </td>
                                                </tr>
                                                @endforeach

                                                <!-- Subtotal -->
                                                <tr style="background: #f8f9fa;">
                                                    <td style="padding: 10px 8px; font-size: 14px; font-weight: bold; color: #333; border-bottom: 1px solid #ddd;">
                                                        Bill Total Amount
                                                    </td>
                                                    <td style="padding: 10px 8px; text-align: right; font-size: 14px; font-weight: bold; color: #333; border-bottom: 1px solid #ddd;">
                                                        NRs. {{ number_format(collect($sampleReceipt->payment->bill->billItems)->sum('final_amount'), 2) }}
                                                    </td>
                                                </tr>

                                                <!-- Total Amount Paid So Far -->
                                                <tr style="background: #e3f2fd;">
                                                    <td style="padding: 10px 8px; font-size: 14px; font-weight: bold; color: #333; border-bottom: 1px solid #ddd;">
                                                        Total Amount Paid So Far
                                                    </td>
                                                    <td style="padding: 10px 8px; text-align: right; font-size: 14px; font-weight: bold; color: #333; border-bottom: 1px solid #ddd;">
                                                        NRs. {{ number_format($sampleReceipt->amount + 1500, 2) }}
                                                    </td>
                                                </tr>

                                                <!-- Amount Paid This Receipt -->
                                                <tr>
                                                    <td style="background: #27ae60; color: white; padding: 10px 8px; font-size: 15px; font-weight: bold; border-radius: 4px; border: none;">
                                                        Amount Paid (This Receipt)
                                                    </td>
                                                    <td style="background: #27ae60; color: white; padding: 10px 8px; text-align: right; font-size: 16px; font-weight: bold; border-radius: 4px; border: none;">
                                                        NRs. {{ number_format($sampleReceipt->amount, 2) }}
                                                    </td>
                                                </tr>

                                                @php
                                                    $billTotal = collect($sampleReceipt->payment->bill->billItems)->sum('final_amount');
                                                    $remainingBalance = $billTotal - $sampleReceipt->amount;
                                                @endphp
                                                @if($remainingBalance > 0)
                                                <tr>
                                                    <td style="background: #f39c12; color: white; padding: 8px; font-size: 13px; font-weight: bold; border-radius: 4px; border: none;">
                                                        Remaining Balance
                                                    </td>
                                                    <td style="background: #f39c12; color: white; padding: 8px; text-align: right; font-size: 14px; font-weight: bold; border-radius: 4px; border: none;">
                                                        NRs. {{ number_format($remainingBalance, 2) }}
                                                    </td>
                                                </tr>
                                                @else
                                                <tr>
                                                    <td style="background: #27ae60; color: white; padding: 8px; font-size: 13px; font-weight: bold; border-radius: 4px; border: none;">
                                                        Bill Status
                                                    </td>
                                                    <td style="background: #27ae60; color: white; padding: 8px; text-align: right; font-size: 14px; font-weight: bold; border-radius: 4px; border: none;">
                                                        FULLY PAID
                                                    </td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            @if($samplePendingBills->count() > 0)
                            <!-- Other Pending Bills -->
                            <div style="margin: 15px 20px; padding: 12px; border: 2px solid #f39c12; border-radius: 6px; background: #fef9e7;">
                                <div style="font-size: 12px; font-weight: bold; color: #e67e22; margin-bottom: 8px; text-align: center;">
                                    ⚠️ OTHER PENDING BILLS
                                </div>
                                <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
                                    <thead>
                                        <tr style="background: #f39c12; color: white;">
                                            <th style="padding: 4px; text-align: left; border: 1px solid #e67e22;">Bill Number</th>
                                            <th style="padding: 4px; text-align: center; border: 1px solid #e67e22;">Due Date</th>
                                            <th style="padding: 4px; text-align: right; border: 1px solid #e67e22;">Amount Due</th>
                                            <th style="padding: 4px; text-align: center; border: 1px solid #e67e22;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($samplePendingBills as $bill)
                                        <tr>
                                            <td style="padding: 4px; border: 1px solid #e67e22; font-size: 9px;">{{ $bill->bill_number }}</td>
                                            <td style="padding: 4px; text-align: center; border: 1px solid #e67e22; font-size: 9px;">{{ $bill->due_date->format('M d, Y') }}</td>
                                            <td style="padding: 4px; text-align: right; border: 1px solid #e67e22; font-size: 9px;">NRs. {{ number_format($bill->balance_amount, 2) }}</td>
                                            <td style="padding: 4px; text-align: center; border: 1px solid #e67e22;">
                                                <span style="background: {{ $bill->is_overdue ? '#e74c3c' : '#f39c12' }}; color: white; padding: 1px 4px; border-radius: 2px; font-size: 8px;">
                                                    {{ strtoupper($bill->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr style="background: #f8f9fa; font-weight: bold;">
                                            <td colspan="2" style="padding: 6px; border: 1px solid #e67e22; font-size: 9px;">Total Outstanding</td>
                                            <td style="padding: 6px; text-align: right; border: 1px solid #e67e22; font-size: 9px;">NRs. {{ number_format($samplePendingBills->sum('balance_amount'), 2) }}</td>
                                            <td style="padding: 6px; border: 1px solid #e67e22;"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @endif

                            <!-- Footer -->
                            <div style="position: absolute; bottom: 0; left: 0; right: 0; background: #2c3e50; color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; font-size: 11px;">
                                <div style="text-align: left;">
                                    <div style="width: 120px; border-bottom: 1px solid rgba(255,255,255,0.5); margin-bottom: 5px; padding-bottom: 15px;"></div>
                                    <div style="font-size: 11px; opacity: 0.9;">Received by: {{ $sampleReceipt->issuer->name }}</div>
                                </div>

                                <div style="text-align: right;">
                                    <div style="font-size: 11px; opacity: 0.9; margin-bottom: 2px;">{{ now()->format('M d, Y') }}</div>
                                    <div style="font-size: 9px; opacity: 0.7;">Computer generated receipt</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printPreview() {
    const receiptContent = document.getElementById('receipt-preview').innerHTML;
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Receipt Template Preview</title>
            <style>
                body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }
                @media print {
                    body { margin: 0; padding: 0; }
                }
                @page {
                    size: A4;
                    margin: 0;
                }
            </style>
        </head>
        <body>
            ${receiptContent}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}
</script>
@endsection

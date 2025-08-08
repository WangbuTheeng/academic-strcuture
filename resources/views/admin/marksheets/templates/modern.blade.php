<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marksheet - {{ $student->first_name }} {{ $student->last_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }

        .marksheet {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .institution-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            background: #2563eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .institution-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .institution-address {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 10px;
        }

        .marksheet-title {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-top: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #2563eb;
        }

        .info-section h3 {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: 600;
            color: #374151;
            width: 120px;
            flex-shrink: 0;
        }

        .info-value {
            color: #1f2937;
            flex: 1;
        }

        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .marks-table th {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .marks-table td {
            padding: 10px 8px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }

        .marks-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .marks-table tbody tr:hover {
            background: #f3f4f6;
        }

        .subject-name {
            text-align: left !important;
            font-weight: 600;
            color: #1f2937;
        }

        .pass-mark {
            color: #059669;
            font-weight: 600;
        }

        .fail-mark {
            color: #dc2626;
            font-weight: 600;
        }

        .grade-a { color: #059669; font-weight: bold; }
        .grade-b { color: #0891b2; font-weight: bold; }
        .grade-c { color: #ca8a04; font-weight: bold; }
        .grade-d { color: #ea580c; font-weight: bold; }
        .grade-f { color: #dc2626; font-weight: bold; }

        .summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: #fff;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }

        .summary-card.pass {
            border-color: #10b981;
            background: linear-gradient(135deg, #ecfdf5, #f0fdf4);
        }

        .summary-card.fail {
            border-color: #ef4444;
            background: linear-gradient(135deg, #fef2f2, #fef7f7);
        }

        .summary-title {
            font-size: 14px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .summary-value.pass {
            color: #059669;
        }

        .summary-value.fail {
            color: #dc2626;
        }

        .summary-subtitle {
            font-size: 12px;
            color: #6b7280;
        }

        .result-section {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 8px;
        }

        .result-section.pass {
            background: linear-gradient(135deg, #ecfdf5, #f0fdf4);
            border: 2px solid #10b981;
        }

        .result-section.fail {
            background: linear-gradient(135deg, #fef2f2, #fef7f7);
            border: 2px solid #ef4444;
        }

        .result-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .result-title.pass {
            color: #059669;
        }

        .result-title.fail {
            color: #dc2626;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .signature {
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid #374151;
            margin-bottom: 8px;
            height: 40px;
        }

        .signature-label {
            font-size: 11px;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .signature-name {
            font-size: 10px;
            color: #374151;
            margin-top: 4px;
            font-weight: 500;
        }

        .institution-logo-img {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
            border-radius: 50%;
            object-fit: cover;
        }

        .remarks-section {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            border-left: 4px solid #2563eb;
        }

        .remarks-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .remarks-content {
            font-size: 12px;
            color: #374151;
            line-height: 1.5;
            font-style: italic;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #2563eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }

        .generation-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .qr-code {
            width: 60px;
            height: 60px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #6b7280;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .marksheet {
                max-width: none;
                margin: 0;
                padding: 15px;
            }
            
            .no-print {
                display: none;
            }
        }

        @page {
            margin: 1cm;
            size: A4;
        }
    </style>
</head>
<body>
    <div class="marksheet">
        <!-- Header -->
        <div class="header">
            @if(isset($instituteSettings) && $instituteSettings && $instituteSettings->logo_url)
                <img src="{{ $instituteSettings->logo_url }}" alt="School Logo" class="institution-logo-img">
            @else
                <div class="institution-logo">
                    {{ (isset($instituteSettings) && $instituteSettings && $instituteSettings->institution_name) ? substr($instituteSettings->institution_name, 0, 3) : 'AMS' }}
                </div>
            @endif
            <div class="institution-name">{{ (isset($instituteSettings) && $instituteSettings) ? $instituteSettings->institution_name : 'Academic Management System' }}</div>
            <div class="institution-address">
                {{ (isset($instituteSettings) && $instituteSettings) ? $instituteSettings->institution_address : 'Excellence in Education | Kathmandu, Nepal' }}
            </div>
            <div class="marksheet-title">Academic Marksheet</div>
        </div>

        <!-- Student Information -->
        <div class="student-info">
            <div class="info-section">
                <h3>Student Information</h3>
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $student->first_name }} {{ $student->last_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Roll Number:</span>
                    <span class="info-value">{{ $student->currentEnrollment->roll_no ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Class:</span>
                    <span class="info-value">{{ $student->currentEnrollment->class->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Program:</span>
                    <span class="info-value">{{ $student->currentEnrollment->program->name ?? 'N/A' }}</span>
                </div>
            </div>
            
            <div class="info-section">
                <h3>Examination Details</h3>
                <div class="info-row">
                    <span class="info-label">Exam:</span>
                    <span class="info-value">{{ $exam->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Academic Year:</span>
                    <span class="info-value">{{ $exam->academicYear->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Exam Type:</span>
                    <span class="info-value">{{ ucfirst($exam->exam_type) ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    <span class="info-value">{{ $bikramSambatDate }}</span>
                </div>
            </div>
        </div>

        <!-- Marks Table -->
        <table class="marks-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 25%;">Subject</th>
                    <th rowspan="2" style="width: 10%;">Full Marks</th>
                    <th rowspan="2" style="width: 10%;">Pass Marks</th>
                    <th colspan="3" style="width: 30%;">Marks Obtained</th>
                    <th rowspan="2" style="width: 8%;">Total</th>
                    <th rowspan="2" style="width: 8%;">%</th>
                    <th rowspan="2" style="width: 6%;">Grade</th>
                    <th rowspan="2" style="width: 8%;">Result</th>
                </tr>
                <tr>
                    <th style="width: 10%;">Theory</th>
                    <th style="width: 10%;">Practical</th>
                    <th style="width: 10%;">Assessment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($marks as $mark)
                    <tr>
                        <td class="subject-name">{{ $mark->subject->name }}</td>
                        <td>{{ number_format($mark->exam->max_marks, 0) }}</td>
                        <td>{{ number_format($mark->exam->pass_marks, 0) }}</td>
                        <td>{{ $mark->theory_marks ? number_format($mark->theory_marks, 1) : '-' }}</td>
                        <td>{{ $mark->practical_marks ? number_format($mark->practical_marks, 1) : '-' }}</td>
                        <td>{{ $mark->assess_marks ? number_format($mark->assess_marks, 1) : '-' }}</td>
                        <td class="font-weight-bold">{{ number_format($mark->total_marks, 1) }}</td>
                        <td>{{ number_format($mark->percentage, 1) }}%</td>
                        <td class="grade-{{ strtolower($mark->grade) }}">{{ $mark->grade }}</td>
                        <td class="{{ $mark->result === 'Pass' ? 'pass-mark' : 'fail-mark' }}">
                            {{ $mark->result }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-card">
                <div class="summary-title">Total Marks</div>
                <div class="summary-value">{{ number_format($totalMarks, 1) }}</div>
                <div class="summary-subtitle">Out of {{ number_format($maxMarks, 0) }}</div>
            </div>
            
            <div class="summary-card">
                <div class="summary-title">Overall Percentage</div>
                <div class="summary-value">{{ number_format($overallPercentage, 1) }}%</div>
                <div class="summary-subtitle">Grade: {{ $overallGrade }}</div>
            </div>
        </div>

        <!-- Result -->
        <div class="result-section {{ strtolower($overallResult) }}">
            <div class="result-title {{ strtolower($overallResult) }}">
                {{ $overallResult === 'Pass' ? '✓ PASSED' : '✗ FAILED' }}
            </div>
            @if($overallResult === 'Pass')
                <p>Congratulations! You have successfully passed the examination.</p>
            @else
                <p>You need to improve in the failed subjects to pass the examination.</p>
            @endif
        </div>

        <!-- Remarks Section -->
        @if(isset($overallRemarks))
        <div class="remarks-section">
            <div class="remarks-title">Remarks</div>
            <div class="remarks-content">{{ $overallRemarks }}</div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-label">Class Teacher</div>
            </div>

            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-label">Principal</div>
                @if(isset($instituteSettings) && $instituteSettings && $instituteSettings->principal_name)
                    <div class="signature-name">{{ $instituteSettings->principal_name }}</div>
                @endif
            </div>

            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-label">Controller of Examinations</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="generation-info">
                <span>Generated on: {{ $generatedAt->format('F j, Y \a\t g:i A') }}</span>
                <span>Bikram Sambat: {{ $bikramSambatDate }}</span>
            </div>
            
            <div style="margin-top: 15px;">
                <div class="qr-code">
                    QR Code
                </div>
                <p style="margin-top: 10px; font-size: 9px;">
                    This is a computer-generated marksheet. For verification, please contact the institution.
                </p>
            </div>
        </div>
    </div>

    @if(isset($isPreview) && $isPreview)
        <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
            <button onclick="window.print()" style="background: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">
                Print Marksheet
            </button>
        </div>
    @endif
</body>
</html>

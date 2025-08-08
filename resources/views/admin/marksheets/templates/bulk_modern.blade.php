<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Marksheets - {{ $exam->name }}</title>
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
            page-break-after: always;
        }

        .marksheet:last-child {
            page-break-after: auto;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .institution-logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
            background: #2563eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            font-weight: bold;
        }

        .institution-name {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .institution-address {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 15px;
        }

        .marksheet-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e40af;
            text-transform: uppercase;
        }

        .student-info {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
        }

        .info-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }

        .info-section h3 {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
        }

        .info-row {
            margin-bottom: 10px;
            display: flex;
        }

        .info-label {
            font-weight: 600;
            color: #374151;
            width: 120px;
            flex-shrink: 0;
        }

        .info-value {
            flex: 1;
            color: #111827;
        }

        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .marks-table th,
        .marks-table td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: center;
        }

        .marks-table th {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
        }

        .marks-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .subject-name {
            text-align: left !important;
            font-weight: 600;
            color: #374151;
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
        .grade-c { color: #d97706; font-weight: bold; }
        .grade-d { color: #dc2626; font-weight: bold; }

        .summary {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }

        .summary-card {
            flex: 1;
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            border: 1px solid #d1d5db;
        }

        .summary-title {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .summary-subtitle {
            font-size: 11px;
            color: #6b7280;
        }

        .result-section {
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .result-section.pass {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            border: 2px solid #059669;
        }

        .result-section.fail {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border: 2px solid #dc2626;
        }

        .result-title.pass {
            color: #059669;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .result-title.fail {
            color: #dc2626;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .signature {
            text-align: center;
            width: 30%;
        }

        .signature-line {
            border-bottom: 2px solid #374151;
            height: 40px;
            margin-bottom: 10px;
        }

        .signature-label {
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            font-size: 11px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            border-top: 2px solid #e5e7eb;
            padding-top: 15px;
        }

        .generation-info {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 10px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .marksheet {
                margin: 0;
                padding: 15px;
            }
            
            .no-print {
                display: none !important;
            }
        }

        @page {
            margin: 1cm;
            size: A4;
        }
    </style>
</head>
<body>
    @foreach($marksheets as $marksheet)
    <div class="marksheet">
        <!-- Header -->
        <div class="header">
            <div class="institution-logo">AMS</div>
            <div class="institution-name">Academic Management System</div>
            <div class="institution-address">Excellence in Education | Kathmandu, Nepal</div>
            <div class="marksheet-title">Academic Marksheet</div>
        </div>

        <!-- Student Information -->
        <div class="student-info">
            <div class="info-section">
                <h3>Student Information</h3>
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $marksheet['student']->first_name }} {{ $marksheet['student']->last_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Roll Number:</span>
                    <span class="info-value">{{ $marksheet['student']->currentEnrollment->roll_no ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Class:</span>
                    <span class="info-value">{{ $marksheet['student']->currentEnrollment->class->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Program:</span>
                    <span class="info-value">{{ $marksheet['student']->currentEnrollment->program->name ?? 'N/A' }}</span>
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
                    <span class="info-label">Semester:</span>
                    <span class="info-value">{{ $exam->semester->name ?? 'N/A' }}</span>
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
                    <th style="width: 30%;">Subject</th>
                    <th style="width: 12%;">Full Marks</th>
                    <th style="width: 12%;">Pass Marks</th>
                    <th style="width: 12%;">Obtained</th>
                    <th style="width: 12%;">Percentage</th>
                    <th style="width: 10%;">Grade</th>
                    <th style="width: 12%;">Result</th>
                </tr>
            </thead>
            <tbody>
                @foreach($marksheet['marks'] as $mark)
                    <tr>
                        <td class="subject-name">{{ $mark->subject->name }}</td>
                        <td>{{ number_format($mark->exam->max_marks, 0) }}</td>
                        <td>{{ number_format($mark->exam->pass_marks, 0) }}</td>
                        <td>{{ number_format($mark->total_marks, 1) }}</td>
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
                <div class="summary-value">{{ number_format($marksheet['totalMarks'], 1) }}</div>
                <div class="summary-subtitle">Out of {{ number_format($marksheet['maxMarks'], 0) }}</div>
            </div>
            
            <div class="summary-card">
                <div class="summary-title">Overall Percentage</div>
                <div class="summary-value">{{ number_format($marksheet['overallPercentage'], 1) }}%</div>
                <div class="summary-subtitle">Grade: {{ $marksheet['overallGrade'] }}</div>
            </div>
        </div>

        <!-- Result -->
        <div class="result-section {{ strtolower($marksheet['overallResult']) }}">
            <div class="result-title {{ strtolower($marksheet['overallResult']) }}">
                {{ $marksheet['overallResult'] === 'Pass' ? '✓ PASSED' : '✗ FAILED' }}
            </div>
            @if($marksheet['overallResult'] === 'Pass')
                <p>Congratulations! You have successfully passed the examination.</p>
            @else
                <p>You need to improve in the failed subjects to pass the examination.</p>
            @endif
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-label">Class Teacher</div>
            </div>
            
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-label">Principal</div>
            </div>
            
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-label">Controller</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="generation-info">
                <span>Generated on: {{ $generatedAt->format('F j, Y \a\t g:i A') }}</span>
                <span>Bikram Sambat: {{ $bikramSambatDate }}</span>
            </div>
            <p style="font-size: 9px; color: #6b7280;">
                This is a computer-generated marksheet. For verification, please contact the institution.
            </p>
        </div>
    </div>
    @endforeach
</body>
</html>

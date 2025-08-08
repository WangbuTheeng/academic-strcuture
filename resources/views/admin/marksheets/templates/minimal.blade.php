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
            font-size: 11px;
            line-height: 1.3;
            color: #000;
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
            margin-bottom: 20px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }

        .institution-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .marksheet-title {
            font-size: 14px;
            font-weight: bold;
        }

        .student-info {
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            width: 100px;
            flex-shrink: 0;
        }

        .info-value {
            flex: 1;
        }

        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .marks-table th,
        .marks-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-size: 10px;
        }

        .marks-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .subject-name {
            text-align: left !important;
        }

        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #000;
        }

        .summary-item {
            text-align: center;
        }

        .summary-label {
            font-weight: bold;
            font-size: 10px;
        }

        .summary-value {
            font-size: 12px;
            font-weight: bold;
        }

        .result-section {
            text-align: center;
            padding: 10px;
            border: 1px solid #000;
            margin-bottom: 20px;
        }

        .result-title {
            font-size: 14px;
            font-weight: bold;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .signature {
            text-align: center;
            width: 30%;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            height: 30px;
            margin-bottom: 5px;
        }

        .signature-label {
            font-size: 10px;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            border-top: 1px solid #000;
            padding-top: 10px;
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
            margin: 0.5cm;
            size: A4;
        }
    </style>
</head>
<body>
    <div class="marksheet">
        <!-- Header -->
        <div class="header">
            <div class="institution-name">{{ (isset($instituteSettings) && $instituteSettings) ? $instituteSettings->institution_name : 'Academic Management System' }}</div>
            <div class="marksheet-title">Academic Marksheet</div>
        </div>

        <!-- Student Information -->
        <div class="student-info">
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $student->first_name }} {{ $student->last_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Roll No:</span>
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
            <div class="info-row">
                <span class="info-label">Exam:</span>
                <span class="info-value">{{ $exam->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Year:</span>
                <span class="info-value">{{ $exam->academicYear->name ?? 'N/A' }}</span>
            </div>
        </div>

        <!-- Marks Table -->
        <table class="marks-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 25%;">Subject</th>
                    <th rowspan="2" style="width: 10%;">Full</th>
                    <th rowspan="2" style="width: 10%;">Pass</th>
                    <th colspan="3" style="width: 30%;">Marks</th>
                    <th rowspan="2" style="width: 8%;">Total</th>
                    <th rowspan="2" style="width: 8%;">%</th>
                    <th rowspan="2" style="width: 9%;">Result</th>
                </tr>
                <tr>
                    <th style="width: 10%;">Th</th>
                    <th style="width: 10%;">Pr</th>
                    <th style="width: 10%;">As</th>
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
                        <td style="font-weight: bold;">{{ number_format($mark->total_marks, 1) }}</td>
                        <td>{{ number_format($mark->percentage, 1) }}</td>
                        <td>{{ $mark->result }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-item">
                <div class="summary-label">Total</div>
                <div class="summary-value">{{ number_format($totalMarks, 1) }}/{{ number_format($maxMarks, 0) }}</div>
            </div>
            
            <div class="summary-item">
                <div class="summary-label">Percentage</div>
                <div class="summary-value">{{ number_format($overallPercentage, 1) }}%</div>
            </div>
            
            <div class="summary-item">
                <div class="summary-label">Grade</div>
                <div class="summary-value">{{ $overallGrade }}</div>
            </div>
        </div>

        <!-- Result -->
        <div class="result-section">
            <div class="result-title">{{ $overallResult === 'Pass' ? 'PASSED' : 'FAILED' }}</div>
        </div>

        <!-- Remarks -->
        @if(isset($overallRemarks))
        <div style="border: 1px solid #000; padding: 10px; margin-bottom: 20px;">
            <div style="font-weight: bold; margin-bottom: 5px;">Remarks:</div>
            <div style="font-style: italic;">{{ $overallRemarks }}</div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-label">Teacher</div>
            </div>

            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-label">Principal</div>
                @if(isset($instituteSettings) && $instituteSettings && $instituteSettings->principal_name)
                    <div style="font-size: 9px; margin-top: 3px;">{{ $instituteSettings->principal_name }}</div>
                @endif
            </div>

            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-label">Controller</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Generated: {{ $generatedAt->format('Y-m-d') }} | {{ $bikramSambatDate }}
        </div>
    </div>

    @if(isset($isPreview) && $isPreview)
        <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
            <button onclick="window.print()" style="background: #333; color: white; padding: 8px 16px; border: none; cursor: pointer;">
                Print
            </button>
        </div>
    @endif
</body>
</html>

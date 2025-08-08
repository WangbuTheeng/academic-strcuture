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
            color: #000;
            background: #fff;
        }

        .marksheet {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background: #fff;
            border: 2px solid #000;
            page-break-after: always;
        }

        .marksheet:last-child {
            page-break-after: auto;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .institution-name {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .institution-address {
            font-size: 14px;
            margin-bottom: 15px;
        }

        .marksheet-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .student-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
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
            text-transform: uppercase;
            margin-bottom: 15px;
            text-decoration: underline;
        }

        .info-row {
            margin-bottom: 8px;
            display: flex;
        }

        .info-label {
            font-weight: bold;
            width: 120px;
            flex-shrink: 0;
        }

        .info-value {
            flex: 1;
            border-bottom: 1px dotted #000;
            padding-bottom: 2px;
        }

        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 2px solid #000;
        }

        .marks-table th,
        .marks-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .marks-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .subject-name {
            text-align: left !important;
            font-weight: bold;
        }

        .pass-mark {
            color: #000;
            font-weight: bold;
        }

        .fail-mark {
            color: #000;
            font-weight: bold;
            text-decoration: underline;
        }

        .summary {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border: 2px solid #000;
        }

        .summary-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 15px;
            border-right: 1px solid #000;
        }

        .summary-item:last-child {
            border-right: none;
        }

        .summary-label {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
        }

        .result-section {
            text-align: center;
            padding: 20px;
            border: 2px solid #000;
            margin-bottom: 30px;
        }

        .result-title {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .signatures {
            display: table;
            width: 100%;
            margin-top: 50px;
        }

        .signature {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 0 20px;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            height: 40px;
            margin-bottom: 10px;
        }

        .signature-label {
            font-weight: bold;
            text-transform: uppercase;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 15px;
        }

        .generation-info {
            font-size: 10px;
            margin-bottom: 10px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .marksheet {
                margin: 0;
                padding: 20px;
                border: none;
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
                        <td>{{ $mark->grade }}</td>
                        <td class="{{ $mark->result === 'Pass' ? 'pass-mark' : 'fail-mark' }}">
                            {{ $mark->result }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-item">
                <div class="summary-label">Total Marks</div>
                <div class="summary-value">{{ number_format($marksheet['totalMarks'], 1) }}/{{ number_format($marksheet['maxMarks'], 0) }}</div>
            </div>
            
            <div class="summary-item">
                <div class="summary-label">Overall Percentage</div>
                <div class="summary-value">{{ number_format($marksheet['overallPercentage'], 1) }}%</div>
            </div>
            
            <div class="summary-item">
                <div class="summary-label">Overall Grade</div>
                <div class="summary-value">{{ $marksheet['overallGrade'] }}</div>
            </div>
        </div>

        <!-- Result -->
        <div class="result-section">
            <div class="result-title">
                {{ $marksheet['overallResult'] === 'Pass' ? 'PASSED' : 'FAILED' }}
            </div>
            @if($marksheet['overallResult'] === 'Pass')
                <p>The student has successfully passed the examination.</p>
            @else
                <p>The student needs to improve in the failed subjects.</p>
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
                Generated on: {{ $generatedAt->format('F j, Y \a\t g:i A') }} | {{ $bikramSambatDate }}
            </div>
            <p style="font-size: 9px;">
                This is a computer-generated marksheet. For verification, please contact the institution.
            </p>
        </div>
    </div>
    @endforeach
</body>
</html>

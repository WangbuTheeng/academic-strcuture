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
            color: #000;
            background: #fff;
        }

        .marksheet {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background: #fff;
            border: 2px solid #000;
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
            margin-bottom: 5px;
        }

        .institution-contact {
            font-size: 12px;
            margin-bottom: 3px;
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
    <div class="marksheet">
        <!-- Header -->
        <div class="header">
            @if($instituteSettings && (is_object($instituteSettings) ? $instituteSettings->institution_logo : ($instituteSettings['institution_logo'] ?? null)))
                <img src="{{ is_object($instituteSettings) ? $instituteSettings->getLogoUrl() : asset('storage/' . $instituteSettings['institution_logo']) }}" alt="School Logo" style="width: 60px; height: 60px; margin: 0 auto 10px; display: block;">
            @endif
            <div class="institution-name">{{ (isset($instituteSettings) && $instituteSettings) ? (is_object($instituteSettings) ? $instituteSettings->institution_name : ($instituteSettings['institution_name'] ?? 'Academic Management System')) : 'Academic Management System' }}</div>
            <div class="institution-address">{{ (isset($instituteSettings) && $instituteSettings) ? (is_object($instituteSettings) ? $instituteSettings->institution_address : ($instituteSettings['institution_address'] ?? 'Excellence in Education | Kathmandu, Nepal')) : 'Excellence in Education | Kathmandu, Nepal' }}</div>
            @php
                $phone = (isset($instituteSettings) && $instituteSettings) ? (is_object($instituteSettings) ? $instituteSettings->institution_phone : ($instituteSettings['institution_phone'] ?? null)) : null;
                $email = (isset($instituteSettings) && $instituteSettings) ? (is_object($instituteSettings) ? $instituteSettings->institution_email : ($instituteSettings['institution_email'] ?? null)) : null;
            @endphp
            @if($phone)
                <div class="institution-contact">Phone: {{ $phone }}</div>
            @endif
            @if($email)
                <div class="institution-contact">Email: {{ $email }}</div>
            @endif
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
                        <td style="font-weight: bold;">{{ number_format($mark->total_marks, 1) }}</td>
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
                <div class="summary-value">{{ number_format($totalMarks, 1) }}/{{ number_format($maxMarks, 0) }}</div>
            </div>
            
            <div class="summary-item">
                <div class="summary-label">Overall Percentage</div>
                <div class="summary-value">{{ number_format($overallPercentage, 1) }}%</div>
            </div>
            
            <div class="summary-item">
                <div class="summary-label">Overall Grade</div>
                <div class="summary-value">{{ $overallGrade }}</div>
            </div>
        </div>

        <!-- Result -->
        <div class="result-section">
            <div class="result-title">
                {{ $overallResult === 'Pass' ? 'PASSED' : 'FAILED' }}
            </div>
            @if($overallResult === 'Pass')
                <p>The student has successfully passed the examination.</p>
            @else
                <p>The student needs to improve in the failed subjects.</p>
            @endif
        </div>

        <!-- Remarks Section -->
        @if(isset($overallRemarks))
        <div style="border: 2px solid #000; padding: 15px; margin-bottom: 30px; text-align: center;">
            <div style="font-weight: bold; text-transform: uppercase; margin-bottom: 10px; text-decoration: underline;">Remarks</div>
            <div style="font-style: italic;">{{ $overallRemarks }}</div>
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
                    <div style="font-size: 10px; margin-top: 5px;">{{ $instituteSettings->principal_name }}</div>
                @endif
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

    @if(isset($isPreview) && $isPreview)
        <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
            <button onclick="window.print()" style="background: #000; color: white; padding: 10px 20px; border: none; cursor: pointer; font-weight: 600;">
                Print Marksheet
            </button>
        </div>
    @endif
</body>
</html>

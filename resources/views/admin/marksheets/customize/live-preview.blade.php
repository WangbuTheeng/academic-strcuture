<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Preview - Marksheet Template</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
            font-family: {{ $settings['font_family'] ?? 'Arial' }}, sans-serif;
            font-size: {{ $settings['font_size'] ?? 12 }}px;
            color: {{ $settings['text_color'] ?? '#1f2937' }};
        }
        
        .preview-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .marksheet-container {
            padding: 20px;
        }
        
        .marksheet-header {
            background-color: {{ $settings['header_color'] ?? '#2563eb' }};
            color: white;
            padding: 15px;
            text-align: center;
            margin: -20px -20px 20px -20px;
        }
        
        .marksheet-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .marksheet-table th,
        .marksheet-table td {
            border: 1px {{ $settings['border_style'] ?? 'solid' }} #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .marksheet-table th {
            background-color: {{ $settings['header_color'] ?? '#2563eb' }};
            color: white;
            font-weight: bold;
        }
        
        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .school-logo {
            max-height: 80px;
            max-width: 80px;
        }
        
        .grade-summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 30px;
            margin-top: 40px;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 30px;
            padding-top: 5px;
        }
        
        {!! $customCss ?? '' !!}
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="marksheet-container">
            <!-- School Header -->
            <div class="marksheet-header">
                <div style="display: flex; align-items: center; justify-content: center; gap: 20px;">
                    @if(($settings['show_school_logo'] ?? true) && $instituteSettings && isset($instituteSettings->institution_logo) && $instituteSettings->institution_logo)
                        <img src="{{ $instituteSettings->getLogoUrl() }}" alt="School Logo" class="school-logo">
                    @endif
                    
                    <div style="text-align: center;">
                        <h1 style="margin: 0; font-size: 24px;">{{ $instituteSettings->institution_name ?? 'School Name' }}</h1>
                        @if($settings['show_school_address'] ?? true)
                            <p style="margin: 5px 0; font-size: 14px;">{{ $instituteSettings->institution_address ?? 'School Address' }}</p>
                            @if($instituteSettings->institution_phone)
                                <p style="margin: 0; font-size: 12px;">Phone: {{ $instituteSettings->institution_phone }}</p>
                            @endif
                        @endif
                        <h2 style="margin: 10px 0 0 0; font-size: 18px;">ACADEMIC TRANSCRIPT</h2>
                    </div>
                </div>
            </div>

            <!-- Student Information -->
            <div class="student-info">
                <div>
                    <table style="width: 100%; margin-bottom: 20px;">
                        <tr>
                            <td style="padding: 5px; font-weight: bold;">Student Name:</td>
                            <td style="padding: 5px;">{{ $student->first_name }} {{ $student->last_name }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; font-weight: bold;">Roll Number:</td>
                            <td style="padding: 5px;">{{ $student->currentEnrollment->roll_no ?? 'STU001' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; font-weight: bold;">Class:</td>
                            <td style="padding: 5px;">{{ $student->currentEnrollment->class->name ?? 'Class 10' }}</td>
                        </tr>
                    </table>
                </div>
                <div>
                    <table style="width: 100%; margin-bottom: 20px;">
                        <tr>
                            <td style="padding: 5px; font-weight: bold;">Examination:</td>
                            <td style="padding: 5px;">{{ $exam->name }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; font-weight: bold;">Academic Year:</td>
                            <td style="padding: 5px;">{{ $exam->academicYear->name ?? '2024-2025' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; font-weight: bold;">Date of Issue:</td>
                            <td style="padding: 5px;">{{ $generatedAt->format('F j, Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Marks Table -->
            <table class="marksheet-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Subject</th>
                        @if($settings['show_theory_practical'] ?? true)
                            <th style="width: 12%;">Theory</th>
                            <th style="width: 12%;">Practical</th>
                        @endif
                        @if($settings['show_assessment_marks'] ?? true)
                            <th style="width: 12%;">Assessment</th>
                        @endif
                        <th style="width: 12%;">Total</th>
                        <th style="width: 10%;">Grade</th>
                        @if($settings['show_grade_points'] ?? true)
                            <th style="width: 8%;">GP</th>
                        @endif
                        <th style="width: 10%;">Result</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($marks as $mark)
                        <tr>
                            <td style="font-weight: bold;">{{ $mark->subject->name }}</td>
                            @if($settings['show_theory_practical'] ?? true)
                                <td style="text-align: center;">{{ $mark->theory_marks ?? '-' }}</td>
                                <td style="text-align: center;">{{ $mark->practical_marks ?? '-' }}</td>
                            @endif
                            @if($settings['show_assessment_marks'] ?? true)
                                <td style="text-align: center;">{{ $mark->assess_marks ?? '-' }}</td>
                            @endif
                            <td style="text-align: center; font-weight: bold;">{{ $mark->total_marks }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ $mark->grade }}</td>
                            @if($settings['show_grade_points'] ?? true)
                                <td style="text-align: center;">3.6</td>
                            @endif
                            <td style="text-align: center;">
                                <span style="color: {{ $mark->result == 'Pass' ? '#10b981' : '#ef4444' }}; font-weight: bold;">
                                    {{ $mark->result }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary -->
            <div class="grade-summary">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                    <div>
                        <strong>Total Marks:</strong> {{ $totalMarks }}/{{ $maxMarks }}
                    </div>
                    <div>
                        <strong>Percentage:</strong> {{ number_format($overallPercentage, 1) }}%
                    </div>
                    <div>
                        <strong>Overall Grade:</strong> {{ $overallGrade }}
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <strong>Result:</strong> 
                        <span style="color: {{ $overallResult == 'Pass' ? '#10b981' : '#ef4444' }}; font-weight: bold;">
                            {{ $overallResult }}
                        </span>
                    </div>
                    @if($settings['show_grade_points'] ?? true)
                        <div>
                            <strong>CGPA:</strong> 3.45
                        </div>
                    @endif
                </div>
                
                @if(($settings['show_remarks'] ?? true) && $overallRemarks)
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
                        <strong>Remarks:</strong> {{ $overallRemarks }}
                    </div>
                @endif
            </div>

            <!-- Signatures -->
            <div class="signature-section">
                <div>
                    <div class="signature-line">Class Teacher</div>
                </div>
                <div>
                    <div class="signature-line">Examination Controller</div>
                </div>
                <div>
                    @if($settings['show_principal_name'] ?? true)
                        <div class="signature-line">
                            {{ $instituteSettings->principal_name ?? 'Principal' }}
                            <br><small>Principal</small>
                        </div>
                    @else
                        <div class="signature-line">Principal</div>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #6b7280;">
                <p>This is a computer-generated document. No signature is required.</p>
                <p>Generated on {{ $generatedAt->format('F j, Y \a\t g:i A') }} | {{ $bikramSambatDate }}</p>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh when parent window changes
        window.addEventListener('message', function(event) {
            if (event.data.type === 'updatePreview') {
                location.reload();
            }
        });
    </script>
</body>
</html>

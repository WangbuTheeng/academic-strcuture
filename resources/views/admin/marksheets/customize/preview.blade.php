<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marksheet Preview - {{ $template->name ?? 'Custom Template' }}</title>
    <style>
        @if($template)
            {!! $template->getCssStyles() !!}
        @endif

        body {
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
            font-family: {{ $template ? $template->getSetting('font_family', 'Arial') : 'Arial' }}, sans-serif;
        }
        
        .preview-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .preview-header {
            background: #2563eb;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .preview-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .btn {
            padding: 8px 16px;
            margin: 0 4px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
        
        .btn-primary { background: #2563eb; color: white; }
        .btn-secondary { background: #6b7280; color: white; }
        .btn-success { background: #10b981; color: white; }
        
        @media print {
            .preview-header,
            .preview-actions,
            .no-print {
                display: none !important;
            }
            
            .preview-container {
                box-shadow: none;
                border-radius: 0;
                margin: 0;
                max-width: none;
            }
            
            body {
                background: white;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Preview Actions -->
    @if(!isset($isPdfGeneration) || !$isPdfGeneration)
    <div class="preview-actions no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print
        </button>
        @if($template)
        <a href="{{ route('admin.marksheets.customize.edit', $template) }}" class="btn btn-secondary">
            <i class="fas fa-edit"></i> Edit
        </a>
        @endif
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> Close
        </button>
    </div>

    <!-- Preview Header -->
    <div class="preview-header no-print">
        <div>
            <h3 style="margin: 0;">{{ $template->name ?? 'Custom Template' }} - Preview</h3>
            <small>{{ isset($isPreview) && $isPreview ? 'Sample Data Preview' : 'Live Preview' }}</small>
        </div>
        <div>
            <span class="badge" style="background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 4px;">
                {{ $template ? $template->getTypeLabel() : 'Custom' }}
            </span>
        </div>
    </div>
    @endif

    <!-- Marksheet Content -->
    <div class="preview-container">
        <div class="marksheet-container">
            <!-- School Header -->
            <div class="marksheet-header">
                <div class="school-header-content">
                    @if(($template && $template->hasSetting('show_school_logo')) && $instituteSettings->institution_logo)
                        <img src="{{ $instituteSettings->getLogoUrl() }}" alt="School Logo" class="school-logo">
                    @endif

                    <div class="school-info" style="text-align: center;">
                        @if(!$template || $template->hasSetting('show_school_name'))
                            <h1>{{ $instituteSettings->institution_name ?? 'School Name' }}</h1>
                        @endif

                        @if(!$template || $template->hasSetting('show_school_address'))
                            <p>{{ $instituteSettings->institution_address ?? 'School Address' }}</p>
                        @endif

                        @if(!$template || $template->hasSetting('show_contact_info'))
                            @if($instituteSettings->institution_phone)
                                <p class="contact-info">Phone: {{ $instituteSettings->institution_phone }}</p>
                            @endif
                            @if($instituteSettings->institution_email)
                                <p class="contact-info">Email: {{ $instituteSettings->institution_email }}</p>
                            @endif
                            @if($instituteSettings->institution_website)
                                <p class="contact-info">Website: {{ $instituteSettings->institution_website }}</p>
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

            <!-- Dynamic Marks Table -->
            @include('admin.marksheets.customize.partials.dynamic-table', [
                'template' => $template,
                'exam' => $exam ?? null,
                'marks' => $marks,
                'totalMarks' => $totalMarks ?? 0,
                'maxMarks' => $maxMarks ?? 0,
                'overallPercentage' => $overallPercentage ?? 0,
                'overallGrade' => $overallGrade ?? 'N/A',
                'overallResult' => $overallResult ?? 'Fail',
                'overallRemarks' => $overallRemarks ?? 'Keep up the good work!'
            ])

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
                    @if($template && $template->hasSetting('show_grade_points') && isset($gradingScale))
                        <div>
                            <strong>CGPA:</strong> {{ number_format($gradingScale->calculateGrade($overallPercentage)['gpa'] ?? 0, 2) }}
                        </div>
                    @endif
                </div>

                @if((!$template || $template->hasSetting('show_remarks')) && $overallRemarks)
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
                    @if(($template && $template->hasSetting('show_principal_signature')) && $instituteSettings->getPrincipalSignatureUrl())
                        <img src="{{ $instituteSettings->getPrincipalSignatureUrl() }}" alt="Principal Signature" class="principal-signature">
                    @endif

                    @if(!$template || $template->hasSetting('show_principal_name'))
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
        // Auto-print if requested
        if (new URLSearchParams(window.location.search).get('print') === '1') {
            window.onload = function() {
                setTimeout(() => window.print(), 500);
            };
        }
    </script>
</body>
</html>

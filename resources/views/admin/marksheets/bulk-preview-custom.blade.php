<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Custom Marksheet Preview - {{ $exam->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @if($template)
            {!! $template->getCssStyles() !!}
        @endif

        body {
            font-family: {{ $template ? $template->getSetting('font_family', 'Arial') : 'Arial' }}, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .preview-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
            border-radius: 10px;
        }

        .marksheet-preview {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
            transition: transform 0.3s ease;
            page-break-after: always;
        }

        .marksheet-preview:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .marksheet-preview:last-child {
            page-break-after: auto;
        }

        .marksheet-container {
            padding: 20px;
        }

        /* School Header Styles */
        .marksheet-header {
            margin-bottom: 20px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }

        .school-header-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }

        .school-logo {
            max-height: 80px;
            max-width: 80px;
            object-fit: contain;
        }

        .school-info {
            flex: 1;
        }

        .school-info h1 {
            margin: 0 0 5px 0;
            font-size: 24px;
            color: #1f2937;
            font-weight: bold;
        }

        .school-info p {
            margin: 2px 0;
            color: #6b7280;
            font-size: 14px;
        }

        .contact-info {
            font-size: 12px !important;
        }

        /* Student Info Styles */
        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .student-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .student-info td {
            padding: 5px;
            border: none;
        }

        .student-info td:first-child {
            font-weight: bold;
            color: #374151;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .preview-header {
                display: none;
            }

            .marksheet-preview {
                box-shadow: none;
                border-radius: 0;
                margin-bottom: 0;
                page-break-after: always;
            }

            .marksheet-preview:last-child {
                page-break-after: auto;
            }
        }
        
        .student-info small {
            opacity: 0.9;
        }
        
        .result-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .result-pass {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 2px solid #28a745;
        }
        
        .result-fail {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 2px solid #dc3545;
        }
        
        .marksheet-content {
            padding: 20px;
        }
        
        .action-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .btn-floating {
            border-radius: 50px;
            padding: 12px 20px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            margin-left: 10px;
        }
        
        /* Custom Template Styles */
        {!! $template->custom_css ?? '' !!}
        
        @media print {
            .preview-header, .action-buttons {
                display: none;
            }
            
            .marksheet-preview {
                box-shadow: none;
                margin-bottom: 50px;
                page-break-after: always;
            }
            
            .marksheet-preview:last-child {
                page-break-after: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Action Buttons -->
    <div class="action-buttons">
        <button onclick="window.print()" class="btn btn-primary btn-floating">
            <i class="fas fa-print"></i> Print All
        </button>
        <button onclick="window.close()" class="btn btn-secondary btn-floating">
            <i class="fas fa-times"></i> Close
        </button>
    </div>

    <!-- Header -->
    <div class="preview-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-1">
                        <i class="fas fa-palette"></i> Custom Template Preview
                    </h2>
                    <p class="mb-0 opacity-75">{{ $template->name }} - {{ count($marksheetData) }} Students</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="badge bg-light text-dark fs-6 px-3 py-2">
                        {{ $exam->academicYear->name ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        @foreach($marksheetData as $data)
            <div class="marksheet-preview">
                <div class="marksheet-container">
                    <!-- School Header -->
                    <div class="marksheet-header">
                        <div class="school-header-content">
                            @if(($template && $template->hasSetting('show_school_logo')) && $instituteSettings && (is_object($instituteSettings) ? $instituteSettings->institution_logo : ($instituteSettings['institution_logo'] ?? null)))
                                <img src="{{ is_object($instituteSettings) ? $instituteSettings->getLogoUrl() : asset('storage/' . $instituteSettings['institution_logo']) }}" alt="School Logo" class="school-logo">
                            @endif

                            <div class="school-info" style="text-align: center;">
                                @if(!$template || $template->hasSetting('show_school_name'))
                                    <h1>{{ is_object($instituteSettings) ? ($instituteSettings->institution_name ?? 'School Name') : ($instituteSettings['institution_name'] ?? 'School Name') }}</h1>
                                @endif

                                @if(!$template || $template->hasSetting('show_school_address'))
                                    <p>{{ is_object($instituteSettings) ? ($instituteSettings->institution_address ?? 'School Address') : ($instituteSettings['institution_address'] ?? 'School Address') }}</p>
                                @endif

                                @if(!$template || $template->hasSetting('show_contact_info'))
                                    @php
                                        $phone = is_object($instituteSettings) ? $instituteSettings->institution_phone : ($instituteSettings['institution_phone'] ?? null);
                                        $email = is_object($instituteSettings) ? $instituteSettings->institution_email : ($instituteSettings['institution_email'] ?? null);
                                        $website = is_object($instituteSettings) ? $instituteSettings->institution_website : ($instituteSettings['institution_website'] ?? null);
                                    @endphp
                                    @if($phone)
                                        <p class="contact-info">Phone: {{ $phone }}</p>
                                    @endif
                                    @if($email)
                                        <p class="contact-info">Email: {{ $email }}</p>
                                    @endif
                                    @if($website)
                                        <p class="contact-info">Website: {{ $website }}</p>
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
                                    <td style="padding: 5px;">{{ $data['student']->first_name }} {{ $data['student']->last_name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px; font-weight: bold;">Roll Number:</td>
                                    <td style="padding: 5px;">{{ $data['student']->currentEnrollment->roll_no ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px; font-weight: bold;">Class:</td>
                                    <td style="padding: 5px;">{{ $data['student']->currentEnrollment->class->name ?? 'N/A' }}</td>
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
                                    <td style="padding: 5px;">{{ $exam->academicYear->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px; font-weight: bold;">Date of Issue:</td>
                                    <td style="padding: 5px;">{{ $generatedAt->format('F j, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Dynamic Marks Table -->
                    @php
                        $marks = $data['marks'];
                        $totalMarks = $data['totalMarks'];
                        $maxMarks = $data['maxMarks'];
                        $overallPercentage = $data['overallPercentage'];
                        $overallGrade = $data['overallGrade'];
                        $overallResult = $data['overallResult'];
                        $overallRemarks = $data['overallRemarks'];
                        $student = $data['student'];
                    @endphp

                    @include('admin.marksheets.customize.partials.dynamic-table', [
                        'template' => $template,
                        'exam' => $exam,
                        'marks' => $marks,
                        'totalMarks' => $totalMarks,
                        'maxMarks' => $maxMarks,
                        'overallPercentage' => $overallPercentage,
                        'overallGrade' => $overallGrade,
                        'overallResult' => $overallResult,
                        'overallRemarks' => $overallRemarks,
                        'student' => $student,
                        'instituteSettings' => $instituteSettings
                    ])

                    <!-- Summary -->
                    <div class="grade-summary" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px solid #dee2e6; margin-top: 20px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                            <div>
                                <strong>Total Marks:</strong> {{ $totalMarks }}/{{ $maxMarks }}
                            </div>
                            <div>
                                <strong>Percentage:</strong> {{ number_format($overallPercentage, 1) }}%
                            </div>
                            <div>
                                <strong>Grade:</strong> {{ $overallGrade }}
                            </div>
                        </div>
                        <div style="text-align: center; margin-top: 15px;">
                            <strong>Result: </strong>
                            <span style="color: {{ $overallResult === 'Pass' ? '#10b981' : '#ef4444' }}; font-weight: bold;">
                                {{ $overallResult }}
                            </span>
                        </div>
                        @if($overallRemarks)
                            <div style="margin-top: 10px; text-align: center; font-style: italic;">
                                <strong>Remarks:</strong> {{ $overallRemarks }}
                            </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div style="margin-top: 30px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="margin: 0; font-size: 12px; color: #6b7280;">
                                Generated on: {{ $generatedAt->format('F j, Y \a\t g:i A') }}
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <div style="border-top: 1px solid #000; width: 200px; margin-top: 40px; padding-top: 5px;">
                                <p style="margin: 0; font-size: 12px;">Principal's Signature</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

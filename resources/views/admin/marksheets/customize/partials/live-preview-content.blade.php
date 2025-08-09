<!-- Live Preview Content -->
<div class="marksheet-container" style="font-family: {{ $template->settings['font_family'] ?? 'Arial' }}, sans-serif; font-size: {{ $template->settings['font_size'] ?? 12 }}px; color: {{ $template->settings['text_color'] ?? '#333' }};">
    
    <!-- Custom CSS Styles -->
    <style>
        .marksheet-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
        }
        
        .marksheet-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid {{ $template->settings['header_color'] ?? '#2563eb' }};
        }
        
        .school-logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
            background: {{ $template->settings['header_color'] ?? '#2563eb' }};
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        .marksheet-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .marksheet-table th,
        .marksheet-table td {
            border: 1px {{ $template->settings['border_style'] ?? 'solid' }} #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .marksheet-table th {
            background-color: {{ $template->settings['header_color'] ?? '#2563eb' }};
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        .grade-summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            margin: 20px 0;
        }
        
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-line {
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 5px;
            width: 150px;
        }
        
        @if($template->custom_css)
            {!! $template->custom_css !!}
        @endif
    </style>
    
    <!-- School Header -->
    <div class="marksheet-header">
        <div class="school-header-content">
            {{-- School Logo (Left side) --}}
            <div class="logo-section">
                @if(($template->settings['show_school_logo'] ?? true))
                    @if($instituteSettings && $instituteSettings->institution_logo)
                        <img src="{{ $instituteSettings->getLogoUrl() }}" alt="School Logo" class="school-logo">
                    @else
                        <i class="fas fa-graduation-cap school-logo" style="font-size: 40px;"></i>
                    @endif
                @endif
            </div>

            {{-- School Information (Center) --}}
            <div class="school-info" style="text-align: center;">
                @if(($template->settings['show_school_name'] ?? true))
                    <h1 style="margin: 10px 0; font-size: 24px;">{{ $instituteSettings->institution_name ?? 'Academic Institution' }}</h1>
                @endif

                @if(($template->settings['show_school_address'] ?? true))
                    <p style="margin: 5px 0;">{{ $instituteSettings->institution_address ?? 'Institution Address' }}</p>
                @endif

                <h2 style="margin: 15px 0 0 0; font-size: 18px; color: {{ $template->settings['header_color'] ?? '#2563eb' }};">ACADEMIC TRANSCRIPT</h2>
            </div>

            {{-- School Seal (Right side) --}}
            <div class="seal-section">
                <div class="school-seal">
                    <i class="fas fa-certificate" style="font-size: 40px; opacity: 0.7;"></i>
                    <p style="font-size: 10px; margin: 5px 0 0 0; opacity: 0.7;">School Seal</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Student Information -->
    <div class="student-info" style="margin-bottom: 20px;">
        <table style="width: 100%;">
            <tr>
                <td style="padding: 5px; font-weight: bold; width: 25%;">Student Name:</td>
                <td style="padding: 5px;">{{ $student->first_name ?? 'John' }} {{ $student->last_name ?? 'Doe' }}</td>
                <td style="padding: 5px; font-weight: bold; width: 25%;">Roll Number:</td>
                <td style="padding: 5px;">{{ $student->roll_no ?? 'STU001' }}</td>
            </tr>
            <tr>
                <td style="padding: 5px; font-weight: bold;">Class:</td>
                <td style="padding: 5px;">{{ $exam->class->name ?? 'Grade 10' }}</td>
                <td style="padding: 5px; font-weight: bold;">Exam:</td>
                <td style="padding: 5px;">{{ $exam->name ?? 'Final Examination' }}</td>
            </tr>
            <tr>
                <td style="padding: 5px; font-weight: bold;">Academic Year:</td>
                <td style="padding: 5px;">{{ $exam->academicYear->name ?? '2024-2025' }}</td>
                <td style="padding: 5px; font-weight: bold;">Date:</td>
                <td style="padding: 5px;">{{ $generatedAt->format('F j, Y') }}</td>
            </tr>
        </table>
    </div>
    
    <!-- Marks Table -->
    <table class="marksheet-table">
        <thead>
            <tr>
                <th>Subject</th>
                @if(($template->settings['show_theory_practical'] ?? true))
                    <th>Theory</th>
                    <th>Practical</th>
                @endif
                <th>Total</th>
                <th>Grade</th>
                <th>Result</th>
            </tr>
        </thead>
        <tbody>
            @foreach($marks as $mark)
            <tr>
                <td>{{ $mark->subject->name ?? 'Mathematics' }}</td>
                @if(($template->settings['show_theory_practical'] ?? true))
                    <td style="text-align: center;">{{ $mark->theory_marks ?? '75' }}</td>
                    <td style="text-align: center;">{{ $mark->practical_marks ?? '20' }}</td>
                @endif
                <td style="text-align: center; font-weight: bold;">{{ $mark->total_marks ?? '95' }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $mark->grade ?? 'A+' }}</td>
                <td style="text-align: center;">
                    <span style="color: {{ ($mark->result ?? 'Pass') == 'Pass' ? '#10b981' : '#ef4444' }}; font-weight: bold;">
                        {{ $mark->result ?? 'Pass' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Grade Summary -->
    <div class="grade-summary">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <strong>Total Marks:</strong> {{ $totalMarks ?? '475' }}/{{ $maxMarks ?? '500' }}
            </div>
            <div>
                <strong>Percentage:</strong> {{ number_format($overallPercentage ?? 95, 2) }}%
            </div>
            <div>
                <strong>Grade:</strong> 
                <span style="color: {{ $template->settings['header_color'] ?? '#2563eb' }}; font-weight: bold; font-size: 18px;">
                    {{ $overallGrade ?? 'A+' }}
                </span>
            </div>
            <div>
                <strong>Result:</strong> 
                <span style="color: {{ ($overallResult ?? 'Pass') == 'Pass' ? '#10b981' : '#ef4444' }}; font-weight: bold;">
                    {{ $overallResult ?? 'Pass' }}
                </span>
            </div>
        </div>
        
        @if(($template->settings['show_remarks'] ?? true) && $overallRemarks)
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
                <strong>Remarks:</strong> {{ $overallRemarks ?? 'Excellent performance!' }}
            </div>
        @endif
    </div>
    
    <!-- Signatures -->
    @if(($template->settings['show_principal_name'] ?? true))
    <div class="signature-section">
        <div>
            <div class="signature-line">Class Teacher</div>
        </div>
        <div>
            <div class="signature-line">Examination Controller</div>
        </div>
        <div>
            <div class="signature-line">
                {{ $instituteSettings->principal_name ?? 'Principal' }}
                <br><small>Principal</small>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Footer -->
    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #6b7280;">
        <p>This is a computer-generated document. No signature is required.</p>
        <p>Generated on {{ $generatedAt->format('F j, Y \a\t g:i A') }}</p>
    </div>
</div>

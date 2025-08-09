<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Marksheet Preview - {{ $exam->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .preview-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }
        
        .marksheet-preview {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .marksheet-preview:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .student-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .student-info h5 {
            margin: 0;
            font-weight: 600;
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
        
        .institute-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .institute-logo {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
        }
        
        .marks-table {
            font-size: 14px;
        }
        
        .marks-table th {
            background: #f8f9fa;
            font-weight: 600;
            border-top: none;
        }
        
        .summary-row {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
        }
        
        .grade-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
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
                        <i class="fas fa-eye"></i> Bulk Marksheet Preview
                    </h2>
                    <p class="mb-0 opacity-75">{{ $exam->name }} - {{ count($marksheetData) }} Students</p>
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
                <!-- Student Header -->
                <div class="student-header">
                    <div class="student-info">
                        <h5>{{ $data['student']->full_name }}</h5>
                        <small>Roll No: {{ $data['student']->currentEnrollment->roll_no ?? 'N/A' }} | 
                               Class: {{ $data['student']->currentEnrollment->class->name ?? 'N/A' }}</small>
                    </div>
                    <div class="result-badge {{ $data['overallResult'] === 'Pass' ? 'result-pass' : 'result-fail' }}">
                        {{ $data['overallResult'] }}
                    </div>
                </div>

                <!-- Marksheet Content -->
                <div class="marksheet-content">
                    <!-- Institute Header -->
                    <div class="institute-header">
                        @if($instituteSettings && $instituteSettings->institution_logo)
                            <img src="{{ $instituteSettings->getLogoUrl() }}" alt="Institute Logo" class="institute-logo">
                        @endif
                        <h4 class="text-primary mb-1">{{ $instituteSettings ? $instituteSettings->institution_name : 'Academic Institution' }}</h4>
                        <p class="text-muted mb-0">{{ $instituteSettings ? $instituteSettings->institution_address : 'Institution Address' }}</p>
                        @if($instituteSettings && $instituteSettings->institution_phone)
                            <p class="text-muted mb-0">Phone: {{ $instituteSettings->institution_phone }}</p>
                        @endif
                        @if($instituteSettings && $instituteSettings->institution_email)
                            <p class="text-muted mb-0">Email: {{ $instituteSettings->institution_email }}</p>
                        @endif
                        <h6 class="mt-2 mb-0">MARKSHEET</h6>
                    </div>

                    <!-- Student Details -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Student Name:</strong> {{ $data['student']->full_name }}<br>
                            <strong>Roll Number:</strong> {{ $data['student']->currentEnrollment->roll_no ?? 'N/A' }}<br>
                            <strong>Class:</strong> {{ $data['student']->currentEnrollment->class->name ?? 'N/A' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Exam:</strong> {{ $exam->name }}<br>
                            <strong>Academic Year:</strong> {{ $exam->academicYear->name ?? 'N/A' }}<br>
                            <strong>Date:</strong> {{ $generatedAt->format('F j, Y') }}
                        </div>
                    </div>

                    <!-- Marks Table -->
                    <table class="table table-bordered marks-table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Theory</th>
                                <th>Practical</th>
                                <th>Total</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['marks'] as $mark)
                                <tr>
                                    <td>{{ $mark->subject->name }}</td>
                                    <td>{{ $mark->theory_marks ?? '-' }}</td>
                                    <td>{{ $mark->practical_marks ?? '-' }}</td>
                                    <td>{{ $mark->total_marks }}</td>
                                    <td>
                                        @if($gradingScale)
                                            @php
                                                $percentage = $mark->max_marks > 0 ? ($mark->total_marks / $mark->max_marks) * 100 : 0;
                                                $gradeRange = $gradingScale->gradeRanges()
                                                    ->where('min_percentage', '<=', $percentage)
                                                    ->where('max_percentage', '>=', $percentage)
                                                    ->first();
                                            @endphp
                                            {{ $gradeRange->grade ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="summary-row">
                                <td><strong>TOTAL</strong></td>
                                <td colspan="2"><strong>{{ number_format($data['totalMarks'], 1) }}/{{ number_format($data['maxMarks'], 1) }}</strong></td>
                                <td><strong>{{ number_format($data['overallPercentage'], 1) }}%</strong></td>
                                <td><strong>{{ $data['overallGrade'] }}</strong></td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Grade Information -->
                    <div class="grade-info">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Overall Grade:</strong> {{ $data['overallGrade'] }}
                            </div>
                            <div class="col-md-4">
                                <strong>Percentage:</strong> {{ number_format($data['overallPercentage'], 1) }}%
                            </div>
                            <div class="col-md-4">
                                <strong>Result:</strong> 
                                <span class="badge {{ $data['overallResult'] === 'Pass' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $data['overallResult'] }}
                                </span>
                            </div>
                        </div>
                        @if($data['overallRemarks'])
                            <div class="mt-2">
                                <strong>Remarks:</strong> {{ $data['overallRemarks'] }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

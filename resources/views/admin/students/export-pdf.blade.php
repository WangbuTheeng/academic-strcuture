<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Students List - {{ date('Y-m-d H:i:s') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .school-info {
            margin-bottom: 20px;
        }
        .school-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 15px;
        }
        .school-header.no-logo {
            justify-content: center;
        }
        .school-header.no-logo .school-details {
            text-align: center;
        }
        .school-logo {
            flex-shrink: 0;
        }
        .school-details {
            text-align: left;
        }
        @media print {
            .school-header {
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        @if($school)
        <div class="school-info">
            <div class="school-header {{ $school->logo_path ? '' : 'no-logo' }}">
                @if($school->logo_path)
                <div class="school-logo">
                    <img src="{{ asset('storage/' . $school->logo_path) }}" alt="{{ $school->name }} Logo" style="max-height: 80px; max-width: 120px;">
                </div>
                @endif
                <div class="school-details">
                    <h1 style="margin: 0; color: #2c3e50; font-size: 24px;">{{ $school->name }}</h1>
                    @if($school->address)
                    <p style="margin: 2px 0; color: #666; font-size: 14px;"><strong>Address:</strong> {{ $school->address }}</p>
                    @endif
                    @if($school->phone)
                    <p style="margin: 2px 0; color: #666; font-size: 14px;"><strong>Phone:</strong> {{ $school->phone }}</p>
                    @endif
                    @if($school->email)
                    <p style="margin: 2px 0; color: #666; font-size: 14px;"><strong>Email:</strong> {{ $school->email }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div style="margin-top: 20px;">
            <h2 style="color: #34495e; margin: 10px 0;">Students List</h2>
            <p style="margin: 5px 0; color: #666;">Generated on: {{ date('F j, Y \a\t g:i A') }}</p>
            <p style="margin: 5px 0; color: #666;">Total Students: {{ count($students) }}</p>
        </div>
        @else
        <h1>Students List</h1>
        <p>Generated on: {{ date('F j, Y \a\t g:i A') }}</p>
        <p>Total Students: {{ count($students) }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Admission No.</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Class</th>
                <th>Status</th>
                <th>Admission Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
            <tr>
                <td>{{ $student->admission_number }}</td>
                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                <td>{{ $student->gender }}</td>
                <td>{{ $student->phone }}</td>
                <td>{{ $student->currentEnrollment?->class?->name ?? 'Not Enrolled' }}</td>
                <td>{{ ucfirst($student->status) }}</td>
                <td>{{ $student->admission_date ? $student->admission_date->format('Y-m-d') : '' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; color: #666;">No students found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        @if($school)
        <p>This report was generated automatically by {{ $school->name }}</p>
        <p>Academic Management System</p>
        @else
        <p>This report was generated automatically by the Academic Management System</p>
        @endif
    </div>

    <script>
        // Auto-print when opened
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>

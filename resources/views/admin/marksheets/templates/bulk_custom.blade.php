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

        /* Custom template styles will be injected here */
        @if($template && $template->custom_css)
            {!! $template->custom_css !!}
        @endif
    </style>
</head>
<body>
    @foreach($marksheets as $marksheet)
        <div class="marksheet">
            @include('admin.marksheets.customize.preview', [
                'student' => $marksheet['student'],
                'exam' => $exam,
                'marks' => $marksheet['marks'],
                'totalMarks' => $marksheet['totalMarks'],
                'maxMarks' => $marksheet['maxMarks'],
                'overallPercentage' => $marksheet['overallPercentage'],
                'overallGrade' => $marksheet['overallGrade'],
                'overallResult' => $marksheet['overallResult'],
                'overallRemarks' => $marksheet['overallRemarks'],
                'instituteSettings' => $instituteSettings,
                'generatedAt' => $generatedAt,
                'bikramSambatDate' => $bikramSambatDate,
                'template' => $template,
                'gradingScale' => $gradingScale,
                'isPdfGeneration' => true
            ])
        </div>
    @endforeach
</body>
</html>

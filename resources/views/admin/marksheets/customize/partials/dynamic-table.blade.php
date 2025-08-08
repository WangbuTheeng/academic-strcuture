{{-- Enhanced Dynamic Marksheet Table --}}
@php
    $tableColumns = $template->getTableColumns($exam ?? null);
    $hasAssessment = $exam && $exam->has_assessment && $exam->assess_max > 0;
    $hasPractical = $exam && $exam->has_practical && $exam->practical_max > 0;
    $hasTheory = $exam && $exam->theory_max > 0;
@endphp

{{-- Only show table if there are columns to display --}}
@if(count($tableColumns) > 0)
<table class="marksheet-table">
    <thead>
        <tr>
            @foreach($tableColumns as $column)
                <th style="width: {{ $column['width'] }};"
                    data-column-type="{{ $column['type'] ?? 'text' }}"
                    @if(isset($column['max_marks'])) data-max-marks="{{ $column['max_marks'] }}" @endif>
                    {{ $column['label'] }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($marks as $mark)
            <tr>
                @foreach($tableColumns as $column)
                    @php
                        $cellClass = match($column['key']) {
                            'subject' => 'subject-cell',
                            'total_marks' => 'total-cell',
                            'grade' => 'grade-cell',
                            'theory_marks', 'practical_marks', 'assessment_marks' => 'marks-cell',
                            'result' => 'result-cell',
                            default => 'marks-cell'
                        };

                        // Add additional classes based on column type
                        if (isset($column['type'])) {
                            $cellClass .= ' ' . $column['type'] . '-type';
                        }
                    @endphp
                    <td class="{{ $cellClass }}" data-column="{{ $column['key'] }}">
                        @switch($column['key'])
                            @case('subject')
                                {{ $mark->subject->name }}
                                @break
                            
                            @case('theory_marks')
                                @if($hasTheory)
                                    <span class="marks-value">{{ $mark->theory_marks ? number_format($mark->theory_marks, 0) : '-' }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                                @break

                            @case('practical_marks')
                                @if($hasPractical)
                                    <span class="marks-value">{{ $mark->practical_marks ? number_format($mark->practical_marks, 0) : '-' }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                                @break

                            @case('assessment_marks')
                                @if($hasAssessment)
                                    <span class="marks-value">{{ $mark->assess_marks ? number_format($mark->assess_marks, 0) : '-' }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                                @break
                            
                            @case('total_marks')
                                <span class="total-marks-value"><strong>{{ $mark->total_marks ? number_format($mark->total_marks, 0) : ($mark->total ? number_format($mark->total, 0) : '-') }}</strong></span>
                                @break
                            
                            @case('grade')
                                <strong>{{ $mark->grade }}</strong>
                                @break
                            
                            @case('grade_points')
                                @if($template->gradingScale)
                                    {{ number_format($template->gradingScale->getGradePoint($mark->percentage), 1) }}
                                @else
                                    -
                                @endif
                                @break
                            
                            @case('attendance')
                                @if(isset($mark->attendance))
                                    {{ $mark->attendance }}%
                                @else
                                    95%
                                @endif
                                @break
                            
                            @case('result')
                                <span class="{{ $mark->result == 'Pass' ? 'result-pass' : 'result-fail' }}">
                                    {{ $mark->result }}
                                </span>
                                @break
                            
                            @case('rank')
                                {{ $loop->iteration }}
                                @break
                            
                            @case('percentage')
                                {{ number_format($mark->percentage, 1) }}%
                                @break
                            
                            @case('max_marks')
                                {{ $mark->max_marks }}
                                @break
                            
                            @case('obtained_marks')
                                {{ $mark->obtained_marks }}
                                @break
                            
                            @case('homework_marks')
                                @if(isset($mark->homework_marks))
                                    {{ $mark->homework_marks }}
                                @else
                                    -
                                @endif
                                @break
                            
                            @case('project_marks')
                                @if(isset($mark->project_marks))
                                    {{ $mark->project_marks }}
                                @else
                                    -
                                @endif
                                @break
                            
                            @case('quiz_marks')
                                @if(isset($mark->quiz_marks))
                                    {{ $mark->quiz_marks }}
                                @else
                                    -
                                @endif
                                @break
                            
                            @case('participation')
                                @if(isset($mark->participation))
                                    {{ $mark->participation }}
                                @else
                                    A
                                @endif
                                @break
                            
                            @case('remarks')
                                @if(isset($mark->remarks))
                                    {{ $mark->remarks }}
                                @else
                                    Good
                                @endif
                                @break
                            
                            @case('teacher_signature')
                                _______________
                                @break
                            
                            @case('date')
                                {{ now()->format('Y-m-d') }}
                                @break
                            
                            @default
                                {{-- Custom column - try to get value from mark object --}}
                                @if(isset($mark->{$column['key']}))
                                    {{ $mark->{$column['key']} }}
                                @else
                                    -
                                @endif
                        @endswitch
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
@else
    {{-- No columns to display --}}
    <div class="alert alert-warning">
        <h5><i class="fas fa-exclamation-triangle"></i> No Table Columns Available</h5>
        <p class="mb-0">
            The current exam configuration doesn't have any components configured (theory, practical, or assessment).
            Please configure your exam with appropriate mark distributions to generate the marksheet table.
        </p>
    </div>
@endif

{{-- Summary Row (if enabled) --}}
@if($template->hasSetting('show_summary_row'))
    <div class="grade-summary mt-3">
        <div class="row">
            <div class="col-md-6">
                <h6>Overall Performance</h6>
                <p><strong>Total Marks:</strong> {{ $totalMarks ?? 0 }}/{{ $maxMarks ?? 0 }}</p>
                <p><strong>Percentage:</strong> {{ $overallPercentage ?? 0 }}%</p>
                <p><strong>Grade:</strong> {{ $overallGrade ?? 'N/A' }}</p>
                <p><strong>Result:</strong> 
                    <span style="color: {{ ($overallResult ?? 'Fail') == 'Pass' ? '#10b981' : '#ef4444' }}; font-weight: bold;">
                        {{ $overallResult ?? 'Fail' }}
                    </span>
                </p>
            </div>
            <div class="col-md-6">
                @if($template->hasSetting('show_remarks'))
                    <h6>Remarks</h6>
                    <p>{{ $overallRemarks ?? 'Keep up the good work!' }}</p>
                @endif
                
                @if($template->hasSetting('show_grade_points'))
                    <p><strong>GPA:</strong> 
                        @if($template->gradingScale)
                            {{ number_format($template->gradingScale->getGradePoint($overallPercentage ?? 0), 2) }}
                        @else
                            N/A
                        @endif
                    </p>
                @endif
            </div>
        </div>
    </div>
@endif

<style>
.marksheet-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    font-size: {{ $template->settings['font_size'] ?? 12 }}px;
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
}

.grade-summary h6 {
    color: {{ $template->settings['header_color'] ?? '#2563eb' }};
    margin-bottom: 10px;
}

.grade-summary p {
    margin-bottom: 5px;
}
</style>

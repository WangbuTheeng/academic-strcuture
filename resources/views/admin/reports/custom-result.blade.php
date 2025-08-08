@extends('layouts.admin')

@section('title', 'Custom Report Results')
@section('page-title', 'Custom Report Results')

@section('content')
<div class="container-fluid">
    <!-- Sub-Navigation -->
    @include('admin.reports.partials.sub-navbar')

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Custom Report Results</h1>
            <p class="mb-0 text-muted">Generated on {{ $generated_at->format('F j, Y \a\t g:i A') }}</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.custom') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Builder
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Applied Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Applied Filters</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <strong>Report Type:</strong><br>
                    <span class="badge badge-primary">{{ ucfirst(str_replace('_', ' ', $filters['report_type'])) }}</span>
                </div>
                @if(isset($filters['academic_year_id']) && $filters['academic_year_id'])
                <div class="col-md-3 mb-2">
                    <strong>Academic Year:</strong><br>
                    {{ $academicYear->name ?? 'N/A' }}
                </div>
                @endif
                @if(isset($filters['class_ids']) && !empty($filters['class_ids']))
                <div class="col-md-3 mb-2">
                    <strong>Classes:</strong><br>
                    @foreach($selectedClasses as $class)
                        <span class="badge badge-info mr-1">{{ $class->name }}</span>
                    @endforeach
                </div>
                @endif
                @if(isset($filters['subject_ids']) && !empty($filters['subject_ids']))
                <div class="col-md-3 mb-2">
                    <strong>Subjects:</strong><br>
                    @foreach($selectedSubjects as $subject)
                        <span class="badge badge-success mr-1">{{ $subject->name }}</span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Report Content -->
    @if($filters['report_type'] === 'performance')
        @include('admin.reports.partials.performance-analysis', ['data' => $reportData])
    @elseif($filters['report_type'] === 'marks')
        @include('admin.reports.partials.marks-analysis', ['data' => $reportData])
    @elseif($filters['report_type'] === 'grades')
        @include('admin.reports.partials.grade-distribution', ['data' => $reportData])
    @elseif($filters['report_type'] === 'comparison')
        @include('admin.reports.partials.comparative-analysis', ['data' => $reportData])
    @else
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-chart-bar fa-4x text-gray-300 mb-4"></i>
                <h5 class="text-gray-800 mb-3">Report Generated Successfully</h5>
                <p class="text-muted">Your custom report has been generated with the selected filters.</p>
                
                @if(isset($reportData['summary']))
                <div class="row mt-4">
                    @foreach($reportData['summary'] as $key => $value)
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $value }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Export Options -->
    <div class="card shadow mt-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Export Options</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <form method="POST" action="{{ route('admin.reports.custom') }}" style="display: inline;">
                        @csrf
                        @foreach($filters as $key => $value)
                            @if(is_array($value))
                                @foreach($value as $item)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <input type="hidden" name="format" value="pdf">
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </button>
                    </form>
                </div>
                <div class="col-md-4 mb-3">
                    <form method="POST" action="{{ route('admin.reports.custom') }}" style="display: inline;">
                        @csrf
                        @foreach($filters as $key => $value)
                            @if(is_array($value))
                                @foreach($value as $item)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <input type="hidden" name="format" value="excel">
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-file-excel"></i> Download Excel
                        </button>
                    </form>
                </div>
                <div class="col-md-4 mb-3">
                    <button onclick="window.print()" class="btn btn-primary btn-block">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Ensure text visibility */
.text-gray-800, h1, h2, h3, h4, h5, h6 {
    color: #2d3748 !important;
}

.text-muted {
    color: #6c757d !important;
}

.text-primary {
    color: #4e73df !important;
}

/* Ensure all text in cards is visible */
.card-body, .card-header {
    color: #2d3748 !important;
}

@media print {
    .btn, .card-header, .no-print {
        display: none !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    .container-fluid {
        padding: 0 !important;
    }

    /* Ensure print text is black */
    * {
        color: #000 !important;
    }
}
</style>
@endsection

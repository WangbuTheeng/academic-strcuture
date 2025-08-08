@extends('layouts.admin')

@section('title', 'Custom Report Builder')
@section('page-title', 'Custom Report Builder')

@section('content')
<div class="container-fluid">
    <!-- Sub-Navigation -->
    @include('admin.reports.partials.sub-navbar')

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Custom Report Builder</h1>
            <p class="mb-0 text-muted">Create customized reports with advanced filtering and export options</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>

    <!-- Report Builder Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Build Your Custom Report</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.reports.custom') }}" id="customReportForm">
                @csrf

                <!-- Report Type Selection -->
                <div class="mb-4">
                    <label class="form-label font-weight-bold">Report Type</label>
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card report-type-card h-100" data-type="performance">
                                <div class="card-body text-center">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="report_type" value="performance" id="type-performance" class="custom-control-input" checked>
                                        <label class="custom-control-label" for="type-performance">
                                            <i class="fas fa-chart-line fa-2x text-primary mb-2 d-block"></i>
                                            <h6 class="card-title">Performance Analysis</h6>
                                            <p class="card-text text-muted small">Student performance metrics and analytics</p>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card report-type-card h-100" data-type="marks">
                                <div class="card-body text-center">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="report_type" value="marks" id="type-marks" class="custom-control-input">
                                        <label class="custom-control-label" for="type-marks">
                                            <i class="fas fa-clipboard-list fa-2x text-success mb-2 d-block"></i>
                                            <h6 class="card-title">Marks Report</h6>
                                            <p class="card-text text-muted small">Detailed marks breakdown and analysis</p>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card report-type-card h-100" data-type="grades">
                                <div class="card-body text-center">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="report_type" value="grades" id="type-grades" class="custom-control-input">
                                        <label class="custom-control-label" for="type-grades">
                                            <i class="fas fa-medal fa-2x text-warning mb-2 d-block"></i>
                                            <h6 class="card-title">Grade Distribution</h6>
                                            <p class="card-text text-muted small">Grade analysis and distribution</p>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card report-type-card h-100" data-type="comparison">
                                <div class="card-body text-center">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="report_type" value="comparison" id="type-comparison" class="custom-control-input">
                                        <label class="custom-control-label" for="type-comparison">
                                            <i class="fas fa-balance-scale fa-2x text-info mb-2 d-block"></i>
                                            <h6 class="card-title">Comparative Analysis</h6>
                                            <p class="card-text text-muted small">Compare performance across periods</p>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Filters Section -->
                <div class="row mb-4">
                    <!-- Academic Year Filter -->
                    <div class="col-lg-4 col-md-6 mb-3">
                        <label for="academic_year_id" class="form-label">Academic Year</label>
                        <select name="academic_year_id" id="academic_year_id" class="form-control">
                            <option value="">All Academic Years</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Classes Filter -->
                    <div class="col-lg-4 col-md-6 mb-3">
                        <label for="class_ids" class="form-label">Classes</label>
                        <select name="class_ids[]" id="class_ids" multiple class="form-control" size="4">
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->level->name }})</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple</small>
                    </div>

                    <!-- Subjects Filter -->
                    <div class="col-lg-4 col-md-6 mb-3">
                        <label for="subject_ids" class="form-label">Subjects</label>
                        <select name="subject_ids[]" id="subject_ids" multiple class="form-control" size="4">
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->department->name }})</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple</small>
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Exams Filter -->
                    <div class="col-lg-4 col-md-6 mb-3">
                        <label for="exam_ids" class="form-label">Exams</label>
                        <select name="exam_ids[]" id="exam_ids" multiple class="form-control" size="4">
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->name }} ({{ $exam->academicYear->name ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple</small>
                    </div>

                    <!-- Date Range -->
                    <div class="col-lg-4 col-md-6 mb-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" name="date_from" id="date_from" class="form-control">
                    </div>

                    <div class="col-lg-4 col-md-6 mb-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" name="date_to" id="date_to" class="form-control">
                    </div>
                </div>

                <!-- Advanced Options -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Include Options</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="include_charts" id="include_charts" checked>
                            <label class="form-check-label" for="include_charts">
                                Include Charts and Graphs
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="include_summary" id="include_summary" checked>
                            <label class="form-check-label" for="include_summary">
                                Include Summary Statistics
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="include_details" id="include_details">
                            <label class="form-check-label" for="include_details">
                                Include Detailed Breakdown
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Output Format</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="format" value="html" id="format_html" checked>
                            <label class="form-check-label" for="format_html">
                                <i class="fas fa-eye text-primary"></i> View in Browser
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="format" value="pdf" id="format_pdf">
                            <label class="form-check-label" for="format_pdf">
                                <i class="fas fa-file-pdf text-danger"></i> Download PDF
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="format" value="excel" id="format_excel">
                            <label class="form-check-label" for="format_excel">
                                <i class="fas fa-file-excel text-success"></i> Download Excel
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Generate Button -->
                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-chart-bar"></i> Generate Custom Report
                    </button>
                        <i class="fas fa-undo"></i> Reset Form
                    </button>
                    <button type="button" class="btn btn-info btn-lg" id="saveTemplate">
                        <i class="fas fa-save"></i> Save as Template
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Templates -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Quick Report Templates</h6>
            <p class="mb-0 text-muted small">Pre-configured reports for common use cases</p>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Template 1: Current Year Performance -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card border-left-primary h-100 template-card" data-template="current-year">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <i class="fas fa-chart-line fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="card-title font-weight-bold">Current Year Performance</h6>
                                    <p class="card-text text-muted small">Overall performance analysis for the current academic year</p>
                                    <button class="btn btn-primary btn-sm">
                                        Use Template <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Template 2: Class Comparison -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card border-left-success h-100 template-card" data-template="class-comparison">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <i class="fas fa-balance-scale fa-2x text-success"></i>
                                </div>
                                <div>
                                    <h6 class="card-title font-weight-bold">Class Comparison</h6>
                                    <p class="card-text text-muted small">Compare performance across different classes</p>
                                    <button class="btn btn-success btn-sm">
                                        Use Template <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Template 3: Subject Analysis -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card border-left-info h-100 template-card" data-template="subject-analysis">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <i class="fas fa-book fa-2x text-info"></i>
                                </div>
                                <div>
                                    <h6 class="card-title font-weight-bold">Subject Performance</h6>
                                    <p class="card-text text-muted small">Detailed analysis of subject-wise performance</p>
                                    <button class="btn btn-info btn-sm">
                                        Use Template <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Section -->
    <div class="alert alert-info">
        <div class="d-flex align-items-start">
            <i class="fas fa-info-circle text-info mt-1 me-3"></i>
            <div>
                <h6 class="alert-heading">How to Use the Custom Report Builder</h6>
                <ul class="mb-0 small">
                    <li><strong>Report Type:</strong> Choose the type of analysis you want to perform</li>
                    <li><strong>Filters:</strong> Select specific criteria to narrow down your data</li>
                    <li><strong>Multiple Selection:</strong> Hold Ctrl/Cmd to select multiple items in dropdowns</li>
                    <li><strong>Date Range:</strong> Limit results to a specific time period</li>
                    <li><strong>Output Format:</strong> Choose how you want to view or download the report</li>
                </ul>
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
.card-body, .card-header, .form-label, label {
    color: #2d3748 !important;
}

.report-type-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.report-type-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.report-type-card.selected {
    border-color: #4e73df;
    box-shadow: 0 0 0 2px rgba(78, 115, 223, 0.2);
}

.template-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.template-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Report type card selection
    const reportTypeCards = document.querySelectorAll('.report-type-card');
    const reportTypeInputs = document.querySelectorAll('input[name="report_type"]');

    reportTypeCards.forEach(card => {
        card.addEventListener('click', function() {
            const type = this.dataset.type;
            const input = document.getElementById(`type-${type}`);
            if (input) {
                input.checked = true;
                updateSelectedCard();
            }
        });
    });

    reportTypeInputs.forEach(input => {
        input.addEventListener('change', updateSelectedCard);
    });

    function updateSelectedCard() {
        reportTypeCards.forEach(card => card.classList.remove('selected'));
        const checkedInput = document.querySelector('input[name="report_type"]:checked');
        if (checkedInput) {
            const selectedCard = document.querySelector(`[data-type="${checkedInput.value}"]`);
            if (selectedCard) {
                selectedCard.classList.add('selected');
            }
        }
    }

    // Template selection
    const templateCards = document.querySelectorAll('.template-card');
    templateCards.forEach(card => {
        card.addEventListener('click', function() {
            const template = this.dataset.template;
            applyTemplate(template);
        });
    });

    function applyTemplate(template) {
        switch(template) {
            case 'current-year':
                document.getElementById('type-performance').checked = true;
                // Set current academic year
                break;
            case 'class-comparison':
                document.getElementById('type-comparison').checked = true;
                break;
            case 'subject-analysis':
                document.getElementById('type-marks').checked = true;
                break;
        }
        updateSelectedCard();
    }

    // Auto-populate date fields with current academic year
    const today = new Date();
    const currentYear = today.getFullYear();
    const academicYearStart = new Date(currentYear, 3, 1); // April 1st
    const academicYearEnd = new Date(currentYear + 1, 2, 31); // March 31st next year

    if (today < academicYearStart) {
        academicYearStart.setFullYear(currentYear - 1);
        academicYearEnd.setFullYear(currentYear);
    }

    document.getElementById('date_from').value = academicYearStart.toISOString().split('T')[0];
    document.getElementById('date_to').value = academicYearEnd.toISOString().split('T')[0];

    // Initialize selected card
    updateSelectedCard();
});
</script>
@endsection

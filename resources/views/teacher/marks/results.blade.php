@extends('layouts.teacher')

@section('title', 'View Results')
@section('page-title', 'View Results')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">View Results</h1>
            <p class="mb-0 text-muted">Review approved marks for your subjects</p>
        </div>
        <div>
            <a href="{{ route('teacher.marks.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Back to Mark Entry
            </a>
        </div>
    </div>

    <!-- Subject Summary Cards -->
    <div class="row mb-4">
        @foreach($assignedSubjects as $assignment)
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="subject-icon bg-primary text-white rounded-circle mx-auto mb-2">
                            {{ substr($assignment->subject->code, 0, 2) }}
                        </div>
                        <h6 class="card-title">{{ $assignment->subject->name }}</h6>
                        <p class="card-text text-muted small">{{ $assignment->class->name }}</p>
                        <div class="text-primary fw-bold">
                            {{ $results->where('subject_id', $assignment->subject_id)->count() }} Results
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Results Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-chart-line me-2"></i>Approved Results ({{ $results->total() }})
            </h6>
        </div>
        <div class="card-body">
            @if($results->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Exam</th>
                                <th>Theory</th>
                                <th>Practical</th>
                                <th>Assessment</th>
                                <th>Total</th>
                                <th>Grade</th>
                                <th>Result</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)
                                <tr>
                                    <td>
                                        <strong>{{ $result->student->first_name }} {{ $result->student->last_name }}</strong>
                                        <br><small class="text-muted">{{ $result->student->admission_number }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $result->subject->name }}</strong>
                                        <br><small class="text-muted">{{ $result->subject->code }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $result->exam->name }}</strong>
                                        <br><small class="text-muted">{{ $result->exam->exam_type }}</small>
                                    </td>
                                    <td class="text-center">
                                        {{ $result->theory_marks ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $result->practical_marks ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $result->assess_marks ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ $result->total }}</strong>
                                        @if($result->percentage)
                                            <br><small class="text-muted">({{ number_format($result->percentage, 1) }}%)</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($result->grade)
                                            <span class="badge bg-primary">{{ $result->grade }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($result->result == 'Pass')
                                            <span class="badge bg-success">Pass</span>
                                        @elseif($result->result == 'Fail')
                                            <span class="badge bg-danger">Fail</span>
                                        @else
                                            <span class="badge bg-warning">{{ $result->result }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <small>{{ $result->created_at->format('M d, Y') }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $results->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No approved results found</h5>
                    <p class="text-muted">Results will appear here once marks are approved by administrators.</p>
                    <a href="{{ route('teacher.marks.index') }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Enter Marks
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .subject-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: bold;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
</style>
@endpush
@endsection

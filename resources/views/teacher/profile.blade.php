@extends('layouts.teacher')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">My Profile</h1>
            <p class="mb-0 text-muted">View your profile and subject assignments</p>
        </div>
        <div>
            <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Teacher Information -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Teacher Information</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-5x text-gray-300"></i>
                    </div>
                    <h5 class="mb-1">{{ $teacher->name }}</h5>
                    <p class="text-muted mb-3">{{ $teacher->email }}</p>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 text-primary">{{ $subjectAssignments->flatten()->count() }}</div>
                            <div class="text-muted small">Total Assignments</div>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-success">{{ $subjectAssignments->count() }}</div>
                            <div class="text-muted small">Academic Years</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Current Year Stats</h6>
                </div>
                <div class="card-body">
                    @if($currentAcademicYear && isset($subjectAssignments[$currentAcademicYear->id]))
                        @php
                            $currentAssignments = $subjectAssignments[$currentAcademicYear->id];
                            $uniqueSubjects = $currentAssignments->pluck('subject_id')->unique()->count();
                            $uniqueClasses = $currentAssignments->pluck('class_id')->unique()->count();
                        @endphp
                        
                        <div class="row text-center">
                            <div class="col-12 mb-3">
                                <div class="h5 text-info">{{ $uniqueSubjects }}</div>
                                <div class="text-muted small">Subjects Teaching</div>
                            </div>
                            <div class="col-12">
                                <div class="h5 text-warning">{{ $uniqueClasses }}</div>
                                <div class="text-muted small">Classes Teaching</div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-info-circle fa-2x text-gray-300 mb-2"></i>
                            <p class="text-muted mb-0">No assignments for current academic year</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Subject Assignments -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subject Assignments</h6>
                </div>
                <div class="card-body">
                    @if($subjectAssignments->count() > 0)
                        @foreach($subjectAssignments as $academicYearId => $assignments)
                            @php
                                $academicYear = $assignments->first()->academicYear;
                                $isCurrent = $currentAcademicYear && $academicYear->id == $currentAcademicYear->id;
                            @endphp
                            
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">
                                        {{ $academicYear->name }}
                                        @if($isCurrent)
                                            <span class="badge bg-success ms-2">Current</span>
                                        @endif
                                    </h6>
                                    <small class="text-muted">{{ $assignments->count() }} assignments</small>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Subject</th>
                                                <th>Class</th>
                                                <th>Level</th>
                                                <th>Department</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($assignments as $assignment)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $assignment->subject->name }}</strong>
                                                        <br><small class="text-muted">{{ $assignment->subject->code }}</small>
                                                    </td>
                                                    <td>{{ $assignment->class->name }}</td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $assignment->class->level->name }}</span>
                                                    </td>
                                                    <td>
                                                        @if($assignment->class->department)
                                                            {{ $assignment->class->department->name }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($assignment->is_active)
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            @if(!$loop->last)
                                <hr>
                            @endif
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chalkboard-teacher fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-muted">No Subject Assignments</h5>
                            <p class="text-muted">You haven't been assigned to any subjects yet. Please contact the administrator.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

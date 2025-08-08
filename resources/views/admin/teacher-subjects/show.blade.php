@extends('layouts.admin')

@section('title', 'Teacher Assignment Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Teacher Assignment Details</h3>
                    <div>
                        <a href="{{ route('admin.teacher-subjects.edit', $teacherSubject) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.teacher-subjects.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Assignments
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Teacher</th>
                                    <td>{{ $teacherSubject->teacher->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $teacherSubject->teacher->email }}</td>
                                </tr>
                                <tr>
                                    <th>Subject</th>
                                    <td>{{ $teacherSubject->subject->name }} ({{ $teacherSubject->subject->code }})</td>
                                </tr>
                                <tr>
                                    <th>Class</th>
                                    <td>{{ $teacherSubject->class->name }}</td>
                                </tr>
                                <tr>
                                    <th>Level</th>
                                    <td>{{ $teacherSubject->class->level->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Academic Year</th>
                                    <td>
                                        {{ $teacherSubject->academicYear->name }}
                                        @if($teacherSubject->academicYear->is_current)
                                            <span class="badge badge-success">Current</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($teacherSubject->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $teacherSubject->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $teacherSubject->updated_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Subject Details -->
                    @if($teacherSubject->subject->description)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Subject Description</h5>
                            <div class="card">
                                <div class="card-body">
                                    {{ $teacherSubject->subject->description }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="btn-group" role="group">
                                <form action="{{ route('admin.teacher-subjects.toggle-status', $teacherSubject) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-{{ $teacherSubject->is_active ? 'warning' : 'success' }}" 
                                            onclick="return confirm('Are you sure you want to {{ $teacherSubject->is_active ? 'deactivate' : 'activate' }} this assignment?')">
                                        <i class="fas fa-{{ $teacherSubject->is_active ? 'pause' : 'play' }}"></i>
                                        {{ $teacherSubject->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.teacher-subjects.destroy', $teacherSubject) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this assignment? This action cannot be undone.')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

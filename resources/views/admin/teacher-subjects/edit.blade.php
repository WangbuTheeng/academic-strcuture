@extends('layouts.admin')

@section('title', 'Edit Teacher Assignment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Edit Teacher Assignment</h3>
                    <a href="{{ route('admin.teacher-subjects.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Assignments
                    </a>
                </div>

                <form action="{{ route('admin.teacher-subjects.update', $teacherSubject) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <!-- Teacher Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id" class="required">Teacher</label>
                                    <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                        <option value="">Select Teacher</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('user_id', $teacherSubject->user_id) == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->name }} ({{ $teacher->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Subject Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subject_id" class="required">Subject</label>
                                    <select name="subject_id" id="subject_id" class="form-control @error('subject_id') is-invalid @enderror" required>
                                        <option value="">Select Subject</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ old('subject_id', $teacherSubject->subject_id) == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }} ({{ $subject->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Class Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="class_id" class="required">Class</label>
                                    <select name="class_id" id="class_id" class="form-control @error('class_id') is-invalid @enderror" required>
                                        <option value="">Select Class</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id', $teacherSubject->class_id) == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }} ({{ $class->level->name ?? 'No Level' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Academic Year Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="academic_year_id" class="required">Academic Year</label>
                                    <select name="academic_year_id" id="academic_year_id" class="form-control @error('academic_year_id') is-invalid @enderror" required>
                                        <option value="">Select Academic Year</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ old('academic_year_id', $teacherSubject->academic_year_id) == $year->id ? 'selected' : '' }}>
                                                {{ $year->name }} 
                                                @if($year->is_current)
                                                    <span class="badge badge-success">Current</span>
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_year_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $teacherSubject->is_active) ? 'checked' : '' }}>
                                        <label for="is_active" class="form-check-label">Active Assignment</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($errors->has('error'))
                            <div class="alert alert-danger">
                                {{ $errors->first('error') }}
                            </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Assignment
                        </button>
                        <a href="{{ route('admin.teacher-subjects.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.required::after {
    content: " *";
    color: red;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize select2 for better UX
    if (typeof $.fn.select2 !== 'undefined') {
        $('#user_id, #subject_id, #class_id, #academic_year_id').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    }
});
</script>
@endpush
@endsection

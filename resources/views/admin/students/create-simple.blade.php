@extends('layouts.admin')

@section('title', 'Register New Student - Simple Form')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Register New Student (Simple Form)</h1>
        <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Students
        </a>
    </div>

    <!-- Simple Registration Form -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Student Registration Form</h5>
        </div>
        <form method="POST" action="{{ route('admin.students.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                    <!-- Personal Details -->
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                               class="form-control @error('first_name') is-invalid @enderror">
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                               class="form-control @error('last_name') is-invalid @enderror">
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" required
                               class="form-control @error('date_of_birth') is-invalid @enderror">
                        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                        <select name="gender" id="gender" required class="form-select @error('gender') is-invalid @enderror">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="nationality" class="form-label">Nationality <span class="text-danger">*</span></label>
                        <input type="text" name="nationality" id="nationality" value="{{ old('nationality', 'Nepali') }}" required
                               class="form-control @error('nationality') is-invalid @enderror">
                        @error('nationality')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
                               class="form-control @error('phone') is-invalid @enderror">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea name="address" id="address" rows="2" required
                                  class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Guardian Information -->
                    <div class="col-md-6 mb-3">
                        <label for="guardian_name" class="form-label">Guardian Name <span class="text-danger">*</span></label>
                        <input type="text" name="guardian_name" id="guardian_name" value="{{ old('guardian_name') }}" required
                               class="form-control @error('guardian_name') is-invalid @enderror">
                        @error('guardian_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="guardian_relation" class="form-label">Guardian Relation <span class="text-danger">*</span></label>
                        <select name="guardian_relation" id="guardian_relation" required
                                class="form-select @error('guardian_relation') is-invalid @enderror">
                            <option value="">Select Relation</option>
                            <option value="Father" {{ old('guardian_relation') == 'Father' ? 'selected' : '' }}>Father</option>
                            <option value="Mother" {{ old('guardian_relation') == 'Mother' ? 'selected' : '' }}>Mother</option>
                            <option value="Guardian" {{ old('guardian_relation') == 'Guardian' ? 'selected' : '' }}>Guardian</option>
                        </select>
                        @error('guardian_relation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="guardian_phone" class="form-label">Guardian Phone <span class="text-danger">*</span></label>
                        <input type="tel" name="guardian_phone" id="guardian_phone" value="{{ old('guardian_phone') }}" required
                               class="form-control @error('guardian_phone') is-invalid @enderror">
                        @error('guardian_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="disability_status" class="form-label">Disability Status <span class="text-danger">*</span></label>
                        <select name="disability_status" id="disability_status" required
                                class="form-select @error('disability_status') is-invalid @enderror">
                            <option value="none" {{ old('disability_status') == 'none' ? 'selected' : '' }}>None</option>
                            <option value="visual" {{ old('disability_status') == 'visual' ? 'selected' : '' }}>Visual</option>
                            <option value="hearing" {{ old('disability_status') == 'hearing' ? 'selected' : '' }}>Hearing</option>
                            <option value="mobility" {{ old('disability_status') == 'mobility' ? 'selected' : '' }}>Mobility</option>
                            <option value="learning" {{ old('disability_status') == 'learning' ? 'selected' : '' }}>Learning</option>
                            <option value="other" {{ old('disability_status') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('disability_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="admission_date" class="form-label">Admission Date <span class="text-danger">*</span></label>
                        <input type="date" name="admission_date" id="admission_date" value="{{ old('admission_date', date('Y-m-d')) }}" required
                               class="form-control @error('admission_date') is-invalid @enderror">
                        @error('admission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Register Student
                </button>
                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

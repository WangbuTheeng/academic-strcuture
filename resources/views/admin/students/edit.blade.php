@extends('layouts.admin')

@section('title', 'Edit Student - ' . $student->full_name)

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container-fluid px-4">
    <form method="POST" action="{{ route('admin.students.update', $student) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Edit Student</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.students.show', $student) }}">{{ $student->full_name }}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.students.show', $student) }}" class="btn btn-secondary me-2">
                    <i class="fas fa-eye"></i> View Student
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Student
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <!-- Student Photo and Basic Info -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Student Photo</h6>
                    </div>
                    <div class="card-body text-center">
                        <div id="photo-preview-container" class="mb-3">
                            @if($student->photo)
                                <img id="photo-preview" class="rounded-circle border border-4 border-primary shadow-lg"
                                     src="{{ Storage::url($student->photo) }}"
                                     alt="{{ $student->full_name }}"
                                     style="width: 180px; height: 180px; object-fit: cover;">
                            @else
                                <div id="photo-placeholder" class="rounded-circle bg-gradient-primary d-flex align-items-center justify-content-center mx-auto border border-4 border-primary shadow-lg"
                                     style="width: 180px; height: 180px;">
                                    <span class="text-white fw-bold" style="font-size: 3.5rem;">
                                        {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                        @error('photo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Max size: 2MB. JPG, PNG, GIF.</small>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Status</h6>
                    </div>
                    <div class="card-body">
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                            <option value="transferred" {{ old('status', $student->status) == 'transferred' ? 'selected' : '' }}>Transferred</option>
                            <option value="dropped" {{ old('status', $student->status) == 'dropped' ? 'selected' : '' }}>Dropped</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Student Details Form -->
            <div class="col-lg-8">
                <!-- Personal Information -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $student->first_name) }}" class="form-control @error('first_name') is-invalid @enderror" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $student->last_name) }}" class="form-control @error('last_name') is-invalid @enderror" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '') }}" class="form-control @error('date_of_birth') is-invalid @enderror" required>
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                    <option value="Male" {{ old('gender', $student->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $student->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender', $student->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $student->phone) }}" class="form-control @error('phone') is-invalid @enderror" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $student->email) }}" class="form-control @error('email') is-invalid @enderror">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nationality" class="form-label">Nationality <span class="text-danger">*</span></label>
                                <input type="text" name="nationality" id="nationality" value="{{ old('nationality', $student->nationality) }}" class="form-control @error('nationality') is-invalid @enderror" required>
                                @error('nationality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="blood_group" class="form-label">Blood Group</label>
                                <input type="text" name="blood_group" id="blood_group" value="{{ old('blood_group', $student->blood_group) }}" class="form-control @error('blood_group') is-invalid @enderror" maxlength="5">
                                @error('blood_group')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="religion" class="form-label">Religion</label>
                                <input type="text" name="religion" id="religion" value="{{ old('religion', $student->religion) }}" class="form-control @error('religion') is-invalid @enderror" maxlength="50">
                                @error('religion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="caste" class="form-label">Caste</label>
                                <input type="text" name="caste" id="caste" value="{{ old('caste', $student->caste) }}" class="form-control @error('caste') is-invalid @enderror" maxlength="50">
                                @error('caste')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="mother_tongue" class="form-label">Mother Tongue</label>
                                <input type="text" name="mother_tongue" id="mother_tongue" value="{{ old('mother_tongue', $student->mother_tongue) }}" class="form-control @error('mother_tongue') is-invalid @enderror" maxlength="50">
                                @error('mother_tongue')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="disability_status" class="form-label">Disability Status <span class="text-danger">*</span></label>
                                <select name="disability_status" id="disability_status" class="form-select @error('disability_status') is-invalid @enderror" required>
                                    <option value="none" {{ old('disability_status', $student->disability_status) == 'none' ? 'selected' : '' }}>None</option>
                                    <option value="visual" {{ old('disability_status', $student->disability_status) == 'visual' ? 'selected' : '' }}>Visual</option>
                                    <option value="hearing" {{ old('disability_status', $student->disability_status) == 'hearing' ? 'selected' : '' }}>Hearing</option>
                                    <option value="mobility" {{ old('disability_status', $student->disability_status) == 'mobility' ? 'selected' : '' }}>Mobility</option>
                                    <option value="learning" {{ old('disability_status', $student->disability_status) == 'learning' ? 'selected' : '' }}>Learning</option>
                                    <option value="other" {{ old('disability_status', $student->disability_status) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('disability_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="special_needs" class="form-label">Special Needs</label>
                                <textarea name="special_needs" id="special_needs" class="form-control @error('special_needs') is-invalid @enderror" rows="3">{{ old('special_needs', $student->special_needs) }}</textarea>
                                @error('special_needs')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3" required>{{ old('address', $student->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="temporary_address" class="form-label">Temporary Address</label>
                                <textarea name="temporary_address" id="temporary_address" class="form-control @error('temporary_address') is-invalid @enderror" rows="3">{{ old('temporary_address', $student->temporary_address) }}</textarea>
                                @error('temporary_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Academic Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="admission_number" class="form-label">Admission Number <span class="text-danger">*</span></label>
                                <input type="text" name="admission_number" id="admission_number" value="{{ old('admission_number', $student->admission_number) }}" class="form-control @error('admission_number') is-invalid @enderror" required>
                                @error('admission_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="admission_date" class="form-label">Admission Date <span class="text-danger">*</span></label>
                                <input type="date" name="admission_date" id="admission_date" value="{{ old('admission_date', $student->admission_date ? $student->admission_date->format('Y-m-d') : '') }}" class="form-control @error('admission_date') is-invalid @enderror" required>
                                @error('admission_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact Information -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Emergency Contact Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="emergency_contact_name" class="form-label">Contact Name</label>
                                <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $student->emergency_contact_name) }}" class="form-control @error('emergency_contact_name') is-invalid @enderror" maxlength="100">
                                @error('emergency_contact_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="emergency_contact_phone" class="form-label">Contact Phone</label>
                                <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $student->emergency_contact_phone) }}" class="form-control @error('emergency_contact_phone') is-invalid @enderror" maxlength="15">
                                @error('emergency_contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="emergency_contact_relation" class="form-label">Relationship</label>
                                <input type="text" name="emergency_contact_relation" id="emergency_contact_relation" value="{{ old('emergency_contact_relation', $student->emergency_contact_relation) }}" class="form-control @error('emergency_contact_relation') is-invalid @enderror" maxlength="20">
                                @error('emergency_contact_relation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guardian Information -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Guardian Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="guardian_name" class="form-label">Guardian Name <span class="text-danger">*</span></label>
                                <input type="text" name="guardian_name" id="guardian_name" value="{{ old('guardian_name', $student->guardian_name) }}" class="form-control @error('guardian_name') is-invalid @enderror" required>
                                @error('guardian_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="guardian_phone" class="form-label">Guardian Phone <span class="text-danger">*</span></label>
                                <input type="text" name="guardian_phone" id="guardian_phone" value="{{ old('guardian_phone', $student->guardian_phone) }}" class="form-control @error('guardian_phone') is-invalid @enderror" required>
                                @error('guardian_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="guardian_email" class="form-label">Guardian Email</label>
                                <input type="email" name="guardian_email" id="guardian_email" value="{{ old('guardian_email', $student->guardian_email) }}" class="form-control @error('guardian_email') is-invalid @enderror">
                                @error('guardian_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="guardian_relation" class="form-label">Relationship <span class="text-danger">*</span></label>
                                <input type="text" name="guardian_relation" id="guardian_relation" value="{{ old('guardian_relation', $student->guardian_relation) }}" class="form-control @error('guardian_relation') is-invalid @enderror" required>
                                @error('guardian_relation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Legal Documentation -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Legal Documentation</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="citizenship_number" class="form-label">Citizenship Number</label>
                                <input type="text" name="citizenship_number" id="citizenship_number" value="{{ old('citizenship_number', $student->citizenship_number) }}" class="form-control @error('citizenship_number') is-invalid @enderror" maxlength="20">
                                @error('citizenship_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="citizenship_issue_date" class="form-label">Issue Date</label>
                                <input type="date" name="citizenship_issue_date" id="citizenship_issue_date" value="{{ old('citizenship_issue_date', $student->citizenship_issue_date ? $student->citizenship_issue_date->format('Y-m-d') : '') }}" class="form-control @error('citizenship_issue_date') is-invalid @enderror">
                                @error('citizenship_issue_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="citizenship_issue_district" class="form-label">Issue District</label>
                                <input type="text" name="citizenship_issue_district" id="citizenship_issue_district" value="{{ old('citizenship_issue_district', $student->citizenship_issue_district) }}" class="form-control @error('citizenship_issue_district') is-invalid @enderror" maxlength="50">
                                @error('citizenship_issue_district')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic History -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Academic History</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="previous_school_name" class="form-label">Previous School Name</label>
                                <input type="text" name="previous_school_name" id="previous_school_name" value="{{ old('previous_school_name', $student->previous_school_name) }}" class="form-control @error('previous_school_name') is-invalid @enderror" maxlength="150">
                                @error('previous_school_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="transfer_certificate_no" class="form-label">Transfer Certificate No.</label>
                                <input type="text" name="transfer_certificate_no" id="transfer_certificate_no" value="{{ old('transfer_certificate_no', $student->transfer_certificate_no) }}" class="form-control @error('transfer_certificate_no') is-invalid @enderror" maxlength="50">
                                @error('transfer_certificate_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="transfer_certificate_date" class="form-label">Transfer Certificate Date</label>
                                <input type="date" name="transfer_certificate_date" id="transfer_certificate_date" value="{{ old('transfer_certificate_date', $student->transfer_certificate_date ? $student->transfer_certificate_date->format('Y-m-d') : '') }}" class="form-control @error('transfer_certificate_date') is-invalid @enderror">
                                @error('transfer_certificate_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="migration_certificate_no" class="form-label">Migration Certificate No.</label>
                                <input type="text" name="migration_certificate_no" id="migration_certificate_no" value="{{ old('migration_certificate_no', $student->migration_certificate_no) }}" class="form-control @error('migration_certificate_no') is-invalid @enderror" maxlength="50">
                                @error('migration_certificate_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(45deg, #4f46e5, #7c3aed);
}
.card {
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.2);
}
.rounded-circle {
    transition: all 0.3s ease;
}
.rounded-circle:hover {
    transform: scale(1.05);
}
</style>
@endpush

@push('scripts')
<script>
document.getElementById('photo').addEventListener('change', function(event) {
    const preview = document.getElementById('photo-preview');
    const placeholder = document.getElementById('photo-placeholder');
    const file = event.target.files[0];
    const reader = new FileReader();

    reader.onload = function(e) {
        if (preview) {
            preview.src = e.target.result;
        } else if (placeholder) {
            placeholder.style.display = 'none';
            const newPreview = document.createElement('img');
            newPreview.id = 'photo-preview';
            newPreview.className = 'rounded-circle border border-4 border-primary shadow-lg';
            newPreview.src = e.target.result;
            newPreview.style.width = '180px';
            newPreview.style.height = '180px';
            newPreview.style.objectFit = 'cover';
            document.getElementById('photo-preview-container').appendChild(newPreview);
        }
    };

    if (file) {
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
@endsection

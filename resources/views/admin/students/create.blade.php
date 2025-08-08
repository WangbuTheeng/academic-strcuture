@extends('layouts.admin')

@section('title', 'Register New Student')

@push('styles')
<style>
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #6c757d;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .step-circle.active {
        background-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
    }
    
    .step-label {
        font-size: 14px;
        color: #6c757d;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .step-label.active {
        color: #0d6efd;
        font-weight: 600;
    }
    
    .progress-line {
        height: 2px;
        background-color: #dee2e6;
        flex: 1;
        margin: 0 15px;
        transition: all 0.3s ease;
    }
    
    .progress-line.active {
        background-color: #0d6efd;
    }
    
    .form-step {
        display: none;
    }
    
    .form-step.active {
        display: block;
    }
    
    .card-header {
        border-bottom: 2px solid #e9ecef;
    }
    
    .form-label.fw-bold {
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }
    
    .photo-preview {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border: 3px solid #dee2e6;
        border-radius: 50%;
        transition: all 0.3s ease;
    }
    
    .photo-preview:hover {
        border-color: #0d6efd;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-dark">
                <i class="fas fa-user-plus text-primary me-2"></i>
                Register New Student
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
                    <li class="breadcrumb-item active">Register New Student</li>
                </ol>
            </nav>
            <p class="text-muted">Complete student registration with all required information</p>
        </div>
        <div>
            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Students
            </a>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex align-items-center justify-content-between">
                <!-- Step 1 -->
                <div class="d-flex align-items-center">
                    <div class="step-circle active" id="step-circle-1">
                        <span>1</span>
                    </div>
                    <span class="step-label active ms-2" id="step-label-1">Personal Details</span>
                </div>
                
                <!-- Progress Line 1 -->
                <div class="progress-line" id="progress-line-1"></div>
                
                <!-- Step 2 -->
                <div class="d-flex align-items-center">
                    <div class="step-circle" id="step-circle-2">
                        <span>2</span>
                    </div>
                    <span class="step-label ms-2" id="step-label-2">Contact & Guardian</span>
                </div>
                
                <!-- Progress Line 2 -->
                <div class="progress-line" id="progress-line-2"></div>
                
                <!-- Step 3 -->
                <div class="d-flex align-items-center">
                    <div class="step-circle" id="step-circle-3">
                        <span>3</span>
                    </div>
                    <span class="step-label ms-2" id="step-label-3">Academic & Documents</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Form -->
    <div class="card shadow-sm">
        <form method="POST" action="{{ route('admin.students.store') }}" enctype="multipart/form-data" id="student-form">
            @csrf

            <!-- Step 1: Personal Details -->
            <div class="form-step active" id="step-1">
                <div class="card-header bg-light">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user text-primary me-2"></i>
                        Personal Information
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Photo Upload -->
                        <div class="col-12 mb-4">
                            <label class="form-label fw-bold">
                                Student Photo <span class="text-muted">(Optional)</span>
                            </label>
                            <div class="d-flex align-items-center">
                                <div class="me-4">
                                    <img id="photo-preview" class="photo-preview" 
                                         src="https://via.placeholder.com/100x100/e9ecef/6c757d?text=Photo" 
                                         alt="Photo preview">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="photo" id="photo" accept="image/*"
                                           class="form-control @error('photo') is-invalid @enderror">
                                    <div class="form-text">Optional: JPG, PNG up to 2MB</div>
                                    @error('photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- First Name -->
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label fw-bold">
                                First Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                                   class="form-control @error('first_name') is-invalid @enderror"
                                   placeholder="Enter first name">
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label fw-bold">
                                Last Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                                   class="form-control @error('last_name') is-invalid @enderror"
                                   placeholder="Enter last name">
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-md-6 mb-3">
                            <label for="date_of_birth" class="form-label fw-bold">
                                Date of Birth <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" required
                                   class="form-control @error('date_of_birth') is-invalid @enderror"
                                   max="{{ date('Y-m-d') }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label fw-bold">
                                Gender <span class="text-danger">*</span>
                            </label>
                            <select name="gender" id="gender" required
                                    class="form-select @error('gender') is-invalid @enderror">
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Blood Group -->
                        <div class="col-md-6 mb-3">
                            <label for="blood_group" class="form-label fw-bold">Blood Group</label>
                            <select name="blood_group" id="blood_group" class="form-select">
                                <option value="">Select Blood Group</option>
                                <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                                <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                                <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                                <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                                <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                                <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                            </select>
                        </div>

                        <!-- Nationality -->
                        <div class="col-md-6 mb-3">
                            <label for="nationality" class="form-label fw-bold">
                                Nationality <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nationality" id="nationality" value="{{ old('nationality', 'Nepali') }}" required
                                   class="form-control @error('nationality') is-invalid @enderror"
                                   placeholder="Enter nationality">
                            @error('nationality')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Religion -->
                        <div class="col-md-6 mb-3">
                            <label for="religion" class="form-label fw-bold">Religion</label>
                            <input type="text" name="religion" id="religion" value="{{ old('religion') }}"
                                   class="form-control" placeholder="Enter religion">
                        </div>

                        <!-- Caste -->
                        <div class="col-md-6 mb-3">
                            <label for="caste" class="form-label fw-bold">Caste</label>
                            <input type="text" name="caste" id="caste" value="{{ old('caste') }}"
                                   class="form-control" placeholder="Enter caste">
                        </div>

                        <!-- Mother Tongue -->
                        <div class="col-md-6 mb-3">
                            <label for="mother_tongue" class="form-label fw-bold">Mother Tongue</label>
                            <input type="text" name="mother_tongue" id="mother_tongue" value="{{ old('mother_tongue') }}"
                                   class="form-control" placeholder="Enter mother tongue">
                        </div>

                        <!-- Disability Status -->
                        <div class="col-md-6 mb-3">
                            <label for="disability_status" class="form-label fw-bold">
                                Disability Status <span class="text-danger">*</span>
                            </label>
                            <select name="disability_status" id="disability_status" required class="form-select">
                                <option value="none" {{ old('disability_status', 'none') == 'none' ? 'selected' : '' }}>None</option>
                                <option value="visual" {{ old('disability_status') == 'visual' ? 'selected' : '' }}>Visual</option>
                                <option value="hearing" {{ old('disability_status') == 'hearing' ? 'selected' : '' }}>Hearing</option>
                                <option value="mobility" {{ old('disability_status') == 'mobility' ? 'selected' : '' }}>Mobility</option>
                                <option value="learning" {{ old('disability_status') == 'learning' ? 'selected' : '' }}>Learning</option>
                                <option value="other" {{ old('disability_status') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <!-- Special Needs -->
                        <div class="col-12 mb-3">
                            <label for="special_needs" class="form-label fw-bold">Special Needs</label>
                            <textarea name="special_needs" id="special_needs" rows="3" class="form-control"
                                      placeholder="Describe any special needs or accommodations required...">{{ old('special_needs') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light text-end">
                    <button type="button" onclick="nextStep()" class="btn btn-primary">
                        Next: Contact & Guardian
                        <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            <!-- Step 2: Contact & Guardian Information -->
            <div class="form-step" id="step-2">
                <div class="card-header bg-light">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-address-book text-primary me-2"></i>
                        Contact & Guardian Information
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Contact Information Section -->
                    <div class="mb-4">
                        <h5 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-phone me-2"></i>Contact Information
                        </h5>
                        <div class="row">
                            <!-- Phone -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label fw-bold">
                                    Phone Number <span class="text-danger">*</span>
                                </label>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
                                       class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="Enter phone number">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-bold">Email Address</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="Enter email address">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Email must be unique within your school only
                                </small>
                            </div>

                            <!-- Address -->
                            <div class="col-12 mb-3">
                                <label for="address" class="form-label fw-bold">
                                    Permanent Address <span class="text-danger">*</span>
                                </label>
                                <textarea name="address" id="address" rows="3" required
                                          class="form-control @error('address') is-invalid @enderror"
                                          placeholder="Enter permanent address...">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Temporary Address -->
                            <div class="col-12 mb-3">
                                <label for="temporary_address" class="form-label fw-bold">Temporary Address</label>
                                <textarea name="temporary_address" id="temporary_address" rows="2"
                                          class="form-control"
                                          placeholder="Enter temporary address (if different from permanent)...">{{ old('temporary_address') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Guardian Information Section -->
                    <div class="mb-4">
                        <h5 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-user-shield me-2"></i>Guardian Information
                        </h5>
                        <div class="row">
                            <!-- Guardian Name -->
                            <div class="col-md-6 mb-3">
                                <label for="guardian_name" class="form-label fw-bold">
                                    Guardian Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="guardian_name" id="guardian_name" value="{{ old('guardian_name') }}" required
                                       class="form-control @error('guardian_name') is-invalid @enderror"
                                       placeholder="Enter guardian name">
                                @error('guardian_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Guardian Relation -->
                            <div class="col-md-6 mb-3">
                                <label for="guardian_relation" class="form-label fw-bold">
                                    Relation <span class="text-danger">*</span>
                                </label>
                                <select name="guardian_relation" id="guardian_relation" required
                                        class="form-select @error('guardian_relation') is-invalid @enderror">
                                    <option value="">Select Relation</option>
                                    <option value="Father" {{ old('guardian_relation') == 'Father' ? 'selected' : '' }}>Father</option>
                                    <option value="Mother" {{ old('guardian_relation') == 'Mother' ? 'selected' : '' }}>Mother</option>
                                    <option value="Guardian" {{ old('guardian_relation') == 'Guardian' ? 'selected' : '' }}>Guardian</option>
                                    <option value="Uncle" {{ old('guardian_relation') == 'Uncle' ? 'selected' : '' }}>Uncle</option>
                                    <option value="Aunt" {{ old('guardian_relation') == 'Aunt' ? 'selected' : '' }}>Aunt</option>
                                    <option value="Grandfather" {{ old('guardian_relation') == 'Grandfather' ? 'selected' : '' }}>Grandfather</option>
                                    <option value="Grandmother" {{ old('guardian_relation') == 'Grandmother' ? 'selected' : '' }}>Grandmother</option>
                                    <option value="Other" {{ old('guardian_relation') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('guardian_relation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Guardian Phone -->
                            <div class="col-md-6 mb-3">
                                <label for="guardian_phone" class="form-label fw-bold">
                                    Guardian Phone <span class="text-danger">*</span>
                                </label>
                                <input type="tel" name="guardian_phone" id="guardian_phone" value="{{ old('guardian_phone') }}" required
                                       class="form-control @error('guardian_phone') is-invalid @enderror"
                                       placeholder="Enter guardian phone">
                                @error('guardian_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Guardian Email -->
                            <div class="col-md-6 mb-3">
                                <label for="guardian_email" class="form-label fw-bold">Guardian Email</label>
                                <input type="email" name="guardian_email" id="guardian_email" value="{{ old('guardian_email') }}"
                                       class="form-control" placeholder="Enter guardian email">
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact Section -->
                    <div>
                        <h5 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>Emergency Contact
                        </h5>
                        <div class="row">
                            <!-- Emergency Contact Name -->
                            <div class="col-md-4 mb-3">
                                <label for="emergency_contact_name" class="form-label fw-bold">Contact Name</label>
                                <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                                       class="form-control" placeholder="Enter contact name">
                            </div>

                            <!-- Emergency Contact Phone -->
                            <div class="col-md-4 mb-3">
                                <label for="emergency_contact_phone" class="form-label fw-bold">Contact Phone</label>
                                <input type="tel" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                                       class="form-control" placeholder="Enter contact phone">
                            </div>

                            <!-- Emergency Contact Relation -->
                            <div class="col-md-4 mb-3">
                                <label for="emergency_contact_relation" class="form-label fw-bold">Relation</label>
                                <input type="text" name="emergency_contact_relation" id="emergency_contact_relation" value="{{ old('emergency_contact_relation') }}"
                                       class="form-control" placeholder="Enter relation">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light d-flex justify-content-between">
                    <button type="button" onclick="prevStep()" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Previous
                    </button>
                    <button type="button" onclick="nextStep()" class="btn btn-primary">
                        Next: Academic & Documents
                        <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            <!-- Step 3: Academic & Documents -->
            <div class="form-step" id="step-3">
                <div class="card-header bg-light">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-graduation-cap text-primary me-2"></i>
                        Academic Information & Documents
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Admission Information Section -->
                    <div class="mb-4">
                        <h5 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-calendar-plus me-2"></i>Admission Information
                        </h5>
                        <div class="row">
                            <!-- Admission Date -->
                            <div class="col-md-6 mb-3">
                                <label for="admission_date" class="form-label fw-bold">
                                    Admission Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="admission_date" id="admission_date" value="{{ old('admission_date', date('Y-m-d')) }}" required
                                       class="form-control @error('admission_date') is-invalid @enderror">
                                @error('admission_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Admission Number -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Admission Number</label>
                                <div class="form-control bg-light text-muted">
                                    <i class="fas fa-magic me-2"></i>Auto-generated after registration
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Legal Documentation Section -->
                    <div class="mb-4">
                        <h5 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-id-card me-2"></i>Legal Documentation
                        </h5>
                        <div class="row">
                            <!-- Citizenship Number -->
                            <div class="col-md-6 mb-3">
                                <label for="citizenship_number" class="form-label fw-bold">Citizenship Number</label>
                                <input type="text" name="citizenship_number" id="citizenship_number" value="{{ old('citizenship_number') }}"
                                       class="form-control" placeholder="Enter citizenship number">
                                <div class="form-text">Required for students 16 years and above</div>
                            </div>

                            <!-- Citizenship Issue Date -->
                            <div class="col-md-6 mb-3">
                                <label for="citizenship_issue_date" class="form-label fw-bold">Issue Date</label>
                                <input type="date" name="citizenship_issue_date" id="citizenship_issue_date" value="{{ old('citizenship_issue_date') }}"
                                       class="form-control">
                            </div>

                            <!-- Citizenship Issue District -->
                            <div class="col-12 mb-3">
                                <label for="citizenship_issue_district" class="form-label fw-bold">Issue District</label>
                                <input type="text" name="citizenship_issue_district" id="citizenship_issue_district" value="{{ old('citizenship_issue_district') }}"
                                       class="form-control" placeholder="Enter issue district">
                            </div>
                        </div>
                    </div>

                    <!-- Academic History Section -->
                    <div>
                        <h5 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-school me-2"></i>Previous Academic History
                        </h5>
                        <div class="row">
                            <!-- Previous School -->
                            <div class="col-12 mb-3">
                                <label for="previous_school_name" class="form-label fw-bold">Previous School Name</label>
                                <input type="text" name="previous_school_name" id="previous_school_name" value="{{ old('previous_school_name') }}"
                                       class="form-control" placeholder="Enter previous school name">
                            </div>

                            <!-- Transfer Certificate -->
                            <div class="col-md-6 mb-3">
                                <label for="transfer_certificate_no" class="form-label fw-bold">Transfer Certificate No.</label>
                                <input type="text" name="transfer_certificate_no" id="transfer_certificate_no" value="{{ old('transfer_certificate_no') }}"
                                       class="form-control" placeholder="Enter TC number">
                            </div>

                            <!-- TC Date -->
                            <div class="col-md-6 mb-3">
                                <label for="transfer_certificate_date" class="form-label fw-bold">TC Date</label>
                                <input type="date" name="transfer_certificate_date" id="transfer_certificate_date" value="{{ old('transfer_certificate_date') }}"
                                       class="form-control">
                            </div>

                            <!-- Migration Certificate -->
                            <div class="col-12 mb-3">
                                <label for="migration_certificate_no" class="form-label fw-bold">Migration Certificate No.</label>
                                <input type="text" name="migration_certificate_no" id="migration_certificate_no" value="{{ old('migration_certificate_no') }}"
                                       class="form-control" placeholder="Enter migration certificate number">
                                <div class="form-text">Required for +2/Bachelor programs</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light d-flex justify-content-between">
                    <button type="button" onclick="prevStep()" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Previous
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Register Student
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentStep = 1;
    const totalSteps = 3;

    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.form-step').forEach(stepEl => {
            stepEl.classList.remove('active');
        });

        // Show current step
        document.getElementById(`step-${step}`).classList.add('active');

        // Update progress indicators
        updateProgressIndicators(step);
    }

    function updateProgressIndicators(step) {
        for (let i = 1; i <= totalSteps; i++) {
            const stepCircle = document.getElementById(`step-circle-${i}`);
            const stepLabel = document.getElementById(`step-label-${i}`);

            if (i <= step) {
                stepCircle.classList.add('active');
                stepLabel.classList.add('active');
            } else {
                stepCircle.classList.remove('active');
                stepLabel.classList.remove('active');
            }
        }

        // Update progress lines
        for (let i = 1; i < totalSteps; i++) {
            const progressLine = document.getElementById(`progress-line-${i}`);
            if (i < step) {
                progressLine.classList.add('active');
            } else {
                progressLine.classList.remove('active');
            }
        }
    }

    function nextStep() {
        console.log('nextStep() called, current step:', currentStep);

        if (validateCurrentStep()) {
            if (currentStep < totalSteps) {
                currentStep++;
                console.log('Moving to step:', currentStep);
                showStep(currentStep);
            }
        } else {
            console.log('Validation failed for step:', currentStep);
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    }

    function validateCurrentStep() {
        const currentStepEl = document.getElementById(`step-${currentStep}`);
        const requiredFields = currentStepEl.querySelectorAll('[required]');
        let isValid = true;
        let invalidFields = [];

        requiredFields.forEach(field => {
            let fieldValid = true;
            let errorMessage = '';

            if (!field.value.trim()) {
                fieldValid = false;
                errorMessage = 'This field is required';
            } else if (field.type === 'date' && field.name === 'date_of_birth') {
                // Special validation for date of birth
                const selectedDate = new Date(field.value);
                const today = new Date();
                today.setHours(0, 0, 0, 0); // Reset time to compare dates only

                if (selectedDate > today) {
                    fieldValid = false;
                    errorMessage = 'Date of birth cannot be in the future';
                }
            }

            if (!fieldValid) {
                field.classList.add('is-invalid');
                isValid = false;

                // Get field label for better error message
                const label = document.querySelector(`label[for="${field.id}"]`);
                const fieldName = label ? label.textContent.replace('*', '').trim() : field.name;
                invalidFields.push(fieldName + (errorMessage ? ` (${errorMessage})` : ''));

                console.log('Invalid field:', fieldName, 'Value:', field.value, 'Error:', errorMessage);
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            console.log('Validation failed for step', currentStep, 'Invalid fields:', invalidFields);

            // Remove any existing alerts
            const existingAlert = currentStepEl.querySelector('.alert');
            if (existingAlert) {
                existingAlert.remove();
            }

            // Show Bootstrap alert with detailed error information
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Please fix the following issues:</strong>
                <ul class="mb-0 mt-2">
                    ${invalidFields.map(field => `<li>${field}</li>`).join('')}
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            currentStepEl.querySelector('.card-body').insertBefore(alertDiv, currentStepEl.querySelector('.card-body').firstChild);

            // Scroll to the alert
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Auto-dismiss after 15 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 15000);
        } else {
            console.log('Validation passed for step', currentStep);

            // Remove any existing alerts on successful validation
            const existingAlert = currentStepEl.querySelector('.alert');
            if (existingAlert) {
                existingAlert.remove();
            }
        }

        return isValid;
    }

    // Photo preview functionality
    document.getElementById('photo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photo-preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Function to validate specific field types
    function validateField(field) {
        let isValid = true;
        let errorMessage = '';

        if (!field.value.trim()) {
            isValid = false;
            errorMessage = 'This field is required';
        } else if (field.type === 'date' && field.name === 'date_of_birth') {
            const selectedDate = new Date(field.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (selectedDate > today) {
                isValid = false;
                errorMessage = 'Date of birth cannot be in the future';
            }
        } else if (field.type === 'email' && field.value.trim()) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(field.value.trim())) {
                isValid = false;
                errorMessage = 'Please enter a valid email address';
            }
        } else if (field.type === 'tel' && field.value.trim()) {
            const phoneRegex = /^[\d\s\-\+\(\)]+$/;
            if (!phoneRegex.test(field.value.trim()) || field.value.trim().length < 7) {
                isValid = false;
                errorMessage = 'Please enter a valid phone number';
            }
        }

        return { isValid, errorMessage };
    }

    // Initialize form
    document.addEventListener('DOMContentLoaded', function() {
        showStep(1);

        // Add real-time validation to fields
        document.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
            field.addEventListener('blur', function() {
                const validation = validateField(this);
                if (!validation.isValid) {
                    this.classList.add('is-invalid');

                    // Show error message
                    let errorDiv = this.parentNode.querySelector('.field-error');
                    if (!errorDiv) {
                        errorDiv = document.createElement('div');
                        errorDiv.className = 'field-error text-danger small mt-1';
                        this.parentNode.appendChild(errorDiv);
                    }
                    errorDiv.textContent = validation.errorMessage;
                } else {
                    this.classList.remove('is-invalid');
                    const errorDiv = this.parentNode.querySelector('.field-error');
                    if (errorDiv) {
                        errorDiv.remove();
                    }
                }
            });

            field.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    const validation = validateField(this);
                    if (validation.isValid) {
                        this.classList.remove('is-invalid');
                        const errorDiv = this.parentNode.querySelector('.field-error');
                        if (errorDiv) {
                            errorDiv.remove();
                        }
                    }
                }
            });
        });

        // Add form validation on submit
        document.getElementById('student-form').addEventListener('submit', function(e) {
            console.log('Form submission attempted');

            // Final validation before submit
            const allRequiredFields = document.querySelectorAll('[required]');
            let allValid = true;
            let invalidFields = [];

            allRequiredFields.forEach(field => {
                let fieldValid = true;
                let errorMessage = '';

                if (!field.value.trim()) {
                    fieldValid = false;
                    errorMessage = 'This field is required';
                } else if (field.type === 'date' && field.name === 'date_of_birth') {
                    // Special validation for date of birth
                    const selectedDate = new Date(field.value);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    if (selectedDate > today) {
                        fieldValid = false;
                        errorMessage = 'Date of birth cannot be in the future';
                    }
                } else if (field.type === 'email' && field.value.trim()) {
                    // Email validation
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(field.value.trim())) {
                        fieldValid = false;
                        errorMessage = 'Please enter a valid email address';
                    }
                } else if (field.type === 'tel' && field.value.trim()) {
                    // Phone validation (basic)
                    const phoneRegex = /^[\d\s\-\+\(\)]+$/;
                    if (!phoneRegex.test(field.value.trim()) || field.value.trim().length < 7) {
                        fieldValid = false;
                        errorMessage = 'Please enter a valid phone number';
                    }
                }

                if (!fieldValid) {
                    field.classList.add('is-invalid');
                    allValid = false;

                    // Get field label for better error message
                    const label = document.querySelector(`label[for="${field.id}"]`);
                    const fieldName = label ? label.textContent.replace('*', '').trim() : field.name;
                    invalidFields.push(fieldName + (errorMessage ? ` (${errorMessage})` : ''));
                    console.log('Form submission - Invalid field:', fieldName, 'Value:', field.value, 'Error:', errorMessage);
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!allValid) {
                console.log('Form submission prevented - Invalid fields:', invalidFields);
                e.preventDefault();

                // Remove any existing alerts
                const existingAlert = document.querySelector('.alert-danger');
                if (existingAlert) {
                    existingAlert.remove();
                }

                // Show error message with specific fields
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                alertDiv.innerHTML = `
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Cannot submit form - Please fix the following issues:</strong>
                    <ul class="mb-0 mt-2">
                        ${invalidFields.map(field => `<li>${field}</li>`).join('')}
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.card'));

                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });

                // Focus on first invalid field and navigate to its step
                const firstInvalidField = document.querySelector('.is-invalid');
                if (firstInvalidField) {
                    // Find which step contains the invalid field
                    const stepElement = firstInvalidField.closest('.form-step');
                    if (stepElement) {
                        const stepNumber = stepElement.id.replace('step-', '');
                        if (stepNumber && stepNumber !== currentStep.toString()) {
                            currentStep = parseInt(stepNumber);
                            showStep(currentStep);
                        }
                    }

                    // Focus on the field after a short delay
                    setTimeout(() => {
                        firstInvalidField.focus();
                        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 300);
                }
            } else {
                console.log('Form validation passed - submitting form');
            }
        });
    });
</script>
@endpush
@endsection

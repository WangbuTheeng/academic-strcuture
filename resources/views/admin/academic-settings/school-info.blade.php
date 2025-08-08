@extends('layouts.admin')

@section('title', 'School Information')
@section('page-title', 'Academic Settings')

@section('content')
<div class="container-fluid">
    <!-- Include Reports Sub-Navigation -->
    @include('admin.reports.partials.sub-navbar')

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">School Information</h1>
            <p class="mb-0 text-muted">Manage your school's basic information and settings</p>
        </div>
    </div>

    <div class="row">
        <!-- School Information Form -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.academic-settings.school-info.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- School Name -->
                            <div class="col-md-6 mb-3">
                                <label for="institution_name" class="form-label">School Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('institution_name') is-invalid @enderror" 
                                       id="institution_name" name="institution_name" 
                                       value="{{ old('institution_name', $settings->institution_name) }}" required>
                                @error('institution_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Principal Name -->
                            <div class="col-md-6 mb-3">
                                <label for="principal_name" class="form-label">Principal Name</label>
                                <input type="text" class="form-control @error('principal_name') is-invalid @enderror" 
                                       id="principal_name" name="principal_name" 
                                       value="{{ old('principal_name', $settings->principal_name) }}">
                                @error('principal_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- School Address -->
                        <div class="mb-3">
                            <label for="institution_address" class="form-label">School Address <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('institution_address') is-invalid @enderror" 
                                      id="institution_address" name="institution_address" rows="3" required>{{ old('institution_address', $settings->institution_address) }}</textarea>
                            @error('institution_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Phone -->
                            <div class="col-md-6 mb-3">
                                <label for="institution_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('institution_phone') is-invalid @enderror" 
                                       id="institution_phone" name="institution_phone" 
                                       value="{{ old('institution_phone', $settings->institution_phone) }}" required>
                                @error('institution_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="institution_email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('institution_email') is-invalid @enderror" 
                                       id="institution_email" name="institution_email" 
                                       value="{{ old('institution_email', $settings->institution_email) }}">
                                @error('institution_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Website -->
                        <div class="mb-3">
                            <label for="institution_website" class="form-label">Website URL</label>
                            <input type="url" class="form-control @error('institution_website') is-invalid @enderror" 
                                   id="institution_website" name="institution_website" 
                                   value="{{ old('institution_website', $settings->institution_website) }}"
                                   placeholder="https://www.yourschool.com">
                            @error('institution_website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- School Logo -->
                        <div class="mb-3">
                            <label for="institution_logo" class="form-label">School Logo</label>
                            <input type="file" class="form-control @error('institution_logo') is-invalid @enderror" 
                                   id="institution_logo" name="institution_logo" accept="image/*">
                            <div class="form-text">Upload a logo image (JPEG, PNG, JPG, GIF). Maximum size: 2MB</div>
                            @error('institution_logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update School Information
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Current Information Preview -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Current Information</h6>
                </div>
                <div class="card-body text-center">
                    <!-- Logo Preview -->
                    <div class="mb-3">
                        @if($settings->institution_logo)
                            <img src="{{ $settings->logo_url }}" alt="School Logo" class="img-fluid" style="max-height: 100px;">
                        @else
                            <div class="bg-light p-4 rounded">
                                <i class="fas fa-school fa-3x text-muted"></i>
                                <p class="text-muted mt-2 mb-0">No logo uploaded</p>
                            </div>
                        @endif
                    </div>

                    <!-- School Details -->
                    <div class="text-start">
                        <h5 class="font-weight-bold">{{ $settings->institution_name ?: 'School Name Not Set' }}</h5>
                        
                        @if($settings->principal_name)
                            <p class="mb-1"><strong>Principal:</strong> {{ $settings->principal_name }}</p>
                        @endif
                        
                        @if($settings->institution_address)
                            <p class="mb-1"><strong>Address:</strong><br>{{ $settings->institution_address }}</p>
                        @endif
                        
                        @if($settings->institution_phone)
                            <p class="mb-1"><strong>Phone:</strong> {{ $settings->institution_phone }}</p>
                        @endif
                        
                        @if($settings->institution_email)
                            <p class="mb-1"><strong>Email:</strong> {{ $settings->institution_email }}</p>
                        @endif
                        
                        @if($settings->institution_website)
                            <p class="mb-1"><strong>Website:</strong> 
                                <a href="{{ $settings->institution_website }}" target="_blank">{{ $settings->institution_website }}</a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.academic-settings.academic-year') }}" class="btn btn-outline-primary">
                            <i class="fas fa-calendar-alt"></i> Manage Academic Year
                        </a>
                        <a href="{{ route('admin.academic-settings.grading') }}" class="btn btn-outline-success">
                            <i class="fas fa-chart-line"></i> Grading System
                        </a>
                        <a href="{{ route('admin.academic-settings.backup') }}" class="btn btn-outline-warning">
                            <i class="fas fa-database"></i> Backup & Restore
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

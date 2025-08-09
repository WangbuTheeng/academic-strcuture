@extends('layouts.admin')

@section('title', 'Academic Settings')
@section('page-title', 'Academic Settings')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.institute-settings.index') }}">Institute Settings</a></li>
            <li class="breadcrumb-item active" aria-current="page">Academic Settings</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">School Information</h1>
            <p class="mb-0 text-muted">Manage your school's basic information and settings</p>
        </div>
        <div>
            <a href="{{ route('admin.institute-settings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Institute Settings
            </a>
        </div>
    </div>



    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.institute-settings.update-academic') }}">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- School Information -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">School Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="institution_name" class="form-label">School Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('institution_name') is-invalid @enderror"
                                           id="institution_name" name="institution_name"
                                           value="{{ old('institution_name', $settings->institution_name) }}" required>
                                    @error('institution_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="principal_name" class="form-label">Principal Name</label>
                                    <input type="text" class="form-control @error('principal_name') is-invalid @enderror"
                                           id="principal_name" name="principal_name"
                                           value="{{ old('principal_name', $settings->principal_name) }}">
                                    @error('principal_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="institution_address" class="form-label">School Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('institution_address') is-invalid @enderror"
                                              id="institution_address" name="institution_address" rows="3" required>{{ old('institution_address', $settings->institution_address) }}</textarea>
                                    @error('institution_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="institution_phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control @error('institution_phone') is-invalid @enderror"
                                           id="institution_phone" name="institution_phone"
                                           value="{{ old('institution_phone', $settings->institution_phone) }}">
                                    @error('institution_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="institution_email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control @error('institution_email') is-invalid @enderror"
                                           id="institution_email" name="institution_email"
                                           value="{{ old('institution_email', $settings->institution_email) }}">
                                    @error('institution_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="institution_website" class="form-label">Website</label>
                                    <input type="url" class="form-control @error('institution_website') is-invalid @enderror"
                                           id="institution_website" name="institution_website"
                                           value="{{ old('institution_website', $settings->institution_website) }}"
                                           placeholder="https://example.com">
                                    @error('institution_website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="principal_email" class="form-label">Principal Email</label>
                                    <input type="email" class="form-control @error('principal_email') is-invalid @enderror"
                                           id="principal_email" name="principal_email"
                                           value="{{ old('principal_email', $settings->principal_email) }}">
                                    @error('principal_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save School Information
                            </button>

                            <a href="{{ route('admin.institute-settings.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Institute Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function resetForm() {
    if (confirm('Are you sure you want to reset the form? All unsaved changes will be lost.')) {
        document.querySelector('form').reset();
    }
}
</script>
@endsection

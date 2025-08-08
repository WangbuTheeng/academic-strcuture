@extends('layouts.admin')

@section('title', 'Institute Settings')
@section('page-title', 'Institute Settings')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Institute Settings</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Institute Settings</h1>
            <p class="mb-0 text-muted">Manage your institution's basic information and settings</p>
        </div>
        <div>
            <a href="{{ route('admin.institute-settings.academic') }}" class="btn btn-info">
                <i class="fas fa-calendar-academic"></i> Academic Settings
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.institute-settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Basic Information -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="institution_name" class="form-label">Institution Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('institution_name') is-invalid @enderror" 
                                           id="institution_name" name="institution_name" 
                                           value="{{ old('institution_name', $settings->institution_name) }}" required>
                                    @error('institution_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            

                        </div>

                        <div class="form-group mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address" name="address" rows="3">{{ old('address', $settings->institution_address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Contact Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $settings->institution_phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $settings->institution_email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                   id="website" name="website" value="{{ old('website', $settings->institution_website) }}" 
                                   placeholder="https://example.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Principal Information -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Principal Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="principal_name" class="form-label">Principal Name</label>
                            <input type="text" class="form-control @error('principal_name') is-invalid @enderror" 
                                   id="principal_name" name="principal_name" value="{{ old('principal_name', $settings->principal_name) }}">
                            @error('principal_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                    </div>
                </div>
            </div>

            <!-- Logo & Actions -->
            <div class="col-lg-4">
                <!-- Institution Logo -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Institution Logo</h6>
                    </div>
                    <div class="card-body text-center">
                        @if($settings && $settings->institution_logo)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $settings->institution_logo) }}"
                                     alt="Institution Logo" class="img-fluid mb-3" style="max-height: 200px; border: 1px solid #ddd; border-radius: 8px;"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div style="display: none;" class="alert alert-warning">
                                    <small>Logo file not found: {{ $settings->institution_logo }}</small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <a href="{{ route('admin.institute-settings.remove-logo') }}"
                                   class="btn btn-outline-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to remove the logo?')">
                                    <i class="fas fa-trash"></i> Remove Logo
                                </a>
                            </div>
                        @else
                            <div class="text-muted mb-3">
                                <i class="fas fa-image fa-3x text-gray-300 mb-2"></i>
                                <p class="mb-0">No logo uploaded</p>
                                <small class="text-muted">Upload a logo to display your institution's branding</small>
                            </div>
                        @endif
                        
                        <div class="form-group">
                            <label for="logo" class="form-label">Upload New Logo</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                   id="logo" name="logo" accept="image/*" onchange="previewLogo(this)">
                            <div class="form-text">Upload an image file (JPEG, PNG, GIF). Max size: 2MB</div>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <!-- Logo Preview -->
                            <div id="logoPreview" class="mt-3" style="display: none;">
                                <label class="form-label">Preview:</label>
                                <div class="border rounded p-2 text-center">
                                    <img id="logoPreviewImg" src="" alt="Logo Preview" style="max-height: 150px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Settings
                            </button>
                            
                            <a href="{{ route('admin.institute-settings.academic') }}" class="btn btn-info">
                                <i class="fas fa-calendar-academic"></i> Academic Settings
                            </a>
                            
                            <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                <i class="fas fa-undo"></i> Reset Form
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Quick Info -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Quick Info</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <div class="mb-3">
                                <h5 class="text-primary">{{ $settings->institution_name ?: 'Not Set' }}</h5>
                                <small class="text-muted">Institution Name</small>
                            </div>
                            
                            @if($settings->institution_phone)
                            <div class="mb-3">
                                <h6 class="text-success">{{ $settings->institution_phone }}</h6>
                                <small class="text-muted">Phone Number</small>
                            </div>
                            @endif
                            
                            @if($settings->institution_email)
                            <div class="mb-3">
                                <h6 class="text-info">{{ $settings->institution_email }}</h6>
                                <small class="text-muted">Email Address</small>
                            </div>
                            @endif
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
        // Hide logo preview
        document.getElementById('logoPreview').style.display = 'none';
    }
}

function previewLogo(input) {
    const preview = document.getElementById('logoPreview');
    const previewImg = document.getElementById('logoPreviewImg');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endsection

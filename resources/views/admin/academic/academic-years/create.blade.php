@extends('layouts.admin')

@section('title', 'Add New Academic Year')

@section('content') 

@include('admin.academic.partials.sub-navbar')

<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Add New Academic Year</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.academic.index') }}">Academic Structure</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.academic-years.index') }}">Academic Years</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.academic-years.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Academic Year Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.academic-years.store') }}">
                        @csrf
                        
                        <div class="form-group">
                            <label for="name">Academic Year Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}"
                                   placeholder="e.g., 2081-2082" required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                    @if(str_contains($message, 'already been taken'))
                                        <br><small>This academic year already exists in your school. Please choose a different year.</small>
                                    @endif
                                </div>
                            @enderror
                            <small class="form-text text-muted">
                                Enter the academic year in format: YYYY-YYYY (e.g., 2081-2082)
                                <br><strong>Note:</strong> Each academic year name must be unique within your school.
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_current" name="is_current" value="1" {{ old('is_current') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_current">
                                    Set as Current Academic Year
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                If checked, this will become the current academic year and any existing current year will be deactivated.
                            </small>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Academic Year
                            </button>
                            <a href="{{ route('admin.academic-years.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-generate academic year name based on start date
document.getElementById('start_date').addEventListener('change', function() {
    const startDate = new Date(this.value);
    if (startDate) {
        const startYear = startDate.getFullYear();
        const endYear = startYear + 1;
        const nameField = document.getElementById('name');
        if (!nameField.value) {
            nameField.value = startYear + '-' + endYear;
        }
        
        // Auto-set end date to one year later
        const endDate = new Date(startDate);
        endDate.setFullYear(endDate.getFullYear() + 1);
        endDate.setDate(endDate.getDate() - 1); // One day before next year starts
        document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
    }
});
</script>
@endpush
@endsection

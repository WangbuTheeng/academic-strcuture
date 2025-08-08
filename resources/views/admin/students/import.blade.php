@extends('layouts.admin')

@section('title', 'Import Students')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
                    <li class="breadcrumb-item active">Import Students</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">Import Students</h1>
            <p class="text-muted">Import student data from Excel file</p>
        </div>
        <div>
            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Students
            </a>
        </div>
    </div>

    <!-- Import Instructions Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Import Instructions</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5>Before importing, please:</h5>
                    <ol>
                        <li>Download the template file to see the required format</li>
                        <li>Fill in your student data following the exact column headers</li>
                        <li>Ensure all required fields are filled</li>
                        <li>Save your file as CSV format (recommended) or Excel (.xlsx)</li>
                        <li>Maximum file size: 10MB</li>
                    </ol>
                    
                    <h5 class="mt-4">Required Fields:</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul>
                                <li><strong>first_name</strong> - Student's first name (required)</li>
                                <li><strong>last_name</strong> - Student's last name (required)</li>
                                <li><strong>date_of_birth</strong> - Format: YYYY-MM-DD (required)</li>
                                <li><strong>gender</strong> - Male, Female, or Other (required)</li>
                                <li><strong>phone</strong> - Student's phone number (required)</li>
                                <li><strong>address</strong> - Student's address (required)</li>
                                <li><strong>guardian_name</strong> - Guardian's full name (required)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul>
                                <li><strong>guardian_phone</strong> - Guardian's phone (required)</li>
                                <li><strong>admission_date</strong> - Format: YYYY-MM-DD (required)</li>
                                <li><strong>guardian_relation</strong> - Optional (default: Parent)</li>
                                <li><strong>email</strong> - Optional, must be unique if provided</li>
                                <li><strong>nationality</strong> - Optional (default: Nepali)</li>
                                <li><strong>status</strong> - Optional (default: active)</li>
                            </ul>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <h6><i class="fas fa-info-circle"></i> Important Notes:</h6>
                        <ul class="mb-0">
                            <li>Date format must be YYYY-MM-DD (e.g., 2010-01-15)</li>
                            <li>Email addresses must be unique within your school</li>
                            <li>Empty rows will be skipped automatically</li>
                            <li>If there are errors, successful rows will still be imported</li>
                            <li>You can import up to 1000 students at once</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="border rounded p-3 bg-light">
                        <i class="fas fa-download fa-3x text-success mb-3"></i>
                        <h5>Download Template</h5>
                        <p class="text-muted">Get the CSV template with sample data</p>
                        <a href="{{ route('admin.students.download-template') }}" class="btn btn-success">
                            <i class="fas fa-download"></i> Download CSV Template
                        </a>
                        <small class="d-block mt-2 text-info">
                            <i class="fas fa-info-circle"></i> CSV format is recommended for best compatibility
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Upload Excel File</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="excel_file" class="form-label">Select Excel File</label>
                            <input type="file" 
                                   class="form-control @error('excel_file') is-invalid @enderror" 
                                   id="excel_file" 
                                   name="excel_file" 
                                   accept=".xlsx,.xls,.csv"
                                   required>
                            @error('excel_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Supported formats: Excel (.xlsx, .xls) and CSV files. Maximum size: 10MB
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-upload"></i> Import Students
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Import Errors (if any) -->
    @if(session('import_errors'))
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-warning">
            <h6 class="m-0 font-weight-bold text-white">Import Errors</h6>
        </div>
        <div class="card-body">
            <p class="text-warning"><strong>The following rows had errors and were not imported:</strong></p>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Row</th>
                            <th>Errors</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(session('import_errors') as $error)
                        <tr>
                            <td>{{ $error['row'] }}</td>
                            <td>
                                <ul class="mb-0">
                                    @foreach($error['errors'] as $errorMessage)
                                    <li>{{ $errorMessage }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.getElementById('excel_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileSize = file.size / 1024 / 1024; // Convert to MB
        if (fileSize > 10) {
            alert('File size must be less than 10MB');
            e.target.value = '';
        }
    }
});
</script>
@endpush
@endsection

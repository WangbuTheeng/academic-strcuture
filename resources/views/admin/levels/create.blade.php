@extends('layouts.admin')

@section('title', 'Create Level')
@section('page-title', 'Create Level')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Create Level</h1>
            <p class="mb-0 text-muted">Add a new educational level</p>
        </div>
        <div>
            <a href="{{ route('admin.levels.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Levels
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Level Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.levels.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Level Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="e.g., School, College, Bachelor">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="order" class="form-label">Display Order <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                           id="order" name="order" value="{{ old('order') }}" 
                                           min="1" placeholder="1, 2, 3...">
                                    @error('order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Lower numbers appear first (1 = School, 2 = College, 3 = Bachelor)</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.levels.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Level
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Guidelines</h6>
                </div>
                <div class="card-body">
                    <h6>Level Examples:</h6>
                    <ul class="list-unstyled">
                        <li><strong>1. School</strong> - Primary/Secondary education</li>
                        <li><strong>2. College</strong> - Higher secondary education</li>
                        <li><strong>3. Bachelor</strong> - Undergraduate programs</li>
                        <li><strong>4. Master</strong> - Graduate programs</li>
                    </ul>
                    
                    <hr>
                    
                    <h6>Display Order:</h6>
                    <p class="small text-muted">
                        The order determines how levels appear in dropdowns and lists. 
                        Use sequential numbers starting from 1.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

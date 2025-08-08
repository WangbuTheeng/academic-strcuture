@extends('layouts.admin')

@section('title', 'Edit Level')
@section('page-title', 'Edit Level')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Level</h1>
            <p class="mb-0 text-muted">Update level information</p>
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
                    <form method="POST" action="{{ route('admin.levels.update', $level) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Level Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $level->name) }}" 
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
                                           id="order" name="order" value="{{ old('order', $level->order) }}" 
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
                                    <i class="fas fa-save"></i> Update Level
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
                    <h6 class="m-0 font-weight-bold text-info">Level Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-12 mb-3">
                            <div class="h4 text-primary">{{ $level->classes()->count() }}</div>
                            <div class="text-muted">Classes</div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6>Guidelines:</h6>
                    <p class="small text-muted">
                        Be careful when changing the order as it affects how levels appear 
                        throughout the system. Make sure the order is unique.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

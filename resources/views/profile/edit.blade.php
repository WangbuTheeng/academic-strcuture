@extends('layouts.admin')

@section('title', 'Profile Settings')
@section('page-title', 'Profile Settings')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Profile Settings</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Profile Settings</li>
                </ol>
            </nav>
            <p class="text-muted">Update your account's profile information and email address</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Profile Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profile Information</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', auth()->user()->name) }}" required autofocus autocomplete="name" />
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required autocomplete="username" />
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>

                            @if (session('success'))
                                <div class="text-success small">{{ session('success') }}</div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Update Password -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Password</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Ensure your account is using a long, random password to stay secure.</p>

                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">{{ __('Current Password') }}</label>
                            <input id="current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" />
                            @error('current_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('New Password') }}</label>
                            <input id="password" name="password" type="password" class="form-control" autocomplete="new-password" />
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" />
                            @error('password_confirmation')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <button type="submit" class="btn btn-primary">{{ __('Update Password') }}</button>

                            @if (session('password-updated'))
                                <div class="text-success small">{{ __('Password updated successfully.') }}</div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Security -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Account Security</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-shield-alt text-success me-2"></i>
                        <span class="small">Your account is secure</span>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-clock text-info me-2"></i>
                        <span class="small">Last login: {{ auth()->user()->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-check text-primary me-2"></i>
                        <span class="small">Role: {{ ucfirst(auth()->user()->role ?? 'User') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Fee Management Overview')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-university text-primary me-2"></i>Fee Management System
            </h1>
            <p class="text-muted mb-0">Comprehensive academic fee management and analytics</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.fees.enhanced-payments.mobile-entry') }}" class="btn btn-success">
                <i class="fas fa-mobile-alt me-2"></i>Quick Payment
            </a>
            <a href="{{ route('admin.fees.bills.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>New Bill
            </a>
        </div>
    </div>

    <!-- System Status Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                System Status
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-success">
                                <i class="fas fa-check-circle me-1"></i>Active
                            </div>
                            <div class="text-xs text-muted">All modules operational</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-server fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Students
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format(\App\Models\Student::active()->count()) }}
                            </div>
                            <div class="text-xs text-muted">Enrolled students</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Fee Structures
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format(\App\Models\FeeStructure::active()->count()) }}
                            </div>
                            <div class="text-xs text-muted">Active configurations</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cogs fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Actions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format(\App\Models\Payment::where('status', 'pending')->count()) }}
                            </div>
                            <div class="text-xs text-muted">Need verification</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Modules Grid -->
    <div class="row mb-4">
        <!-- Core Management -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-cog me-2"></i>Core Management
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.fees.structures.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-layer-group text-primary me-2"></i>
                                Fee Structures
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ \App\Models\FeeStructure::count() }}</span>
                        </a>
                        <a href="{{ route('admin.fees.bills.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-invoice text-info me-2"></i>
                                Student Bills
                            </div>
                            <span class="badge bg-info rounded-pill">{{ \App\Models\StudentBill::count() }}</span>
                        </a>
                        <a href="{{ route('admin.fees.payments.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-credit-card text-success me-2"></i>
                                Payments
                            </div>
                            <span class="badge bg-success rounded-pill">{{ \App\Models\Payment::verified()->count() }}</span>
                        </a>
                        <a href="{{ route('admin.fees.receipts.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-receipt text-warning me-2"></i>
                                Receipts
                            </div>
                            <span class="badge bg-warning rounded-pill">{{ \App\Models\PaymentReceipt::active()->count() }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics & Reporting -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-chart-bar me-2"></i>Analytics & Reporting
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.fees.reports.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-tachometer-alt text-primary me-2"></i>
                            Fee Reports Dashboard
                            <small class="text-muted d-block">Comprehensive financial analytics</small>
                        </a>
                        <a href="{{ route('admin.fees.advanced-bills.analytics') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-line text-info me-2"></i>
                            Bill Analytics
                            <small class="text-muted d-block">Advanced billing insights</small>
                        </a>
                        <a href="{{ route('admin.fees.due-tracking.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                            Due Tracking
                            <small class="text-muted d-block">Overdue payment management</small>
                        </a>
                        <a href="{{ route('admin.fees.reports.daily-collection') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-calendar-day text-success me-2"></i>
                            Daily Collection
                            <small class="text-muted d-block">Today's payment summary</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Features -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-rocket me-2"></i>Enhanced Features
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.fees.enhanced-payments.dashboard') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-tachometer-alt text-primary me-2"></i>
                            Payment Dashboard
                            <small class="text-muted d-block">Real-time payment analytics</small>
                        </a>
                        <a href="{{ route('admin.fees.enhanced-payments.mobile-entry') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-mobile-alt text-success me-2"></i>
                            Mobile Payment Entry
                            <small class="text-muted d-block">Touch-optimized interface</small>
                        </a>
                        <a href="{{ route('admin.fees.enhanced-payments.bulk-payment') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-layer-group text-info me-2"></i>
                            Bulk Payment Processing
                            <small class="text-muted d-block">Process multiple payments</small>
                        </a>
                        <a href="{{ route('admin.fees.advanced-bills.generate') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-magic text-warning me-2"></i>
                            Advanced Bill Generation
                            <small class="text-muted d-block">Installments & templates</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <a href="{{ route('admin.fees.enhanced-payments.mobile-entry') }}" class="btn btn-success btn-block h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-mobile-alt fa-2x mb-2"></i>
                                <span>Quick Payment</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <a href="{{ route('admin.fees.bills.create') }}" class="btn btn-primary btn-block h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-plus fa-2x mb-2"></i>
                                <span>Create Bill</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <a href="{{ route('admin.fees.structures.create') }}" class="btn btn-info btn-block h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-cogs fa-2x mb-2"></i>
                                <span>Fee Structure</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <a href="{{ route('admin.fees.reports.daily-collection') }}" class="btn btn-warning btn-block h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                <span>Daily Report</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <a href="{{ route('admin.fees.due-tracking.index') }}" class="btn btn-danger btn-block h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <span>Due Tracking</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <a href="{{ route('admin.fees.enhanced-payments.bulk-payment') }}" class="btn btn-secondary btn-block h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-layer-group fa-2x mb-2"></i>
                                <span>Bulk Payment</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>System Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Version:</strong></td>
                                <td>Phase 3-4 Complete</td>
                            </tr>
                            <tr>
                                <td><strong>Features:</strong></td>
                                <td>
                                    <span class="badge bg-success me-1">Core Management</span>
                                    <span class="badge bg-info me-1">Advanced Analytics</span>
                                    <span class="badge bg-warning me-1">Mobile Support</span>
                                    <span class="badge bg-primary">Enhanced UI</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Database:</strong></td>
                                <td>
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    Connected & Optimized
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Last Updated:</strong></td>
                                <td>{{ now()->format('d M Y, h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Usage Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border-right">
                                <div class="h4 mb-0 text-primary">{{ number_format(\App\Models\StudentBill::count()) }}</div>
                                <small class="text-muted">Total Bills</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h4 mb-0 text-success">{{ number_format(\App\Models\Payment::verified()->count()) }}</div>
                            <small class="text-muted">Payments</small>
                        </div>
                        <div class="col-6">
                            <div class="border-right">
                                <div class="h4 mb-0 text-info">{{ number_format(\App\Models\PaymentReceipt::count()) }}</div>
                                <small class="text-muted">Receipts</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-0 text-warning">{{ number_format(\App\Models\FeeStructure::active()->count()) }}</div>
                            <small class="text-muted">Fee Structures</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-block {
        width: 100%;
        min-height: 80px;
    }
    
    .card-header.bg-primary,
    .card-header.bg-success,
    .card-header.bg-info {
        border-bottom: none;
    }
    
    .list-group-item-action:hover {
        background-color: #f8f9fa;
        transform: translateX(2px);
        transition: all 0.2s ease;
    }
    
    .border-right {
        border-right: 1px solid #e3e6f0;
    }
    
    @media (max-width: 768px) {
        .btn-block {
            min-height: 60px;
        }
        
        .btn-block i {
            font-size: 1.5rem !important;
        }
    }
</style>
@endpush

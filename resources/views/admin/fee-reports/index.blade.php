@extends('layouts.admin')

@section('title', 'Fee Reports Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-bar text-primary me-2"></i>Fee Reports Dashboard
            </h1>
            <p class="text-muted mb-0">Comprehensive financial analytics and reporting</p>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-download me-2"></i>Export Reports
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.fees.reports.export-collection') }}">
                    <i class="fas fa-file-csv me-2"></i>Collection Report
                </a></li>
                <li><a class="dropdown-item" href="{{ route('admin.fees.due-tracking.export-overdue') }}">
                    <i class="fas fa-file-excel me-2"></i>Overdue Report
                </a></li>
            </ul>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Billed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($totalBilled, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
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
                                Total Collected
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($totalCollected, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                                Outstanding
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($totalOutstanding, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                Collection Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($collectionRate, 1) }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="row no-gutters align-items-center">
                        <div class="col">
                            <div class="progress progress-sm mr-2">
                                <div class="progress-bar bg-info" role="progressbar" 
                                     style="width: {{ $collectionRate }}%" 
                                     aria-valuenow="{{ $collectionRate }}" 
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Monthly Collection Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Collection Trend</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="{{ route('admin.fees.reports.monthly-collection') }}">View Details</a>
                            <a class="dropdown-item" href="{{ route('admin.fees.reports.export-collection') }}">Export Data</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlyCollectionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Category Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Collection by Category</h6>
                    <a href="{{ route('admin.fees.reports.category-wise') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-chart-pie fa-sm"></i> View Details
                    </a>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Reports Row -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock me-2"></i>Recent Payments
                    </h6>
                </div>
                <div class="card-body">
                    @if($recentPayments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Method</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPayments as $payment)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $payment->student->full_name }}</div>
                                            <small class="text-muted">{{ $payment->bill->bill_number }}</small>
                                        </td>
                                        <td>
                                            <span class="text-success fw-bold">
                                                Rs. {{ number_format($payment->amount, 2) }}
                                            </span>
                                        </td>
                                        <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $payment->payment_method_label }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.fees.payments.index') }}" class="btn btn-sm btn-primary">
                                View All Payments
                            </a>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-credit-card fa-2x text-gray-300 mb-2"></i>
                            <p class="text-muted">No recent payments found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-trophy me-2"></i>Top Paying Students
                    </h6>
                </div>
                <div class="card-body">
                    @if($topPayingStudents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Student</th>
                                        <th>Total Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topPayingStudents as $index => $student)
                                    <tr>
                                        <td>
                                            @if($index < 3)
                                                <i class="fas fa-medal text-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'dark') }}"></i>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $student->full_name }}</div>
                                            <small class="text-muted">{{ $student->admission_number }}</small>
                                        </td>
                                        <td>
                                            <span class="text-success fw-bold">
                                                Rs. {{ number_format($student->payments_sum_amount, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.fees.reports.student-wise') }}" class="btn btn-sm btn-primary">
                                View Student Report
                            </a>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-user-graduate fa-2x text-gray-300 mb-2"></i>
                            <p class="text-muted">No payment data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Reports -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Quick Access Reports
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.fees.reports.daily-collection') }}" class="btn btn-outline-primary btn-block h-100">
                                <i class="fas fa-calendar-day fa-2x mb-2"></i>
                                <div>Daily Collection</div>
                                <small class="text-muted">Today's payments</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.fees.reports.outstanding-dues') }}" class="btn btn-outline-warning btn-block h-100">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <div>Outstanding Dues</div>
                                <small class="text-muted">Pending payments</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.fees.reports.payment-method-wise') }}" class="btn btn-outline-info btn-block h-100">
                                <i class="fas fa-credit-card fa-2x mb-2"></i>
                                <div>Payment Methods</div>
                                <small class="text-muted">Method analysis</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.fees.due-tracking.index') }}" class="btn btn-outline-danger btn-block h-100">
                                <i class="fas fa-bell fa-2x mb-2"></i>
                                <div>Due Tracking</div>
                                <small class="text-muted">Overdue management</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Collection Chart
    const monthlyCtx = document.getElementById('monthlyCollectionChart').getContext('2d');
    const monthlyData = @json($monthlyCollections);
    
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => {
                const date = new Date(item.year, item.month - 1);
                return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            }),
            datasets: [{
                label: 'Collection Amount',
                data: monthlyData.map(item => item.total),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rs. ' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Amount: Rs. ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Category Pie Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryData = @json($categoryWiseCollection);
    
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: categoryData.map(item => item.fee_category.charAt(0).toUpperCase() + item.fee_category.slice(1)),
            datasets: [{
                data: categoryData.map(item => item.total_collected),
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', 
                    '#e74a3b', '#858796', '#5a5c69', '#6f42c1'
                ],
                hoverBackgroundColor: [
                    '#2e59d9', '#17a673', '#2c9faf', '#dda20a', 
                    '#c0392b', '#717384', '#484e5a', '#5a32a3'
                ],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': Rs. ' + context.parsed.toLocaleString();
                        }
                    }
                },
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush

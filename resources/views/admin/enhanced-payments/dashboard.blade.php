@extends('layouts.admin')

@section('title', 'Enhanced Payment Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt text-primary me-2"></i>Payment Dashboard
            </h1>
            <p class="text-muted mb-0">Real-time payment analytics and management</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.enhanced-payments.mobile-entry') }}" class="btn btn-success">
                <i class="fas fa-mobile-alt me-2"></i>Mobile Entry
            </a>
            <a href="{{ route('admin.enhanced-payments.bulk-payment') }}" class="btn btn-info">
                <i class="fas fa-layer-group me-2"></i>Bulk Payment
            </a>
            <a href="{{ route('admin.fees.payments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>New Payment
            </a>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body py-2">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-auto">
                    <label class="form-label mb-0 fw-bold">Time Period:</label>
                </div>
                <div class="col-auto">
                    <select name="date_range" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="today" {{ $dateRange === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ $dateRange === 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $dateRange === 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ $dateRange === 'year' ? 'selected' : '' }}>This Year</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshDashboard()">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Payments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($totalPayments, 2) }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ number_format($paymentCount) }} transactions
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
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Average Payment
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($averagePayment, 2) }}
                            </div>
                            <div class="text-xs text-muted">
                                Per transaction
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                                Payment Methods
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $paymentMethods->count() }}
                            </div>
                            <div class="text-xs text-muted">
                                Active methods
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
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
                                Pending Verifications
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $pendingVerifications->count() }}
                            </div>
                            <div class="text-xs text-muted">
                                Need attention
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Payment Methods Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Methods</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($paymentMethods as $method)
                            <span class="mr-2">
                                <i class="fas fa-circle text-primary"></i> {{ ucwords(str_replace('_', ' ', $method->payment_method)) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Hourly Distribution (for today) -->
        @if($dateRange === 'today' && $hourlyPayments->count() > 0)
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Today's Hourly Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="hourlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Real-time Analytics -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Trends</h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary" onclick="loadAnalytics('day')">Day</button>
                        <button type="button" class="btn btn-outline-primary active" onclick="loadAnalytics('week')">Week</button>
                        <button type="button" class="btn btn-outline-primary" onclick="loadAnalytics('month')">Month</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="trendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Recent Activities Row -->
    <div class="row">
        <!-- Recent Payments -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Payments</h6>
                    <a href="{{ route('admin.fees.payments.index') }}" class="btn btn-sm btn-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @if($recentPayments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPayments as $payment)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $payment->student->full_name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $payment->bill->bill_number ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <span class="text-success fw-bold">
                                                {{ $payment->formatted_amount }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $payment->payment_method_label }}
                                            </span>
                                        </td>
                                        <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $payment->status === 'verified' ? 'success' : 'warning' }}">
                                                {{ $payment->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.fees.payments.show', $payment) }}" 
                                                   class="btn btn-outline-info btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($payment->receipts->count() > 0)
                                                    <a href="{{ route('admin.fees.receipts.download', $payment->receipts->first()) }}" 
                                                       class="btn btn-outline-success btn-sm" title="Receipt">
                                                        <i class="fas fa-receipt"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-credit-card fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">No Recent Payments</h5>
                            <p class="text-muted">Start by processing some payments.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pending Verifications -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Pending Verifications</h6>
                </div>
                <div class="card-body">
                    @if($pendingVerifications->count() > 0)
                        @foreach($pendingVerifications as $payment)
                            <div class="d-flex align-items-center border-bottom py-2">
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $payment->student->full_name ?? 'N/A' }}</div>
                                    <small class="text-muted">
                                        {{ $payment->formatted_amount }} • {{ $payment->payment_method_label }}
                                    </small>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-success btn-sm" 
                                            onclick="verifyPayment({{ $payment->id }})" title="Verify">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            onclick="rejectPayment({{ $payment->id }})" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.fees.payments.index', ['status' => 'pending']) }}" 
                               class="btn btn-sm btn-warning">
                                View All Pending
                            </a>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="text-muted mb-0">All payments verified!</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.enhanced-payments.mobile-entry') }}" class="btn btn-success">
                            <i class="fas fa-mobile-alt me-2"></i>Mobile Payment Entry
                        </a>
                        <a href="{{ route('admin.fees.payments.quick-entry') }}" class="btn btn-primary">
                            <i class="fas fa-bolt me-2"></i>Quick Payment
                        </a>
                        <a href="{{ route('admin.enhanced-payments.bulk-payment') }}" class="btn btn-info">
                            <i class="fas fa-layer-group me-2"></i>Bulk Payment
                        </a>
                        <a href="{{ route('admin.fees.reports.daily-collection') }}" class="btn btn-outline-primary">
                            <i class="fas fa-chart-bar me-2"></i>Daily Report
                        </a>
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
    // Payment Methods Chart
    const paymentMethodsData = @json($paymentMethods);
    
    if (paymentMethodsData.length > 0) {
        const ctx = document.getElementById('paymentMethodsChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: paymentMethodsData.map(item => item.payment_method.replace('_', ' ').toUpperCase()),
                datasets: [{
                    data: paymentMethodsData.map(item => item.total),
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#c0392b', '#717384'],
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
                    }
                }
            }
        });
    }

    @if($dateRange === 'today' && $hourlyPayments->count() > 0)
    // Hourly Chart
    const hourlyData = @json($hourlyPayments);
    const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
    
    new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: hourlyData.map(item => item.hour + ':00'),
            datasets: [{
                label: 'Payments',
                data: hourlyData.map(item => item.total),
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1
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
            }
        }
    });
    @endif

    // Functions
    function refreshDashboard() {
        window.location.reload();
    }

    function loadAnalytics(period) {
        // Update active button
        document.querySelectorAll('.btn-group .btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');

        // Load analytics data
        fetch(`{{ route('admin.enhanced-payments.analytics-api') }}?period=${period}`)
            .then(response => response.json())
            .then(data => {
                updateTrendsChart(data, period);
            })
            .catch(error => {
                console.error('Error loading analytics:', error);
            });
    }

    function updateTrendsChart(data, period) {
        // Implementation for updating trends chart
        console.log('Updating trends chart with data:', data);
    }

    function verifyPayment(paymentId) {
        if (confirm('Are you sure you want to verify this payment?')) {
            fetch(`{{ url('admin/fees/payments') }}/${paymentId}/verify`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error verifying payment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error verifying payment');
            });
        }
    }

    function rejectPayment(paymentId) {
        const reason = prompt('Please enter rejection reason:');
        if (reason) {
            fetch(`{{ url('admin/fees/payments') }}/${paymentId}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ remarks: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error rejecting payment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error rejecting payment');
            });
        }
    }
</script>
@endpush

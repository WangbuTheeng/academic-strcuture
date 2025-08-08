@extends('layouts.admin')

@section('title', 'Category-wise Fee Report')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-pie text-primary me-2"></i>Category-wise Fee Report
            </h1>
            <p class="text-muted mb-0">Fee collection analysis by category</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.fees.reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Reports
            </a>
            <button type="button" class="btn btn-success" onclick="exportReport()">
                <i class="fas fa-download me-2"></i>Export Excel
            </button>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Date Range Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.fees.reports.category-wise') }}">
                <div class="row">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-2"></i>Apply Filter
                        </button>
                        <a href="{{ route('admin.fees.reports.category-wise') }}" class="btn btn-secondary">
                            <i class="fas fa-undo me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        @php
            $totalBilled = $categoryData->sum('total_billed');
            $totalCollected = $categoryData->sum('total_collected');
            $totalOutstanding = $categoryData->sum('outstanding');
            $collectionRate = $totalBilled > 0 ? ($totalCollected / $totalBilled) * 100 : 0;
        @endphp
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Categories</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $categoryData->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
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
                                Total Billed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">NRs. {{ number_format($totalBilled, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
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
                                Total Collected</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">NRs. {{ number_format($totalCollected, 2) }}</div>
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
                                Collection Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($collectionRate, 1) }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category-wise Report Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Category-wise Fee Analysis</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Fee Category</th>
                            <th>Total Billed</th>
                            <th>Total Collected</th>
                            <th>Outstanding</th>
                            <th>Collection Rate</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categoryData as $category)
                        @php
                            $categoryCollectionRate = $category->total_billed > 0 ? ($category->total_collected / $category->total_billed) * 100 : 0;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-bold">{{ ucfirst(str_replace('_', ' ', $category->fee_category)) }}</div>
                            </td>
                            <td>NRs. {{ number_format($category->total_billed, 2) }}</td>
                            <td>NRs. {{ number_format($category->total_collected, 2) }}</td>
                            <td>
                                @if($category->outstanding > 0)
                                    <span class="text-danger fw-bold">NRs. {{ number_format($category->outstanding, 2) }}</span>
                                @else
                                    <span class="text-success fw-bold">NRs. 0.00</span>
                                @endif
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-{{ $categoryCollectionRate >= 80 ? 'success' : ($categoryCollectionRate >= 50 ? 'warning' : 'danger') }}" 
                                         role="progressbar" style="width: {{ $categoryCollectionRate }}%">
                                        {{ number_format($categoryCollectionRate, 1) }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($categoryCollectionRate >= 80)
                                    <span class="badge bg-success">Excellent</span>
                                @elseif($categoryCollectionRate >= 60)
                                    <span class="badge bg-info">Good</span>
                                @elseif($categoryCollectionRate >= 40)
                                    <span class="badge bg-warning">Average</span>
                                @else
                                    <span class="badge bg-danger">Poor</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No data found for the selected date range</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Collection by Category</h6>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Outstanding by Category</h6>
                </div>
                <div class="card-body">
                    <canvas id="outstandingChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Category Collection Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: [
            @foreach($categoryData as $category)
                '{{ ucfirst(str_replace("_", " ", $category->fee_category)) }}',
            @endforeach
        ],
        datasets: [{
            data: [
                @foreach($categoryData as $category)
                    {{ $category->total_collected }},
                @endforeach
            ],
            backgroundColor: [
                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
                '#858796', '#5a5c69', '#6f42c1', '#e83e8c', '#fd7e14'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Outstanding Chart
const outstandingCtx = document.getElementById('outstandingChart').getContext('2d');
const outstandingChart = new Chart(outstandingCtx, {
    type: 'bar',
    data: {
        labels: [
            @foreach($categoryData as $category)
                '{{ ucfirst(str_replace("_", " ", $category->fee_category)) }}',
            @endforeach
        ],
        datasets: [{
            label: 'Outstanding Amount',
            data: [
                @foreach($categoryData as $category)
                    {{ $category->outstanding }},
                @endforeach
            ],
            backgroundColor: '#e74a3b'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

function exportReport() {
    // Get current filter parameters
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    
    // Create download link
    const exportUrl = `{{ route('admin.fees.reports.category-wise') }}?${params.toString()}`;
    window.open(exportUrl, '_blank');
}

// Initialize DataTable
$(document).ready(function() {
    $('#dataTable').DataTable({
        "pageLength": 25,
        "order": [[ 1, "desc" ]], // Order by total billed
        "columnDefs": [
            { "orderable": false, "targets": [5] }
        ]
    });
});
</script>
@endpush
@endsection

@props([
    'paginator',
    'route' => null,
    'showPerPage' => false,
    'perPageOptions' => [15, 25, 50, 100],
    'defaultPerPage' => 15
])

@if($paginator->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }}
            of {{ $paginator->total() }} results
        </div>
        <div class="pagination-wrapper">
            {{ $paginator->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endif

<style>
/* Fix pagination button sizes - Matching payments page exactly */
.pagination-wrapper .pagination {
    margin-bottom: 0;
}

.pagination-wrapper .page-link {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    line-height: 1.5;
}

.pagination-wrapper .page-item.active .page-link {
    background-color: #4e73df;
    border-color: #4e73df;
}

.pagination-wrapper .page-link:hover {
    background-color: #f8f9fc;
    border-color: #dee2e6;
}

/* Responsive table improvements */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }

    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}
</style>



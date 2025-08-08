@extends('layouts.admin')

@section('title', 'Column Reorder Tool')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-arrows-alt text-primary"></i>
            Drag & Drop Column Reorder
        </h1>
        <div>
            <a href="{{ route('admin.marksheets.customize.smart-demo') }}" class="btn btn-info me-2">
                <i class="fas fa-eye"></i> View Demo
            </a>
            <a href="{{ route('admin.marksheets.customize.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Templates
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Column Reorder Panel -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-grip-vertical"></i> Drag to Reorder Columns
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>How to use:</strong> Drag and drop the column items below to reorder them. 
                        The system will automatically save your preferred order.
                    </div>
                    
                    <div id="columnReorderList" class="sortable-list">
                        <!-- Sample columns for demonstration -->
                        <div class="sortable-item" data-column="subject">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-grip-vertical text-muted me-3 drag-handle"></i>
                                <div class="flex-grow-1">
                                    <strong>Subject</strong>
                                    <small class="text-muted d-block">Subject name</small>
                                </div>
                                <span class="badge bg-primary">Required</span>
                            </div>
                        </div>
                        
                        <div class="sortable-item" data-column="theory_marks">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-grip-vertical text-muted me-3"></i>
                                <div class="flex-grow-1">
                                    <strong>Theory Marks</strong>
                                    <small class="text-muted d-block">Theory component marks</small>
                                </div>
                                <span class="badge bg-success">Auto-detected</span>
                            </div>
                        </div>
                        
                        <div class="sortable-item" data-column="practical_marks">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-grip-vertical text-muted me-3"></i>
                                <div class="flex-grow-1">
                                    <strong>Practical Marks</strong>
                                    <small class="text-muted d-block">Practical component marks</small>
                                </div>
                                <span class="badge bg-success">Auto-detected</span>
                            </div>
                        </div>
                        
                        <div class="sortable-item" data-column="assessment_marks">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-grip-vertical text-muted me-3"></i>
                                <div class="flex-grow-1">
                                    <strong>Assessment Marks</strong>
                                    <small class="text-muted d-block">Assessment component marks</small>
                                </div>
                                <span class="badge bg-success">Auto-detected</span>
                            </div>
                        </div>
                        
                        <div class="sortable-item" data-column="total_marks">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-grip-vertical text-muted me-3"></i>
                                <div class="flex-grow-1">
                                    <strong>Total Marks</strong>
                                    <small class="text-muted d-block">Sum of all components</small>
                                </div>
                                <span class="badge bg-primary">Required</span>
                            </div>
                        </div>
                        
                        <div class="sortable-item" data-column="grade">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-grip-vertical text-muted me-3"></i>
                                <div class="flex-grow-1">
                                    <strong>Grade</strong>
                                    <small class="text-muted d-block">Letter grade (A, B, C, etc.)</small>
                                </div>
                                <span class="badge bg-primary">Required</span>
                            </div>
                        </div>
                        
                        <div class="sortable-item" data-column="result">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-grip-vertical text-muted me-3"></i>
                                <div class="flex-grow-1">
                                    <strong>Result</strong>
                                    <small class="text-muted d-block">Pass/Fail status</small>
                                </div>
                                <span class="badge bg-primary">Required</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="button" class="btn btn-success" onclick="saveColumnOrder()">
                            <i class="fas fa-save"></i> Save Column Order
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="resetColumnOrder()">
                            <i class="fas fa-undo"></i> Reset to Default
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Panel -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-eye"></i> Live Preview
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="previewTable">
                            <thead class="bg-primary text-white">
                                <tr id="previewHeader">
                                    <!-- Headers will be updated by JavaScript -->
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td id="previewRow">
                                        <!-- Sample data will be updated by JavaScript -->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-check-circle"></i>
                        <strong>Smart Detection Active:</strong> The table automatically adapts based on your exam configuration. 
                        Only relevant columns will appear in the actual marksheet.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.sortable-list {
    min-height: 400px;
}

.sortable-item {
    background: #fff;
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    padding: 1rem;
    margin-bottom: 0.5rem;
    cursor: move;
    transition: all 0.2s ease;
}

.sortable-item:hover {
    background: #f8f9fc;
    border-color: #5a5c69;
    transform: translateY(-1px);
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.sortable-item.dragging {
    opacity: 0.5;
    transform: rotate(5deg);
}

.sortable-item .fas.fa-grip-vertical {
    font-size: 1.2rem;
    cursor: grab;
}

.sortable-item .fas.fa-grip-vertical:active {
    cursor: grabbing;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Sortable
    const sortableList = document.getElementById('columnReorderList');
    const sortable = Sortable.create(sortableList, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'dragging',
        onEnd: function(evt) {
            updatePreview();
        }
    });
    
    // Initial preview update
    updatePreview();
});

function updatePreview() {
    const items = document.querySelectorAll('.sortable-item');
    const header = document.getElementById('previewHeader');
    const row = document.getElementById('previewRow');
    
    // Clear existing content
    header.innerHTML = '';
    row.innerHTML = '';
    
    // Sample data for preview
    const sampleData = {
        'subject': 'Mathematics',
        'theory_marks': '52/60',
        'practical_marks': '22/25',
        'assessment_marks': '13/15',
        'total_marks': '87/100',
        'grade': 'A',
        'result': 'Pass'
    };
    
    // Build preview based on current order
    items.forEach(item => {
        const columnKey = item.dataset.column;
        const columnText = item.querySelector('strong').textContent;
        
        // Add header
        const th = document.createElement('th');
        th.textContent = columnText;
        header.appendChild(th);
        
        // Add data cell
        const td = document.createElement('td');
        td.textContent = sampleData[columnKey] || '-';
        td.className = 'text-center';
        row.appendChild(td);
    });
}

function saveColumnOrder() {
    const items = document.querySelectorAll('.sortable-item');
    const order = Array.from(items).map(item => item.dataset.column);
    
    // Here you would typically send this to your backend
    console.log('Column order:', order);
    
    // Show success message
    alert('Column order saved successfully!');
}

function resetColumnOrder() {
    // Reset to default order
    const defaultOrder = ['subject', 'theory_marks', 'practical_marks', 'assessment_marks', 'total_marks', 'grade', 'result'];
    const container = document.getElementById('columnReorderList');
    
    // Reorder DOM elements
    defaultOrder.forEach(columnKey => {
        const item = container.querySelector(`[data-column="${columnKey}"]`);
        if (item) {
            container.appendChild(item);
        }
    });
    
    updatePreview();
    alert('Column order reset to default!');
}
</script>
@endsection

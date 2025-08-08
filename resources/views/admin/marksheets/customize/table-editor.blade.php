@extends('layouts.admin')

@section('title', 'Enhanced Table Editor')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-magic text-primary"></i>
            Enhanced Table Editor with Smart Detection
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

    <!-- Smart Detection Info -->
    <div class="alert alert-success mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="alert-heading mb-2">
                    <i class="fas fa-lightbulb"></i> Smart Table Detection
                </h5>
                <p class="mb-0">
                    The system automatically detects your exam configuration and shows only relevant columns.
                    Use this editor to fine-tune column order and add custom fields.
                </p>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-success" onclick="saveTableConfiguration()">
                    <i class="fas fa-save"></i> Save Configuration
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Table Configuration Panel -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs"></i> Table Configuration
                    </h6>
                </div>
                <div class="card-body">
                    <form id="tableConfigForm">
                        <div class="mb-4">
                            <label class="form-label font-weight-bold">Available Columns</label>
                            <div id="columnsList" class="border rounded p-3" style="min-height: 400px;">
                                <!-- Columns will be populated by JavaScript -->
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="newColumnKey" class="form-label">Add Custom Column</label>
                                    <input type="text" class="form-control" id="newColumnKey" placeholder="e.g., homework_marks">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="newColumnLabel" class="form-label">Column Label</label>
                                    <input type="text" class="form-control" id="newColumnLabel" placeholder="e.g., Homework">
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-primary" onclick="addCustomColumn()">
                            <i class="fas fa-plus"></i> Add Column
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Live Preview Panel -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-eye"></i> Live Preview
                    </h6>
                </div>
                <div class="card-body">
                    <div id="tablePreview" class="table-responsive">
                        <!-- Preview will be updated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.column-item {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 10px;
    cursor: move;
}

.column-item.enabled {
    background: #d4edda;
    border-color: #c3e6cb;
}

.column-item.disabled {
    background: #f8d7da;
    border-color: #f5c6cb;
    opacity: 0.7;
}

.column-controls {
    display: flex;
    gap: 5px;
    align-items: center;
}

.drag-handle {
    cursor: grab;
    color: #6c757d;
}

.drag-handle:active {
    cursor: grabbing;
}

.width-input {
    width: 80px;
}

#tablePreview table {
    font-size: 12px;
}

#tablePreview th {
    background-color: #2563eb;
    color: white;
    padding: 8px;
    text-align: center;
}

#tablePreview td {
    padding: 6px;
    text-align: center;
    border: 1px solid #dee2e6;
}
</style>

<script>
let tableColumns = [
    {key: 'subject', label: 'Subject', width: '25%', enabled: true, order: 1},
    {key: 'theory_marks', label: 'Theory', width: '10%', enabled: true, order: 2},
    {key: 'practical_marks', label: 'Practical', width: '10%', enabled: true, order: 3},
    {key: 'assessment_marks', label: 'Assessment', width: '10%', enabled: true, order: 4},
    {key: 'total_marks', label: 'Total', width: '10%', enabled: true, order: 5},
    {key: 'grade', label: 'Grade', width: '8%', enabled: true, order: 6},
    {key: 'grade_points', label: 'GP', width: '8%', enabled: true, order: 7},
    {key: 'attendance', label: 'Attendance', width: '8%', enabled: false, order: 8},
    {key: 'result', label: 'Result', width: '8%', enabled: true, order: 9},
    {key: 'rank', label: 'Rank', width: '8%', enabled: false, order: 10}
];

function renderColumns() {
    const container = document.getElementById('columnsList');
    container.innerHTML = '';
    
    tableColumns.sort((a, b) => a.order - b.order).forEach((column, index) => {
        const div = document.createElement('div');
        div.className = `column-item ${column.enabled ? 'enabled' : 'disabled'}`;
        div.draggable = true;
        div.dataset.columnKey = column.key;
        
        div.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-grip-vertical drag-handle me-2"></i>
                    <strong>${column.label}</strong>
                    <small class="text-muted ms-2">(${column.key})</small>
                </div>
                <div class="column-controls">
                    <input type="text" class="form-control form-control-sm width-input" 
                           value="${column.width}" 
                           onchange="updateColumnWidth('${column.key}', this.value)">
                    <button type="button" class="btn btn-sm ${column.enabled ? 'btn-success' : 'btn-secondary'}" 
                            onclick="toggleColumn('${column.key}')">
                        <i class="fas fa-${column.enabled ? 'eye' : 'eye-slash'}"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" 
                            onclick="removeColumn('${column.key}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        container.appendChild(div);
    });
    
    updatePreview();
}

function toggleColumn(key) {
    const column = tableColumns.find(col => col.key === key);
    if (column) {
        column.enabled = !column.enabled;
        renderColumns();
    }
}

function updateColumnWidth(key, width) {
    const column = tableColumns.find(col => col.key === key);
    if (column) {
        column.width = width;
        updatePreview();
    }
}

function removeColumn(key) {
    if (confirm('Are you sure you want to remove this column?')) {
        tableColumns = tableColumns.filter(col => col.key !== key);
        renderColumns();
    }
}

function addCustomColumn() {
    const key = document.getElementById('newColumnKey').value.trim();
    const label = document.getElementById('newColumnLabel').value.trim();
    
    if (!key || !label) {
        alert('Please enter both column key and label');
        return;
    }
    
    if (tableColumns.find(col => col.key === key)) {
        alert('Column with this key already exists');
        return;
    }
    
    const maxOrder = Math.max(...tableColumns.map(col => col.order));
    tableColumns.push({
        key: key,
        label: label,
        width: '10%',
        enabled: true,
        order: maxOrder + 1
    });
    
    document.getElementById('newColumnKey').value = '';
    document.getElementById('newColumnLabel').value = '';
    
    renderColumns();
}

function updatePreview() {
    const enabledColumns = tableColumns.filter(col => col.enabled).sort((a, b) => a.order - b.order);
    
    let html = `
        <table class="table table-bordered">
            <thead>
                <tr>
                    ${enabledColumns.map(col => `<th style="width: ${col.width}">${col.label}</th>`).join('')}
                </tr>
            </thead>
            <tbody>
                <tr>
                    ${enabledColumns.map(col => {
                        switch(col.key) {
                            case 'subject': return '<td>Mathematics</td>';
                            case 'theory_marks': return '<td>75</td>';
                            case 'practical_marks': return '<td>20</td>';
                            case 'assessment_marks': return '<td>15</td>';
                            case 'total_marks': return '<td>110</td>';
                            case 'grade': return '<td>A</td>';
                            case 'grade_points': return '<td>4.0</td>';
                            case 'attendance': return '<td>95%</td>';
                            case 'result': return '<td>Pass</td>';
                            case 'rank': return '<td>5</td>';
                            default: return '<td>-</td>';
                        }
                    }).join('')}
                </tr>
                <tr>
                    ${enabledColumns.map(col => {
                        switch(col.key) {
                            case 'subject': return '<td>Science</td>';
                            case 'theory_marks': return '<td>80</td>';
                            case 'practical_marks': return '<td>18</td>';
                            case 'assessment_marks': return '<td>12</td>';
                            case 'total_marks': return '<td>110</td>';
                            case 'grade': return '<td>A</td>';
                            case 'grade_points': return '<td>4.0</td>';
                            case 'attendance': return '<td>92%</td>';
                            case 'result': return '<td>Pass</td>';
                            case 'rank': return '<td>3</td>';
                            default: return '<td>-</td>';
                        }
                    }).join('')}
                </tr>
            </tbody>
        </table>
    `;
    
    document.getElementById('tablePreview').innerHTML = html;
}

function saveTableConfiguration() {
    // Here you would send the configuration to the server
    const config = {
        table_columns: tableColumns
    };
    
    console.log('Saving configuration:', config);
    alert('Table configuration saved successfully!');
}

// Initialize drag and drop
document.addEventListener('DOMContentLoaded', function() {
    renderColumns();
    
    // Add drag and drop functionality
    const container = document.getElementById('columnsList');
    
    container.addEventListener('dragstart', function(e) {
        if (e.target.classList.contains('column-item')) {
            e.dataTransfer.setData('text/plain', e.target.dataset.columnKey);
        }
    });
    
    container.addEventListener('dragover', function(e) {
        e.preventDefault();
    });
    
    container.addEventListener('drop', function(e) {
        e.preventDefault();
        const draggedKey = e.dataTransfer.getData('text/plain');
        const dropTarget = e.target.closest('.column-item');
        
        if (dropTarget && dropTarget.dataset.columnKey !== draggedKey) {
            const draggedIndex = tableColumns.findIndex(col => col.key === draggedKey);
            const targetIndex = tableColumns.findIndex(col => col.key === dropTarget.dataset.columnKey);
            
            // Reorder columns
            const draggedColumn = tableColumns[draggedIndex];
            tableColumns.splice(draggedIndex, 1);
            tableColumns.splice(targetIndex, 0, draggedColumn);
            
            // Update order values
            tableColumns.forEach((col, index) => {
                col.order = index + 1;
            });
            
            renderColumns();
        }
    });
});
</script>
@endsection

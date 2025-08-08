@extends('layouts.admin')

@section('title', 'Drag & Drop Template Builder')
@section('page-title', 'Template Builder')

@section('content')
<div class="container-fluid">
    <!-- Include Reports Sub-Navigation -->
    @include('admin.reports.partials.sub-navbar')

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Drag & Drop Template Builder</h1>
            <p class="mb-0 text-muted">Create and customize marksheet templates with drag-and-drop interface</p>
        </div>
        <div>
            <button type="button" class="btn btn-success" onclick="saveTemplate()">
                <i class="fas fa-save"></i> Save Template
            </button>
            <a href="{{ route('admin.marksheets.customize.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Templates
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Template Builder -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Template Design</h6>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="previewTemplate()">
                            <i class="fas fa-eye"></i> Preview
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetTemplate()">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Template Canvas -->
                    <div id="template-canvas" class="border rounded p-3 bg-light" style="min-height: 600px;">
                        <!-- Header Section -->
                        <div class="template-section" data-section="header">
                            <div class="section-header d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 text-primary">Header Section</h6>
                                <div class="section-controls">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleSection('header')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="section-content border rounded p-3 bg-white" id="header-section">
                                <div class="row">
                                    <div class="col-md-3 text-center">
                                        <div class="draggable-element" data-element="logo">
                                            <i class="fas fa-image fa-2x text-muted"></i>
                                            <p class="small mb-0">School Logo</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-center">
                                        <div class="draggable-element" data-element="school-info">
                                            <h5 class="mb-1">School Name</h5>
                                            <p class="small mb-0">Address, Phone, Email</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <div class="draggable-element" data-element="seal">
                                            <i class="fas fa-certificate fa-2x text-muted"></i>
                                            <p class="small mb-0">School Seal</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Student Info Section -->
                        <div class="template-section mt-4" data-section="student-info">
                            <div class="section-header d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 text-primary">Student Information</h6>
                                <div class="section-controls">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleSection('student-info')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="section-content border rounded p-3 bg-white" id="student-info-section">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="draggable-element" data-element="student-name">
                                            <strong>Student Name:</strong> John Doe
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="draggable-element" data-element="roll-number">
                                            <strong>Roll Number:</strong> 2024001
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="draggable-element" data-element="class-section">
                                            <strong>Class:</strong> Grade 10 - A
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="draggable-element" data-element="academic-year">
                                            <strong>Academic Year:</strong> 2024-2025
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Marks Table Section -->
                        <div class="template-section mt-4" data-section="marks-table">
                            <div class="section-header d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 text-primary">Marks Table</h6>
                                <div class="section-controls">
                                    <button class="btn btn-sm btn-outline-primary" onclick="configureTable()">
                                        <i class="fas fa-cog"></i> Configure
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleSection('marks-table')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="section-content border rounded p-3 bg-white" id="marks-table-section">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm" id="marks-table">
                                        <thead class="table-light">
                                            <tr id="table-headers">
                                                <th class="draggable-column" data-column="subject">Subject</th>
                                                <th class="draggable-column" data-column="theory">Theory</th>
                                                <th class="draggable-column" data-column="practical">Practical</th>
                                                <th class="draggable-column" data-column="total">Total</th>
                                                <th class="draggable-column" data-column="grade">Grade</th>
                                                <th class="draggable-column" data-column="remarks">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Mathematics</td>
                                                <td>85</td>
                                                <td>90</td>
                                                <td>175</td>
                                                <td>A+</td>
                                                <td>Excellent</td>
                                            </tr>
                                            <tr>
                                                <td>Science</td>
                                                <td>78</td>
                                                <td>82</td>
                                                <td>160</td>
                                                <td>A</td>
                                                <td>Good</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Section -->
                        <div class="template-section mt-4" data-section="footer">
                            <div class="section-header d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 text-primary">Footer Section</h6>
                                <div class="section-controls">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleSection('footer')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="section-content border rounded p-3 bg-white" id="footer-section">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="draggable-element" data-element="class-teacher">
                                            <p class="mb-0">Class Teacher</p>
                                            <div class="border-top mt-2 pt-1">Signature</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="draggable-element" data-element="principal">
                                            <p class="mb-0">Principal</p>
                                            <div class="border-top mt-2 pt-1">Signature</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="draggable-element" data-element="date">
                                            <p class="mb-0">Date: {{ date('Y-m-d') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuration Panel -->
        <div class="col-lg-4">
            <!-- Template Settings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Template Settings</h6>
                </div>
                <div class="card-body">
                    <form id="template-settings-form">
                        <div class="mb-3">
                            <label for="template-name" class="form-label">Template Name</label>
                            <input type="text" class="form-control" id="template-name" value="Custom Template">
                        </div>

                        <div class="mb-3">
                            <label for="page-orientation" class="form-label">Page Orientation</label>
                            <select class="form-control" id="page-orientation">
                                <option value="portrait">Portrait</option>
                                <option value="landscape">Landscape</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="font-family" class="form-label">Font Family</label>
                            <select class="form-control" id="font-family">
                                <option value="Arial">Arial</option>
                                <option value="Times New Roman">Times New Roman</option>
                                <option value="Helvetica">Helvetica</option>
                                <option value="Georgia">Georgia</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="font-size" class="form-label">Base Font Size</label>
                            <select class="form-control" id="font-size">
                                <option value="10px">10px</option>
                                <option value="12px" selected>12px</option>
                                <option value="14px">14px</option>
                                <option value="16px">16px</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Color Scheme</label>
                            <div class="row">
                                <div class="col-6">
                                    <label for="primary-color" class="form-label small">Primary</label>
                                    <input type="color" class="form-control form-control-color" id="primary-color" value="#0d6efd">
                                </div>
                                <div class="col-6">
                                    <label for="secondary-color" class="form-label small">Secondary</label>
                                    <input type="color" class="form-control form-control-color" id="secondary-color" value="#6c757d">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Available Elements -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Available Elements</h6>
                </div>
                <div class="card-body">
                    <div class="element-palette">
                        <div class="element-item" draggable="true" data-element="text-block">
                            <i class="fas fa-font"></i> Text Block
                        </div>
                        <div class="element-item" draggable="true" data-element="image">
                            <i class="fas fa-image"></i> Image
                        </div>
                        <div class="element-item" draggable="true" data-element="signature">
                            <i class="fas fa-signature"></i> Signature
                        </div>
                        <div class="element-item" draggable="true" data-element="date">
                            <i class="fas fa-calendar"></i> Date
                        </div>
                        <div class="element-item" draggable="true" data-element="qr-code">
                            <i class="fas fa-qrcode"></i> QR Code
                        </div>
                        <div class="element-item" draggable="true" data-element="barcode">
                            <i class="fas fa-barcode"></i> Barcode
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="loadTemplate()">
                            <i class="fas fa-upload"></i> Load Template
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="exportTemplate()">
                            <i class="fas fa-download"></i> Export Template
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="duplicateTemplate()">
                            <i class="fas fa-copy"></i> Duplicate Template
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Configuration Modal -->
<div class="modal fade" id="tableConfigModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configure Marks Table</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Available Columns</h6>
                        <div id="available-columns" class="border rounded p-3" style="min-height: 200px;">
                            <div class="column-item" data-column="subject">Subject</div>
                            <div class="column-item" data-column="theory">Theory Marks</div>
                            <div class="column-item" data-column="practical">Practical Marks</div>
                            <div class="column-item" data-column="total">Total Marks</div>
                            <div class="column-item" data-column="percentage">Percentage</div>
                            <div class="column-item" data-column="grade">Grade</div>
                            <div class="column-item" data-column="gpa">GPA</div>
                            <div class="column-item" data-column="remarks">Remarks</div>
                            <div class="column-item" data-column="attendance">Attendance</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Selected Columns</h6>
                        <div id="selected-columns" class="border rounded p-3" style="min-height: 200px;">
                            <!-- Selected columns will appear here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="applyTableConfig()">Apply Changes</button>
            </div>
        </div>
    </div>
</div>

<style>
.template-section {
    margin-bottom: 1rem;
}

.section-content {
    transition: all 0.3s ease;
}

.section-content.collapsed {
    display: none;
}

.draggable-element {
    cursor: move;
    padding: 0.5rem;
    border: 2px dashed transparent;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
}

.draggable-element:hover {
    border-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.1);
}

.draggable-column {
    cursor: move;
    position: relative;
}

.draggable-column:hover {
    background-color: rgba(13, 110, 253, 0.1);
}

.element-palette .element-item {
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    cursor: move;
    transition: all 0.2s ease;
}

.element-palette .element-item:hover {
    border-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.1);
}

.column-item {
    padding: 0.5rem;
    margin-bottom: 0.25rem;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    cursor: move;
    background-color: #f8f9fa;
}

.column-item:hover {
    background-color: #e9ecef;
}

#template-canvas {
    font-family: Arial, sans-serif;
    font-size: 12px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeDragAndDrop();
    initializeTableConfiguration();
    initializeTemplateSettings();
});

function initializeDragAndDrop() {
    // Make table headers sortable
    const tableHeaders = document.getElementById('table-headers');
    if (tableHeaders) {
        new Sortable(tableHeaders, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: function(evt) {
                updateTablePreview();
            }
        });
    }

    // Make sections sortable
    const templateCanvas = document.getElementById('template-canvas');
    if (templateCanvas) {
        new Sortable(templateCanvas, {
            animation: 150,
            handle: '.section-header',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: function(evt) {
                console.log('Section moved:', evt.oldIndex, 'to', evt.newIndex);
            }
        });
    }

    // Make elements within sections sortable
    document.querySelectorAll('.section-content').forEach(section => {
        new Sortable(section, {
            group: 'template-elements',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: function(evt) {
                console.log('Element moved within section');
            }
        });
    });
}

function initializeTableConfiguration() {
    // Make available columns sortable
    const availableColumns = document.getElementById('available-columns');
    if (availableColumns) {
        new Sortable(availableColumns, {
            group: {
                name: 'columns',
                pull: 'clone',
                put: false
            },
            animation: 150,
            sort: false
        });
    }

    // Make selected columns sortable
    const selectedColumns = document.getElementById('selected-columns');
    if (selectedColumns) {
        new Sortable(selectedColumns, {
            group: 'columns',
            animation: 150,
            onAdd: function(evt) {
                // Remove clone class and make it a real item
                evt.item.classList.remove('sortable-clone');
            }
        });
    }
}

function initializeTemplateSettings() {
    // Apply settings changes in real-time
    document.getElementById('font-family').addEventListener('change', function() {
        document.getElementById('template-canvas').style.fontFamily = this.value;
    });

    document.getElementById('font-size').addEventListener('change', function() {
        document.getElementById('template-canvas').style.fontSize = this.value;
    });

    document.getElementById('primary-color').addEventListener('change', function() {
        document.documentElement.style.setProperty('--bs-primary', this.value);
    });

    document.getElementById('page-orientation').addEventListener('change', function() {
        const canvas = document.getElementById('template-canvas');
        if (this.value === 'landscape') {
            canvas.style.width = '100%';
            canvas.style.maxWidth = '1000px';
        } else {
            canvas.style.width = '100%';
            canvas.style.maxWidth = '700px';
        }
    });
}

function toggleSection(sectionId) {
    const section = document.getElementById(sectionId + '-section');
    const button = event.target;

    if (section.classList.contains('collapsed')) {
        section.classList.remove('collapsed');
        button.innerHTML = '<i class="fas fa-eye"></i>';
    } else {
        section.classList.add('collapsed');
        button.innerHTML = '<i class="fas fa-eye-slash"></i>';
    }
}

function configureTable() {
    // Populate current table configuration
    const currentHeaders = document.querySelectorAll('#table-headers th');
    const selectedColumns = document.getElementById('selected-columns');
    selectedColumns.innerHTML = '';

    currentHeaders.forEach(header => {
        const columnItem = document.createElement('div');
        columnItem.className = 'column-item';
        columnItem.dataset.column = header.dataset.column;
        columnItem.textContent = header.textContent;
        selectedColumns.appendChild(columnItem);
    });

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('tableConfigModal'));
    modal.show();
}

function applyTableConfig() {
    const selectedColumns = document.querySelectorAll('#selected-columns .column-item');
    const tableHeaders = document.getElementById('table-headers');

    // Clear current headers
    tableHeaders.innerHTML = '';

    // Add new headers based on selection
    selectedColumns.forEach(column => {
        const th = document.createElement('th');
        th.className = 'draggable-column';
        th.dataset.column = column.dataset.column;
        th.textContent = column.textContent;
        tableHeaders.appendChild(th);
    });

    // Reinitialize sortable for new headers
    new Sortable(tableHeaders, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        onEnd: function(evt) {
            updateTablePreview();
        }
    });

    // Close modal
    bootstrap.Modal.getInstance(document.getElementById('tableConfigModal')).hide();

    updateTablePreview();
}

function updateTablePreview() {
    // Update table body to match headers
    const headers = document.querySelectorAll('#table-headers th');
    const rows = document.querySelectorAll('#marks-table tbody tr');

    // This would update the table body based on the new header order
    console.log('Table preview updated with', headers.length, 'columns');
}

function previewTemplate() {
    // Generate preview in a new window
    const templateContent = document.getElementById('template-canvas').innerHTML;
    const previewWindow = window.open('', '_blank', 'width=800,height=600');

    previewWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Template Preview</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { font-family: ${document.getElementById('font-family').value}; font-size: ${document.getElementById('font-size').value}; }
                .template-section { margin-bottom: 1rem; }
                .section-header { display: none; }
                .section-content { border: none !important; }
                @media print { body { margin: 0; } }
            </style>
        </head>
        <body class="p-4">
            ${templateContent}
        </body>
        </html>
    `);

    previewWindow.document.close();
}

function saveTemplate() {
    const templateData = {
        name: document.getElementById('template-name').value,
        orientation: document.getElementById('page-orientation').value,
        fontFamily: document.getElementById('font-family').value,
        fontSize: document.getElementById('font-size').value,
        primaryColor: document.getElementById('primary-color').value,
        secondaryColor: document.getElementById('secondary-color').value,
        sections: getSectionConfiguration(),
        tableColumns: getTableConfiguration()
    };

    // Send to server
    fetch('{{ route("admin.marksheets.customize.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(templateData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Template saved successfully!');
        } else {
            alert('Error saving template: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving template');
    });
}

function getSectionConfiguration() {
    const sections = [];
    document.querySelectorAll('.template-section').forEach((section, index) => {
        sections.push({
            id: section.dataset.section,
            order: index,
            visible: !section.querySelector('.section-content').classList.contains('collapsed')
        });
    });
    return sections;
}

function getTableConfiguration() {
    const columns = [];
    document.querySelectorAll('#table-headers th').forEach((header, index) => {
        columns.push({
            id: header.dataset.column,
            name: header.textContent,
            order: index
        });
    });
    return columns;
}

function resetTemplate() {
    if (confirm('Are you sure you want to reset the template? All changes will be lost.')) {
        location.reload();
    }
}

function loadTemplate() {
    // This would open a file picker or template selector
    alert('Load template functionality would be implemented here');
}

function exportTemplate() {
    const templateData = {
        name: document.getElementById('template-name').value,
        configuration: getSectionConfiguration(),
        tableColumns: getTableConfiguration()
    };

    const dataStr = JSON.stringify(templateData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});

    const link = document.createElement('a');
    link.href = URL.createObjectURL(dataBlob);
    link.download = templateData.name.replace(/\s+/g, '_').toLowerCase() + '_template.json';
    link.click();
}

function duplicateTemplate() {
    const currentName = document.getElementById('template-name').value;
    document.getElementById('template-name').value = currentName + ' (Copy)';
    alert('Template duplicated! Don\'t forget to save it with the new name.');
}
</script>
@endsection

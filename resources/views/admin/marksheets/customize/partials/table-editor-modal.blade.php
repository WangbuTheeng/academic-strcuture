<!-- Advanced Table Editor Modal -->
<div class="modal fade" id="tableEditorModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-table me-2"></i>Advanced Table Editor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0" style="height: 70vh;">
                    <!-- Left Panel - Table Structure -->
                    <div class="col-md-3 bg-light border-end">
                        <div class="p-3">
                            <h6 class="mb-3">Table Structure</h6>
                            
                            <!-- Table Dimensions -->
                            <div class="mb-3">
                                <label class="form-label">Rows</label>
                                <input type="number" class="form-control form-control-sm" id="table-rows" value="5" min="1" max="50">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Columns</label>
                                <input type="number" class="form-control form-control-sm" id="table-columns" value="5" min="1" max="20">
                            </div>
                            
                            <!-- Column Management -->
                            <div class="mb-3">
                                <h6 class="mb-2">Columns</h6>
                                <div id="column-list" class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                    <!-- Column items will be populated here -->
                                </div>
                                <button class="btn btn-sm btn-outline-primary w-100 mt-2" id="add-column">
                                    <i class="fas fa-plus"></i> Add Column
                                </button>
                            </div>
                            
                            <!-- Table Style -->
                            <div class="mb-3">
                                <h6 class="mb-2">Table Style</h6>
                                <div class="mb-2">
                                    <label class="form-label">Border Width</label>
                                    <input type="range" class="form-range" id="border-width" min="0" max="5" value="1">
                                    <div class="text-center small" id="border-width-value">1px</div>
                                </div>
                                
                                <div class="mb-2">
                                    <label class="form-label">Border Color</label>
                                    <input type="color" class="form-control form-control-color form-control-sm" id="border-color" value="#000000">
                                </div>
                                
                                <div class="mb-2">
                                    <label class="form-label">Header Background</label>
                                    <input type="color" class="form-control form-control-color form-control-sm" id="header-bg" value="#f8f9fa">
                                </div>
                                
                                <div class="mb-2">
                                    <label class="form-label">Cell Padding</label>
                                    <input type="range" class="form-range" id="cell-padding" min="2" max="20" value="8">
                                    <div class="text-center small" id="cell-padding-value">8px</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Center Panel - Table Preview -->
                    <div class="col-md-6">
                        <div class="p-3 h-100 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Table Preview</h6>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary" id="table-zoom-out">
                                        <i class="fas fa-search-minus"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" id="table-zoom-in">
                                        <i class="fas fa-search-plus"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" id="table-fit">
                                        <i class="fas fa-expand-arrows-alt"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="flex-grow-1 overflow-auto border rounded bg-white p-3">
                                <div id="table-preview-container" style="transform-origin: top left;">
                                    <table id="table-preview" class="table table-bordered">
                                        <!-- Table content will be generated here -->
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Panel - Column Properties -->
                    <div class="col-md-3 bg-light border-start">
                        <div class="p-3">
                            <h6 class="mb-3">Column Properties</h6>
                            
                            <div id="column-properties">
                                <p class="text-muted text-center">Select a column to edit properties</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="apply-table-changes">Apply Changes</button>
            </div>
        </div>
    </div>
</div>

<style>
.column-item {
    display: flex;
    align-items: center;
    padding: 6px 8px;
    margin-bottom: 4px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.column-item:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
}

.column-item.active {
    background: #e3f2fd;
    border-color: #2196f3;
}

.column-item .column-name {
    flex-grow: 1;
    font-size: 13px;
}

.column-item .column-controls {
    display: flex;
    gap: 4px;
}

.column-item .btn {
    padding: 2px 6px;
    font-size: 11px;
}

#table-preview {
    font-size: 12px;
    margin-bottom: 0;
}

#table-preview th,
#table-preview td {
    position: relative;
    min-width: 80px;
    text-align: center;
    vertical-align: middle;
}

#table-preview th {
    background-color: #f8f9fa;
    font-weight: 600;
    cursor: pointer;
}

#table-preview th:hover {
    background-color: #e9ecef;
}

#table-preview th.selected {
    background-color: #e3f2fd;
    color: #1976d2;
}

.table-cell-input {
    border: none;
    background: transparent;
    width: 100%;
    text-align: center;
    font-size: 12px;
    padding: 4px;
}

.table-cell-input:focus {
    outline: 2px solid #2196f3;
    background: white;
}

.column-type-badge {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 10px;
    background: #6c757d;
    color: white;
}

.column-type-badge.text { background: #28a745; }
.column-type-badge.number { background: #007bff; }
.column-type-badge.grade { background: #ffc107; color: #000; }
.column-type-badge.percentage { background: #17a2b8; }

.resize-handle {
    position: absolute;
    right: -3px;
    top: 0;
    bottom: 0;
    width: 6px;
    cursor: col-resize;
    background: transparent;
}

.resize-handle:hover {
    background: #2196f3;
}

.sortable-ghost {
    opacity: 0.5;
}

.sortable-chosen {
    background: #e3f2fd;
}
</style>

<script>
class TableEditor {
    constructor() {
        this.currentTable = null;
        this.selectedColumn = null;
        this.tableZoom = 1;
        this.columns = [
            { id: 'subject', name: 'Subject', type: 'text', width: 120, align: 'left' },
            { id: 'theory', name: 'Theory', type: 'number', width: 80, align: 'center' },
            { id: 'practical', name: 'Practical', type: 'number', width: 80, align: 'center' },
            { id: 'total', name: 'Total', type: 'number', width: 80, align: 'center' },
            { id: 'grade', name: 'Grade', type: 'grade', width: 60, align: 'center' }
        ];
        this.rows = 5;
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.updateColumnList();
        this.generateTable();
    }
    
    setupEventListeners() {
        // Table dimensions
        document.getElementById('table-rows').addEventListener('change', (e) => {
            this.rows = parseInt(e.target.value);
            this.generateTable();
        });
        
        document.getElementById('table-columns').addEventListener('change', (e) => {
            const newCount = parseInt(e.target.value);
            this.adjustColumnCount(newCount);
        });
        
        // Style controls
        document.getElementById('border-width').addEventListener('input', (e) => {
            document.getElementById('border-width-value').textContent = e.target.value + 'px';
            this.updateTableStyle();
        });
        
        document.getElementById('border-color').addEventListener('change', () => {
            this.updateTableStyle();
        });
        
        document.getElementById('header-bg').addEventListener('change', () => {
            this.updateTableStyle();
        });
        
        document.getElementById('cell-padding').addEventListener('input', (e) => {
            document.getElementById('cell-padding-value').textContent = e.target.value + 'px';
            this.updateTableStyle();
        });
        
        // Zoom controls
        document.getElementById('table-zoom-in').addEventListener('click', () => {
            this.tableZoom = Math.min(this.tableZoom * 1.2, 3);
            this.updateTableZoom();
        });
        
        document.getElementById('table-zoom-out').addEventListener('click', () => {
            this.tableZoom = Math.max(this.tableZoom / 1.2, 0.3);
            this.updateTableZoom();
        });
        
        document.getElementById('table-fit').addEventListener('click', () => {
            this.tableZoom = 1;
            this.updateTableZoom();
        });
        
        // Add column
        document.getElementById('add-column').addEventListener('click', () => {
            this.addColumn();
        });
        
        // Apply changes
        document.getElementById('apply-table-changes').addEventListener('click', () => {
            this.applyChanges();
        });
    }
    
    updateColumnList() {
        const columnList = document.getElementById('column-list');
        columnList.innerHTML = '';
        
        this.columns.forEach((column, index) => {
            const columnItem = document.createElement('div');
            columnItem.className = 'column-item';
            columnItem.dataset.columnId = column.id;
            
            columnItem.innerHTML = `
                <div class="column-name">${column.name}</div>
                <span class="column-type-badge ${column.type}">${column.type}</span>
                <div class="column-controls">
                    <button class="btn btn-outline-danger btn-sm" onclick="tableEditor.removeColumn(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            columnItem.addEventListener('click', () => {
                this.selectColumn(column, index);
            });
            
            columnList.appendChild(columnItem);
        });
        
        // Make columns sortable
        new Sortable(columnList, {
            animation: 150,
            onEnd: (evt) => {
                const movedColumn = this.columns.splice(evt.oldIndex, 1)[0];
                this.columns.splice(evt.newIndex, 0, movedColumn);
                this.generateTable();
            }
        });
    }
    
    selectColumn(column, index) {
        this.selectedColumn = { column, index };
        
        // Update UI
        document.querySelectorAll('.column-item').forEach(item => item.classList.remove('active'));
        document.querySelector(`[data-column-id="${column.id}"]`).classList.add('active');
        
        // Update column properties panel
        this.updateColumnProperties();
        
        // Highlight column in table
        document.querySelectorAll('#table-preview th').forEach((th, i) => {
            th.classList.toggle('selected', i === index);
        });
    }
    
    updateColumnProperties() {
        if (!this.selectedColumn) return;
        
        const { column } = this.selectedColumn;
        const panel = document.getElementById('column-properties');
        
        panel.innerHTML = `
            <div class="mb-3">
                <label class="form-label">Column Name</label>
                <input type="text" class="form-control form-control-sm" id="column-name" value="${column.name}">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Column Type</label>
                <select class="form-select form-select-sm" id="column-type">
                    <option value="text" ${column.type === 'text' ? 'selected' : ''}>Text</option>
                    <option value="number" ${column.type === 'number' ? 'selected' : ''}>Number</option>
                    <option value="grade" ${column.type === 'grade' ? 'selected' : ''}>Grade</option>
                    <option value="percentage" ${column.type === 'percentage' ? 'selected' : ''}>Percentage</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Width (px)</label>
                <input type="number" class="form-control form-control-sm" id="column-width" value="${column.width}" min="50" max="300">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Text Alignment</label>
                <select class="form-select form-select-sm" id="column-align">
                    <option value="left" ${column.align === 'left' ? 'selected' : ''}>Left</option>
                    <option value="center" ${column.align === 'center' ? 'selected' : ''}>Center</option>
                    <option value="right" ${column.align === 'right' ? 'selected' : ''}>Right</option>
                </select>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="column-sortable" ${column.sortable ? 'checked' : ''}>
                    <label class="form-check-label" for="column-sortable">Sortable</label>
                </div>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="column-required" ${column.required ? 'checked' : ''}>
                    <label class="form-check-label" for="column-required">Required</label>
                </div>
            </div>
        `;
        
        // Add event listeners for property changes
        document.getElementById('column-name').addEventListener('change', (e) => {
            column.name = e.target.value;
            this.updateColumnList();
            this.generateTable();
        });
        
        document.getElementById('column-type').addEventListener('change', (e) => {
            column.type = e.target.value;
            this.updateColumnList();
            this.generateTable();
        });
        
        document.getElementById('column-width').addEventListener('change', (e) => {
            column.width = parseInt(e.target.value);
            this.generateTable();
        });
        
        document.getElementById('column-align').addEventListener('change', (e) => {
            column.align = e.target.value;
            this.generateTable();
        });
        
        document.getElementById('column-sortable').addEventListener('change', (e) => {
            column.sortable = e.target.checked;
        });
        
        document.getElementById('column-required').addEventListener('change', (e) => {
            column.required = e.target.checked;
        });
    }
    
    generateTable() {
        const table = document.getElementById('table-preview');
        
        // Generate header
        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');
        
        this.columns.forEach((column, index) => {
            const th = document.createElement('th');
            th.textContent = column.name;
            th.style.width = column.width + 'px';
            th.style.textAlign = column.align;
            th.addEventListener('click', () => this.selectColumn(column, index));
            headerRow.appendChild(th);
        });
        
        thead.appendChild(headerRow);
        
        // Generate body
        const tbody = document.createElement('tbody');
        
        for (let i = 0; i < this.rows; i++) {
            const row = document.createElement('tr');
            
            this.columns.forEach((column) => {
                const td = document.createElement('td');
                td.style.textAlign = column.align;
                
                const input = document.createElement('input');
                input.className = 'table-cell-input';
                input.type = column.type === 'number' ? 'number' : 'text';
                input.placeholder = this.getSampleData(column.type, i);
                
                td.appendChild(input);
                row.appendChild(td);
            });
            
            tbody.appendChild(row);
        }
        
        table.innerHTML = '';
        table.appendChild(thead);
        table.appendChild(tbody);
        
        this.updateTableStyle();
    }
    
    getSampleData(type, rowIndex) {
        const samples = {
            text: ['Mathematics', 'Science', 'English', 'History', 'Geography'][rowIndex] || 'Subject',
            number: [85, 78, 92, 76, 88][rowIndex] || 80,
            grade: ['A+', 'A', 'A+', 'B+', 'A'][rowIndex] || 'A',
            percentage: ['85%', '78%', '92%', '76%', '88%'][rowIndex] || '80%'
        };
        
        return samples[type] || '';
    }
    
    updateTableStyle() {
        const table = document.getElementById('table-preview');
        const borderWidth = document.getElementById('border-width').value;
        const borderColor = document.getElementById('border-color').value;
        const headerBg = document.getElementById('header-bg').value;
        const cellPadding = document.getElementById('cell-padding').value;
        
        table.style.borderWidth = borderWidth + 'px';
        table.style.borderColor = borderColor;
        
        table.querySelectorAll('th, td').forEach(cell => {
            cell.style.borderWidth = borderWidth + 'px';
            cell.style.borderColor = borderColor;
            cell.style.padding = cellPadding + 'px';
        });
        
        table.querySelectorAll('th').forEach(th => {
            th.style.backgroundColor = headerBg;
        });
    }
    
    updateTableZoom() {
        const container = document.getElementById('table-preview-container');
        container.style.transform = `scale(${this.tableZoom})`;
    }
    
    addColumn() {
        const newColumn = {
            id: 'column_' + Date.now(),
            name: 'New Column',
            type: 'text',
            width: 100,
            align: 'center',
            sortable: false,
            required: false
        };
        
        this.columns.push(newColumn);
        this.updateColumnList();
        this.generateTable();
        document.getElementById('table-columns').value = this.columns.length;
    }
    
    removeColumn(index) {
        if (this.columns.length > 1) {
            this.columns.splice(index, 1);
            this.updateColumnList();
            this.generateTable();
            document.getElementById('table-columns').value = this.columns.length;
            
            // Clear selection if removed column was selected
            if (this.selectedColumn && this.selectedColumn.index === index) {
                this.selectedColumn = null;
                document.getElementById('column-properties').innerHTML = '<p class="text-muted text-center">Select a column to edit properties</p>';
            }
        }
    }
    
    adjustColumnCount(newCount) {
        while (this.columns.length < newCount) {
            this.addColumn();
        }
        
        while (this.columns.length > newCount && this.columns.length > 1) {
            this.removeColumn(this.columns.length - 1);
        }
    }
    
    applyChanges() {
        // Apply table configuration to the main editor
        if (window.editor && window.editor.selectedElement) {
            const element = window.editor.selectedElement;
            element.columns = this.columns.map(col => col.name);
            element.columnConfig = this.columns;
            element.rows = this.rows;
            element.borderWidth = parseInt(document.getElementById('border-width').value);
            element.borderColor = document.getElementById('border-color').value;
            element.headerBackground = document.getElementById('header-bg').value;
            element.cellPadding = parseInt(document.getElementById('cell-padding').value);
            
            window.editor.redraw();
            window.editor.saveState();
        }
        
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('tableEditorModal')).hide();
    }
    
    openEditor(tableElement) {
        this.currentTable = tableElement;
        
        // Load existing configuration
        if (tableElement.columnConfig) {
            this.columns = [...tableElement.columnConfig];
        }
        if (tableElement.rows) {
            this.rows = tableElement.rows;
            document.getElementById('table-rows').value = this.rows;
        }
        
        this.updateColumnList();
        this.generateTable();
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('tableEditorModal'));
        modal.show();
    }
}

// Initialize table editor
let tableEditor;
document.addEventListener('DOMContentLoaded', function() {
    tableEditor = new TableEditor();
});
</script>

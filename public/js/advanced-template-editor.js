/**
 * Advanced Template Editor
 * Professional drag-and-drop template editor for marksheets
 */

class AdvancedTemplateEditor {
    constructor() {
        this.canvas = null;
        this.selectedElement = null;
        this.elements = [];
        this.history = [];
        this.historyIndex = -1;
        this.zoom = 1;
        this.gridSize = 20;
        this.snapToGrid = true;
        this.showGrid = true;
        this.clipboard = null;
        
        this.init();
    }

    init() {
        this.setupCanvas();
        this.setupEventListeners();
        this.setupDragAndDrop();
        this.setupToolbar();
        this.setupPropertyPanels();
        this.drawGrid();
        this.saveState();
    }

    setupCanvas() {
        this.canvas = document.getElementById('main-canvas');
        this.ctx = this.canvas.getContext('2d');
        
        // Set canvas size
        this.canvas.width = 794; // A4 width in pixels at 96 DPI
        this.canvas.height = 1123; // A4 height in pixels at 96 DPI
        
        // Setup canvas event listeners
        this.canvas.addEventListener('click', this.handleCanvasClick.bind(this));
        this.canvas.addEventListener('mousedown', this.handleMouseDown.bind(this));
        this.canvas.addEventListener('mousemove', this.handleMouseMove.bind(this));
        this.canvas.addEventListener('mouseup', this.handleMouseUp.bind(this));
        this.canvas.addEventListener('contextmenu', this.handleContextMenu.bind(this));
    }

    setupEventListeners() {
        // Toolbar events
        document.getElementById('undo').addEventListener('click', () => this.undo());
        document.getElementById('redo').addEventListener('click', () => this.redo());
        document.getElementById('copy').addEventListener('click', () => this.copy());
        document.getElementById('paste').addEventListener('click', () => this.paste());
        document.getElementById('delete').addEventListener('click', () => this.deleteSelected());
        
        // Zoom events
        document.getElementById('zoom-in').addEventListener('click', () => this.zoomIn());
        document.getElementById('zoom-out').addEventListener('click', () => this.zoomOut());
        document.getElementById('fit-to-screen').addEventListener('click', () => this.fitToScreen());
        
        // Grid events
        document.getElementById('show-grid').addEventListener('change', (e) => {
            this.showGrid = e.target.checked;
            this.redraw();
        });
        
        document.getElementById('snap-to-grid').addEventListener('change', (e) => {
            this.snapToGrid = e.target.checked;
        });
        
        // Template events
        document.getElementById('save-template').addEventListener('click', () => this.saveTemplate());
        document.getElementById('preview-template').addEventListener('click', () => this.previewTemplate());
        
        // Canvas size events
        document.getElementById('canvas-width').addEventListener('change', (e) => {
            this.canvas.width = parseInt(e.target.value);
            this.redraw();
        });
        
        document.getElementById('canvas-height').addEventListener('change', (e) => {
            this.canvas.height = parseInt(e.target.value);
            this.redraw();
        });
        
        // Background color
        document.getElementById('canvas-bg-color').addEventListener('change', (e) => {
            this.canvas.style.backgroundColor = e.target.value;
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', this.handleKeyboard.bind(this));
    }

    setupDragAndDrop() {
        const elementItems = document.querySelectorAll('.element-item');
        
        elementItems.forEach(item => {
            item.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', item.dataset.element);
                item.classList.add('element-dragging');
            });
            
            item.addEventListener('dragend', () => {
                item.classList.remove('element-dragging');
            });
        });
        
        // Canvas drop zone
        this.canvas.addEventListener('dragover', (e) => {
            e.preventDefault();
            this.canvas.classList.add('drop-zone-active');
        });
        
        this.canvas.addEventListener('dragleave', () => {
            this.canvas.classList.remove('drop-zone-active');
        });
        
        this.canvas.addEventListener('drop', (e) => {
            e.preventDefault();
            this.canvas.classList.remove('drop-zone-active');
            
            const elementType = e.dataTransfer.getData('text/plain');
            const rect = this.canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            this.createElement(elementType, x, y);
        });
    }

    setupToolbar() {
        const tools = ['select-tool', 'text-tool', 'image-tool', 'table-tool', 'shape-tool'];
        
        tools.forEach(toolId => {
            document.getElementById(toolId).addEventListener('click', (e) => {
                // Remove active class from all tools
                tools.forEach(id => document.getElementById(id).classList.remove('active'));
                // Add active class to clicked tool
                e.target.closest('button').classList.add('active');
                
                this.currentTool = toolId.replace('-tool', '');
            });
        });
        
        // Set default tool
        document.getElementById('select-tool').classList.add('active');
        this.currentTool = 'select';
    }

    setupPropertyPanels() {
        // Typography controls
        const typographyControls = {
            'font-family': (value) => this.updateSelectedElement('fontFamily', value),
            'font-size': (value) => this.updateSelectedElement('fontSize', parseInt(value)),
            'font-weight': (value) => this.updateSelectedElement('fontWeight', value),
            'text-color': (value) => this.updateSelectedElement('color', value)
        };
        
        Object.keys(typographyControls).forEach(controlId => {
            const element = document.getElementById(controlId);
            if (element) {
                element.addEventListener('change', (e) => {
                    typographyControls[controlId](e.target.value);
                    this.redraw();
                    this.saveState();
                });
            }
        });
        
        // Layout controls
        const layoutControls = {
            'element-x': (value) => this.updateSelectedElement('x', parseInt(value)),
            'element-y': (value) => this.updateSelectedElement('y', parseInt(value)),
            'element-width': (value) => this.updateSelectedElement('width', parseInt(value)),
            'element-height': (value) => this.updateSelectedElement('height', parseInt(value)),
            'element-rotation': (value) => {
                this.updateSelectedElement('rotation', parseInt(value));
                document.getElementById('rotation-value').textContent = value + '°';
            },
            'element-opacity': (value) => {
                this.updateSelectedElement('opacity', parseInt(value) / 100);
                document.getElementById('opacity-value').textContent = value + '%';
            }
        };
        
        Object.keys(layoutControls).forEach(controlId => {
            const element = document.getElementById(controlId);
            if (element) {
                element.addEventListener('input', (e) => {
                    layoutControls[controlId](e.target.value);
                    this.redraw();
                });
                
                element.addEventListener('change', () => {
                    this.saveState();
                });
            }
        });
        
        // Alignment controls
        const alignmentControls = ['align-left', 'align-center', 'align-right', 'align-justify'];
        alignmentControls.forEach(controlId => {
            document.getElementById(controlId).addEventListener('click', () => {
                const alignment = controlId.replace('align-', '');
                this.updateSelectedElement('textAlign', alignment);
                this.redraw();
                this.saveState();
            });
        });
    }

    createElement(type, x, y) {
        const element = {
            id: 'element_' + Date.now(),
            type: type,
            x: this.snapToGrid ? Math.round(x / this.gridSize) * this.gridSize : x,
            y: this.snapToGrid ? Math.round(y / this.gridSize) * this.gridSize : y,
            width: 150,
            height: 30,
            rotation: 0,
            opacity: 1,
            visible: true,
            locked: false,
            ...this.getDefaultProperties(type)
        };
        
        this.elements.push(element);
        this.selectElement(element);
        this.redraw();
        this.updateLayersList();
        this.saveState();
    }

    getDefaultProperties(type) {
        const defaults = {
            'heading': {
                text: 'Heading Text',
                fontFamily: 'Arial',
                fontSize: 24,
                fontWeight: 'bold',
                color: '#000000',
                textAlign: 'center',
                height: 40
            },
            'paragraph': {
                text: 'Paragraph text goes here',
                fontFamily: 'Arial',
                fontSize: 12,
                fontWeight: 'normal',
                color: '#000000',
                textAlign: 'left',
                height: 60
            },
            'label': {
                text: 'Label:',
                fontFamily: 'Arial',
                fontSize: 12,
                fontWeight: 'bold',
                color: '#000000',
                textAlign: 'left',
                width: 80
            },
            'student-name': {
                text: 'Student Name: {{student.name}}',
                fontFamily: 'Arial',
                fontSize: 14,
                fontWeight: 'normal',
                color: '#000000',
                textAlign: 'left'
            },
            'roll-number': {
                text: 'Roll No: {{student.roll_number}}',
                fontFamily: 'Arial',
                fontSize: 12,
                fontWeight: 'normal',
                color: '#000000',
                textAlign: 'left'
            },
            'school-logo': {
                src: '/images/default-logo.png',
                width: 80,
                height: 80
            },
            'student-photo': {
                src: '/images/default-student.png',
                width: 100,
                height: 120
            },
            'signature': {
                src: '/images/signature-placeholder.png',
                width: 120,
                height: 40
            },
            'marks-table': {
                columns: ['Subject', 'Theory', 'Practical', 'Total', 'Grade'],
                rows: 5,
                width: 400,
                height: 200,
                borderColor: '#000000',
                borderWidth: 1
            },
            'qr-code': {
                data: '{{student.id}}',
                width: 60,
                height: 60
            },
            'date': {
                text: 'Date: {{current_date}}',
                fontFamily: 'Arial',
                fontSize: 12,
                color: '#000000'
            },
            'line': {
                width: 200,
                height: 2,
                color: '#000000'
            }
        };
        
        return defaults[type] || {};
    }

    selectElement(element) {
        this.selectedElement = element;
        this.updatePropertyPanels();
        this.showSelectionHandles();
        this.updateLayersList();
    }

    updatePropertyPanels() {
        if (!this.selectedElement) {
            document.getElementById('typography-controls').style.display = 'none';
            document.getElementById('layout-controls').style.display = 'none';
            document.getElementById('properties-panel').innerHTML = '<p class="text-muted text-center">Select an element to edit properties</p>';
            return;
        }
        
        const element = this.selectedElement;
        
        // Show/hide relevant panels
        const isTextElement = ['heading', 'paragraph', 'label', 'student-name', 'roll-number', 'date'].includes(element.type);
        document.getElementById('typography-controls').style.display = isTextElement ? 'block' : 'none';
        document.getElementById('layout-controls').style.display = 'block';
        
        // Update typography controls
        if (isTextElement) {
            document.getElementById('font-family').value = element.fontFamily || 'Arial';
            document.getElementById('font-size').value = element.fontSize || 12;
            document.getElementById('font-weight').value = element.fontWeight || 'normal';
            document.getElementById('text-color').value = element.color || '#000000';
        }
        
        // Update layout controls
        document.getElementById('element-x').value = element.x;
        document.getElementById('element-y').value = element.y;
        document.getElementById('element-width').value = element.width;
        document.getElementById('element-height').value = element.height;
        document.getElementById('element-rotation').value = element.rotation || 0;
        document.getElementById('element-opacity').value = (element.opacity || 1) * 100;
        document.getElementById('rotation-value').textContent = (element.rotation || 0) + '°';
        document.getElementById('opacity-value').textContent = Math.round((element.opacity || 1) * 100) + '%';
        
        // Update properties panel with element-specific controls
        this.updateElementSpecificProperties();
    }

    updateElementSpecificProperties() {
        const element = this.selectedElement;
        const panel = document.getElementById('properties-panel');
        
        let html = `<h6 class="mb-2">${element.type.replace('-', ' ').toUpperCase()}</h6>`;
        
        if (element.text !== undefined) {
            html += `
                <div class="mb-2">
                    <label class="form-label">Text Content</label>
                    <textarea class="form-control form-control-sm" id="element-text" rows="3">${element.text}</textarea>
                </div>
            `;
        }
        
        if (element.src !== undefined) {
            html += `
                <div class="mb-2">
                    <label class="form-label">Image Source</label>
                    <input type="text" class="form-control form-control-sm" id="element-src" value="${element.src}">
                </div>
                <div class="mb-2">
                    <label class="form-label">Upload Image</label>
                    <input type="file" class="form-control form-control-sm" id="element-upload" accept="image/*">
                </div>
            `;
        }
        
        if (element.type === 'marks-table') {
            html += `
                <div class="mb-2">
                    <button class="btn btn-sm btn-primary w-100" onclick="editor.openTableEditor()">
                        <i class="fas fa-edit"></i> Edit Table
                    </button>
                </div>
            `;
        }
        
        panel.innerHTML = html;
        
        // Add event listeners for new controls
        const textElement = document.getElementById('element-text');
        if (textElement) {
            textElement.addEventListener('input', (e) => {
                this.updateSelectedElement('text', e.target.value);
                this.redraw();
            });
        }
        
        const srcElement = document.getElementById('element-src');
        if (srcElement) {
            srcElement.addEventListener('change', (e) => {
                this.updateSelectedElement('src', e.target.value);
                this.redraw();
            });
        }
    }

    updateSelectedElement(property, value) {
        if (this.selectedElement) {
            this.selectedElement[property] = value;
        }
    }

    showSelectionHandles() {
        if (!this.selectedElement) {
            document.getElementById('selection-handles').style.display = 'none';
            return;
        }
        
        const handles = document.getElementById('selection-handles');
        const element = this.selectedElement;
        
        handles.style.display = 'block';
        handles.style.left = element.x + 'px';
        handles.style.top = element.y + 'px';
        handles.style.width = element.width + 'px';
        handles.style.height = element.height + 'px';
    }

    redraw() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        if (this.showGrid) {
            this.drawGrid();
        }
        
        this.elements.forEach(element => {
            if (element.visible) {
                this.drawElement(element);
            }
        });
        
        this.showSelectionHandles();
    }

    drawGrid() {
        this.ctx.strokeStyle = '#e0e0e0';
        this.ctx.lineWidth = 0.5;
        
        for (let x = 0; x <= this.canvas.width; x += this.gridSize) {
            this.ctx.beginPath();
            this.ctx.moveTo(x, 0);
            this.ctx.lineTo(x, this.canvas.height);
            this.ctx.stroke();
        }
        
        for (let y = 0; y <= this.canvas.height; y += this.gridSize) {
            this.ctx.beginPath();
            this.ctx.moveTo(0, y);
            this.ctx.lineTo(this.canvas.width, y);
            this.ctx.stroke();
        }
    }

    drawElement(element) {
        this.ctx.save();
        
        // Apply transformations
        this.ctx.globalAlpha = element.opacity || 1;
        
        if (element.rotation) {
            this.ctx.translate(element.x + element.width / 2, element.y + element.height / 2);
            this.ctx.rotate((element.rotation * Math.PI) / 180);
            this.ctx.translate(-element.width / 2, -element.height / 2);
        } else {
            this.ctx.translate(element.x, element.y);
        }
        
        // Draw based on element type
        switch (element.type) {
            case 'heading':
            case 'paragraph':
            case 'label':
            case 'student-name':
            case 'roll-number':
            case 'date':
                this.drawTextElement(element);
                break;
            case 'school-logo':
            case 'student-photo':
            case 'signature':
                this.drawImageElement(element);
                break;
            case 'marks-table':
                this.drawTableElement(element);
                break;
            case 'line':
                this.drawLineElement(element);
                break;
            case 'qr-code':
                this.drawQRCodeElement(element);
                break;
        }
        
        // Draw selection border
        if (element === this.selectedElement) {
            this.ctx.strokeStyle = '#2196f3';
            this.ctx.lineWidth = 2;
            this.ctx.setLineDash([5, 5]);
            this.ctx.strokeRect(0, 0, element.width, element.height);
            this.ctx.setLineDash([]);
        }
        
        this.ctx.restore();
    }

    drawTextElement(element) {
        this.ctx.font = `${element.fontWeight || 'normal'} ${element.fontSize || 12}px ${element.fontFamily || 'Arial'}`;
        this.ctx.fillStyle = element.color || '#000000';
        this.ctx.textAlign = element.textAlign || 'left';
        this.ctx.textBaseline = 'top';
        
        const lines = this.wrapText(element.text || '', element.width);
        const lineHeight = (element.fontSize || 12) * 1.2;
        
        lines.forEach((line, index) => {
            let x = 0;
            if (element.textAlign === 'center') x = element.width / 2;
            else if (element.textAlign === 'right') x = element.width;
            
            this.ctx.fillText(line, x, index * lineHeight);
        });
    }

    drawImageElement(element) {
        // Draw placeholder rectangle
        this.ctx.fillStyle = '#f0f0f0';
        this.ctx.fillRect(0, 0, element.width, element.height);
        this.ctx.strokeStyle = '#ccc';
        this.ctx.strokeRect(0, 0, element.width, element.height);
        
        // Draw icon or text
        this.ctx.fillStyle = '#666';
        this.ctx.font = '12px Arial';
        this.ctx.textAlign = 'center';
        this.ctx.textBaseline = 'middle';
        this.ctx.fillText(element.type.replace('-', ' '), element.width / 2, element.height / 2);
    }

    drawTableElement(element) {
        const cellWidth = element.width / (element.columns?.length || 5);
        const cellHeight = element.height / ((element.rows || 5) + 1); // +1 for header
        
        // Draw table border
        this.ctx.strokeStyle = element.borderColor || '#000000';
        this.ctx.lineWidth = element.borderWidth || 1;
        this.ctx.strokeRect(0, 0, element.width, element.height);
        
        // Draw grid
        for (let i = 1; i < (element.columns?.length || 5); i++) {
            this.ctx.beginPath();
            this.ctx.moveTo(i * cellWidth, 0);
            this.ctx.lineTo(i * cellWidth, element.height);
            this.ctx.stroke();
        }
        
        for (let i = 1; i <= (element.rows || 5); i++) {
            this.ctx.beginPath();
            this.ctx.moveTo(0, i * cellHeight);
            this.ctx.lineTo(element.width, i * cellHeight);
            this.ctx.stroke();
        }
        
        // Draw headers
        this.ctx.font = 'bold 12px Arial';
        this.ctx.fillStyle = '#000000';
        this.ctx.textAlign = 'center';
        this.ctx.textBaseline = 'middle';
        
        (element.columns || ['Subject', 'Theory', 'Practical', 'Total', 'Grade']).forEach((header, index) => {
            this.ctx.fillText(header, (index + 0.5) * cellWidth, cellHeight / 2);
        });
    }

    drawLineElement(element) {
        this.ctx.strokeStyle = element.color || '#000000';
        this.ctx.lineWidth = element.height;
        this.ctx.beginPath();
        this.ctx.moveTo(0, element.height / 2);
        this.ctx.lineTo(element.width, element.height / 2);
        this.ctx.stroke();
    }

    drawQRCodeElement(element) {
        // Draw placeholder for QR code
        this.ctx.fillStyle = '#000000';
        this.ctx.fillRect(0, 0, element.width, element.height);
        this.ctx.fillStyle = '#ffffff';
        this.ctx.font = '10px Arial';
        this.ctx.textAlign = 'center';
        this.ctx.textBaseline = 'middle';
        this.ctx.fillText('QR', element.width / 2, element.height / 2);
    }

    wrapText(text, maxWidth) {
        const words = text.split(' ');
        const lines = [];
        let currentLine = words[0];
        
        for (let i = 1; i < words.length; i++) {
            const word = words[i];
            const width = this.ctx.measureText(currentLine + ' ' + word).width;
            if (width < maxWidth) {
                currentLine += ' ' + word;
            } else {
                lines.push(currentLine);
                currentLine = word;
            }
        }
        lines.push(currentLine);
        return lines;
    }

    // Canvas event handlers
    handleCanvasClick(e) {
        const rect = this.canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const clickedElement = this.getElementAtPosition(x, y);

        if (clickedElement) {
            this.selectElement(clickedElement);
        } else {
            this.selectedElement = null;
            this.updatePropertyPanels();
            document.getElementById('selection-handles').style.display = 'none';
        }

        this.redraw();
    }

    handleMouseDown(e) {
        if (this.currentTool === 'select' && this.selectedElement) {
            this.isDragging = true;
            const rect = this.canvas.getBoundingClientRect();
            this.dragStartX = e.clientX - rect.left - this.selectedElement.x;
            this.dragStartY = e.clientY - rect.top - this.selectedElement.y;
        }
    }

    handleMouseMove(e) {
        if (this.isDragging && this.selectedElement) {
            const rect = this.canvas.getBoundingClientRect();
            let newX = e.clientX - rect.left - this.dragStartX;
            let newY = e.clientY - rect.top - this.dragStartY;

            if (this.snapToGrid) {
                newX = Math.round(newX / this.gridSize) * this.gridSize;
                newY = Math.round(newY / this.gridSize) * this.gridSize;
            }

            this.selectedElement.x = Math.max(0, Math.min(newX, this.canvas.width - this.selectedElement.width));
            this.selectedElement.y = Math.max(0, Math.min(newY, this.canvas.height - this.selectedElement.height));

            this.updatePropertyPanels();
            this.redraw();
        }
    }

    handleMouseUp(e) {
        if (this.isDragging) {
            this.isDragging = false;
            this.saveState();
        }
    }

    handleContextMenu(e) {
        e.preventDefault();
        const rect = this.canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const clickedElement = this.getElementAtPosition(x, y);
        if (clickedElement) {
            this.selectElement(clickedElement);
            this.showContextMenu(e.clientX, e.clientY);
        }
    }

    handleKeyboard(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

        switch (e.key) {
            case 'Delete':
            case 'Backspace':
                this.deleteSelected();
                break;
            case 'c':
                if (e.ctrlKey) this.copy();
                break;
            case 'v':
                if (e.ctrlKey) this.paste();
                break;
            case 'z':
                if (e.ctrlKey && !e.shiftKey) this.undo();
                else if (e.ctrlKey && e.shiftKey) this.redo();
                break;
            case 'ArrowUp':
                if (this.selectedElement) {
                    this.selectedElement.y = Math.max(0, this.selectedElement.y - (e.shiftKey ? 10 : 1));
                    this.updatePropertyPanels();
                    this.redraw();
                }
                break;
            case 'ArrowDown':
                if (this.selectedElement) {
                    this.selectedElement.y = Math.min(this.canvas.height - this.selectedElement.height, this.selectedElement.y + (e.shiftKey ? 10 : 1));
                    this.updatePropertyPanels();
                    this.redraw();
                }
                break;
            case 'ArrowLeft':
                if (this.selectedElement) {
                    this.selectedElement.x = Math.max(0, this.selectedElement.x - (e.shiftKey ? 10 : 1));
                    this.updatePropertyPanels();
                    this.redraw();
                }
                break;
            case 'ArrowRight':
                if (this.selectedElement) {
                    this.selectedElement.x = Math.min(this.canvas.width - this.selectedElement.width, this.selectedElement.x + (e.shiftKey ? 10 : 1));
                    this.updatePropertyPanels();
                    this.redraw();
                }
                break;
        }
    }

    getElementAtPosition(x, y) {
        // Check elements in reverse order (top to bottom)
        for (let i = this.elements.length - 1; i >= 0; i--) {
            const element = this.elements[i];
            if (x >= element.x && x <= element.x + element.width &&
                y >= element.y && y <= element.y + element.height) {
                return element;
            }
        }
        return null;
    }

    // Utility functions
    undo() {
        if (this.historyIndex > 0) {
            this.historyIndex--;
            this.loadState(this.history[this.historyIndex]);
        }
    }

    redo() {
        if (this.historyIndex < this.history.length - 1) {
            this.historyIndex++;
            this.loadState(this.history[this.historyIndex]);
        }
    }

    copy() {
        if (this.selectedElement) {
            this.clipboard = JSON.parse(JSON.stringify(this.selectedElement));
        }
    }

    paste() {
        if (this.clipboard) {
            const newElement = JSON.parse(JSON.stringify(this.clipboard));
            newElement.id = 'element_' + Date.now();
            newElement.x += 20;
            newElement.y += 20;
            this.elements.push(newElement);
            this.selectElement(newElement);
            this.redraw();
            this.updateLayersList();
            this.saveState();
        }
    }

    deleteSelected() {
        if (this.selectedElement) {
            const index = this.elements.indexOf(this.selectedElement);
            if (index > -1) {
                this.elements.splice(index, 1);
                this.selectedElement = null;
                this.updatePropertyPanels();
                this.redraw();
                this.updateLayersList();
                this.saveState();
            }
        }
    }

    zoomIn() {
        this.zoom = Math.min(this.zoom * 1.2, 5);
        this.updateZoom();
    }

    zoomOut() {
        this.zoom = Math.max(this.zoom / 1.2, 0.1);
        this.updateZoom();
    }

    fitToScreen() {
        const container = document.querySelector('.canvas-wrapper');
        const containerWidth = container.clientWidth - 100;
        const containerHeight = container.clientHeight - 100;

        const scaleX = containerWidth / this.canvas.width;
        const scaleY = containerHeight / this.canvas.height;

        this.zoom = Math.min(scaleX, scaleY);
        this.updateZoom();
    }

    updateZoom() {
        this.canvas.style.transform = `scale(${this.zoom})`;
        this.canvas.style.transformOrigin = 'top left';
        document.getElementById('zoom-level').textContent = Math.round(this.zoom * 100) + '%';
    }

    saveState() {
        const state = {
            elements: JSON.parse(JSON.stringify(this.elements)),
            canvasWidth: this.canvas.width,
            canvasHeight: this.canvas.height
        };

        // Remove future history if we're not at the end
        this.history = this.history.slice(0, this.historyIndex + 1);
        this.history.push(state);
        this.historyIndex++;

        // Limit history size
        if (this.history.length > 50) {
            this.history.shift();
            this.historyIndex--;
        }
    }

    loadState(state) {
        this.elements = JSON.parse(JSON.stringify(state.elements));
        this.canvas.width = state.canvasWidth;
        this.canvas.height = state.canvasHeight;
        this.selectedElement = null;
        this.updatePropertyPanels();
        this.redraw();
        this.updateLayersList();
    }

    updateLayersList() {
        const layersList = document.getElementById('layers-list');
        layersList.innerHTML = '';

        this.elements.forEach((element, index) => {
            const layerItem = document.createElement('div');
            layerItem.className = 'layer-item';
            if (element === this.selectedElement) {
                layerItem.classList.add('active');
            }

            layerItem.innerHTML = `
                <div class="d-flex align-items-center w-100">
                    <i class="fas fa-${this.getElementIcon(element.type)} me-2"></i>
                    <span class="flex-grow-1">${element.type.replace('-', ' ')}</span>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary btn-sm" onclick="editor.toggleElementVisibility(${index})" title="Toggle Visibility">
                            <i class="fas fa-${element.visible ? 'eye' : 'eye-slash'}"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="editor.toggleElementLock(${index})" title="Toggle Lock">
                            <i class="fas fa-${element.locked ? 'lock' : 'unlock'}"></i>
                        </button>
                    </div>
                </div>
            `;

            layerItem.addEventListener('click', () => {
                this.selectElement(element);
                this.redraw();
            });

            layersList.appendChild(layerItem);
        });
    }

    getElementIcon(type) {
        const icons = {
            'heading': 'heading',
            'paragraph': 'paragraph',
            'label': 'tag',
            'student-name': 'user',
            'roll-number': 'id-card',
            'school-logo': 'school',
            'student-photo': 'user-circle',
            'signature': 'signature',
            'marks-table': 'table',
            'qr-code': 'qrcode',
            'date': 'calendar',
            'line': 'minus'
        };
        return icons[type] || 'square';
    }

    toggleElementVisibility(index) {
        this.elements[index].visible = !this.elements[index].visible;
        this.redraw();
        this.updateLayersList();
        this.saveState();
    }

    toggleElementLock(index) {
        this.elements[index].locked = !this.elements[index].locked;
        this.updateLayersList();
    }

    showContextMenu(x, y) {
        // Remove existing context menu
        const existingMenu = document.querySelector('.context-menu');
        if (existingMenu) {
            existingMenu.remove();
        }

        const menu = document.createElement('div');
        menu.className = 'context-menu';
        menu.style.left = x + 'px';
        menu.style.top = y + 'px';

        menu.innerHTML = `
            <div class="context-menu-item" onclick="editor.copy()">
                <i class="fas fa-copy me-2"></i>Copy
            </div>
            <div class="context-menu-item" onclick="editor.paste()">
                <i class="fas fa-paste me-2"></i>Paste
            </div>
            <div class="context-menu-divider"></div>
            <div class="context-menu-item" onclick="editor.deleteSelected()">
                <i class="fas fa-trash me-2"></i>Delete
            </div>
            <div class="context-menu-item" onclick="editor.duplicateSelected()">
                <i class="fas fa-clone me-2"></i>Duplicate
            </div>
            <div class="context-menu-divider"></div>
            <div class="context-menu-item" onclick="editor.bringToFront()">
                <i class="fas fa-arrow-up me-2"></i>Bring to Front
            </div>
            <div class="context-menu-item" onclick="editor.sendToBack()">
                <i class="fas fa-arrow-down me-2"></i>Send to Back
            </div>
        `;

        document.body.appendChild(menu);

        // Remove menu when clicking elsewhere
        setTimeout(() => {
            document.addEventListener('click', function removeMenu() {
                menu.remove();
                document.removeEventListener('click', removeMenu);
            });
        }, 100);
    }

    duplicateSelected() {
        if (this.selectedElement) {
            this.copy();
            this.paste();
        }
    }

    bringToFront() {
        if (this.selectedElement) {
            const index = this.elements.indexOf(this.selectedElement);
            if (index > -1) {
                this.elements.splice(index, 1);
                this.elements.push(this.selectedElement);
                this.redraw();
                this.updateLayersList();
                this.saveState();
            }
        }
    }

    sendToBack() {
        if (this.selectedElement) {
            const index = this.elements.indexOf(this.selectedElement);
            if (index > -1) {
                this.elements.splice(index, 1);
                this.elements.unshift(this.selectedElement);
                this.redraw();
                this.updateLayersList();
                this.saveState();
            }
        }
    }

    saveTemplate() {
        const templateData = {
            name: document.getElementById('template-name').value,
            width: this.canvas.width,
            height: this.canvas.height,
            elements: this.elements,
            settings: {
                showGrid: this.showGrid,
                snapToGrid: this.snapToGrid,
                gridSize: this.gridSize
            }
        };

        // Send to server
        fetch('/admin/marksheets/customize', {
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

    previewTemplate() {
        const previewWindow = window.open('', '_blank', 'width=900,height=700');
        const canvas = document.createElement('canvas');
        canvas.width = this.canvas.width;
        canvas.height = this.canvas.height;
        const ctx = canvas.getContext('2d');

        // Draw all elements without selection indicators
        this.elements.forEach(element => {
            if (element.visible) {
                const tempSelected = this.selectedElement;
                this.selectedElement = null;
                this.ctx = ctx;
                this.drawElement(element);
                this.selectedElement = tempSelected;
                this.ctx = this.canvas.getContext('2d');
            }
        });

        previewWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Template Preview</title>
                <style>
                    body { margin: 0; padding: 20px; background: #f0f0f0; }
                    canvas { background: white; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
                </style>
            </head>
            <body>
                <h2>Template Preview</h2>
                ${canvas.outerHTML}
            </body>
            </html>
        `);

        previewWindow.document.close();
    }

    openTableEditor() {
        if (this.selectedElement && this.selectedElement.type === 'marks-table') {
            if (window.tableEditor) {
                window.tableEditor.openEditor(this.selectedElement);
            }
        }
    }
}

// Initialize editor when DOM is loaded
let editor;
document.addEventListener('DOMContentLoaded', function() {
    editor = new AdvancedTemplateEditor();
});

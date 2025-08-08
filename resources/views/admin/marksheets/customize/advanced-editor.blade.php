@extends('layouts.admin')

@section('title', 'Advanced Template Editor')
@section('page-title', 'Advanced Template Editor')

@section('content')
<div class="container-fluid p-0" style="height: 100vh; overflow: hidden;">
    <!-- Include Reports Sub-Navigation -->
    @include('admin.reports.partials.sub-navbar')

    <!-- Main Editor Interface -->
    <div class="editor-container d-flex" style="height: calc(100vh - 120px);">
        
        <!-- Left Sidebar - Element Library & Layers -->
        <div class="left-sidebar bg-light border-end" style="width: 280px; overflow-y: auto;">
            <!-- Toolbar -->
            <div class="toolbar bg-white border-bottom p-2">
                <div class="btn-group w-100" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="select-tool" title="Select">
                        <i class="fas fa-mouse-pointer"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="text-tool" title="Text">
                        <i class="fas fa-font"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="image-tool" title="Image">
                        <i class="fas fa-image"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="table-tool" title="Table">
                        <i class="fas fa-table"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="shape-tool" title="Shape">
                        <i class="fas fa-square"></i>
                    </button>
                </div>
            </div>

            <!-- Element Library -->
            <div class="element-library">
                <div class="accordion" id="elementAccordion">
                    <!-- Text Elements -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#textElements">
                                <i class="fas fa-font me-2"></i> Text Elements
                            </button>
                        </h2>
                        <div id="textElements" class="accordion-collapse collapse show">
                            <div class="accordion-body p-2">
                                <div class="element-item" draggable="true" data-element="heading">
                                    <i class="fas fa-heading"></i> Heading
                                </div>
                                <div class="element-item" draggable="true" data-element="paragraph">
                                    <i class="fas fa-paragraph"></i> Paragraph
                                </div>
                                <div class="element-item" draggable="true" data-element="label">
                                    <i class="fas fa-tag"></i> Label
                                </div>
                                <div class="element-item" draggable="true" data-element="student-name">
                                    <i class="fas fa-user"></i> Student Name
                                </div>
                                <div class="element-item" draggable="true" data-element="roll-number">
                                    <i class="fas fa-id-card"></i> Roll Number
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Image Elements -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#imageElements">
                                <i class="fas fa-image me-2"></i> Images
                            </button>
                        </h2>
                        <div id="imageElements" class="accordion-collapse collapse">
                            <div class="accordion-body p-2">
                                <div class="element-item" draggable="true" data-element="school-logo">
                                    <i class="fas fa-school"></i> School Logo
                                </div>
                                <div class="element-item" draggable="true" data-element="student-photo">
                                    <i class="fas fa-user-circle"></i> Student Photo
                                </div>
                                <div class="element-item" draggable="true" data-element="signature">
                                    <i class="fas fa-signature"></i> Signature
                                </div>
                                <div class="element-item" draggable="true" data-element="seal">
                                    <i class="fas fa-certificate"></i> School Seal
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Elements -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tableElements">
                                <i class="fas fa-table me-2"></i> Tables
                            </button>
                        </h2>
                        <div id="tableElements" class="accordion-collapse collapse">
                            <div class="accordion-body p-2">
                                <div class="element-item" draggable="true" data-element="marks-table">
                                    <i class="fas fa-table"></i> Marks Table
                                </div>
                                <div class="element-item" draggable="true" data-element="summary-table">
                                    <i class="fas fa-chart-bar"></i> Summary Table
                                </div>
                                <div class="element-item" draggable="true" data-element="grade-table">
                                    <i class="fas fa-graduation-cap"></i> Grade Table
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Special Elements -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#specialElements">
                                <i class="fas fa-star me-2"></i> Special
                            </button>
                        </h2>
                        <div id="specialElements" class="accordion-collapse collapse">
                            <div class="accordion-body p-2">
                                <div class="element-item" draggable="true" data-element="qr-code">
                                    <i class="fas fa-qrcode"></i> QR Code
                                </div>
                                <div class="element-item" draggable="true" data-element="barcode">
                                    <i class="fas fa-barcode"></i> Barcode
                                </div>
                                <div class="element-item" draggable="true" data-element="date">
                                    <i class="fas fa-calendar"></i> Date
                                </div>
                                <div class="element-item" draggable="true" data-element="line">
                                    <i class="fas fa-minus"></i> Line
                                </div>
                                <div class="element-item" draggable="true" data-element="border">
                                    <i class="fas fa-border-style"></i> Border
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Layer Manager -->
            <div class="layer-manager mt-3">
                <div class="d-flex justify-content-between align-items-center p-2 bg-white border-bottom">
                    <h6 class="mb-0">Layers</h6>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary" id="add-layer" title="Add Layer">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="btn btn-outline-secondary" id="delete-layer" title="Delete Layer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div id="layers-list" class="p-2">
                    <!-- Layers will be populated here -->
                </div>
            </div>
        </div>

        <!-- Main Canvas Area -->
        <div class="canvas-area flex-grow-1 d-flex flex-column">
            <!-- Top Toolbar -->
            <div class="top-toolbar bg-white border-bottom p-2 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="btn-group me-3">
                        <button class="btn btn-sm btn-outline-secondary" id="undo" title="Undo">
                            <i class="fas fa-undo"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" id="redo" title="Redo">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                    
                    <div class="btn-group me-3">
                        <button class="btn btn-sm btn-outline-secondary" id="copy" title="Copy">
                            <i class="fas fa-copy"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" id="paste" title="Paste">
                            <i class="fas fa-paste"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" id="delete" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                    <div class="zoom-controls d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-secondary" id="zoom-out">
                            <i class="fas fa-search-minus"></i>
                        </button>
                        <span class="mx-2" id="zoom-level">100%</span>
                        <button class="btn btn-sm btn-outline-secondary" id="zoom-in">
                            <i class="fas fa-search-plus"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary ms-2" id="fit-to-screen">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex align-items-center">
                    <div class="form-check form-switch me-3">
                        <input class="form-check-input" type="checkbox" id="show-grid" checked>
                        <label class="form-check-label" for="show-grid">Grid</label>
                    </div>
                    <div class="form-check form-switch me-3">
                        <input class="form-check-input" type="checkbox" id="snap-to-grid" checked>
                        <label class="form-check-label" for="snap-to-grid">Snap</label>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-success" id="save-template">
                            <i class="fas fa-save"></i> Save
                        </button>
                        <button class="btn btn-sm btn-primary" id="preview-template">
                            <i class="fas fa-eye"></i> Preview
                        </button>
                    </div>
                </div>
            </div>

            <!-- Canvas Container -->
            <div class="canvas-container flex-grow-1 position-relative overflow-auto bg-secondary" style="background-image: radial-gradient(circle, #ccc 1px, transparent 1px); background-size: 20px 20px;">
                <!-- Rulers -->
                <div class="ruler-horizontal bg-white border-bottom" style="height: 20px; position: sticky; top: 0; z-index: 10;">
                    <canvas id="horizontal-ruler" width="100%" height="20"></canvas>
                </div>
                <div class="ruler-vertical bg-white border-end" style="width: 20px; position: sticky; left: 0; z-index: 10;">
                    <canvas id="vertical-ruler" width="20" height="100%"></canvas>
                </div>

                <!-- Main Canvas -->
                <div class="canvas-wrapper position-relative" style="margin: 50px;">
                    <canvas id="main-canvas" class="border bg-white shadow" width="794" height="1123" style="cursor: default;"></canvas>
                    
                    <!-- Selection Handles -->
                    <div id="selection-handles" class="position-absolute" style="display: none;">
                        <div class="handle handle-nw"></div>
                        <div class="handle handle-ne"></div>
                        <div class="handle handle-sw"></div>
                        <div class="handle handle-se"></div>
                        <div class="handle handle-n"></div>
                        <div class="handle handle-s"></div>
                        <div class="handle handle-e"></div>
                        <div class="handle handle-w"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar - Properties -->
        <div class="right-sidebar bg-light border-start" style="width: 320px; overflow-y: auto;">
            <!-- Template Settings -->
            <div class="template-settings">
                <div class="p-3 bg-white border-bottom">
                    <h6 class="mb-3">Template Settings</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Template Name</label>
                        <input type="text" class="form-control form-control-sm" id="template-name" value="Untitled Template">
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Width</label>
                            <input type="number" class="form-control form-control-sm" id="canvas-width" value="794">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Height</label>
                            <input type="number" class="form-control form-control-sm" id="canvas-height" value="1123">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Orientation</label>
                        <select class="form-select form-select-sm" id="page-orientation">
                            <option value="portrait">Portrait</option>
                            <option value="landscape">Landscape</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Background Color</label>
                        <input type="color" class="form-control form-control-color form-control-sm" id="canvas-bg-color" value="#ffffff">
                    </div>
                </div>
            </div>

            <!-- Element Properties -->
            <div class="element-properties">
                <div class="p-3 bg-white border-bottom">
                    <h6 class="mb-3">Element Properties</h6>
                    <div id="properties-panel">
                        <p class="text-muted text-center">Select an element to edit properties</p>
                    </div>
                </div>
            </div>

            <!-- Typography Panel -->
            <div class="typography-panel">
                <div class="p-3 bg-white border-bottom">
                    <h6 class="mb-3">Typography</h6>
                    <div id="typography-controls" style="display: none;">
                        <div class="mb-2">
                            <label class="form-label">Font Family</label>
                            <select class="form-select form-select-sm" id="font-family">
                                <option value="Arial">Arial</option>
                                <option value="Times New Roman">Times New Roman</option>
                                <option value="Helvetica">Helvetica</option>
                                <option value="Georgia">Georgia</option>
                                <option value="Verdana">Verdana</option>
                            </select>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-6">
                                <label class="form-label">Size</label>
                                <input type="number" class="form-control form-control-sm" id="font-size" value="12" min="8" max="72">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Weight</label>
                                <select class="form-select form-select-sm" id="font-weight">
                                    <option value="normal">Normal</option>
                                    <option value="bold">Bold</option>
                                    <option value="lighter">Light</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color form-control-sm" id="text-color" value="#000000">
                        </div>

                        <div class="btn-group w-100 mb-2">
                            <button class="btn btn-sm btn-outline-secondary" id="align-left">
                                <i class="fas fa-align-left"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" id="align-center">
                                <i class="fas fa-align-center"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" id="align-right">
                                <i class="fas fa-align-right"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" id="align-justify">
                                <i class="fas fa-align-justify"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Layout Panel -->
            <div class="layout-panel">
                <div class="p-3 bg-white">
                    <h6 class="mb-3">Layout & Position</h6>
                    <div id="layout-controls" style="display: none;">
                        <div class="row mb-2">
                            <div class="col-6">
                                <label class="form-label">X Position</label>
                                <input type="number" class="form-control form-control-sm" id="element-x" value="0">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Y Position</label>
                                <input type="number" class="form-control form-control-sm" id="element-y" value="0">
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-6">
                                <label class="form-label">Width</label>
                                <input type="number" class="form-control form-control-sm" id="element-width" value="100">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Height</label>
                                <input type="number" class="form-control form-control-sm" id="element-height" value="30">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Rotation</label>
                            <input type="range" class="form-range" id="element-rotation" min="0" max="360" value="0">
                            <div class="text-center small" id="rotation-value">0°</div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Opacity</label>
                            <input type="range" class="form-range" id="element-opacity" min="0" max="100" value="100">
                            <div class="text-center small" id="opacity-value">100%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Table Editor Modal -->
@include('admin.marksheets.customize.partials.table-editor-modal')

<style>
/* Editor Layout */
.editor-container {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.left-sidebar, .right-sidebar {
    background: #f8f9fa;
    border-color: #dee2e6;
}

/* Element Library */
.element-item {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    margin-bottom: 4px;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    cursor: move;
    transition: all 0.2s ease;
    font-size: 13px;
}

.element-item:hover {
    background: #e3f2fd;
    border-color: #2196f3;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.element-item i {
    margin-right: 8px;
    width: 16px;
    color: #666;
}

/* Canvas Styling */
.canvas-container {
    background: #e5e5e5;
}

#main-canvas {
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    border-radius: 4px;
}

/* Selection Handles */
.handle {
    position: absolute;
    width: 8px;
    height: 8px;
    background: #2196f3;
    border: 1px solid white;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

.handle-nw { top: -4px; left: -4px; cursor: nw-resize; }
.handle-ne { top: -4px; right: -4px; cursor: ne-resize; }
.handle-sw { bottom: -4px; left: -4px; cursor: sw-resize; }
.handle-se { bottom: -4px; right: -4px; cursor: se-resize; }
.handle-n { top: -4px; left: 50%; transform: translateX(-50%); cursor: n-resize; }
.handle-s { bottom: -4px; left: 50%; transform: translateX(-50%); cursor: s-resize; }
.handle-e { top: 50%; right: -4px; transform: translateY(-50%); cursor: e-resize; }
.handle-w { top: 50%; left: -4px; transform: translateY(-50%); cursor: w-resize; }

/* Toolbar Styling */
.toolbar {
    border-bottom: 1px solid #dee2e6;
}

.top-toolbar {
    background: linear-gradient(to bottom, #ffffff, #f8f9fa);
    border-bottom: 1px solid #dee2e6;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

/* Layer Manager */
.layer-manager {
    background: white;
    border-top: 1px solid #dee2e6;
}

.layer-item {
    display: flex;
    align-items: center;
    padding: 6px 8px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background 0.2s ease;
}

.layer-item:hover {
    background: #f8f9fa;
}

.layer-item.active {
    background: #e3f2fd;
    border-left: 3px solid #2196f3;
}

/* Property Panels */
.element-properties, .typography-panel, .layout-panel {
    border-bottom: 1px solid #dee2e6;
}

.form-label {
    font-size: 12px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 4px;
}

.form-control-sm, .form-select-sm {
    font-size: 12px;
    padding: 4px 8px;
}

/* Rulers */
.ruler-horizontal, .ruler-vertical {
    background: #f8f9fa;
    border-color: #dee2e6;
    font-size: 10px;
    color: #666;
}

/* Zoom Controls */
.zoom-controls {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 4px 8px;
}

#zoom-level {
    font-size: 12px;
    font-weight: 600;
    min-width: 40px;
    text-align: center;
}

/* Grid Background */
.canvas-container.show-grid {
    background-image:
        linear-gradient(rgba(0,0,0,0.1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,0,0,0.1) 1px, transparent 1px);
    background-size: 20px 20px;
}

/* Accordion Customization */
.accordion-button {
    font-size: 13px;
    font-weight: 600;
    padding: 8px 12px;
}

.accordion-button:not(.collapsed) {
    background: #e3f2fd;
    color: #1976d2;
}

.accordion-body {
    padding: 8px;
}

/* Button Groups */
.btn-group .btn {
    border-color: #dee2e6;
}

.btn-group .btn:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.btn-group .btn.active {
    background: #2196f3;
    border-color: #2196f3;
    color: white;
}

/* Form Controls */
.form-range {
    height: 4px;
}

.form-range::-webkit-slider-thumb {
    background: #2196f3;
    border: none;
    width: 16px;
    height: 16px;
}

.form-check-input:checked {
    background-color: #2196f3;
    border-color: #2196f3;
}

/* Scrollbars */
.left-sidebar::-webkit-scrollbar,
.right-sidebar::-webkit-scrollbar {
    width: 6px;
}

.left-sidebar::-webkit-scrollbar-track,
.right-sidebar::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.left-sidebar::-webkit-scrollbar-thumb,
.right-sidebar::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.left-sidebar::-webkit-scrollbar-thumb:hover,
.right-sidebar::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .left-sidebar, .right-sidebar {
        width: 250px;
    }
}

@media (max-width: 992px) {
    .left-sidebar {
        width: 200px;
    }
    .right-sidebar {
        width: 280px;
    }
}

/* Animation Classes */
.element-dragging {
    opacity: 0.7;
    transform: rotate(5deg);
    z-index: 1000;
}

.drop-zone-active {
    background: rgba(33, 150, 243, 0.1);
    border: 2px dashed #2196f3;
}

/* Context Menu */
.context-menu {
    position: absolute;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    padding: 4px 0;
    z-index: 1000;
    min-width: 150px;
}

.context-menu-item {
    padding: 6px 12px;
    cursor: pointer;
    font-size: 13px;
    transition: background 0.2s ease;
}

.context-menu-item:hover {
    background: #f8f9fa;
}

.context-menu-divider {
    height: 1px;
    background: #dee2e6;
    margin: 4px 0;
}
</style>

<script src="{{ asset('js/advanced-template-editor.js') }}"></script>
@endsection

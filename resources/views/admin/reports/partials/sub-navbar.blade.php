<!-- Reports Sub-Navigation -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <nav class="navbar navbar-expand-lg navbar-light p-0 text-black">
            <div class="container-fluid p-0">
                <span class="navbar-brand mb-0 h1 text-primary">
                    <i class="fas fa-chart-bar me-2"></i>Reports & Analytics
                </span>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#reportsNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="reportsNavbar">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active fw-bold' : '' }}" 
                               href="{{ route('admin.reports.index') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>Overview
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.academic') ? 'active fw-bold' : '' }}" 
                               href="{{ route('admin.reports.academic') }}">
                                <i class="fas fa-graduation-cap me-1"></i>Academic Performance
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.student-progress') ? 'active fw-bold' : '' }}" 
                               href="{{ route('admin.reports.student-progress') }}">
                                <i class="fas fa-user-graduate me-1"></i>Student Progress
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.class-performance') ? 'active fw-bold' : '' }}" 
                               href="{{ route('admin.reports.class-performance') }}">
                                <i class="fas fa-chalkboard me-1"></i>Class Performance
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.custom') ? 'active fw-bold' : '' }}"
                               href="{{ route('admin.reports.custom') }}">
                                <i class="fas fa-cogs me-1"></i>Custom Reports
                            </a>
                        </li>

                        <!-- Academic Settings Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.academic-settings.*') || request()->routeIs('admin.grading-scales.*') || request()->routeIs('admin.institute-settings.*') ? 'active fw-bold' : '' }}"
                               href="#" id="academicSettingsDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-university me-1"></i>Academic Settings
                            </a>
                            <ul class="dropdown-menu">
                                <li><h6 class="dropdown-header">Institution Settings</h6></li>
                                <li><a class="dropdown-item" href="{{ route('admin.institute-settings.index') }}">
                                    <i class="fas fa-school me-2"></i>School Information
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.institute-settings.academic') }}">
                                    <i class="fas fa-calendar-academic me-2"></i>Academic Year
                                </a></li>
                                <li><hr class="dropdown-divider"></li>

                                <li><h6 class="dropdown-header">Grading System</h6></li>
                                <li><a class="dropdown-item" href="{{ route('admin.grading-scales.index') }}">
                                    <i class="fas fa-chart-line me-2"></i>Grading Scales
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.grading-scales.create') }}">
                                    <i class="fas fa-plus me-2"></i>Create New Scale
                                </a></li>
                                <li><hr class="dropdown-divider"></li>

                                <li><h6 class="dropdown-header">Academic Structure</h6></li>
                                <li><a class="dropdown-item" href="{{ route('admin.academic-settings.levels') }}">
                                    <i class="fas fa-layer-group me-2"></i>Academic Levels
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.academic-settings.programs') }}">
                                    <i class="fas fa-graduation-cap me-2"></i>Programs
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.academic-settings.subjects') }}">
                                    <i class="fas fa-book me-2"></i>Subjects
                                </a></li>
                            </ul>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.marksheets.*') ? 'active fw-bold' : '' }}"
                               href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-file-alt me-1"></i>Marksheets
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.marksheets.index') }}">
                                    <i class="fas fa-list me-2"></i>Generate Marksheets
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.marksheets.customize.index') }}">
                                    <i class="fas fa-palette me-2"></i>Customize Templates
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.marksheets.customize.drag-drop-builder') }}">
                                    <i class="fas fa-mouse-pointer me-2"></i>Drag & Drop Builder
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.marksheets.customize.advanced-editor') }}">
                                    <i class="fas fa-magic me-2"></i>Advanced Editor
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('admin.marksheets.customize.create') }}">
                                    <i class="fas fa-plus me-2"></i>Create New Template
                                </a></li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.grading-scales.*') ? 'active fw-bold' : '' }}"
                               href="{{ route('admin.grading-scales.index') }}">
                                <i class="fas fa-chart-line me-1"></i>Grading Scales
                            </a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.academic-settings.*') ? 'active fw-bold' : '' }}"
                               href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog me-1"></i>Academic Settings
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.academic-settings.school-info') }}">
                                    <i class="fas fa-school me-2"></i>School Information
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.academic-settings.academic-year') }}">
                                    <i class="fas fa-calendar-alt me-2"></i>Academic Year
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.academic-settings.grading') }}">
                                    <i class="fas fa-chart-line me-2"></i>Grading System
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('admin.academic-settings.backup') }}">
                                    <i class="fas fa-database me-2"></i>Backup & Restore
                                </a></li>
                            </ul>
                        </li>
                    </ul>
                    
                    <div class="navbar-nav">
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle btn btn-outline-success btn-sm" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-download me-1"></i>Export
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" onclick="exportReport('pdf')">
                                    <i class="fas fa-file-pdf me-2"></i>Export as PDF
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportReport('excel')">
                                    <i class="fas fa-file-excel me-2"></i>Export as Excel
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportReport('csv')">
                                    <i class="fas fa-file-csv me-2"></i>Export as CSV
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" onclick="printReport()">
                                    <i class="fas fa-print me-2"></i>Print Report
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</div>

<style>
.nav-link.active {
    color: #0d6efd !important;
    background-color: rgba(13, 110, 253, 0.1);
    border-radius: 0.375rem;
}

.dropdown-item.active {
    background-color: #0d6efd;
    color: white;
}

.navbar-nav .nav-link {
    padding: 0.5rem 0.75rem;
    margin: 0 0.25rem;
    border-radius: 0.375rem;
    transition: all 0.2s ease-in-out;
    color: #212529 !important;
}

.navbar-nav .nav-link:hover {
    background-color: rgba(13, 110, 253, 0.05);
}
</style>

<script>
function exportReport(format) {
    // This would implement export functionality
    alert(`Export as ${format.toUpperCase()} functionality would be implemented here`);
}

function printReport() {
    // This would implement print functionality
    window.print();
}
</script>

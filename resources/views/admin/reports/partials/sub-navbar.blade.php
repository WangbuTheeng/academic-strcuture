<!-- Reports Sub-Navigation -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-2">
        <nav class="navbar navbar-expand-xl navbar-light p-0">
            <div class="container-fluid p-0">
                <div class="d-flex align-items-center">
                    <span class="navbar-brand mb-0 h6 h-md-5 text-primary me-3">
                        <i class="fas fa-chart-bar me-1 me-md-2"></i>
                        <span class="d-none d-sm-inline">Reports & Analytics</span>
                        <span class="d-inline d-sm-none">Reports</span>
                    </span>
                </div>

                <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#reportsNavbar" aria-controls="reportsNavbar" aria-expanded="false">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="reportsNavbar">
                    <ul class="navbar-nav me-auto flex-wrap">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active fw-bold' : '' }}"
                               href="{{ route('admin.reports.index') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>
                                <span class="d-none d-lg-inline">Overview</span>
                                <span class="d-inline d-lg-none">Home</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.academic') ? 'active fw-bold' : '' }}"
                               href="{{ route('admin.reports.academic') }}">
                                <i class="fas fa-graduation-cap me-1"></i>
                                <span class="d-none d-lg-inline">Academic Performance</span>
                                <span class="d-inline d-lg-none">Academic</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.student-progress') ? 'active fw-bold' : '' }}"
                               href="{{ route('admin.reports.student-progress') }}">
                                <i class="fas fa-user-graduate me-1"></i>
                                <span class="d-none d-lg-inline">Student Progress</span>
                                <span class="d-inline d-lg-none">Students</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.class-performance') ? 'active fw-bold' : '' }}"
                               href="{{ route('admin.reports.class-performance') }}">
                                <i class="fas fa-chalkboard me-1"></i>
                                <span class="d-none d-lg-inline">Class Performance</span>
                                <span class="d-inline d-lg-none">Classes</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.custom') ? 'active fw-bold' : '' }}"
                               href="{{ route('admin.reports.custom') }}">
                                <i class="fas fa-cogs me-1"></i>
                                <span class="d-none d-lg-inline">Custom Reports</span>
                                <span class="d-inline d-lg-none">Custom</span>
                            </a>
                        </li>

                        <!-- Academic Settings Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.academic-settings.*') || request()->routeIs('admin.grading-scales.*') || request()->routeIs('admin.institute-settings.*') ? 'active fw-bold' : '' }}"
                               href="#" id="academicSettingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-university me-1"></i>
                                <span class="d-none d-xl-inline">Academic Settings</span>
                                <span class="d-inline d-xl-none">Settings</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start">
                                <li><h6 class="dropdown-header">Institution Settings</h6></li>
                                <li><a class="dropdown-item" href="{{ route('admin.institute-settings.index') }}">
                                    <i class="fas fa-school me-2"></i>School Information
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.institute-settings.academic') }}">
                                    <i class="fas fa-calendar-alt me-2"></i>Academic Year
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
                            </ul>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.marksheets.*') ? 'active fw-bold' : '' }}"
                               href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-file-alt me-1"></i>
                                <span class="d-none d-xl-inline">Marksheets</span>
                                <span class="d-inline d-xl-none">Marks</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start">
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

                        <li class="nav-item d-none d-xxl-block">
                            <a class="nav-link {{ request()->routeIs('admin.grading-scales.*') ? 'active fw-bold' : '' }}"
                               href="{{ route('admin.grading-scales.index') }}">
                                <i class="fas fa-chart-line me-1"></i>Grading Scales
                            </a>
                        </li>
                    </ul>

                    <div class="navbar-nav ms-auto">
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle btn btn-outline-success btn-sm px-2 px-md-3" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-download me-1"></i>
                                <span class="d-none d-md-inline">Export</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" onclick="exportReport('pdf')">
                                    <i class="fas fa-file-pdf me-2 text-danger"></i>Export as PDF
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportReport('excel')">
                                    <i class="fas fa-file-excel me-2 text-success"></i>Export as Excel
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportReport('csv')">
                                    <i class="fas fa-file-csv me-2 text-info"></i>Export as CSV
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" onclick="printReport()">
                                    <i class="fas fa-print me-2 text-secondary"></i>Print Report
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
.card.shadow-sm {
    position: relative;
    z-index: 20;
}
/* Responsive Navigation Styles */
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
    padding: 0.4rem 0.6rem;
    margin: 0 0.1rem;
    border-radius: 0.375rem;
    transition: all 0.2s ease-in-out;
    color: #212529 !important;
    white-space: nowrap;
}

.navbar-nav .nav-link:hover {
    background-color: rgba(13, 110, 253, 0.05);
    color: #0d6efd !important;
}

/* Mobile optimizations */
@media (max-width: 575.98px) {
    .navbar-nav .nav-link {
        padding: 0.5rem 0.75rem;
        margin: 0.1rem 0;
        font-size: 0.9rem;
    }

    .navbar-brand {
        font-size: 1rem !important;
    }

    .dropdown-menu {
        font-size: 0.9rem;
    }
}

/* Tablet optimizations */
@media (min-width: 576px) and (max-width: 991.98px) {
    .navbar-nav .nav-link {
        padding: 0.4rem 0.5rem;
        font-size: 0.9rem;
    }
}

/* Large screen optimizations */
@media (min-width: 1200px) {
    .navbar-nav .nav-link {
        padding: 0.5rem 0.75rem;
        margin: 0 0.2rem;
    }
}

/* Extra large screen optimizations */
@media (min-width: 1400px) {
    .navbar-nav .nav-link {
        padding: 0.5rem 1rem;
        margin: 0 0.25rem;
    }
}

/* Dropdown menu responsive positioning */
.dropdown-menu-end {
    --bs-position: end;
}

@media (max-width: 991.98px) {
    .dropdown-menu-lg-start {
        --bs-position: start;
    }
}

/* Navbar toggler improvements */
.navbar-toggler {
    border: none;
    padding: 0.25rem 0.5rem;
}

.navbar-toggler:focus {
    box-shadow: none;
}

/* Flex wrap for better mobile layout */
.navbar-nav.flex-wrap {
    flex-wrap: wrap;
}

@media (max-width: 1199.98px) {
    .navbar-nav.flex-wrap .nav-item {
        flex: 0 0 auto;
    }
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

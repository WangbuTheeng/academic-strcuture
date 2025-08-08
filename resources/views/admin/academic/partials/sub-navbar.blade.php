<!-- Academic Structure Sub-Navigation -->
<div class="card shadow-sm mb-4 sub-navbar-card" style="position: sticky; top: 0; z-index: 1045;">
    <div class="card-body py-2">
        <nav class="navbar navbar-expand-lg navbar-light p-0">
            <div class="container-fluid p-0">
                <span class="navbar-brand mb-0 h1 text-primary fw-bold d-flex align-items-center">
                    <i class="fas fa-sitemap me-2"></i>
                    <span class="d-none d-sm-inline">Academic Structure</span>
                    <span class="d-inline d-sm-none">Academic</span>
                </span>

                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#academicNavbar" aria-controls="academicNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="academicNavbar">
                    <ul class="navbar-nav me-auto flex-wrap">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.academic.index') ? 'active fw-bold' : '' }}"
                               href="{{ route('admin.academic.index') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>
                                <span class="d-none d-md-inline">Overview</span>
                                <span class="d-inline d-md-none">Home</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.faculties.*') ? 'active fw-bold' : '' }}"
                               href="{{ route('admin.faculties.index') }}">
                                <i class="fas fa-university me-1"></i>
                                <span>Faculties</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active fw-bold' : '' }}"
                               href="{{ route('admin.departments.index') }}">
                                <i class="fas fa-building me-1"></i>
                                <span class="d-none d-lg-inline">Departments</span>
                                <span class="d-inline d-lg-none">Depts</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.programs.*') ? 'active fw-bold' : '' }}"
                               href="{{ route('admin.programs.index') }}">
                                <i class="fas fa-graduation-cap me-1"></i>
                                <span>Programs</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.classes.*') ? 'active fw-bold' : '' }}"
                               href="{{ route('admin.classes.index') }}">
                                <i class="fas fa-chalkboard me-1"></i>
                                <span>Classes</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.subjects.*') ? 'active fw-bold' : '' }}"
                               href="{{ route('admin.subjects.index') }}">
                                <i class="fas fa-book me-1"></i>
                                <span>Subjects</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.academic-years.*') ? 'active fw-bold' : '' }}"
                               href="{{ route('admin.academic-years.index') }}">
                                <i class="fas fa-calendar-alt me-1"></i>
                                <span class="d-none d-lg-inline">Academic Years</span>
                                <span class="d-inline d-lg-none">Years</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.academic.reports') ? 'active fw-bold' : '' }}" 
                               href="{{ route('admin.academic.reports') }}">
                                <i class="fas fa-chart-bar me-1"></i>Reports
                            </a>
                        </li>
                    </ul>
                    
                    <div class="navbar-nav ms-auto">
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle btn btn-outline-primary btn-sm d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-plus me-1"></i>
                                <span class="d-none d-sm-inline">Quick Add</span>
                                <span class="d-inline d-sm-none">Add</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header">Academic Structure</h6></li>
                                <li><a class="dropdown-item" href="{{ route('admin.faculties.create') }}">
                                    <i class="fas fa-university me-2"></i>New Faculty
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.departments.create') }}">
                                    <i class="fas fa-building me-2"></i>New Department
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.programs.create') }}">
                                    <i class="fas fa-graduation-cap me-2"></i>New Program
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.classes.create') }}">
                                    <i class="fas fa-chalkboard me-2"></i>New Class
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.subjects.create') }}">
                                    <i class="fas fa-book me-2"></i>New Subject
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header">Academic Years</h6></li>
                                <li><a class="dropdown-item" href="{{ route('admin.academic-years.create') }}">
                                    <i class="fas fa-calendar me-2"></i>New Academic Year
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
/* Base styles for navigation */
.nav-link.active {
    color: #0d6efd !important;
    background-color: rgba(13, 110, 253, 0.1);
    border-radius: 0.375rem;
}

.dropdown-item.active {
    background-color: #0d6efd !important;
    color: white !important;
}

.navbar-nav .nav-link {
    padding: 0.5rem 0.75rem;
    margin: 0 0.25rem;
    border-radius: 0.375rem;
    transition: all 0.2s ease-in-out;
    color: #212529 !important;
    white-space: nowrap;
}

.navbar-nav .nav-link:hover {
    background-color: rgba(13, 110, 253, 0.05);
    color: #0d6efd !important;
}

/* Fix dropdown z-index issues */
.dropdown-menu {
    z-index: 1050 !important;
}

.navbar {
    z-index: 1040 !important;
}

.card {
    z-index: 1 !important;
}

.sub-navbar-card {
    z-index: 1045 !important;
    background-color: #ffffff;
    border: 1px solid #e3e6f0;
}

/* Ensure all text is properly visible */
.dropdown-item {
    color: #212529 !important;
}

.dropdown-item:hover {
    background-color: #0d6efd !important;
    color: #ffffff !important;
}

.dropdown-item.active {
    background-color: #0d6efd !important;
    color: white !important;
}

.dropdown-header {
    color: #6c757d !important;
    font-weight: 600 !important;
}

/* Responsive design for different screen sizes */

/* Extra large screens (1400px and up) */
@media (min-width: 1400px) {
    .navbar-nav .nav-link {
        padding: 0.5rem 1rem;
        margin: 0 0.5rem;
    }
}

/* Large screens (1200px to 1399px) */
@media (max-width: 1399px) and (min-width: 1200px) {
    .navbar-nav .nav-link {
        padding: 0.5rem 0.75rem;
        margin: 0 0.25rem;
        font-size: 0.9rem;
    }
}

/* Medium screens (992px to 1199px) */
@media (max-width: 1199px) and (min-width: 992px) {
    .navbar-nav .nav-link {
        padding: 0.4rem 0.6rem;
        margin: 0 0.2rem;
        font-size: 0.85rem;
    }

    .navbar-brand {
        font-size: 1.1rem !important;
    }
}

/* Small screens (768px to 991px) - Tablet landscape */
@media (max-width: 991px) and (min-width: 768px) {
    .navbar-collapse {
        margin-top: 1rem;
    }

    .navbar-nav {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
    }

    .navbar-nav .nav-item {
        margin: 0.25rem;
    }

    .navbar-nav .nav-link {
        padding: 0.4rem 0.6rem;
        margin: 0;
        font-size: 0.8rem;
        text-align: center;
    }

    .navbar-nav .dropdown-menu {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        min-width: 200px;
    }
}

/* Small screens (576px to 767px) - Tablet portrait */
@media (max-width: 767px) and (min-width: 576px) {
    .navbar-collapse {
        margin-top: 1rem;
    }

    .navbar-nav {
        flex-direction: column;
        width: 100%;
    }

    .navbar-nav .nav-item {
        width: 100%;
        margin: 0.1rem 0;
    }

    .navbar-nav .nav-link {
        width: 100%;
        margin: 0;
        padding: 0.6rem 1rem;
        text-align: left;
        border-radius: 0.25rem;
    }

    .navbar-nav .dropdown-menu {
        position: static;
        float: none;
        width: 100%;
        margin-top: 0;
        background-color: #f8f9fa;
        border: none;
        box-shadow: none;
    }

    .navbar-nav .dropdown-item {
        padding: 0.5rem 1.5rem;
    }

    .sub-navbar-card .card-body {
        padding: 1rem;
    }
}

/* Extra small screens (up to 575px) - Mobile */
@media (max-width: 575px) {
    .sub-navbar-card {
        margin-bottom: 1rem !important;
    }

    .sub-navbar-card .card-body {
        padding: 0.75rem;
    }

    .navbar-brand {
        font-size: 1rem !important;
        margin-bottom: 0.5rem;
    }

    .navbar-toggler {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .navbar-collapse {
        margin-top: 0.75rem;
    }

    .navbar-nav {
        flex-direction: column;
        width: 100%;
    }

    .navbar-nav .nav-item {
        width: 100%;
        margin: 0.05rem 0;
    }

    .navbar-nav .nav-link {
        width: 100%;
        margin: 0;
        padding: 0.5rem 0.75rem;
        text-align: left;
        border-radius: 0.25rem;
        font-size: 0.85rem;
    }

    .navbar-nav .dropdown-menu {
        position: static;
        float: none;
        width: 100%;
        margin-top: 0;
        background-color: #f8f9fa;
        border: none;
        box-shadow: none;
        padding: 0;
    }

    .navbar-nav .dropdown-item {
        padding: 0.4rem 1rem;
        font-size: 0.8rem;
    }

    .navbar-nav .dropdown-header {
        padding: 0.3rem 1rem;
        font-size: 0.75rem;
    }

    /* Quick Add button adjustments */
    .navbar-nav .btn {
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
    }
}

/* Ultra small screens (up to 360px) - Very small mobile */
@media (max-width: 360px) {
    .navbar-brand {
        font-size: 0.9rem !important;
    }

    .navbar-nav .nav-link {
        padding: 0.4rem 0.5rem;
        font-size: 0.8rem;
    }

    .navbar-nav .dropdown-item {
        padding: 0.3rem 0.75rem;
        font-size: 0.75rem;
    }

    .sub-navbar-card .card-body {
        padding: 0.5rem;
    }
}

/* Ensure proper spacing and alignment */
.navbar-collapse.show {
    animation: slideDown 0.3s ease-in-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Fix for dropdown toggle icons */
.dropdown-toggle::after {
    margin-left: 0.5rem;
}

@media (max-width: 767px) {
    .dropdown-toggle::after {
        float: right;
        margin-top: 0.5rem;
    }
}
</style>

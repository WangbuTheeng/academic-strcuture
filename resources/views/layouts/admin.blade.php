<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Academic Management System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom Admin Styles -->
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #6b7280;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }

        .sidebar-brand {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            text-align: center;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }

        .sidebar-brand h4 {
            color: white;
            font-weight: 800;
            margin: 0;
            font-size: 1.75rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .sidebar-brand small {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.875rem;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .sidebar-nav {
            padding: 1.5rem 0;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            padding: 1rem 1.5rem;
            border-radius: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            display: flex;
            align-items: center;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: white;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: white !important;
            transform: translateX(8px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .nav-link:hover::before {
            transform: scaleY(1);
        }

        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white !important;
            transform: translateX(8px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .nav-link.active::before {
            transform: scaleY(1);
        }

        .nav-link i {
            width: 24px;
            margin-right: 1rem;
            text-align: center;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .top-navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 2rem;
            margin-bottom: 0;
            color: #374151;
        }

        .top-navbar h5 {
            color: #1f2937;
            font-weight: 600;
        }

        .top-navbar span {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .content-wrapper {
            padding: 2rem;
        }

        /* Card Styles */
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
            border-radius: 10px 10px 0 0 !important;
        }

        /* Stats Cards */
        .stats-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, #6366f1 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .stats-card.success {
            background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
        }

        .stats-card.warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);
        }

        .stats-card.info {
            background: linear-gradient(135deg, var(--info-color) 0%, #2563eb 100%);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stats-label {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .content-wrapper {
                padding: 1rem;
            }
        }

        /* Other existing styles */
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 0;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: ">";
            color: #6b7280;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: #374151;
            background-color: #f9fafb;
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 500;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
        }

        .bg-purple {
            background-color: #8b5cf6;
            color: white;
        }

        /* Sidebar Collapsible Styles */
        .nav-link[data-bs-toggle="collapse"] {
            position: relative;
        }

        .nav-link[data-bs-toggle="collapse"] .fa-chevron-down {
            transition: transform 0.3s ease;
            font-size: 0.8rem;
            margin-left: auto;
        }

        .nav-link[data-bs-toggle="collapse"][aria-expanded="true"] .fa-chevron-down {
            transform: rotate(180deg);
        }

        .collapse .nav-link {
            padding: 0.75rem 2rem;
            color: rgba(255, 255, 255, 0.75) !important;
            font-size: 0.9rem;
            border-left: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .collapse .nav-link:hover {
            color: rgba(255, 255, 255, 0.95) !important;
            background: rgba(255, 255, 255, 0.1);
            border-left-color: rgba(255, 255, 255, 0.5);
            transform: translateX(4px);
        }

        .collapse .nav-link.active {
            color: white !important;
            background: var(--primary-color);
            border-left-color: white;
            transform: translateX(4px);
        }
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7) !important;
        }

        .collapse .nav-link:hover {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.05);
            transform: translateX(3px);
        }

        .collapse .nav-link.active {
            color: white !important;
            background-color: var(--primary-color);
            border-right: 2px solid white;
        }

        .collapse .nav-link i {
            width: 16px;
            font-size: 0.8rem;
        }

        /* Navigation Section Dividers */
        .nav-section-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 1rem 1.5rem;
        }

        .nav-section-title {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 1rem 1.5rem 0.5rem;
            margin-bottom: 0;
        }

        /* Enhanced dropdown arrow animation */
        .nav-link .fa-chevron-down {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.75rem;
            opacity: 0.7;
        }

        .nav-link:hover .fa-chevron-down {
            opacity: 1;
        }

        /* Quick Access Cards */
        .quick-access-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .quick-access-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-color: var(--primary-color) !important;
        }

        .quick-access-card a {
            color: inherit;
        }

        .quick-access-card:hover a {
            text-decoration: none !important;
        }

        /* Badge visibility fix */
        .badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: .75em;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.35rem;
        }

        .badge-primary {
            background-color: #007bff !important;
        }

        .badge-secondary {
            background-color: #6c757d !important;
        }

        .badge-success {
            background-color: #28a745 !important;
        }

        .badge-danger {
            background-color: #dc3545 !important;
        }

        .badge-info {
            background-color: #17a2b8 !important;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Brand -->
        <div class="sidebar-brand">
            <h4><i class="fas fa-graduation-cap me-2"></i>AMS</h4>
            <small>Academic Management System</small>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @can('manage-users')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*', 'admin.teacher-subjects.*', 'admin.permissions.*') ? 'active' : '' }}"
                       data-bs-toggle="collapse" href="#userManagementCollapse" role="button"
                       aria-expanded="{{ request()->routeIs('admin.users.*', 'admin.teacher-subjects.*', 'admin.permissions.*') ? 'true' : 'false' }}"
                       aria-controls="userManagementCollapse">
                        <i class="fas fa-users-cog"></i>
                        <span>User Management</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.users.*', 'admin.teacher-subjects.*', 'admin.permissions.*') ? 'show' : '' }}" id="userManagementCollapse">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                    <i class="fas fa-users"></i>
                                    <span>All Users</span>
                                </a>
                            </li>
                            @can('manage-teachers')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.teacher-subjects.*') ? 'active' : '' }}" href="{{ route('admin.teacher-subjects.index') }}">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                    <span>Teacher Assignments</span>
                                </a>
                            </li>
                            @endcan
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}" href="{{ route('admin.permissions.index') }}">
                                    <i class="fas fa-key"></i>
                                    <span>Permissions</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endcan

                <!-- Section Divider -->
                <div class="nav-section-divider"></div>
                <div class="nav-section-title">Management</div>

                @can('manage-students')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.students.*', 'admin.enrollments.*') ? 'active' : '' }}"
                       data-bs-toggle="collapse" href="#studentManagementCollapse" role="button"
                       aria-expanded="{{ request()->routeIs('admin.students.*', 'admin.enrollments.*') ? 'true' : 'false' }}"
                       aria-controls="studentManagementCollapse">
                        <i class="fas fa-user-graduate"></i>
                        <span>Student Management</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.students.*', 'admin.enrollments.*') ? 'show' : '' }}" id="studentManagementCollapse">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" href="{{ route('admin.students.index') }}">
                                    <i class="fas fa-users"></i>
                                    <span>All Students</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.enrollments.*') ? 'active' : '' }}" href="{{ route('admin.enrollments.index') }}">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Student Enrollments</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endcan

                @can('manage-academic-structure')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.academic.index') ? 'active' : '' }}" href="{{ route('admin.academic.index') }}">
                        <i class="fas fa-sitemap"></i>
                        <span>Academic Structure</span>
                    </a>
                </li>
                @endcan

                <!-- Section Divider -->
                <div class="nav-section-divider"></div>
                <div class="nav-section-title">Examinations</div>

                @can('manage-exams')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.exams.*') ? 'active' : '' }}" href="{{ route('admin.exams.index') }}">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Examinations</span>
                    </a>
                </li>
                @endcan

                @can('enter-marks')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.marks.*') ? 'active' : '' }}" href="{{ route('admin.marks.index') }}">
                        <i class="fas fa-edit"></i>
                        <span>Mark Entry</span>
                    </a>
                </li>
                @endcan

                <!-- Section Divider -->
                <div class="nav-section-divider"></div>
                <div class="nav-section-title">System</div>

                @can('manage-system')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.grading-scales.*') ? 'active' : '' }}" href="{{ route('admin.grading-scales.index') }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Grading Scales</span>
                    </a>
                </li>
                @endcan

                {{-- Temporarily removed permission check for testing --}}
                {{-- @can('manage-fees') --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.fees.*') ? 'active' : '' }}"
                       data-bs-toggle="collapse" href="#feeManagementCollapse" role="button"
                       aria-expanded="{{ request()->routeIs('admin.fees.*') ? 'true' : 'false' }}"
                       aria-controls="feeManagementCollapse">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Fee Management</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.fees.*') ? 'show' : '' }}" id="feeManagementCollapse">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.fees.overview') ? 'active' : '' }}" href="{{ route('admin.fees.overview') }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Overview</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.fees.structures.*') ? 'active' : '' }}" href="{{ route('admin.fees.structures.index') }}">
                                    <i class="fas fa-cogs"></i>
                                    <span>Fee Structures</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.fees.bills.*') ? 'active' : '' }}" href="{{ route('admin.fees.bills.index') }}">
                                    <i class="fas fa-file-invoice"></i>
                                    <span>Student Bills</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.fees.payments.*') ? 'active' : '' }}" href="{{ route('admin.fees.payments.index') }}">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Payments</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.fees.payments.quick-entry') ? 'active' : '' }}" href="{{ route('admin.fees.payments.quick-entry') }}">
                                    <i class="fas fa-bolt"></i>
                                    <span>Quick Payment</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.fees.receipts.*') ? 'active' : '' }}" href="{{ route('admin.fees.receipts.index') }}">
                                    <i class="fas fa-receipt"></i>
                                    <span>Receipts</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.fees.due-tracking.*') ? 'active' : '' }}" href="{{ route('admin.fees.due-tracking.index') }}">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>Due Tracking</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.fees.reports.*') ? 'active' : '' }}" href="{{ route('admin.fees.reports.index') }}">
                                    <i class="fas fa-chart-bar"></i>
                                    <span>Fee Reports</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.fees.enhanced-payments.*') ? 'active' : '' }}" href="{{ route('admin.fees.enhanced-payments.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Payment Dashboard</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.fees.advanced-bills.*') ? 'active' : '' }}" href="{{ route('admin.fees.advanced-bills.analytics') }}">
                                    <i class="fas fa-chart-line"></i>
                                    <span>Bill Analytics</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                {{-- @endcan --}}

                @can('manage-system')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.institute-settings.*') || request()->routeIs('admin.academic-settings.*') ? 'active' : '' }}"
                       data-bs-toggle="collapse" href="#academicSettingsCollapse" role="button"
                       aria-expanded="{{ request()->routeIs('admin.institute-settings.*') || request()->routeIs('admin.academic-settings.*') ? 'true' : 'false' }}"
                       aria-controls="academicSettingsCollapse">
                        <i class="fas fa-university"></i>
                        <span>Academic Settings</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.institute-settings.*') || request()->routeIs('admin.academic-settings.*') ? 'show' : '' }}" id="academicSettingsCollapse">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.institute-settings.*') ? 'active' : '' }}" href="{{ route('admin.institute-settings.index') }}">
                                    <i class="fas fa-school"></i>
                                    <span>School Information</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.academic-settings.levels') ? 'active' : '' }}" href="{{ route('admin.academic-settings.levels') }}">
                                    <i class="fas fa-layer-group"></i>
                                    <span>Academic Levels</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.academic-settings.programs') ? 'active' : '' }}" href="{{ route('admin.academic-settings.programs') }}">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span>Programs</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.academic-settings.subjects') ? 'active' : '' }}" href="{{ route('admin.academic-settings.subjects') }}">
                                    <i class="fas fa-book"></i>
                                    <span>Subjects</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endcan

                @can('view-reports')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                </li>
                @endcan

                <li class="nav-item mt-4">
                    <hr style="border-color: rgba(255,255,255,0.2);">
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                        <i class="fas fa-user-cog"></i>
                        Profile Settings
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="showHelpModal()">
                        <i class="fas fa-question-circle"></i>
                        Help & Support
                    </a>
                </li>

                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                        @csrf
                        <button type="submit" class="nav-link border-0 bg-transparent text-start w-100">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation Bar -->
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn btn-link d-md-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="mb-0 ms-2">@yield('page-title', 'Dashboard')</h5>
            </div>
            <div class="d-flex align-items-center">
                <span class="me-3">Welcome, {{ Auth::user()->name }}</span>
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery (for compatibility) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom Scripts -->
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');

            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !sidebarToggle?.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth > 768) {
                sidebar.classList.remove('show');
            }
        });

        // Help modal function
        function showHelpModal() {
            const helpModal = new bootstrap.Modal(document.getElementById('helpModal'));
            helpModal.show();
        }
    </script>

    <!-- Help Modal -->
    <div class="modal fade" id="helpModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-question-circle me-2"></i>Help & Support
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-university text-primary me-2"></i>Academic Settings</h6>
                            <ul class="list-unstyled ms-3">
                                <li><strong>School Information:</strong> Update your institution's name, address, and contact details</li>
                                <li><strong>Grading Scales:</strong> Create and manage grading systems for different levels</li>
                                <li><strong>Academic Levels:</strong> Configure class structures and academic hierarchy</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-file-alt text-success me-2"></i>Template Customization</h6>
                            <ul class="list-unstyled ms-3">
                                <li><strong>Advanced Editor:</strong> Professional template design with drag-and-drop</li>
                                <li><strong>Table Editor:</strong> Customize marksheet tables and columns</li>
                                <li><strong>Template Library:</strong> Browse and manage existing templates</li>
                            </ul>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <h6><i class="fas fa-rocket text-info me-2"></i>Quick Start Guide</h6>
                            <ol>
                                <li><strong>Setup School Information:</strong> Go to Academic Settings → School Information</li>
                                <li><strong>Create Grading Scale:</strong> Navigate to Grading Scales → Create New Scale</li>
                                <li><strong>Design Templates:</strong> Use the Advanced Editor to create custom marksheet templates</li>
                                <li><strong>Configure Academic Structure:</strong> Set up levels, programs, and subjects</li>
                            </ol>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Tip:</strong> Start by setting up your school information and creating a grading scale.
                        This will provide the foundation for all other academic management features.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="{{ route('admin.institute-settings.index') }}" class="btn btn-primary">
                        <i class="fas fa-cog"></i> Setup School Info
                    </a>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>

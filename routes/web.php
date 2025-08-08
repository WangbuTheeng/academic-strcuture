<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\MarksheetController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\Auth\SchoolLoginController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\SchoolController;

// Setup Wizard Routes (accessible without authentication)
Route::prefix('setup')->name('setup.')->group(function () {
    Route::get('/', [SetupController::class, 'index'])->name('index');
    Route::get('/step1', [SetupController::class, 'step1'])->name('step1');
    Route::post('/step1', [SetupController::class, 'processStep1'])->name('process-step1');
    Route::get('/step2', [SetupController::class, 'step2'])->name('step2');
    Route::post('/step2', [SetupController::class, 'processStep2'])->name('process-step2');
    Route::get('/step3', [SetupController::class, 'step3'])->name('step3');
    Route::post('/step3', [SetupController::class, 'processStep3'])->name('process-step3');
    Route::get('/step4', [SetupController::class, 'step4'])->name('step4');
    Route::post('/complete', [SetupController::class, 'complete'])->name('complete');
    Route::get('/complete', [SetupController::class, 'redirectToStep4'])->name('complete-redirect');
    Route::get('/success', [SetupController::class, 'success'])->name('success');
});

// Super-Admin Authentication Routes
Route::prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/login', [SchoolLoginController::class, 'showSuperAdminLogin'])->name('login');
    Route::post('/login', [SchoolLoginController::class, 'superAdminLogin'])->name('login.post');
});

// School Authentication Routes
Route::get('/login', [SchoolLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [SchoolLoginController::class, 'login'])->name('login.post');
Route::post('/logout', [SchoolLoginController::class, 'logout'])->name('logout');

// Super-Admin Routes
Route::prefix('super-admin')->name('super-admin.')->middleware(['auth', 'super-admin'])->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

    // School Management
    Route::resource('schools', SchoolController::class);
    Route::post('schools/generate-code', [SchoolController::class, 'generateCode'])->name('schools.generate-code');
    Route::patch('schools/{school}/status', [SchoolController::class, 'updateStatus'])->name('schools.update-status');
    Route::patch('schools/{school}/reset-password', [SchoolController::class, 'resetPassword'])->name('schools.reset-password');

    // Analytics
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'index'])->name('index');
        Route::get('/overview', [\App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'getSystemOverview'])->name('overview');
        Route::get('/growth', [\App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'getGrowthMetrics'])->name('growth');
        Route::get('/usage', [\App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'getUsageStatistics'])->name('usage');
    });

    // Audit Logs
    Route::prefix('audit')->name('audit.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\AuditController::class, 'index'])->name('index');
        Route::get('/{auditLog}', [\App\Http\Controllers\SuperAdmin\AuditController::class, 'show'])->name('show');
        Route::get('/report/generate', [\App\Http\Controllers\SuperAdmin\AuditController::class, 'report'])->name('report');
        Route::get('/export', [\App\Http\Controllers\SuperAdmin\AuditController::class, 'export'])->name('export');
    });
});

// Redirect root to login (setup disabled for multi-tenant system)
Route::get('/', function () {
    // In multi-tenant system, super-admin creates schools, no setup needed
    return redirect()->route('login');
});

// Dashboard routes (role-based) - with school context
Route::middleware(['auth', 'school-context'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'roleBasedDashboard'])->name('dashboard');
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    });

    // Profile routes
    Route::get('/profile', function () {
        return view('profile.edit');
    })->name('profile.edit');

    Route::patch('/profile', function () {
        // Profile update logic would go here
        return back()->with('success', 'Profile updated successfully.');
    })->name('profile.update');
});

// Admin routes - with school context
Route::middleware(['auth', 'school-context', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Student management routes
    Route::resource('students', \App\Http\Controllers\Admin\StudentController::class);
    Route::get('students/export', [\App\Http\Controllers\Admin\StudentController::class, 'export'])->name('students.export');
    Route::post('students/bulk-action', [\App\Http\Controllers\Admin\StudentController::class, 'bulkAction'])->name('students.bulk-action');

    // Simple student creation form for debugging
    Route::get('students-simple/create', function() {
        return view('admin.students.create-simple');
    })->name('students.create-simple');

    // Academic structure routes
    Route::get('academic', [\App\Http\Controllers\Admin\AcademicStructureController::class, 'index'])->name('academic.index');
    Route::get('academic/hierarchy', [\App\Http\Controllers\Admin\AcademicStructureController::class, 'getHierarchy'])->name('academic.hierarchy');
    Route::get('academic/stats', [\App\Http\Controllers\Admin\AcademicStructureController::class, 'getStats'])->name('academic.stats');
    Route::get('academic/validate', [\App\Http\Controllers\Admin\AcademicStructureController::class, 'validateStructure'])->name('academic.validate');
    Route::get('academic/reports', [\App\Http\Controllers\Admin\AcademicStructureController::class, 'reports'])->name('academic.reports');

    // Level management routes
    Route::resource('levels', \App\Http\Controllers\Admin\LevelController::class);
    Route::post('levels/bulk-action', [\App\Http\Controllers\Admin\LevelController::class, 'bulkAction'])->name('levels.bulk-action');

    // Faculty management routes
    Route::resource('faculties', \App\Http\Controllers\Admin\FacultyController::class);
    Route::post('faculties/bulk-action', [\App\Http\Controllers\Admin\FacultyController::class, 'bulkAction'])->name('faculties.bulk-action');

    // Department management routes
    Route::resource('departments', \App\Http\Controllers\Admin\DepartmentController::class);
    Route::post('departments/bulk-action', [\App\Http\Controllers\Admin\DepartmentController::class, 'bulkAction'])->name('departments.bulk-action');

    // Class management routes
    Route::resource('classes', \App\Http\Controllers\Admin\ClassController::class);
    Route::post('classes/bulk-action', [\App\Http\Controllers\Admin\ClassController::class, 'bulkAction'])->name('classes.bulk-action');

    // Program management routes
    Route::resource('programs', \App\Http\Controllers\Admin\ProgramController::class);
    Route::post('programs/bulk-action', [\App\Http\Controllers\Admin\ProgramController::class, 'bulkAction'])->name('programs.bulk-action');
    Route::get('programs/{program}/manage-structure', [\App\Http\Controllers\Admin\ProgramController::class, 'manageStructure'])->name('programs.manage-structure');
    Route::post('programs/{program}/add-class', [\App\Http\Controllers\Admin\ProgramController::class, 'addClass'])->name('programs.add-class');
    Route::delete('programs/{program}/remove-class/{class}', [\App\Http\Controllers\Admin\ProgramController::class, 'removeClass'])->name('programs.remove-class');
    Route::post('programs/{program}/add-subject', [\App\Http\Controllers\Admin\ProgramController::class, 'addSubject'])->name('programs.add-subject');
    Route::delete('programs/{program}/remove-subject/{subject}', [\App\Http\Controllers\Admin\ProgramController::class, 'removeSubject'])->name('programs.remove-subject');
    Route::get('programs/{program}/classes', [\App\Http\Controllers\Admin\ProgramController::class, 'getClasses'])->name('programs.get-classes');

    // Subject management routes
    Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class);
    Route::post('subjects/bulk-action', [\App\Http\Controllers\Admin\SubjectController::class, 'bulkAction'])->name('subjects.bulk-action');

    // Teacher-Subject assignment routes
    Route::resource('teacher-subjects', \App\Http\Controllers\Admin\TeacherSubjectController::class);
    Route::post('teacher-subjects/bulk-assign', [\App\Http\Controllers\Admin\TeacherSubjectController::class, 'bulkAssign'])->name('teacher-subjects.bulk-assign');
    Route::post('teacher-subjects/bulk-assign-subjects', [\App\Http\Controllers\Admin\TeacherSubjectController::class, 'bulkAssignSubjects'])->name('teacher-subjects.bulk-assign-subjects');
    Route::post('teacher-subjects/{teacherSubject}/toggle-status', [\App\Http\Controllers\Admin\TeacherSubjectController::class, 'toggleStatus'])->name('teacher-subjects.toggle-status');

    // Permission management routes
    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('index');
        Route::post('/create', [\App\Http\Controllers\Admin\PermissionController::class, 'createPermission'])->name('create');
        Route::delete('/permissions/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'deletePermission'])->name('delete');
        Route::put('/roles/{role}/permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'updateRolePermissions'])->name('roles.update');
        Route::post('/users/{user}/permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'assignUserPermissions'])->name('users.assign');
        Route::get('/users/{user}', [\App\Http\Controllers\Admin\PermissionController::class, 'showUserPermissions'])->name('users.show');
        Route::post('/bulk-assign', [\App\Http\Controllers\Admin\PermissionController::class, 'bulkAssignPermissions'])->name('bulk-assign');
        Route::get('/suggestions', [\App\Http\Controllers\Admin\PermissionController::class, 'getPermissionSuggestions'])->name('suggestions');
    });

    // Class-Subject management routes
    Route::post('classes/{class}/add-subject', [\App\Http\Controllers\Admin\ClassController::class, 'addSubject'])->name('classes.add-subject');
    Route::delete('classes/{class}/remove-subject/{subject}', [\App\Http\Controllers\Admin\ClassController::class, 'removeSubject'])->name('classes.remove-subject');

    // Student Enrollment management routes
    // Custom routes must be defined BEFORE resource routes to avoid conflicts
    Route::get('enrollments/bulk/create', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'bulkCreate'])->name('enrollments.bulk-create');
    Route::post('enrollments/bulk/store', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'bulkStore'])->name('enrollments.bulk-store');
    Route::post('enrollments/bulk-action', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'bulkAction'])->name('enrollments.bulk-action');
    Route::get('enrollments/classes-by-program', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'getClassesByProgram'])->name('enrollments.classes-by-program');
    Route::get('enrollments/next-roll-number', [\App\Http\Controllers\Admin\StudentEnrollmentController::class, 'getNextRollNumber'])->name('enrollments.next-roll-number');
    Route::resource('enrollments', \App\Http\Controllers\Admin\StudentEnrollmentController::class);

    // Student Subject Assignment routes
    Route::get('enrollments/{enrollment}/subjects', [\App\Http\Controllers\Admin\StudentSubjectController::class, 'index'])->name('student-subjects.index');
    Route::post('enrollments/{enrollment}/subjects', [\App\Http\Controllers\Admin\StudentSubjectController::class, 'store'])->name('student-subjects.store');
    Route::delete('enrollments/{enrollment}/subjects/{studentSubject}', [\App\Http\Controllers\Admin\StudentSubjectController::class, 'destroy'])->name('student-subjects.destroy');
    Route::patch('enrollments/{enrollment}/subjects/{studentSubject}/status', [\App\Http\Controllers\Admin\StudentSubjectController::class, 'updateStatus'])->name('student-subjects.update-status');
    Route::post('enrollments/{enrollment}/subjects/bulk-assign', [\App\Http\Controllers\Admin\StudentSubjectController::class, 'bulkAssignByProgram'])->name('student-subjects.bulk-assign');
    Route::get('enrollments/{enrollment}/subjects/preview', [\App\Http\Controllers\Admin\StudentSubjectController::class, 'previewSubjectEnrollment'])->name('student-subjects.preview');
    Route::get('subjects-by-program', [\App\Http\Controllers\Admin\StudentSubjectController::class, 'getSubjectsByProgram'])->name('student-subjects.subjects-by-program');

    // Academic Year management routes
    Route::resource('academic-years', \App\Http\Controllers\Admin\AcademicYearController::class);
    Route::post('academic-years/{academicYear}/set-current', [\App\Http\Controllers\Admin\AcademicYearController::class, 'setCurrent'])->name('academic-years.set-current');
    Route::post('academic-years/bulk-action', [\App\Http\Controllers\Admin\AcademicYearController::class, 'bulkAction'])->name('academic-years.bulk-action');



    // Examination management routes
    Route::resource('exams', \App\Http\Controllers\Admin\ExamController::class);
    Route::post('exams/bulk-action', [\App\Http\Controllers\Admin\ExamController::class, 'bulkAction'])->name('exams.bulk-action');

    // Mark entry routes
    Route::resource('marks', \App\Http\Controllers\Admin\MarkController::class);

    // Fee Management routes
    Route::prefix('fees')->name('fees.')->group(function () {
        // Overview page
        Route::get('overview', function () {
            return view('admin.fee-management.overview');
        })->name('overview');

        // Fee Structure management
        Route::resource('structures', \App\Http\Controllers\Admin\FeeStructureController::class);
        Route::post('structures/{structure}/toggle-status', [\App\Http\Controllers\Admin\FeeStructureController::class, 'toggleStatus'])->name('structures.toggle-status');
        Route::get('programs-by-level', [\App\Http\Controllers\Admin\FeeStructureController::class, 'getProgramsByLevel'])->name('programs-by-level');
        Route::get('classes-by-program', [\App\Http\Controllers\Admin\FeeStructureController::class, 'getClassesByProgram'])->name('classes-by-program');

        // Student Bills management
        Route::resource('bills', \App\Http\Controllers\Admin\StudentBillController::class);
        Route::get('bills/bulk/generate', [\App\Http\Controllers\Admin\StudentBillController::class, 'bulkGenerate'])->name('bills.bulk-generate');
        Route::post('bills/bulk/process', [\App\Http\Controllers\Admin\StudentBillController::class, 'processBulkGenerate'])->name('bills.process-bulk-generate');
        Route::get('fee-structures-by-filters', [\App\Http\Controllers\Admin\StudentBillController::class, 'getFeeStructures'])->name('fee-structures-by-filters');

        // Payments management
        Route::resource('payments', \App\Http\Controllers\Admin\PaymentController::class);
        Route::post('payments/{payment}/verify', [\App\Http\Controllers\Admin\PaymentController::class, 'verify'])->name('payments.verify');
        Route::post('payments/{payment}/reject', [\App\Http\Controllers\Admin\PaymentController::class, 'reject'])->name('payments.reject');
        Route::get('payments/quick/entry', [\App\Http\Controllers\Admin\PaymentController::class, 'quickEntry'])->name('payments.quick-entry');
        Route::post('payments/quick/process', [\App\Http\Controllers\Admin\PaymentController::class, 'processQuickEntry'])->name('payments.process-quick-entry');
        Route::get('student-bills-by-student', [\App\Http\Controllers\Admin\PaymentController::class, 'getStudentBills'])->name('student-bills-by-student');

        // Receipt management
        Route::resource('receipts', \App\Http\Controllers\Admin\ReceiptController::class)->only(['index', 'show']);
        Route::get('receipts/{receipt}/download', [\App\Http\Controllers\Admin\ReceiptController::class, 'downloadPdf'])->name('receipts.download');
        Route::get('receipts/{receipt}/print', [\App\Http\Controllers\Admin\ReceiptController::class, 'print'])->name('receipts.print');
        Route::post('receipts/{receipt}/duplicate', [\App\Http\Controllers\Admin\ReceiptController::class, 'generateDuplicate'])->name('receipts.duplicate');
        Route::post('receipts/{receipt}/cancel', [\App\Http\Controllers\Admin\ReceiptController::class, 'cancel'])->name('receipts.cancel');
        Route::post('receipts/bulk/generate', [\App\Http\Controllers\Admin\ReceiptController::class, 'bulkGenerate'])->name('receipts.bulk-generate');
        Route::get('receipts/template/view', [\App\Http\Controllers\Admin\ReceiptController::class, 'template'])->name('receipts.template');
        Route::post('receipts/template/update', [\App\Http\Controllers\Admin\ReceiptController::class, 'updateTemplate'])->name('receipts.template.update');

        // Due tracking and notifications
        Route::prefix('due-tracking')->name('due-tracking.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\DueTrackingController::class, 'index'])->name('index');
            Route::post('send-reminders', [\App\Http\Controllers\Admin\DueTrackingController::class, 'sendReminders'])->name('send-reminders');
            Route::post('automated-reminders', [\App\Http\Controllers\Admin\DueTrackingController::class, 'generateAutomatedReminders'])->name('automated-reminders');
            Route::get('analytics', [\App\Http\Controllers\Admin\DueTrackingController::class, 'analytics'])->name('analytics');
            Route::get('export-overdue', [\App\Http\Controllers\Admin\DueTrackingController::class, 'exportOverdue'])->name('export-overdue');
        });

        // Fee reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\FeeReportController::class, 'index'])->name('index');
            Route::get('daily-collection', [\App\Http\Controllers\Admin\FeeReportController::class, 'dailyCollection'])->name('daily-collection');
            Route::get('monthly-collection', [\App\Http\Controllers\Admin\FeeReportController::class, 'monthlyCollection'])->name('monthly-collection');
            Route::get('outstanding-dues', [\App\Http\Controllers\Admin\FeeReportController::class, 'outstandingDues'])->name('outstanding-dues');
            Route::get('student-wise', [\App\Http\Controllers\Admin\FeeReportController::class, 'studentWise'])->name('student-wise');
            Route::get('category-wise', [\App\Http\Controllers\Admin\FeeReportController::class, 'categoryWise'])->name('category-wise');
            Route::get('payment-method-wise', [\App\Http\Controllers\Admin\FeeReportController::class, 'paymentMethodWise'])->name('payment-method-wise');
            Route::get('export-collection', [\App\Http\Controllers\Admin\FeeReportController::class, 'exportCollection'])->name('export-collection');
            Route::get('analytics-data', [\App\Http\Controllers\Admin\FeeReportController::class, 'analyticsData'])->name('analytics-data');
        });

        // Fee reports route aliases
        Route::prefix('fee-reports')->name('fee-reports.')->group(function () {
            Route::get('daily-collection', [\App\Http\Controllers\Admin\FeeReportController::class, 'dailyCollection'])->name('daily-collection');
            Route::get('monthly-collection', [\App\Http\Controllers\Admin\FeeReportController::class, 'monthlyCollection'])->name('monthly-collection');
            Route::get('outstanding-dues', [\App\Http\Controllers\Admin\FeeReportController::class, 'outstandingDues'])->name('outstanding-dues');
            Route::get('student-wise', [\App\Http\Controllers\Admin\FeeReportController::class, 'studentWise'])->name('student-wise');
            Route::get('category-wise', [\App\Http\Controllers\Admin\FeeReportController::class, 'categoryWise'])->name('category-wise');
        });



        // Enhanced Payment Management (Phase 3-4)
        Route::prefix('enhanced-payments')->name('enhanced-payments.')->group(function () {
            Route::get('dashboard', [\App\Http\Controllers\Admin\EnhancedPaymentController::class, 'dashboard'])->name('dashboard');
            Route::get('entry', [\App\Http\Controllers\Admin\EnhancedPaymentController::class, 'enhancedEntry'])->name('entry');
            Route::post('process', [\App\Http\Controllers\Admin\EnhancedPaymentController::class, 'processEnhancedPayment'])->name('process');
            Route::get('mobile-entry', [\App\Http\Controllers\Admin\EnhancedPaymentController::class, 'mobileEntry'])->name('mobile-entry');
            Route::post('process-mobile-payment', [\App\Http\Controllers\Admin\EnhancedPaymentController::class, 'processMobilePayment'])->name('process-mobile-payment');
            Route::get('search-students', [\App\Http\Controllers\Admin\EnhancedPaymentController::class, 'searchStudents'])->name('search-students');
            Route::get('student-payment-history', [\App\Http\Controllers\Admin\EnhancedPaymentController::class, 'getStudentPaymentHistory'])->name('get-student-payment-history');
            Route::get('get-student-payment-history', [\App\Http\Controllers\Admin\EnhancedPaymentController::class, 'getStudentPaymentHistory'])->name('get-student-payment-history-alias');
            Route::get('bulk-payment', [\App\Http\Controllers\Admin\EnhancedPaymentController::class, 'bulkPayment'])->name('bulk-payment');
            Route::post('bulk-payment/process', [\App\Http\Controllers\Admin\EnhancedPaymentController::class, 'processBulkPayment'])->name('process-bulk-payment');
            Route::get('analytics-api', [\App\Http\Controllers\Admin\EnhancedPaymentController::class, 'analyticsApi'])->name('analytics-api');
        });

        // Advanced Bill Management (Phase 3-4)
        Route::prefix('advanced-bills')->name('advanced-bills.')->group(function () {
            Route::get('generate', [\App\Http\Controllers\Admin\AdvancedBillController::class, 'advancedGenerate'])->name('generate');
            Route::post('generate/process', [\App\Http\Controllers\Admin\AdvancedBillController::class, 'processAdvancedGenerate'])->name('process-generate');
            Route::get('analytics', [\App\Http\Controllers\Admin\AdvancedBillController::class, 'analytics'])->name('analytics');
            Route::get('bulk-operations', [\App\Http\Controllers\Admin\AdvancedBillController::class, 'bulkOperations'])->name('bulk-operations');
            Route::post('bulk-operations/process', [\App\Http\Controllers\Admin\AdvancedBillController::class, 'processBulkOperations'])->name('process-bulk-operations');
            Route::get('templates', [\App\Http\Controllers\Admin\AdvancedBillController::class, 'templates'])->name('templates');
            Route::post('generate-with-template', [\App\Http\Controllers\Admin\AdvancedBillController::class, 'generateWithTemplate'])->name('generate-with-template');
        });
    });
    Route::get('marks/exam/{exam}/dashboard', [\App\Http\Controllers\Admin\MarkController::class, 'examDashboard'])->name('marks.exam-dashboard');
    Route::post('marks/submit', [\App\Http\Controllers\Admin\MarkController::class, 'submit'])->name('marks.submit');
    Route::post('marks/approve', [\App\Http\Controllers\Admin\MarkController::class, 'approve'])->name('marks.approve');
    Route::get('marks/exam/{exam}/submitted', [\App\Http\Controllers\Admin\MarkController::class, 'getSubmittedMarks'])->name('marks.get-submitted');
    Route::post('marks/apply-grace', [\App\Http\Controllers\Admin\MarkController::class, 'applyGrace'])->name('marks.apply-grace');
    Route::post('marks/bulk-action', [\App\Http\Controllers\Admin\MarkController::class, 'bulkAction'])->name('marks.bulk-action');
    Route::post('marks/exam/{exam}/submit-all', [\App\Http\Controllers\Admin\MarkController::class, 'submitAllMarks'])->name('marks.submit-all');

    // Reports & Analytics routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/academic', [\App\Http\Controllers\Admin\ReportController::class, 'academic'])->name('academic');
        Route::get('/class-performance', [\App\Http\Controllers\Admin\ReportController::class, 'classPerformance'])->name('class-performance');
        Route::get('/subject-analysis', [\App\Http\Controllers\Admin\ReportController::class, 'subjectAnalysis'])->name('subject-analysis');
        Route::get('/student-progress', [\App\Http\Controllers\Admin\ReportController::class, 'studentProgress'])->name('student-progress');
        Route::match(['GET', 'POST'], '/custom', [\App\Http\Controllers\Admin\ReportController::class, 'customReport'])->name('custom');
        Route::post('/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('export');
    });

    // Marksheet Generation routes
    Route::prefix('marksheets')->name('marksheets.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\MarksheetController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\MarksheetController::class, 'create'])->name('create');
        Route::post('/generate', [\App\Http\Controllers\Admin\MarksheetController::class, 'generate'])->name('generate');
        Route::post('/bulk-generate', [\App\Http\Controllers\Admin\MarksheetController::class, 'bulkGenerate'])->name('bulk-generate');
        Route::post('/preview', [\App\Http\Controllers\Admin\MarksheetController::class, 'preview'])->name('preview');

        // Marksheet Customization routes
        Route::prefix('customize')->name('customize.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\MarksheetCustomizationController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\MarksheetCustomizationController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\MarksheetCustomizationController::class, 'store'])->name('store');
            Route::get('/{template}/edit', [\App\Http\Controllers\Admin\MarksheetCustomizationController::class, 'edit'])->name('edit');
            Route::put('/{template}', [\App\Http\Controllers\Admin\MarksheetCustomizationController::class, 'update'])->name('update');
            Route::delete('/{template}', [\App\Http\Controllers\Admin\MarksheetCustomizationController::class, 'destroy'])->name('destroy');
            Route::get('/{template}/preview', [\App\Http\Controllers\Admin\MarksheetCustomizationController::class, 'preview'])->name('preview');
            Route::get('/{template}/duplicate', [\App\Http\Controllers\Admin\MarksheetCustomizationController::class, 'duplicate'])->name('duplicate');
            Route::post('/{template}/set-default', [\App\Http\Controllers\Admin\MarksheetCustomizationController::class, 'setDefault'])->name('set-default');
            Route::get('/table-editor', [\App\Http\Controllers\Admin\MarksheetCustomizationController::class, 'tableEditor'])->name('table-editor');
            Route::get('/column-reorder', [\App\Http\Controllers\Admin\MarksheetCustomizationController::class, 'columnReorder'])->name('column-reorder');
            Route::get('/drag-drop-builder', [\App\Http\Controllers\Admin\MarksheetCustomizationController::class, 'dragDropBuilder'])->name('drag-drop-builder');
            Route::get('/advanced-editor', [\App\Http\Controllers\Admin\MarksheetCustomizationController::class, 'advancedEditor'])->name('advanced-editor');
        });
    });

    // Route aliases for backward compatibility
    Route::name('student-bills.')->group(function () {
        Route::get('student-bills', [\App\Http\Controllers\Admin\StudentBillController::class, 'index'])->name('index');
        Route::get('student-bills/create', [\App\Http\Controllers\Admin\StudentBillController::class, 'create'])->name('create');
        Route::post('student-bills', [\App\Http\Controllers\Admin\StudentBillController::class, 'store'])->name('store');
        Route::get('student-bills/{studentBill}', [\App\Http\Controllers\Admin\StudentBillController::class, 'show'])->name('show');
        Route::get('student-bills/{studentBill}/edit', [\App\Http\Controllers\Admin\StudentBillController::class, 'edit'])->name('edit');
        Route::get('student-bills/{studentBill}/preview', [\App\Http\Controllers\Admin\StudentBillController::class, 'preview'])->name('preview');
        Route::put('student-bills/{studentBill}', [\App\Http\Controllers\Admin\StudentBillController::class, 'update'])->name('update');
        Route::delete('student-bills/{studentBill}', [\App\Http\Controllers\Admin\StudentBillController::class, 'destroy'])->name('destroy');
    });

    // Enhanced payments route aliases
    Route::name('enhanced-payments.')->group(function () {
        Route::get('enhanced-payments/mobile-entry', [\App\Http\Controllers\Admin\EnhancedPaymentController::class, 'mobileEntry'])->name('mobile-entry');
        Route::get('enhanced-payments/search-students', [\App\Http\Controllers\Admin\EnhancedPaymentController::class, 'searchStudents'])->name('search-students');
        Route::get('enhanced-payments/dashboard', [\App\Http\Controllers\Admin\EnhancedPaymentController::class, 'dashboard'])->name('dashboard');
        Route::get('enhanced-payments/bulk-payment', [\App\Http\Controllers\Admin\EnhancedPaymentController::class, 'bulkPayment'])->name('bulk-payment');
        Route::get('enhanced-payments/analytics-api', [\App\Http\Controllers\Admin\EnhancedPaymentController::class, 'analyticsApi'])->name('analytics-api');
        Route::get('enhanced-payments/get-student-payment-history', [\App\Http\Controllers\Admin\EnhancedPaymentController::class, 'getStudentPaymentHistory'])->name('get-student-payment-history');
        Route::post('enhanced-payments/process-bulk-payment', [\App\Http\Controllers\Admin\EnhancedPaymentController::class, 'processBulkPayment'])->name('process-bulk-payment');
    });

    // Payments route aliases
    Route::name('payments.')->group(function () {
        Route::get('payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('index');
        Route::get('payments/create', [\App\Http\Controllers\Admin\PaymentController::class, 'create'])->name('create');
        Route::get('payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('show');
    });

    // Receipts route aliases
    Route::name('receipts.')->group(function () {
        Route::get('receipts', [\App\Http\Controllers\Admin\ReceiptController::class, 'index'])->name('index');
        Route::get('receipts/{receipt}', [\App\Http\Controllers\Admin\ReceiptController::class, 'show'])->name('show');
    });



    // Grading Scale routes
    Route::resource('grading-scales', \App\Http\Controllers\Admin\GradingScaleController::class);
    Route::post('grading-scales/{gradingScale}/toggle-status', [\App\Http\Controllers\Admin\GradingScaleController::class, 'toggleStatus'])->name('grading-scales.toggle-status');
    Route::post('grading-scales/{gradingScale}/set-default', [\App\Http\Controllers\Admin\GradingScaleController::class, 'setDefault'])->name('grading-scales.set-default');

    // Result Management routes
    Route::prefix('results')->name('results.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ResultController::class, 'index'])->name('index');
        Route::get('/{exam}', [\App\Http\Controllers\Admin\ResultController::class, 'show'])->name('show');
        Route::post('/{exam}/publish', [\App\Http\Controllers\Admin\ResultController::class, 'publish'])->name('publish');
        Route::post('/{exam}/unpublish', [\App\Http\Controllers\Admin\ResultController::class, 'unpublish'])->name('unpublish');
        Route::post('/{exam}/lock', [\App\Http\Controllers\Admin\ResultController::class, 'lock'])->name('lock');
        Route::post('/{exam}/bulk-approve', [\App\Http\Controllers\Admin\ResultController::class, 'bulkApprove'])->name('bulk-approve');
        Route::get('/{exam}/summary', [\App\Http\Controllers\Admin\ResultController::class, 'summary'])->name('summary');
    });

    // Promotion Management routes
    Route::prefix('promotions')->name('promotions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PromotionController::class, 'index'])->name('index');
        Route::get('/bulk', [\App\Http\Controllers\Admin\PromotionController::class, 'bulk'])->name('bulk');
        Route::post('/bulk', [\App\Http\Controllers\Admin\PromotionController::class, 'processBulk'])->name('process-bulk');
        Route::get('/history', [\App\Http\Controllers\Admin\PromotionController::class, 'history'])->name('history');
        Route::get('/{student}/analyze', [\App\Http\Controllers\Admin\PromotionController::class, 'analyze'])->name('analyze');
        Route::post('/{student}/promote', [\App\Http\Controllers\Admin\PromotionController::class, 'promote'])->name('promote');
    });

    // Grace Marks Management routes
    Route::prefix('grace-marks')->name('grace-marks.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\GraceMarkController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\GraceMarkController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\GraceMarkController::class, 'store'])->name('store');
        Route::get('/{graceMark}', [\App\Http\Controllers\Admin\GraceMarkController::class, 'show'])->name('show');
        Route::post('/{graceMark}/approve', [\App\Http\Controllers\Admin\GraceMarkController::class, 'approve'])->name('approve');
        Route::post('/{graceMark}/reject', [\App\Http\Controllers\Admin\GraceMarkController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [\App\Http\Controllers\Admin\GraceMarkController::class, 'bulkApprove'])->name('bulk-approve');
        Route::get('/reports/summary', [\App\Http\Controllers\Admin\GraceMarkController::class, 'report'])->name('report');
    });

    // Academic Settings routes
    Route::prefix('academic-settings')->name('academic-settings.')->group(function () {
        Route::get('/school-info', [\App\Http\Controllers\Admin\AcademicSettingsController::class, 'schoolInfo'])->name('school-info');
        Route::put('/school-info', [\App\Http\Controllers\Admin\AcademicSettingsController::class, 'updateSchoolInfo'])->name('school-info.update');
        Route::get('/academic-year', [\App\Http\Controllers\Admin\AcademicSettingsController::class, 'academicYear'])->name('academic-year');
        Route::get('/grading', [\App\Http\Controllers\Admin\AcademicSettingsController::class, 'grading'])->name('grading');
        Route::get('/backup', [\App\Http\Controllers\Admin\AcademicSettingsController::class, 'backup'])->name('backup');
        Route::post('/backup/create', [\App\Http\Controllers\Admin\AcademicSettingsController::class, 'createBackup'])->name('backup.create');
        Route::get('/backup/{filename}/download', [\App\Http\Controllers\Admin\AcademicSettingsController::class, 'downloadBackup'])->name('backup.download');
        Route::delete('/backup/{filename}', [\App\Http\Controllers\Admin\AcademicSettingsController::class, 'deleteBackup'])->name('backup.delete');
    });

    // Backup Management routes
    Route::prefix('backups')->name('backups.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('index');
        Route::post('/create', [\App\Http\Controllers\Admin\BackupController::class, 'create'])->name('create');
        Route::get('/{backup}/download', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('download');
        Route::delete('/{backup}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('destroy');
        Route::get('/{backup}/restore', [\App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('restore');
        Route::post('/{backup}/restore', [\App\Http\Controllers\Admin\BackupController::class, 'processRestore'])->name('process-restore');
        Route::post('/schedule', [\App\Http\Controllers\Admin\BackupController::class, 'schedule'])->name('schedule');
        Route::post('/cleanup', [\App\Http\Controllers\Admin\BackupController::class, 'cleanup'])->name('cleanup');
    });

    // Analytics routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('index');
        Route::get('/student-performance', [\App\Http\Controllers\Admin\AnalyticsController::class, 'studentPerformance'])->name('student-performance');
        Route::get('/subject-analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'subjectAnalytics'])->name('subject-analytics');
        Route::get('/class-analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'classAnalytics'])->name('class-analytics');
        Route::get('/exam-analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'examAnalytics'])->name('exam-analytics');
        Route::get('/trend-analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'trendAnalytics'])->name('trend-analytics');
        Route::get('/export', [\App\Http\Controllers\Admin\AnalyticsController::class, 'export'])->name('export');
    });

    // Data Export & Import routes
    Route::prefix('data-export')->name('data-export.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DataExportController::class, 'index'])->name('index');
        Route::get('/students', [\App\Http\Controllers\Admin\DataExportController::class, 'exportStudents'])->name('students');
        Route::get('/marks', [\App\Http\Controllers\Admin\DataExportController::class, 'exportMarks'])->name('marks');
        Route::get('/results', [\App\Http\Controllers\Admin\DataExportController::class, 'exportResults'])->name('results');
        Route::get('/analytics', [\App\Http\Controllers\Admin\DataExportController::class, 'exportAnalytics'])->name('analytics');
        Route::post('/import-students', [\App\Http\Controllers\Admin\DataExportController::class, 'importStudents'])->name('import-students');
        Route::post('/import-marks', [\App\Http\Controllers\Admin\DataExportController::class, 'importMarks'])->name('import-marks');
    });

    // Institute Settings Routes
    Route::prefix('institute-settings')->name('institute-settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\InstituteSettingsController::class, 'index'])->name('index');
        Route::put('/update', [\App\Http\Controllers\Admin\InstituteSettingsController::class, 'update'])->name('update');
        Route::get('/academic', [\App\Http\Controllers\Admin\InstituteSettingsController::class, 'academic'])->name('academic');
        Route::put('/academic', [\App\Http\Controllers\Admin\InstituteSettingsController::class, 'updateAcademic'])->name('update-academic');
        Route::get('/remove-logo', [\App\Http\Controllers\Admin\InstituteSettingsController::class, 'removeLogo'])->name('remove-logo');
        Route::get('/remove-signature', [\App\Http\Controllers\Admin\InstituteSettingsController::class, 'removeSignature'])->name('remove-signature');
    });

    // Storage file serving route (temporary fix for storage link issue)
    Route::get('/storage/{path}', function ($path) {
        $filePath = storage_path('app/public/' . $path);
        if (file_exists($filePath)) {
            return response()->file($filePath);
        }
        abort(404);
    })->where('path', '.*');

    // Academic Settings Routes
    Route::prefix('academic-settings')->name('academic-settings.')->group(function () {
        Route::get('/levels', function() { return view('admin.academic-settings.levels'); })->name('levels');
        Route::get('/programs', function() { return view('admin.academic-settings.programs'); })->name('programs');
        Route::get('/subjects', function() { return view('admin.academic-settings.subjects'); })->name('subjects');
    });
});

// Principal routes - with school context
Route::middleware(['auth', 'school-context', 'role:principal'])->prefix('principal')->name('principal.')->group(function () {
    Route::get('/', function() {
        return app(DashboardController::class)->principalDashboard();
    })->name('dashboard');
});

// Teacher routes - with school context
Route::middleware(['auth', 'school-context', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\Teacher\DashboardController::class, 'profile'])->name('profile');

    // Mark entry routes
    Route::prefix('marks')->name('marks.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\MarkController::class, 'index'])->name('index');
        Route::get('/exam/{exam}/create', [\App\Http\Controllers\Teacher\MarkController::class, 'create'])->name('create');
        Route::post('/exam/{exam}/store', [\App\Http\Controllers\Teacher\MarkController::class, 'store'])->name('store');
        Route::post('/exam/{exam}/submit', [\App\Http\Controllers\Teacher\MarkController::class, 'submit'])->name('submit');
        Route::get('/results', [\App\Http\Controllers\Teacher\MarkController::class, 'results'])->name('results');
    });


});

// Exam status change route with permission-based access - with school context
Route::middleware(['auth', 'school-context', 'permission:manage-exams'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('exams/{exam}/change-status', [\App\Http\Controllers\Admin\ExamController::class, 'changeStatus'])->name('exams.change-status');
});

// Student routes - with school context
Route::middleware(['auth', 'school-context', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/', function() {
        return app(DashboardController::class)->studentDashboard();
    })->name('dashboard');
});

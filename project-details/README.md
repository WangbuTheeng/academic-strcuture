# Academic Management System - Documentation

## 📚 Project Overview

This repository contains comprehensive documentation for the **Academic Management System v3.0** - a web-based student marksheet and reporting system designed specifically for Nepali educational institutions.

## 📋 Documentation Structure

### Core Documentation Files

| File | Description | Content |
|------|-------------|---------|
| **[01-project-overview.md](01-project-overview.md)** | Project introduction and scope | Technology stack, goals, success criteria |
| **[02-student-management.md](02-student-management.md)** | Student information system | Personal details, enrollment, document management |
| **[03-database-schema.md](03-database-schema.md)** | Complete database design | All 21 tables, relationships, constraints |
| **[04-examination-system.md](04-examination-system.md)** | Exam and marking system | Flexible marking schemes, workflows, audit trails |
| **[05-user-roles-permissions.md](05-user-roles-permissions.md)** | Security and access control | Role definitions, permission matrix, security measures |
| **[06-system-features.md](06-system-features.md)** | Advanced features | Setup wizard, promotion engine, backup system |
| **[07-database-migrations.md](07-database-migrations.md)** | Migration implementation | Laravel migrations, execution order, troubleshooting |
| **[TODO.md](TODO.md)** | Implementation roadmap | Phase-by-phase development plan with timelines |

### Legacy Files

| File | Status | Notes |
|------|--------|-------|
| `requirements.md` | ⚠️ Legacy | Original requirements - superseded by new documentation |
| `migrations.md` | ⚠️ Legacy | Original migrations - reorganized into new structure |

## 🎯 System Highlights

### Educational Levels Supported
- **School:** Classes Nursery to 10
- **College:** Classes 11-12 (Science, Management)  
- **Bachelor:** BBS (yearly), BCA (semester-wise)

### Key Features
- ✅ Dynamic exam creation with flexible marking schemes
- ✅ Automatic marksheet generation with Bikram Sambat dates
- ✅ Role-based access control (Admin, Principal, Teacher, Student)
- ✅ Complete audit trail and activity logging
- ✅ Student promotion engine with manual review
- ✅ Backup and restore functionality
- ✅ Document management with verification workflow
- ✅ Grace marks system with proper authorization
- ✅ Re-examination handling
- ✅ Multi-template marksheet generation

### Technology Stack
- **Backend:** Laravel 11
- **Frontend:** Blade Templates + Tailwind CSS + Vanilla JavaScript
- **Database:** MySQL 8.0
- **Authentication:** Laravel Fortify
- **Permissions:** Spatie Laravel Permission
- **PDF Generation:** barryvdh/laravel-dompdf
- **Date Handling:** nepalidate/laravel
- **Activity Logging:** Spatie Laravel Activity Log
- **Backup System:** Spatie Laravel Backup

## 🚀 Quick Start Guide

### 1. System Requirements
- PHP 8.1 or higher
- MySQL 8.0 
- Composer
- Node.js & NPM
- 2GB+ RAM
- 50GB+ storage

### 2. Installation Steps

```bash
# Clone the repository
git clone https://github.com/your-repo/academic-structure.git
cd academic-management-system

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install && npm run build

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed

# Storage linking
php artisan storage:link

# Start development server
php artisan serve
```

### 3. Initial Configuration

1. **Run Setup Wizard**
   - Navigate to `/setup` after installation
   - Configure institution details
   - Set up academic year
   - Create admin account

2. **Configure Institution Settings**
   - Upload school logo and seal
   - Set grading scales
   - Configure marksheet templates

3. **Create User Accounts**
   - Add teachers and assign subjects
   - Set up student accounts
   - Configure role permissions

## 📊 Database Overview

### Core Tables (21 Total)

#### Academic Structure
- `levels` - Educational levels (School, College, Bachelor)
- `faculties` - Academic faculties
- `departments` - Department organization
- `classes` - Class definitions
- `programs` - Academic programs
- `subjects` - Subject catalog

#### Time Management
- `academic_years` - Academic year definitions
- `semesters` - Semester/term management

#### Student Management
- `students` - Complete student information
- `student_enrollments` - Yearly enrollment records
- `student_subjects` - Flexible subject enrollment
- `student_documents` - Document management

#### Examination System
- `exams` - Examination definitions
- `marks` - Student marks and grades
- `grading_scales` - Configurable grading systems

#### User Management
- `users` - System users (Laravel default)
- `teacher_subjects` - Teacher-subject assignments

#### System Administration
- `mark_logs` - Mark change audit trail
- `activity_log` - System-wide activity logging
- `institute_settings` - Institution configuration
- `backups` - Backup management

## 🔐 Security Features

### Authentication & Authorization
- Role-based access control (RBAC)
- Secure password policies
- Session management
- Two-factor authentication (optional)

### Data Protection
- Complete audit trails
- Mark modification logging
- User activity tracking
- Secure file uploads

### System Security
- CSRF protection
- SQL injection prevention
- XSS protection
- Input validation and sanitization

## 📈 Performance Optimization

### Database Optimization
- Strategic indexing for performance
- Query optimization
- Foreign key constraints
- Normalized data structure

### Caching Strategy
- Redis for session storage
- Database query caching
- View caching for reports
- API response caching

### Background Processing
- Queue system for heavy operations
- Asynchronous PDF generation
- Bulk data processing
- Email notifications

## 🎨 User Interface

### Design Principles
- Mobile-first responsive design
- Clean, intuitive interfaces
- Accessibility compliance
- Print-optimized layouts

### User Experience
- Role-specific dashboards
- Contextual navigation
- Server-side validation with JavaScript enhancement
- Traditional form-based interactions

## 📋 Implementation Checklist

### Phase 1: Core Setup ✅
- [x] Database migration execution
- [x] Basic user authentication
- [x] Student information management
- [x] Academic structure setup

### Phase 2: Examination System ✅
- [x] Exam creation and management
- [x] Mark entry interfaces
- [x] Grade calculation system
- [x] Result approval workflow

### Phase 3: Advanced Features ✅
- [x] Marksheet generation
- [x] Backup and restore system
- [x] Student promotion engine
- [x] Analytics and reporting

### Phase 4: Testing & Quality Assurance ✅
- [x] Unit testing implementation
- [x] Feature testing suite
- [x] User acceptance testing
- [x] Performance testing
- [x] Security testing

### Phase 5: Production Deployment
- [ ] Security hardening
- [ ] Performance optimization
- [ ] User training
- [ ] Go-live support

## 🤝 Contributing

### Development Guidelines
1. Follow Laravel coding standards
2. Write comprehensive tests
3. Document all new features
4. Maintain backward compatibility
5. Update documentation accordingly

### Testing Requirements ✅
- [x] Unit tests for all models (60+ tests)
- [x] Feature tests for critical workflows (40+ tests)
- [x] User acceptance tests for business processes (15+ scenarios)
- [x] Performance tests for heavy operations (12+ tests)
- [x] Security tests for vulnerability assessment (20+ tests)

### Test Execution
```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage

# Run performance tests
php artisan test tests/Feature/PerformanceTest.php

# Run security tests
php artisan test tests/Feature/SecurityTest.php
```

## 📞 Support & Maintenance

### Documentation Updates
This documentation is actively maintained and updated with each system release. For the latest version, always refer to the repository's main branch.

### Issue Reporting
Report bugs, feature requests, or documentation issues through the project's issue tracking system.

### Version History
- **v3.0** - Complete system rewrite with modern Laravel
- **v2.x** - Legacy PHP system (deprecated)
- **v1.x** - Initial Excel-based system (obsolete)

---

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🙏 Acknowledgments

- Laravel Framework team for the excellent foundation
- Spatie team for their outstanding Laravel packages
- Nepali Date package contributors
- Educational institutions providing requirements and feedback

---

*This documentation provides a complete guide to understanding, implementing, and maintaining the Academic Management System. For specific implementation details, refer to the individual documentation files listed above.*

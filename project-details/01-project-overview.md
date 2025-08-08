# Academic Management System - Project Overview

## Project Information

**Project Name:** Web-Based Student Marksheet & Reporting System  
**Version:** 3.0 (Final, Production-Ready)  
**Date:** April 5, 2025  
**Institution Type:** Multi-Level (School, College, Bachelor's)  

## Technology Stack

- **Backend Framework:** Laravel 11
- **Frontend:** Blade Templates + Tailwind CSS
- **JavaScript:** Vanilla JavaScript (minimal, for enhancements)
- **Database:** MySQL 8.0
- **PDF Generation:** barryvdh/laravel-dompdf
- **Date Handling:** nepalidate/laravel (Bikram Sambat support)
- **Permissions:** Spatie Laravel Permission
- **Activity Logging:** Spatie Laravel Activity Log
- **Backup System:** Spatie Laravel Backup

## Target Users

1. **Admin** - Full system control and management
2. **Teachers** - Mark entry and class management
3. **Principal** - Result approval and oversight
4. **Students** - View marks and download marksheets

## System Overview

This system is a secure, scalable, and flexible academic management platform designed specifically for Nepali educational institutions.

### Educational Levels Supported

| Level | Classes | Programs |
|-------|---------|----------|
| **School** | Nursery to 10 | General Education |
| **College** | 11-12 | Science, Management |
| **Bachelor's** | Undergraduate | BBS (yearly), BCA (semester-wise) |

### Core Features

#### ✅ Academic Management
- Dynamic exam creation with custom marking schemes
- Flexible grading systems (percentage, GPA, division)
- Subject-wise mark entry and calculation
- Automatic grade and GPA computation
- Re-exam and grace marks handling

#### ✅ Student Management
- Comprehensive student profiles
- Yearly enrollment and promotion system
- Roll number auto-generation
- Academic standing tracking
- Document management (citizenship, certificates, photos)

#### ✅ Reporting & Documentation
- Automatic marksheet generation (PDF)
- Bikram Sambat (BS) date support
- Multiple marksheet templates
- Bulk PDF export capabilities
- Print-ready layouts with institutional branding

#### ✅ Security & Compliance
- Role-based access control
- Complete audit trail
- Activity logging for all critical actions
- Data integrity validation
- Automated backup and restore

#### ✅ Administrative Tools
- Setup wizard for initial configuration
- User management and role assignment
- Academic year and semester management
- Grading scale configuration
- System settings and customization

### Key Differentiators

1. **Nepali Context Aware**
   - Bikram Sambat date system
   - Local academic structure support
   - Nepali institutional requirements

2. **Flexible Marking System**
   - Support for various exam types (80+20, 75+25, 100 theory)
   - Custom assessment components
   - Practical and theory mark separation

3. **Real-world Edge Cases**
   - Re-examination handling
   - Grace marks with proper authorization
   - Mid-year subject changes
   - Student transfer management

4. **Production Ready**
   - Complete audit trail
   - Data backup and recovery
   - Role-based security
   - Scalable architecture

## Out of Scope (Current Version)

The following features are intentionally excluded to maintain focus on core academic functionality:

❌ **Financial Management**
- Fee collection and billing
- Salary management
- Financial reporting

❌ **Attendance System**
- Daily attendance tracking
- Attendance reports
- Leave management

❌ **Online Examination**
- Online test creation
- Remote proctoring
- Digital answer sheets

❌ **Communication Features**
- Parent portal
- SMS/Email notifications
- Chat systems

> **Note:** These features can be integrated via API in Phase 2 or as separate modules.

## Success Criteria

The system will be considered successful when:

1. ✅ Admin can create exams with custom marking schemes
2. ✅ Marksheets adapt layout based on exam configuration
3. ✅ Roll numbers are auto-generated per class/year
4. ✅ Bikram Sambat dates display correctly in all outputs
5. ✅ PDFs are print-ready with institutional branding
6. ✅ Teachers can only access their assigned subjects
7. ✅ Students can view and download their marksheets
8. ✅ Principal approval workflow functions correctly
9. ✅ System prevents unauthorized mark modifications
10. ✅ Grace marks require proper authorization and logging
11. ✅ Student promotion system works with manual review
12. ✅ Backup and restore functions operate from UI
13. ✅ Setup wizard guides first-time configuration
14. ✅ Complete audit trail for all critical operations

## Project Goals

This system aims to:

- **Replace** error-prone Excel sheets and paper-based workflows
- **Provide** a modern, automated, web-based academic management solution
- **Ensure** data accuracy, compliance, and usability for Nepali schools
- **Support** real-world academic scenarios and edge cases
- **Maintain** complete audit trail for transparency and accountability
- **Enable** efficient academic administration and reporting

---

*This document serves as the foundation for the Academic Management System project, outlining the scope, objectives, and key requirements for successful implementation.*

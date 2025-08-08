# User Acceptance Testing Scenarios
## Academic Management System v3.0

**Date:** July 30, 2025  
**Version:** 3.0  
**Testing Phase:** User Acceptance Testing  

---

## 📋 **Test Overview**

### **Scope**
This document outlines comprehensive User Acceptance Testing (UAT) scenarios for the Academic Management System. These tests validate that the system meets business requirements and user expectations.

### **Test Objectives**
- Verify system functionality from end-user perspective
- Validate business workflows and processes
- Ensure system usability and user experience
- Confirm data integrity and security measures
- Test system performance under realistic conditions

### **Test Environment**
- **URL:** http://localhost/academic-structure/public
- **Database:** MySQL 8.0 (Test Environment)
- **Browser:** Chrome, Firefox, Safari, Edge
- **Users:** Admin, Principal, Teacher, Student roles

---

## 🎯 **Test Scenarios by User Role**

### **SCENARIO 1: System Administrator Workflow**

#### **UAT-001: Initial System Setup**
**Objective:** Verify admin can complete initial system configuration

**Pre-conditions:**
- Fresh system installation
- Admin account created

**Test Steps:**
1. Login as system administrator
2. Navigate to Setup Wizard (/setup)
3. Complete institution information:
   - Institution name: "Test Academic Institution"
   - Address: "123 Test Street, Test City"
   - Phone: "+977-1-1234567"
   - Email: "admin@testinstitution.edu.np"
4. Upload institution logo and seal
5. Configure academic year 2081-82
6. Set up grading scale (A+ to F)
7. Create educational levels (School, College)
8. Complete setup wizard

**Expected Results:**
- ✅ Setup wizard completes successfully
- ✅ Institution settings saved correctly
- ✅ Academic year created and set as current
- ✅ Grading scale configured properly
- ✅ Educational levels created
- ✅ Dashboard accessible with setup complete status

**Acceptance Criteria:**
- Setup process takes less than 10 minutes
- All required fields validated properly
- Success messages displayed at each step
- System redirects to dashboard upon completion

---

#### **UAT-002: User Management**
**Objective:** Verify admin can manage user accounts and permissions

**Test Steps:**
1. Navigate to User Management
2. Create new teacher account:
   - Name: "John Teacher"
   - Email: "john.teacher@test.edu"
   - Role: Teacher
   - Assign subjects: Mathematics, Physics
3. Create new principal account:
   - Name: "Jane Principal"
   - Email: "jane.principal@test.edu"
   - Role: Principal
4. Test user permissions:
   - Verify teacher can access assigned subjects only
   - Verify principal can access all academic data
5. Deactivate and reactivate user account
6. Reset user password

**Expected Results:**
- ✅ Users created with correct roles and permissions
- ✅ Email notifications sent for account creation
- ✅ Permission restrictions work correctly
- ✅ Account status changes reflected immediately
- ✅ Password reset functionality works

---

### **SCENARIO 2: Academic Structure Setup**

#### **UAT-003: Class and Subject Management**
**Objective:** Verify academic structure can be configured correctly

**Test Steps:**
1. Create departments:
   - Science Department
   - Management Department
2. Create classes:
   - Class 10 (Science)
   - Class 11 (Science)
   - Class 12 (Management)
3. Create subjects:
   - Mathematics (Science Department)
   - Physics (Science Department)
   - Accountancy (Management Department)
4. Assign subjects to classes
5. Set subject marking schemes:
   - Theory: 80 marks
   - Practical: 20 marks
   - Total: 100 marks

**Expected Results:**
- ✅ Departments created successfully
- ✅ Classes linked to correct departments
- ✅ Subjects assigned to appropriate classes
- ✅ Marking schemes configured properly
- ✅ Academic structure displays correctly in navigation

---

### **SCENARIO 3: Student Management Workflow**

#### **UAT-004: Student Enrollment Process**
**Objective:** Verify complete student enrollment workflow

**Test Steps:**
1. Navigate to Student Management
2. Add new student:
   - Name: "Ram Bahadur Thapa"
   - Roll Number: "2081001"
   - Class: Class 10 (Science)
   - Contact: "+977-9841234567"
   - Email: "ram.thapa@student.test"
   - Address: "Kathmandu, Nepal"
   - Guardian: "Shyam Bahadur Thapa"
3. Upload student documents:
   - Citizenship certificate
   - Previous academic records
   - Passport-size photo
4. Enroll student in subjects:
   - Mathematics
   - Physics
   - Chemistry
   - English
   - Nepali
5. Generate student ID card
6. Test student login credentials

**Expected Results:**
- ✅ Student record created with all information
- ✅ Documents uploaded and verified
- ✅ Subject enrollment completed
- ✅ Student ID card generated with photo
- ✅ Student can login and access dashboard
- ✅ Student sees enrolled subjects and class information

---

### **SCENARIO 4: Examination Management**

#### **UAT-005: Exam Creation and Management**
**Objective:** Verify complete exam lifecycle management

**Test Steps:**
1. Create new examination:
   - Name: "First Terminal Examination 2081"
   - Type: Terminal Exam
   - Academic Year: 2081-82
   - Class: Class 10 (Science)
   - Subject: Mathematics
   - Total Marks: 100 (Theory: 80, Practical: 20)
   - Start Date: Next week
   - End Date: Two weeks from now
   - Submission Deadline: Three weeks from now
2. Schedule exam for multiple subjects
3. Change exam status from Draft to Scheduled
4. Notify teachers about exam schedule
5. Monitor exam progress

**Expected Results:**
- ✅ Exam created with all details
- ✅ Multiple subjects scheduled correctly
- ✅ Status changes reflected in system
- ✅ Email notifications sent to teachers
- ✅ Exam calendar updated
- ✅ Exam appears in teacher dashboard

---

#### **UAT-006: Mark Entry and Approval Workflow**
**Objective:** Verify complete mark entry and approval process

**Test Steps:**
1. Login as teacher (Mathematics)
2. Navigate to exam mark entry
3. Enter marks for all students:
   - Theory marks (out of 80)
   - Practical marks (out of 20)
   - Verify automatic total calculation
4. Save marks as draft
5. Submit marks for approval
6. Login as principal/admin
7. Review submitted marks
8. Approve marks with comments
9. Verify marks are locked after approval
10. Test mark modification restrictions

**Expected Results:**
- ✅ Mark entry interface user-friendly
- ✅ Automatic calculations work correctly
- ✅ Draft saving functionality works
- ✅ Submission workflow operates properly
- ✅ Approval process functions correctly
- ✅ Approved marks cannot be modified
- ✅ Audit trail maintained for all changes

---

### **SCENARIO 5: Result Generation and Publishing**

#### **UAT-007: Marksheet Generation**
**Objective:** Verify marksheet generation and customization

**Test Steps:**
1. Navigate to Result Management
2. Select completed exam
3. Generate individual marksheet:
   - Student: Ram Bahadur Thapa
   - Template: Modern template
   - Include institution logo
   - Add principal signature
4. Generate class-wise marksheets
5. Test different templates:
   - Modern template
   - Classic template
   - Minimal template
6. Export marksheets as PDF
7. Test print functionality

**Expected Results:**
- ✅ Individual marksheet generated correctly
- ✅ All student information displayed
- ✅ Marks and grades calculated properly
- ✅ Institution branding applied
- ✅ Multiple templates work correctly
- ✅ PDF export functions properly
- ✅ Print layout optimized

---

#### **UAT-008: Result Publication**
**Objective:** Verify result publication and student access

**Test Steps:**
1. Publish results for Class 10 Mathematics
2. Configure result visibility settings
3. Test student result access:
   - Login as student
   - View published results
   - Download marksheet
   - Check grade and percentage
4. Test parent/guardian access
5. Verify result security and privacy
6. Test bulk result publication

**Expected Results:**
- ✅ Results published successfully
- ✅ Students can access their results
- ✅ Marksheet download works
- ✅ Grades and calculations correct
- ✅ Privacy settings enforced
- ✅ Bulk operations function properly

---

### **SCENARIO 6: Analytics and Reporting**

#### **UAT-009: Performance Analytics**
**Objective:** Verify analytics dashboard and reporting features

**Test Steps:**
1. Navigate to Analytics Dashboard
2. Review overview statistics:
   - Total students
   - Pass percentage
   - Average marks
   - Grade distribution
3. Generate performance reports:
   - Class-wise performance
   - Subject-wise analysis
   - Student progress tracking
   - Trend analysis
4. Export analytics data:
   - PDF reports
   - Excel spreadsheets
   - CSV data files
5. Test filtering and date range selection

**Expected Results:**
- ✅ Analytics dashboard loads quickly
- ✅ Statistics calculated correctly
- ✅ Reports generated accurately
- ✅ Export functionality works
- ✅ Filters apply correctly
- ✅ Visual charts display properly

---

### **SCENARIO 7: System Administration**

#### **UAT-010: Backup and Restore**
**Objective:** Verify backup and restore functionality

**Test Steps:**
1. Navigate to Backup Management
2. Create manual backup:
   - Include all data
   - Add backup description
   - Verify backup completion
3. Schedule automatic backups:
   - Daily at 2:00 AM
   - Retain 30 days
4. Test backup download
5. Simulate data loss scenario
6. Restore from backup
7. Verify data integrity after restore

**Expected Results:**
- ✅ Manual backup created successfully
- ✅ Scheduled backups configured
- ✅ Backup files downloadable
- ✅ Restore process works correctly
- ✅ Data integrity maintained
- ✅ System functions normally after restore

---

## 📊 **Performance Testing Scenarios**

### **UAT-011: System Performance Under Load**
**Objective:** Verify system performance with realistic user load

**Test Conditions:**
- 50 concurrent users
- 1000 student records
- 100 exams with marks
- Multiple file uploads

**Test Steps:**
1. Simulate concurrent user logins
2. Test simultaneous mark entry by multiple teachers
3. Generate multiple marksheets simultaneously
4. Test database performance with large datasets
5. Monitor system response times
6. Test file upload performance

**Performance Criteria:**
- ✅ Page load time < 3 seconds
- ✅ Mark entry response < 2 seconds
- ✅ Marksheet generation < 10 seconds
- ✅ File upload < 30 seconds (10MB)
- ✅ System remains stable under load
- ✅ No data corruption occurs

---

## 🔒 **Security Testing Scenarios**

### **UAT-012: Security and Access Control**
**Objective:** Verify security measures and access controls

**Test Steps:**
1. Test unauthorized access attempts
2. Verify role-based permissions
3. Test session management
4. Verify data encryption
5. Test SQL injection prevention
6. Test XSS protection
7. Verify file upload security
8. Test password policies

**Security Criteria:**
- ✅ Unauthorized access blocked
- ✅ Permissions enforced correctly
- ✅ Sessions expire appropriately
- ✅ Sensitive data encrypted
- ✅ Injection attacks prevented
- ✅ XSS attacks blocked
- ✅ File uploads validated
- ✅ Strong password policies enforced

---

## ✅ **Acceptance Criteria Summary**

### **Functional Requirements**
- [ ] All user workflows complete successfully
- [ ] Data integrity maintained throughout
- [ ] Calculations and grades accurate
- [ ] Reports and analytics correct
- [ ] Export/import functions properly

### **Non-Functional Requirements**
- [ ] System performance meets criteria
- [ ] Security measures effective
- [ ] User interface intuitive
- [ ] System stability maintained
- [ ] Error handling appropriate

### **Business Requirements**
- [ ] Nepali education system compliance
- [ ] Bikram Sambat date support
- [ ] Multi-level education support
- [ ] Flexible grading systems
- [ ] Comprehensive audit trails

---

## 📝 **Test Execution Checklist**

- [ ] Test environment prepared
- [ ] Test data created
- [ ] User accounts configured
- [ ] Test scenarios executed
- [ ] Results documented
- [ ] Issues identified and logged
- [ ] Acceptance criteria verified
- [ ] Sign-off obtained

**Testing Status:** Ready for Execution  
**Estimated Duration:** 40 hours  
**Required Resources:** 4 testers, 1 week  

---

*This document serves as the comprehensive guide for User Acceptance Testing of the Academic Management System v3.0.*

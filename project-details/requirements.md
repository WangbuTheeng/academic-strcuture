📚 Academic Management System – Final Requirements Specification (v3.0)
Project: Web-Based Student Marksheet & Reporting System
Institution: Multi-Level (School, College, Bachelor's)
Tech Stack: Laravel 11, Tailwind CSS, Livewire / Alpine.js, MySQL
Target Users: Admin, Teachers, Principal, Students
Version: 3.0 (Final, Production-Ready)
Date: April 5, 2025

🎯 1. Overview
This system is a secure, scalable, and flexible academic management platform designed for Nepali institutions offering:

School: Classes Nursery to 10
College: Classes 11–12 (Science, Management)
Bachelor’s: BBS (yearly), BCA (semester-wise)
It replaces error-prone Excel sheets and paper-based workflows with a modern, automated, web-based solution that supports:

Dynamic exams with custom marking schemes (e.g., 80+20, 75+25, 100 theory)
Automatic marksheet generation (PDF) with Bikram Sambat (BS) date
Role-based access (Admin, Teacher, Principal, Student)
Yearly student promotion with roll number reset
Audit trail, data integrity, and backup
Real-world edge cases: re-exams, grace marks, subject changes
❗ Out of Scope: 

Financial management (fees, salaries)
Attendance tracking
Online exams
Parent communication (Phase 2)
This is a focused academic engine — built for accuracy, compliance, and usability in real Nepali schools.

🧑‍🎓 2. Student Information
2.1 Personal Details
student_id (unique identifier)
first_name, last_name
date_of_birth
gender (Male, Female, Other)
blood_group
religion, caste
nationality
mother_tongue
photo_url (required, stored in storage/student-photos/)
2.2 Contact & Guardian
phone, email
address (permanent)
temporary_address (optional, for current location)
emergency_contact_name, emergency_contact_phone, emergency_contact_relation
guardian_name, guardian_relation (Father, Mother, Guardian)
guardian_phone, guardian_email
2.3 Legal & Documentation
citizenship_number → Optional (only required if age ≥ 16)
citizenship_issue_date, citizenship_issue_district
citizenship_document → file path (scanned copy)
previous_school_name
transfer_certificate_no, transfer_certificate_date
migration_certificate_no → for +2/Bachelor programs
disability_status → none, visual, hearing, mobility, learning, other
special_needs → text (e.g., "Extra time in exams", "Wheelchair access")
2.4 Academic Enrollment (Yearly)
admission_number → Permanent unique ID (e.g., ADM-2078-001)
admission_date → When first joined institution
academic_year_id → e.g., 2081, 2082
class_id → e.g., Class 9, BCA1
program_id → Science, BCA, BBS
roll_no → Auto-generated per class/year: 9A-01, BCA1-005
section → A, B, etc.
enrollment_date → When enrolled in this academic year
status → Active, Inactive, Graduated, Transferred
academic_standing → Good, Probation, Repeat, Dismissed
backlog_count → Number of failed subjects carried forward
✅ All enrollment data is stored in student_enrollments table — never modify directly. 

🗃️ 3. Database Schema (Final v3.0)
3.1 Core Tables
levels
School, College, Bachelor
faculties
Faculty of Science, Management, etc.
departments
Computer Dept, Science Dept
classes
Class 9, BCA1, BBS2
programs
Science, BCA, BBS
academic_years
2081, 2082 (with start/end)
semesters
Semester 1, Yearly
subjects
Subject rules: max assess/theory/practical
program_subjects
Links subjects to programs with credit hours, semester, year
student_subjects
Per-student subject enrollment (for flexibility)
exams
Terminal, Quiz, Assessment (with flexible marks)
grading_scales
Configurable grade bands per program/year
students
All student records
student_enrollments
Yearly enrollment, roll number, academic standing
users
Admin, Teacher, Principal, Student
teacher_subjects
Which teacher teaches which subject/class/year
marks
Marks per student, subject, exam
mark_logs
Audit trail for mark changes
institute_settings
School name, logo, seal, marksheet style
activity_log
Audit trail for critical actions
backups
Backup history and restore
student_documents
File paths for citizenship, TC, photo, etc.
3.2 Key Fields in exams Table
id
bigint
Primary Key
name
varchar
"Terminal Exam", "Monthly Quiz"
exam_type
enum
"assessment", "terminal", "quiz", "project"
max_marks
int
Total marks (e.g., 100)
theory_max
int
e.g., 75, 80
practical_max
int
e.g., 25, 20
assess_max
int
e.g., 20
has_practical
boolean
true/false
academic_year_id
bigint
Foreign Key
semester_id
bigint
Optional
class_id
,
program_id
,
subject_id
bigint
Optional (for scoping)
grading_scale_id
bigint
Optional (override default)
submission_deadline
datetime
When marks must be submitted
result_status
enum
"draft", "scheduled", "ongoing", "submitted", "approved", "published", "locked"
is_locked
boolean
true if no edits allowed
approval_date
timestamp
When principal approved
created_by
bigint
User ID
created_at
,
updated_at
timestamp
🔒 Once published, no mark edits without admin override. 

3.3 Key Fields in marks Table
id
bigint
Primary Key
student_id
,
subject_id
,
exam_id
bigint
Foreign Keys
assess_marks
decimal
0–max_assess
theory_marks
decimal
0–theory_max
practical_marks
decimal
0–practical_max
total
decimal
Computed: sum of components
percentage
decimal
(total / max) × 100
grade
varchar
"A+", "B"
gpa
decimal
4.0, 3.6
result
enum
"Pass", "Fail", "Incomplete"
status
enum
"draft", "final"
is_reexam
boolean
true if re-exam
original_exam_id
bigint
Links to failed exam
grace_marks
decimal
e.g., 2.00 added
grace_reason
text
"Borderline", "Medical Certificate"
carry_forward_reason
text
"Failed in 2080"
created_by
,
updated_by
bigint
User IDs
created_at
,
updated_at
timestamp
🔐 Unique constraint: (student_id, subject_id, exam_id) 

3.4 New Table: student_subjects
sql


1
2
3
4
5
⌄
id
student_enrollment_id → FK
subject_id → FK
date_added → DATE
status → ENUM('active', 'dropped')
✅ Allows mid-year subject changes, extra classes, or dropping. 

3.5 New Table: activity_log
sql


1
2
3
4
5
6
7
8
⌄
id
subject_type → e.g., App\Models\Mark
subject_id
action → 'mark_updated', 'exam_deleted', 'result_published'
description → "Mark updated from 65 to 70"
user_id → who did it
ip_address, user_agent → where from
created_at
✅ Full audit trail for compliance and security. 

3.6 New Table: backups
sql


1
2
3
4
5
6
⌄
id
path → backups/2081-04-05.sql.gz
size → in MB
type → 'manual', 'auto'
created_by → user_id
created_at
✅ Admin can trigger, view, and restore backups. 

3.7 New Table: student_documents
sql


1
2
3
4
5
6
7
⌄
id
student_id
doc_type → 'citizenship', 'birth_cert', 'transfer_cert', 'migration', 'photo'
file_path
uploaded_by
is_verified → boolean
uploaded_at
✅ Structured document storage. 

🔄 4. Functional Requirements
4.1 Dynamic Exam & Result Workflow
Admin creates exam with custom marks
After entry:
Teacher submits → status: submitted
Principal reviews → approved → published
Once published:
is_locked = true
No edits without admin override
Marksheets available
4.2 Grace Marks Management
Only Principal or Admin can apply
Must enter grace_reason
Logged in mark_logs and activity_log
Displayed on marksheet: Grace: +2.00
4.3 Yearly Student Promotion
At year-end, run Promotion Engine:
Auto-suggest promotion based on result
Manual review: remove leavers, add new
Assign new class, program, section
Generate new roll numbers
Historical data preserved
✅ No direct class_id update — always new enrollment. 

4.4 Backup & Restore
Daily automated backup (cron)
Admin can:
Click "Create Backup Now"
View backup history
Restore from UI (with confirmation)
Uses spatie/laravel-backup
4.5 Setup Wizard
On first login:

School name, address, logo
Academic year setup
Default grading scale
Admin account creation
Redirect to dashboard
✅ No manual DB setup required. 

4.6 Document Management
Upload:
Citizenship
Birth certificate
Transfer/Migration cert
Photo
View and verify in admin panel
Link to student profile
4.7 Automatic Calculations
total = assess + theory + practical
percentage = (total / exam_max) * 100
grade, gpa → from grading_scale
result = Pass if all subjects ≥ pass mark
4.8 Marksheet Generation
Layout adapts to exam type
Includes:
Student info, class, program
Subject-wise breakdown
Total, %, Grade, GPA
Result, Remarks
Date (AD and BS)
Teacher & Principal signature
Supports multiple templates (configurable)
4.9 PDF Export
Single student: /marksheets/{id}/pdf
Bulk export: One click (queued)
Uses barryvdh/laravel-dompdf
Print-ready with logo and BS date
4.10 Nepali (Bikram Sambat) Date
Display BS date in marksheets, reports, UI
Use package: nepalidate/laravel
Example:
Apr 5, 2025 (2081-12-22 BS)
🔐 5. User Roles & Permissions
Admin
Full CRUD, manage users, assign teachers, override roll numbers, apply grace marks, restore backup, run setup, audit logs
Teacher
Enter marks for assigned subjects, submit marks, view class reports
Principal
Approve/publish results, view all, print reports, apply grace marks (with reason), view audit log
Student
View own marks, download marksheets, track progress
🔐 Authentication: Laravel Fortify
🔐 Authorization: Spatie Laravel Permission 

🎨 6. Frontend Requirements
Tailwind CSS for responsive, clean UI
Livewire for dynamic forms (mark entry, promotion)
Chart.js for analytics dashboards
Mobile-first design
Print-optimized marksheet layout
BS/AD date toggle (optional)
🧪 7. Testing & Validation
Laravel Feature Tests for:
Mark entry validation
GPA and grade calculation
PDF generation
Role-based access
Promotion logic
Seeders for:
Academic years (2081, 2082)
Sample students, subjects, exams
Validation Rules:
Marks ≤ max allowed
Required fields enforced
Unique constraints at DB level
🚀 8. Deployment
Local: Laravel Sail (Docker) or Valet
Production: Laravel Forge, Vapor, or shared hosting
Domain: school.edu.np
SSL: Let’s Encrypt (HTTPS)
Backup: Daily DB export + UI restore
Queue: Redis or database for PDF generation
📥 9. Deliverables
Complete Laravel project with:
Migrations for all 20+ tables
Models with relationships
Controllers (CRUD, marks, reports, promotion, backup)
Tailwind views (Livewire)
PDF generation with BS date
Role-based middleware
Seeder files with sample data
Admin dashboard (optional: Filament PHP)
API endpoints (for future mobile app or finance integration)
🚫 10. Out of Scope
Fees & Billing
Belongs in finance system
Attendance
Complex; can be added later
Online Exams
Out of scope
Parent Portal Chat
Requires real-time messaging
Teacher Salary
Requires payroll compliance
✅ These can be integrated via API in Phase 2. 

✅ 11. Success Criteria
Admin can create a 20-mark quiz with theory only
Marksheet adapts layout based on exam type
Roll numbers auto-generated per class/year
BS date displayed correctly in marksheets
PDFs are print-ready with logo and signature
Teachers can only enter marks for assigned subjects
Students can view/download their marksheets
Principal can approve and publish results
System prevents edits after result publication
Grace marks require reason and are logged
Admin can run promotion with manual review
Backup can be created and restored from UI
Setup wizard guides first-time admin
Audit log tracks all critical actions
📂 12. Next Steps
Run: laravel new academic-system --jet --stack=livewire
Install packages:
bash


1
2
3
4
5
composer require barryvdh/laravel-dompdf
composer require nepalidate/laravel
composer require spatie/laravel-permission
composer require spatie/laravel-activitylog
composer require spatie/laravel-backup
Create and run all migrations
Seed sample data
Build setup wizard
Implement promotion engine
Generate first marksheet PDF
Prepared by: [Your Name]
Institution: [School/College Name]
Contact: admin@yourschool.edu.np
Website: https://yourschool.edu.np

✅ This is the final, complete, and production-ready specification.
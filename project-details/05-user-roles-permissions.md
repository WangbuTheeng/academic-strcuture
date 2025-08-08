# User Roles & Permissions System

## Overview

The system implements a comprehensive role-based access control (RBAC) system using Spatie Laravel Permission package, providing granular control over user actions and data access.

## User Roles

### 1. Admin Role

**Description:** System administrator with full access to all features and data.

#### Core Responsibilities
- Complete system configuration and management
- User account creation and role assignment
- Data backup and restoration
- System maintenance and troubleshooting
- Override capabilities for emergency situations

#### Permissions

##### Student Management
- ✅ Create, read, update, delete students
- ✅ Manage student enrollments across all years
- ✅ Override roll number assignments
- ✅ Handle student transfers and status changes
- ✅ Access all student documents and verification
- ✅ Bulk student operations (import/export)

##### Academic Management
- ✅ Create and manage academic years
- ✅ Configure semesters and terms
- ✅ Set up classes, programs, and subjects
- ✅ Manage grading scales and configurations
- ✅ Override academic rules when necessary

##### Examination System
- ✅ Create, modify, and delete exams
- ✅ Override exam status and workflow
- ✅ Access all marks and results
- ✅ Apply grace marks without limits
- ✅ Unlock published results for corrections
- ✅ Generate system-wide reports

##### User Management
- ✅ Create and manage all user accounts
- ✅ Assign and modify user roles
- ✅ Reset passwords and manage authentication
- ✅ View user activity and audit logs
- ✅ Suspend or activate user accounts

##### System Administration
- ✅ Configure institute settings
- ✅ Manage system backups
- ✅ Access complete audit trails
- ✅ System maintenance and updates
- ✅ Database operations and migrations

### 2. Principal Role

**Description:** Academic head with oversight of educational processes and result approval authority.

#### Core Responsibilities
- Academic oversight and quality assurance
- Result approval and publication
- Grace marks authorization
- Academic policy implementation
- Institutional reporting

#### Permissions

##### Academic Oversight
- ✅ View all academic data and reports
- ✅ Monitor exam progress and completion
- ✅ Access class and program performance analytics
- ✅ Review teacher performance metrics
- ✅ Generate institutional reports

##### Result Management
- ✅ Approve submitted examination results
- ✅ Reject results requiring corrections
- ✅ Publish approved results to students
- ✅ Access all student marks and grades
- ✅ Generate marksheets and transcripts

##### Grace Marks Authority
- ✅ Apply grace marks up to 5 marks per subject
- ✅ Review and approve grace mark requests
- ✅ Provide justification for grace marks
- ✅ Monitor grace mark usage patterns

##### Student Affairs
- ✅ View all student information
- ✅ Approve student status changes
- ✅ Handle academic disciplinary matters
- ✅ Review student academic standing
- ❌ Direct student data modification (read-only)

##### Reporting & Analytics
- ✅ Generate comprehensive academic reports
- ✅ Access performance analytics dashboards
- ✅ Export data for external reporting
- ✅ View audit logs for academic activities

### 3. Teacher Role

**Description:** Subject instructors responsible for mark entry and class management within their assigned scope.

#### Core Responsibilities
- Mark entry for assigned subjects
- Class performance monitoring
- Student academic progress tracking
- Subject-specific reporting

#### Permissions

##### Mark Entry
- ✅ Enter marks for assigned subjects only
- ✅ Modify marks before submission deadline
- ✅ Submit marks for principal approval
- ✅ View mark entry history for own subjects
- ❌ Access marks for non-assigned subjects

##### Student Information
- ✅ View student information for assigned classes
- ✅ Access student academic history
- ✅ View student enrollment details
- ❌ Modify student personal information
- ❌ Change student enrollment status

##### Class Management
- ✅ View class rosters for assigned subjects
- ✅ Generate class performance reports
- ✅ Track student attendance (if enabled)
- ✅ Access subject-specific analytics

##### Examination Access
- ✅ View exam schedules and details
- ✅ Access marking schemes for assigned subjects
- ✅ Submit marks within deadlines
- ❌ Create or modify exam configurations
- ❌ Change exam status or workflow

### 4. Student Role

**Description:** Students with access to their own academic information and results.

#### Core Responsibilities
- View personal academic information
- Access examination results
- Download marksheets and certificates
- Track academic progress

#### Permissions

##### Personal Information
- ✅ View own student profile
- ✅ View enrollment history
- ✅ Access own academic records
- ❌ Modify personal information
- ❌ Access other students' data

##### Academic Results
- ✅ View own marks and grades
- ✅ Download personal marksheets
- ✅ Access result history
- ✅ View academic standing status
- ❌ Access unpublished results

##### Progress Tracking
- ✅ View subject enrollment
- ✅ Track academic progress
- ✅ Access performance analytics
- ✅ View attendance records (if enabled)

## Permission Matrix

| Feature | Admin | Principal | Teacher | Student |
|---------|-------|-----------|---------|---------|
| **Student Management** |
| Create Students | ✅ | ❌ | ❌ | ❌ |
| View All Students | ✅ | ✅ | Assigned Only | Own Only |
| Modify Students | ✅ | ❌ | ❌ | ❌ |
| Delete Students | ✅ | ❌ | ❌ | ❌ |
| **Academic Setup** |
| Manage Academic Years | ✅ | ❌ | ❌ | ❌ |
| Configure Classes | ✅ | ❌ | ❌ | ❌ |
| Setup Subjects | ✅ | ❌ | ❌ | ❌ |
| Grading Scales | ✅ | ❌ | ❌ | ❌ |
| **Examination System** |
| Create Exams | ✅ | ❌ | ❌ | ❌ |
| Enter Marks | ✅ | ❌ | Assigned Only | ❌ |
| Approve Results | ✅ | ✅ | ❌ | ❌ |
| Publish Results | ✅ | ✅ | ❌ | ❌ |
| View Results | ✅ | ✅ | Assigned Only | Own Only |
| **Grace Marks** |
| Apply Grace Marks | ✅ | ✅ (Limited) | ❌ | ❌ |
| **Reports & Analytics** |
| System Reports | ✅ | ✅ | Subject Only | Own Only |
| Export Data | ✅ | ✅ | Limited | Own Only |
| **User Management** |
| Create Users | ✅ | ❌ | ❌ | ❌ |
| Assign Roles | ✅ | ❌ | ❌ | ❌ |
| **System Administration** |
| System Settings | ✅ | ❌ | ❌ | ❌ |
| Backup/Restore | ✅ | ❌ | ❌ | ❌ |
| Audit Logs | ✅ | ✅ | Limited | ❌ |

## Implementation Details

### Role Assignment

```php
// Assign role to user
$user = User::find(1);
$user->assignRole('admin');

// Check user role
if ($user->hasRole('teacher')) {
    // Teacher-specific logic
}

// Multiple role check
if ($user->hasAnyRole(['admin', 'principal'])) {
    // Administrative access
}
```

### Permission Checking

```php
// Direct permission check
if ($user->can('enter-marks')) {
    // Allow mark entry
}

// Role-based permission
if ($user->hasRole('admin') || $user->can('approve-results')) {
    // Allow result approval
}

// Middleware protection
Route::middleware(['role:admin'])->group(function () {
    // Admin-only routes
});
```

### Subject-Based Permissions

Teachers have additional scope-based permissions:

```php
// Check if teacher can access specific subject
public function canAccessSubject(User $teacher, Subject $subject): bool
{
    return TeacherSubject::where('user_id', $teacher->id)
        ->where('subject_id', $subject->id)
        ->where('is_active', true)
        ->exists();
}

// Check class access
public function canAccessClass(User $teacher, $classId): bool
{
    return TeacherSubject::where('user_id', $teacher->id)
        ->where('class_id', $classId)
        ->where('is_active', true)
        ->exists();
}
```

## Security Measures

### Authentication Requirements

- **Strong Password Policy:** Minimum 8 characters, mixed case, numbers
- **Session Management:** Automatic timeout after inactivity
- **Two-Factor Authentication:** Optional for admin accounts
- **Password Reset:** Secure email-based reset process

### Authorization Controls

- **Route Protection:** All routes protected by appropriate middleware
- **API Security:** Token-based authentication for API endpoints
- **Data Filtering:** Users only see data they're authorized to access
- **Action Logging:** All critical actions logged with user attribution

### Audit Trail

Every significant action is logged:

```php
activity()
    ->performedOn($student)
    ->causedBy(auth()->user())
    ->withProperties(['old' => $oldData, 'new' => $newData])
    ->log('Student information updated');
```

## Role Transition Workflows

### Teacher to Principal Promotion

1. Admin assigns 'principal' role
2. System retains teacher permissions for continuity
3. Principal gains additional approval permissions
4. Teacher subject assignments remain active

### Temporary Role Elevation

```php
// Temporary admin access
$user->assignRole('admin');
// Perform administrative tasks
$user->removeRole('admin');
```

### Role Deactivation

- User account suspended (not deleted)
- All permissions revoked
- Audit trail preserved
- Data associations maintained

---

*This role-based permission system ensures secure, appropriate access to system features while maintaining flexibility for institutional needs and providing complete audit trails for compliance.*

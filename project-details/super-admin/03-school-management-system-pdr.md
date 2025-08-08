# 🏫 School Management System PDR
**Project Design Requirements - School Lifecycle & Operations**

## 📋 Overview

This PDR defines the comprehensive school management system that enables Super Administrators to create, configure, monitor, and manage educational institutions within the multi-tenant platform. The system focuses on school lifecycle management while maintaining strict data isolation and security.

---

## 🎯 Core Objectives

### Primary Goals
- **Streamlined School Creation**: Efficient onboarding process for new schools
- **Comprehensive Management**: Full lifecycle management of school entities
- **Credential Security**: Secure generation and management of school access credentials
- **Operational Monitoring**: Real-time monitoring of school status and activities

### Success Metrics
- **Onboarding Time**: < 5 minutes to create and activate a new school
- **Security Compliance**: 100% secure credential generation and storage
- **System Reliability**: 99.9% uptime for school management operations
- **User Satisfaction**: Intuitive interface with minimal training required

---

## 🏗️ System Architecture

### School Entity Model
```sql
CREATE TABLE schools (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,           -- School ID (e.g., ABC001)
    name VARCHAR(255) NOT NULL,                 -- School Name
    password VARCHAR(255) NOT NULL,             -- Hashed school password
    email VARCHAR(255),                         -- Contact email
    phone VARCHAR(20),                          -- Contact phone
    address TEXT,                               -- Physical address
    logo_path VARCHAR(500),                     -- School logo file path
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    settings JSON,                              -- School-specific settings
    created_by BIGINT,                          -- Super admin who created
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);
```

### Related Entities
```sql
-- School statistics tracking
CREATE TABLE school_statistics (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    school_id BIGINT NOT NULL,
    total_students INT DEFAULT 0,
    total_teachers INT DEFAULT 0,
    total_classes INT DEFAULT 0,
    total_subjects INT DEFAULT 0,
    last_login TIMESTAMP NULL,
    last_activity TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    UNIQUE KEY unique_school_stats (school_id)
);

-- School activity logs
CREATE TABLE school_activity_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    school_id BIGINT NOT NULL,
    activity_type VARCHAR(100) NOT NULL,
    description TEXT,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    INDEX idx_school_activity (school_id, created_at)
);
```

---

## 🔧 Core Functionality

### 1. School Creation Process

#### Step-by-Step Workflow
```
1. School Information Entry
   ├── Basic Details (Name, Contact Info)
   ├── School ID Generation (Manual/Auto)
   └── Password Creation (Manual/Auto)

2. Credential Generation
   ├── School Login Credentials
   ├── Default Admin User Creation
   └── Security Validation

3. Initial Setup
   ├── Default Academic Structure
   ├── System Settings Configuration
   └── Welcome Email/Instructions

4. Activation
   ├── Status Set to 'Active'
   ├── Credential Display
   └── Success Confirmation
```

#### Implementation Example
```php
class SchoolCreationService
{
    public function createSchool(array $data): array
    {
        DB::beginTransaction();
        
        try {
            // 1. Create school record
            $school = School::create([
                'name' => $data['name'],
                'code' => $this->generateSchoolCode($data['name']),
                'password' => Hash::make($data['password']),
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'status' => 'active',
                'created_by' => auth()->id(),
                'settings' => $this->getDefaultSettings()
            ]);
            
            // 2. Create default admin user
            $adminUser = $this->createDefaultAdmin($school, $data);
            
            // 3. Initialize school structure
            $this->initializeSchoolStructure($school);
            
            // 4. Create statistics record
            $this->createSchoolStatistics($school);
            
            // 5. Log creation activity
            $this->logSchoolActivity($school, 'school_created', 'School created by super admin');
            
            DB::commit();
            
            return [
                'school' => $school,
                'admin' => $adminUser,
                'credentials' => [
                    'school_id' => $school->code,
                    'school_password' => $data['password'],
                    'admin_email' => $adminUser->email,
                    'admin_password' => $data['admin_password']
                ]
            ];
            
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
```

### 2. School Management Operations

#### Status Management
```php
class SchoolStatusManager
{
    public function activateSchool(School $school): bool
    {
        $school->update(['status' => 'active']);
        $this->logActivity($school, 'school_activated');
        $this->notifySchool($school, 'activation');
        return true;
    }
    
    public function deactivateSchool(School $school): bool
    {
        $school->update(['status' => 'inactive']);
        $this->logActivity($school, 'school_deactivated');
        $this->notifySchool($school, 'deactivation');
        return true;
    }
    
    public function suspendSchool(School $school, string $reason): bool
    {
        $school->update([
            'status' => 'suspended',
            'settings->suspension_reason' => $reason,
            'settings->suspended_at' => now()
        ]);
        $this->logActivity($school, 'school_suspended', $reason);
        $this->notifySchool($school, 'suspension', $reason);
        return true;
    }
}
```

#### Credential Management
```php
class SchoolCredentialManager
{
    public function resetSchoolPassword(School $school): string
    {
        $newPassword = $this->generateSecurePassword();
        
        $school->update([
            'password' => Hash::make($newPassword),
            'settings->password_reset_at' => now(),
            'settings->password_reset_by' => auth()->id()
        ]);
        
        $this->logActivity($school, 'password_reset', 'Password reset by super admin');
        $this->notifySchool($school, 'password_reset', $newPassword);
        
        return $newPassword;
    }
    
    public function generateSchoolCode(string $schoolName): string
    {
        $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $schoolName), 0, 3));
        $counter = 1;
        
        do {
            $code = $baseCode . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        } while (School::where('code', $code)->exists());
        
        return $code;
    }
    
    private function generateSecurePassword(): string
    {
        return Str::random(12) . rand(100, 999) . '!';
    }
}
```

### 3. School Monitoring & Analytics

#### Real-time Statistics
```php
class SchoolAnalyticsService
{
    public function getSchoolStatistics(School $school): array
    {
        return [
            'basic_info' => [
                'name' => $school->name,
                'code' => $school->code,
                'status' => $school->status,
                'created_at' => $school->created_at
            ],
            'usage_stats' => [
                'total_students' => $school->students()->count(),
                'total_teachers' => $school->users()->role('teacher')->count(),
                'total_classes' => $school->classes()->count(),
                'total_subjects' => $school->subjects()->count()
            ],
            'activity_stats' => [
                'last_login' => $this->getLastLogin($school),
                'last_activity' => $this->getLastActivity($school),
                'login_frequency' => $this->getLoginFrequency($school),
                'feature_usage' => $this->getFeatureUsage($school)
            ],
            'system_health' => [
                'data_integrity' => $this->checkDataIntegrity($school),
                'storage_usage' => $this->getStorageUsage($school),
                'performance_metrics' => $this->getPerformanceMetrics($school)
            ]
        ];
    }
    
    public function getSystemOverview(): array
    {
        return [
            'total_schools' => School::count(),
            'active_schools' => School::where('status', 'active')->count(),
            'inactive_schools' => School::where('status', 'inactive')->count(),
            'suspended_schools' => School::where('status', 'suspended')->count(),
            'new_schools_this_month' => School::whereMonth('created_at', now()->month)->count(),
            'total_users' => User::whereNotNull('school_id')->count(),
            'system_health' => $this->getSystemHealth()
        ];
    }
}
```

---

## 🎨 User Interface Design

### 1. School Management Dashboard

#### Layout Structure
```
📊 Statistics Overview
├── Total Schools: 150 (↑12 this month)
├── Active: 142 (94.7%)
├── Inactive: 8 (5.3%)
└── Suspended: 0 (0%)

🎯 Quick Actions
├── [Create New School] [Manage Schools]
├── [System Health] [Analytics]
└── [Export Data] [Settings]

📋 Recent Schools
├── ABC International (Active) - 2 days ago
├── XYZ Academy (Active) - 1 week ago
└── PQR School (Inactive) - 2 weeks ago

🔔 System Alerts
├── 3 schools require attention
└── 1 security notification
```

#### School List Interface
```
🔍 Search & Filter
├── Search: [School name or code...]
├── Status: [All ▼] [Active] [Inactive] [Suspended]
└── Created: [All time ▼] [This month] [Last 3 months]

📋 School Table
┌─────────────────────────────────────────────────────────────┐
│ School Name    │ School ID │ Status   │ Users │ Created    │ Actions │
├─────────────────────────────────────────────────────────────┤
│ ABC School     │ ABC001    │ Active   │ 245   │ 2024-12-01 │ [Manage] │
│ XYZ Academy    │ XYZ001    │ Active   │ 189   │ 2024-11-15 │ [Manage] │
│ PQR Institute  │ PQR001    │ Inactive │ 67    │ 2024-10-20 │ [Manage] │
└─────────────────────────────────────────────────────────────┘
```

### 2. School Creation Wizard

#### Step 1: Basic Information
```
🏫 Create New School - Step 1 of 3

School Information
├── School Name: [ABC International School        ]
├── Contact Email: [info@abcschool.edu           ]
├── Phone Number: [+1-555-0123                   ]
└── Address: [123 Education St, City, State      ]

[Cancel] [Next: Credentials →]
```

#### Step 2: Credentials Setup
```
🔐 Create New School - Step 2 of 3

School Login Credentials
├── School ID: [ABC001] [Generate]
└── Password: [••••••••••••] [Generate] [Show]

Default Admin User
├── Admin Name: [School Administrator]
├── Admin Email: [admin@abc001.school]
└── Admin Password: [••••••••••••] [Generate] [Show]

[← Back] [Next: Review →]
```

#### Step 3: Review & Create
```
✅ Create New School - Step 3 of 3

Review Information
├── School: ABC International School
├── School ID: ABC001
├── Contact: info@abcschool.edu
└── Admin: admin@abc001.school

⚠️ Important: Save these credentials securely
├── School ID: ABC001
├── School Password: [Show credentials after creation]
└── Admin credentials will be displayed after creation

[← Back] [Create School]
```

### 3. School Detail View

#### School Information Panel
```
🏫 ABC International School (ABC001)
Status: ● Active | Created: 2024-12-01 | Last Login: 2 hours ago

📊 Quick Stats
├── Students: 245 | Teachers: 18 | Classes: 12 | Subjects: 24
├── Last Activity: Student enrollment (30 min ago)
└── Storage Used: 2.3 GB / 10 GB (23%)

🔐 Credentials Management
├── School ID: ABC001 [Copy]
├── Password: ••••••••••••• [Show] [Reset]
└── Admin Email: admin@abc001.school [Copy]

⚙️ Actions
├── [Edit School Info] [Change Status] [Reset Password]
├── [View Activity Log] [Download Data] [Send Message]
└── [Deactivate School] [Suspend School]
```

---

## 🔒 Security Implementation

### Access Control
```php
// Super Admin middleware
class SuperAdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->hasRole('super-admin')) {
            abort(403, 'Unauthorized access to super admin area');
        }
        
        // Log super admin access
        Log::info('Super admin access', [
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'route' => $request->route()->getName(),
            'timestamp' => now()
        ]);
        
        return $next($request);
    }
}
```

### Audit Logging
```php
class SchoolAuditLogger
{
    public function logSchoolAction(string $action, School $school, array $details = []): void
    {
        SchoolActivityLog::create([
            'school_id' => $school->id,
            'activity_type' => $action,
            'description' => $this->generateDescription($action, $school),
            'metadata' => array_merge($details, [
                'performed_by' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()
            ])
        ]);
    }
    
    private function generateDescription(string $action, School $school): string
    {
        $descriptions = [
            'school_created' => "School '{$school->name}' was created",
            'school_activated' => "School '{$school->name}' was activated",
            'school_deactivated' => "School '{$school->name}' was deactivated",
            'school_suspended' => "School '{$school->name}' was suspended",
            'password_reset' => "Password was reset for school '{$school->name}'",
            'school_updated' => "School '{$school->name}' information was updated"
        ];
        
        return $descriptions[$action] ?? "Action '{$action}' performed on school '{$school->name}'";
    }
}
```

---

## ✅ Implementation Checklist

### Core Features
- [ ] School creation wizard
- [ ] School management interface
- [ ] Credential generation system
- [ ] Status management (activate/deactivate/suspend)
- [ ] School information editing
- [ ] Bulk operations support

### Security Features
- [ ] Secure password generation
- [ ] Credential encryption
- [ ] Access control implementation
- [ ] Audit logging system
- [ ] Activity monitoring
- [ ] Security notifications

### User Interface
- [ ] Responsive dashboard design
- [ ] Intuitive navigation
- [ ] Search and filtering
- [ ] Bulk action support
- [ ] Mobile-friendly interface
- [ ] Accessibility compliance

### Integration Features
- [ ] Email notification system
- [ ] Export/import capabilities
- [ ] API endpoints
- [ ] Webhook support
- [ ] Third-party integrations
- [ ] Backup/restore functionality

---

**Status**: 🟢 Implementation Complete
**Last Updated**: 2025-01-04
**Next Review**: 2025-02-04

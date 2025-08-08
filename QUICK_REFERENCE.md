# Academic Management System - Quick Reference

## 🚀 **Quick Start**

### **Installation (5 minutes)**
```bash
git clone <repo> && cd academic-management-system
composer install && npm install
cp .env.example .env && php artisan key:generate
# Configure database in .env
php artisan migrate && php artisan db:seed
npm run build && php artisan serve
```

### **Default Credentials**
```
Super Admin: superadmin@system.local / password
School Admin: admin@school.local / password
Teacher: teacher@school.local / password
Student: student@school.local / password
```

## 🏗️ **Architecture Overview**

### **Multi-Tenant Structure**
```
Super Admin (System Level)
├── School 1 (ABC International)
│   ├── Admin, Teachers, Students
│   ├── Faculties, Departments, Programs
│   └── Classes, Subjects, Exams
├── School 2 (PQR College)
│   ├── Admin, Teachers, Students
│   └── Independent data isolation
└── School N...
```

### **Key Models & Relationships**
```php
School (1) → (Many) Users, Students, Faculties
Faculty (1) → (Many) Departments
Department (1) → (Many) Programs, Subjects
Program (1) → (Many) Classes
Class (1) → (Many) StudentEnrollments
Student (1) → (Many) Marks, Enrollments
Exam (1) → (Many) Marks
```

## 🔐 **Data Isolation Implementation**

### **BelongsToSchool Trait**
```php
// Automatically applied to all school-scoped models
use App\Models\Traits\BelongsToSchool;

class Student extends Model {
    use BelongsToSchool; // Automatic school scoping
}
```

### **Global Scope**
```php
// Automatically filters queries by school_id
Student::all(); // Only returns current school's students
User::all();    // Only returns current school's users
Exam::all();    // Only returns current school's exams
```

### **Super Admin Bypass**
```php
// Super admin can access all data
Student::withoutGlobalScope(SchoolScope::class)->get();
User::withoutGlobalScope('user_school_scope')->get();
```

## 🎯 **Key Features**

### **✅ Implemented Features**
- ✅ Multi-tenant architecture with data isolation
- ✅ User management (Admin, Teacher, Student roles)
- ✅ Academic structure (Faculty → Department → Program → Class)
- ✅ Student enrollment and management
- ✅ Examination and mark entry system
- ✅ Grading and GPA calculation
- ✅ Super-admin school management
- ✅ Responsive UI with Bootstrap 5

### **🔄 Core Workflows**

#### **School Setup (Super Admin)**
1. Create school account
2. Set school credentials
3. Configure basic settings
4. Create initial admin user

#### **Academic Setup (School Admin)**
1. Create faculties and departments
2. Define programs and classes
3. Set up subjects and grading scales
4. Create academic years and semesters

#### **Student Management**
1. Register new students
2. Enroll in programs and classes
3. Assign subjects
4. Track academic progress

#### **Examination Process**
1. Create exams for subjects/classes
2. Teachers enter marks
3. System calculates grades and GPA
4. Generate reports and transcripts

## 🗄️ **Database Quick Reference**

### **Core Tables**
```sql
schools              -- Master school data
users               -- All system users (with school_id)
students            -- Student records (with school_id)
faculties           -- Academic faculties (with school_id)
departments         -- Academic departments (with school_id)
programs            -- Degree programs (with school_id)
classes             -- Class sections (with school_id)
subjects            -- Course subjects (with school_id)
academic_years      -- Academic calendar (with school_id)
exams               -- Examinations (with school_id)
marks               -- Student marks (with school_id)
student_enrollments -- Enrollment records (with school_id)
```

### **Key Indexes**
```sql
-- All tables have composite indexes for performance
INDEX (school_id, id)
INDEX (school_id, created_at)
INDEX (school_id, status) -- where applicable
```

## 🛠️ **Development Commands**

### **Artisan Commands**
```bash
# Database
php artisan migrate:fresh --seed
php artisan db:seed --class=SchoolSeeder

# Cache Management
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Testing
php artisan test
php artisan test --filter=UserTest
```

### **Useful Queries**
```php
// Get current school context
$schoolId = session('school_context');
$school = School::find($schoolId);

// Create school-scoped records
Student::create(['name' => 'John', 'school_id' => $schoolId]);

// Query with school context
$students = Student::where('status', 'active')->get();

// Super admin queries (bypass scoping)
$allStudents = Student::withoutGlobalScope(SchoolScope::class)->get();
```

## 🔧 **Configuration**

### **Environment Setup**
```env
# Essential .env variables
APP_NAME="Academic Management System"
DB_DATABASE=academic_management
MAIL_MAILER=smtp
FILESYSTEM_DISK=local
```

### **Key Config Files**
```php
config/auth.php      -- Authentication settings
config/permission.php -- Role & permission config
config/database.php  -- Database configuration
config/mail.php      -- Email settings
```

## 🚨 **Troubleshooting**

### **Common Issues**
```bash
# School context not set
session(['school_context' => 1]);

# Permission denied
php artisan permission:cache-reset

# Database connection
php artisan config:clear && php artisan migrate

# Assets not loading
npm run build

# Storage permissions
chmod -R 755 storage bootstrap/cache
```

### **Debug Commands**
```php
// Check current user and school
dd(auth()->user(), session('school_context'));

// Test data isolation
Student::all()->pluck('school_id')->unique();

// Check permissions
auth()->user()->getAllPermissions();
```

## 📊 **Performance Tips**

### **Query Optimization**
```php
// Use eager loading
Student::with(['currentEnrollment.class'])->get();

// Avoid N+1 queries
$exams = Exam::with(['subject', 'class', 'marks'])->get();

// Use pagination
Student::paginate(15);
```

### **Caching Strategy**
```php
// Cache frequently accessed data
Cache::remember('schools', 3600, fn() => School::all());

// Cache user permissions
Cache::remember("user.{$userId}.permissions", 3600, 
    fn() => $user->getAllPermissions());
```

## 🎯 **Next Steps**

### **Immediate Enhancements**
- [ ] Fee management system
- [ ] Attendance tracking
- [ ] Report generation
- [ ] Email notifications
- [ ] Document management

### **Advanced Features**
- [ ] Mobile application
- [ ] API development
- [ ] Real-time notifications
- [ ] Advanced analytics
- [ ] Third-party integrations

---

**Quick Reference for Academic Management System v1.0**

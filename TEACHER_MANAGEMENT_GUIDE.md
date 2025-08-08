# 🎓 TEACHER MANAGEMENT & PERMISSIONS GUIDE

## 📍 WHERE TO FIND TEACHER OPTIONS:

### **1. 👥 Admin Dashboard - User Management Section**

**Location**: Admin Sidebar → **User Management** (dropdown)

#### **📋 Available Options:**
- **All Users** (`/admin/users`) - Manage all system users including teachers
- **Teacher Assignments** (`/admin/teacher-subjects`) - Assign teachers to subjects and classes

### **2. 🔗 Direct URLs for Teacher Management:**

```
🏠 Admin Dashboard: http://your-domain/admin
👥 User Management: http://your-domain/admin/users  
🎓 Teacher Assignments: http://your-domain/admin/teacher-subjects
👨‍🏫 Teacher Dashboard: http://your-domain/teacher/dashboard
```

### **3. 📱 Navigation Structure:**

```
Admin Sidebar:
├── 📊 Dashboard
├── 👥 User Management (dropdown)
│   ├── 👤 All Users
│   └── 🎓 Teacher Assignments
├── 🎓 Students
├── 🏛️ Academic Structure
├── 📝 Examinations
└── ...
```

---

## 🔐 ROLE-BASED PERMISSIONS SYSTEM:

### **🎯 Permission Structure:**

#### **👨‍💼 Admin Permissions:**
- ✅ **Full Access** to all teacher management features
- ✅ **Create/Edit/Delete** teacher accounts
- ✅ **Assign/Unassign** teachers to subjects
- ✅ **Manage** teacher permissions
- ✅ **View** all teacher data

#### **🏫 Principal Permissions:**
- ✅ **View** teacher assignments
- ✅ **Assign** teachers to subjects
- ✅ **Edit** teacher assignments
- ✅ **Manage** teacher schedules
- ❌ **Cannot** delete teacher accounts

#### **👨‍🏫 Teacher Permissions:**
- ✅ **View** own profile and assignments
- ✅ **Enter marks** for assigned subjects only
- ✅ **View** exam schedules for assigned subjects
- ✅ **View** results for assigned subjects
- ❌ **Cannot** access other teachers' data
- ❌ **Cannot** modify assignments

### **🛡️ Permission Implementation:**

#### **Middleware Protection:**
```php
// Admin routes protected by role
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('teacher-subjects', TeacherSubjectController::class);
});

// Teacher routes protected by custom middleware
Route::middleware(['auth', 'teacher'])->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index']);
});
```

#### **Permission Checks in Views:**
```php
@can('manage-teachers')
    <a href="{{ route('admin.teacher-subjects.index') }}">Teacher Assignments</a>
@endcan

@can('assign-teachers')
    <button>Assign Teacher</button>
@endcan
```

#### **Controller Authorization:**
```php
public function __construct()
{
    $this->middleware(['auth', 'permission:manage-teachers']);
}

// Subject-specific authorization
if (!$teacher->canAccessSubject($subject)) {
    abort(403, 'Access denied to this subject.');
}
```

---

## 🎯 HOW TO MANAGE TEACHERS:

### **1. 👤 Creating Teacher Accounts:**

**Step 1**: Go to **Admin Dashboard** → **User Management** → **All Users**
**Step 2**: Click **"Add New User"**
**Step 3**: Fill in teacher details:
- Name: Teacher's full name
- Email: Teacher's email address
- Password: Initial password
- **Role**: Select **"Teacher"** role

**Step 4**: Click **"Create User"**

### **2. 🎓 Assigning Teachers to Subjects:**

**Step 1**: Go to **Admin Dashboard** → **User Management** → **Teacher Assignments**
**Step 2**: Click **"Assign Teacher"**
**Step 3**: Select:
- **Teacher**: Choose from teacher users
- **Subject**: Select subject to teach
- **Class**: Choose class/section
- **Academic Year**: Select academic year
- **Status**: Set as Active

**Step 4**: Click **"Create Assignment"**

### **3. 📊 Managing Assignments:**

**From Teacher Assignments page you can:**
- ✅ **View** all current assignments
- ✅ **Filter** by teacher, subject, class, or academic year
- ✅ **Search** for specific assignments
- ✅ **Edit** existing assignments
- ✅ **Activate/Deactivate** assignments
- ✅ **Delete** assignments
- ✅ **Bulk assign** multiple teachers

### **4. 🔍 Monitoring Teacher Activity:**

**Teachers can access:**
- **Dashboard**: Overview of assignments and statistics
- **Mark Entry**: Enter marks for assigned subjects only
- **Profile**: View personal assignments and history
- **Results**: View approved results for their subjects

---

## 🚀 QUICK START GUIDE:

### **🎯 For Administrators:**

1. **Login** as admin (`admin@academic.local` / `admin123`)
2. **Navigate** to Admin Dashboard
3. **Create teacher accounts** via User Management → All Users
4. **Assign subjects** via User Management → Teacher Assignments
5. **Monitor activity** through teacher assignment dashboard

### **🎯 For Teachers:**

1. **Login** with teacher credentials
2. **Access** teacher dashboard at `/teacher/dashboard`
3. **View assignments** in profile section
4. **Enter marks** for assigned subjects
5. **View results** for approved marks

### **🎯 Test Teacher Account:**

**Create a test teacher:**
- Email: `john.teacher@school.edu`
- Password: `password`
- Role: `teacher`

**Assign to subject:**
- Subject: Mathematics
- Class: Grade 10
- Academic Year: Current year

---

## 🔧 TECHNICAL IMPLEMENTATION:

### **📁 Key Files:**

#### **Controllers:**
- `app/Http/Controllers/Admin/TeacherSubjectController.php` - Teacher assignment management
- `app/Http/Controllers/Teacher/DashboardController.php` - Teacher dashboard
- `app/Http/Controllers/Teacher/MarkController.php` - Teacher mark entry

#### **Models:**
- `app/Models/User.php` - User model with teacher relationships
- `app/Models/TeacherSubject.php` - Teacher-subject assignment model

#### **Views:**
- `resources/views/admin/teacher-subjects/` - Admin teacher management views
- `resources/views/teacher/` - Teacher interface views

#### **Middleware:**
- `app/Http/Middleware/TeacherMiddleware.php` - Teacher role verification

### **🗄️ Database Tables:**

#### **teacher_subjects table:**
```sql
- id (primary key)
- user_id (foreign key to users)
- subject_id (foreign key to subjects)  
- class_id (foreign key to classes)
- academic_year_id (foreign key to academic_years)
- is_active (boolean)
- created_at, updated_at
```

#### **Relationships:**
```
User (Teacher) → TeacherSubject → Subject
                              → Class
                              → AcademicYear
```

---

## ✅ VERIFICATION CHECKLIST:

### **🔍 Admin Access:**
- [ ] Can access User Management in admin sidebar
- [ ] Can create new teacher accounts
- [ ] Can assign teachers to subjects
- [ ] Can view teacher assignment dashboard
- [ ] Can edit/delete assignments

### **🔍 Teacher Access:**
- [ ] Can login with teacher credentials
- [ ] Can access teacher dashboard
- [ ] Can view only assigned subjects
- [ ] Can enter marks for assigned subjects only
- [ ] Cannot access admin functions

### **🔍 Security:**
- [ ] Teachers cannot access other teachers' data
- [ ] Teachers cannot modify their own assignments
- [ ] Permission checks work correctly
- [ ] Role-based access is enforced

---

**🎊 SUCCESS**: Complete teacher management system with role-based permissions is now implemented and ready for use!

**📞 Support**: If you need help finding any options, check the admin sidebar under "User Management" or access the direct URLs provided above.

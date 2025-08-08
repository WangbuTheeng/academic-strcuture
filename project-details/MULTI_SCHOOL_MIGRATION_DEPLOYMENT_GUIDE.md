# Multi-School System - Migration & Deployment Guide

## 📋 Pre-Implementation Checklist

### ✅ Requirements Verification
- [ ] **Data Isolation Confirmed**: Each school's data must be completely separate
- [ ] **Authentication Flow Approved**: School Code + Email + Password login
- [ ] **Super-Admin Role Defined**: Global management capabilities
- [ ] **Performance Requirements**: Expected number of schools and users
- [ ] **Backup Strategy**: Per-school backup requirements

### ✅ Technical Prerequisites
- [ ] **Laravel Version**: 11.x confirmed
- [ ] **Database**: MySQL 8.0+ with sufficient storage
- [ ] **PHP Version**: 8.1+ with required extensions
- [ ] **Server Resources**: Adequate for multi-school load
- [ ] **Testing Environment**: Separate environment for testing

---

## 🗄️ Database Migration Strategy

### Phase 1: Backup Current System
```bash
# Create full database backup
mysqldump -u username -p database_name > backup_before_multiSchool_$(date +%Y%m%d_%H%M%S).sql

# Create Laravel backup
php artisan backup:run --only-db
```

### Phase 2: Create Migration Files
```bash
# Generate migration files
php artisan make:migration create_schools_table
php artisan make:migration add_school_id_to_tables
php artisan make:migration create_default_school_and_migrate_data
php artisan make:migration add_super_admin_role
```

### Phase 3: Data Migration Script
```php
// database/migrations/create_default_school_and_migrate_data.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\School;
use App\Models\User;

return new class extends Migration
{
    public function up()
    {
        // Create default school for existing data
        $defaultSchool = School::create([
            'name' => 'Default School',
            'code' => 'DEFAULT001',
            'password' => bcrypt('default123'),
            'email' => 'admin@default.school',
            'status' => 'active',
            'settings' => json_encode(['is_default' => true]),
        ]);

        // Migrate existing data to default school
        $this->migrateExistingData($defaultSchool->id);
        
        // Create super-admin user
        $this->createSuperAdmin();
    }

    private function migrateExistingData($schoolId)
    {
        $tables = [
            'users', 'students', 'student_enrollments', 'levels', 
            'faculties', 'departments', 'classes', 'programs', 
            'subjects', 'exams', 'marks', 'institute_settings',
            'teacher_subjects', 'student_subjects', 'grading_scales'
        ];

        foreach ($tables as $table) {
            DB::table($table)->update(['school_id' => $schoolId]);
        }
    }

    private function createSuperAdmin()
    {
        // Create super-admin role if not exists
        if (!DB::table('roles')->where('name', 'super-admin')->exists()) {
            DB::table('roles')->insert([
                'name' => 'super-admin',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create super-admin user
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@system.local',
            'password' => bcrypt('superadmin123'),
            'school_id' => null, // Super-admin doesn't belong to any school
            'email_verified_at' => now(),
        ]);

        $superAdmin->assignRole('super-admin');
    }

    public function down()
    {
        // Remove school_id from all tables
        $tables = [
            'users', 'students', 'student_enrollments', 'levels', 
            'faculties', 'departments', 'classes', 'programs', 
            'subjects', 'exams', 'marks', 'institute_settings',
            'teacher_subjects', 'student_subjects', 'grading_scales'
        ];

        foreach ($tables as $table) {
            DB::table($table)->update(['school_id' => null]);
        }

        // Delete default school
        School::where('code', 'DEFAULT001')->delete();
        
        // Delete super-admin
        User::where('email', 'superadmin@system.local')->delete();
    }
};
```

---

## 🚀 Deployment Steps

### Step 1: Pre-Deployment Testing
```bash
# Run tests in staging environment
php artisan test

# Test migrations on copy of production data
php artisan migrate --pretend

# Verify data integrity
php artisan tinker
>>> App\Models\User::count()
>>> App\Models\Student::count()
```

### Step 2: Maintenance Mode
```bash
# Put application in maintenance mode
php artisan down --message="Upgrading to Multi-School System" --retry=60
```

### Step 3: Execute Migrations
```bash
# Run migrations
php artisan migrate

# Seed super-admin permissions
php artisan db:seed --class=SuperAdminPermissionSeeder

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Step 4: Verify Migration
```bash
# Check data integrity
php artisan tinker
>>> App\Models\School::count() // Should be 1 (default school)
>>> App\Models\User::whereNotNull('school_id')->count() // All existing users
>>> App\Models\User::whereNull('school_id')->count() // Should be 1 (super-admin)
```

### Step 5: Update Configuration
```bash
# Update .env file
echo "MULTI_SCHOOL_ENABLED=true" >> .env
echo "DEFAULT_SCHOOL_CODE=DEFAULT001" >> .env

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm
```

### Step 6: Bring Application Online
```bash
# Remove maintenance mode
php artisan up
```

---

## 🧪 Testing Strategy

### Unit Tests
```php
// tests/Unit/SchoolTest.php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\School;
use App\Models\User;

class SchoolTest extends TestCase
{
    public function test_school_creation()
    {
        $school = School::factory()->create();
        $this->assertDatabaseHas('schools', ['id' => $school->id]);
    }

    public function test_school_code_uniqueness()
    {
        School::factory()->create(['code' => 'TEST001']);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        School::factory()->create(['code' => 'TEST001']);
    }

    public function test_user_belongs_to_school()
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        
        $this->assertEquals($school->id, $user->school_id);
        $this->assertTrue($user->school->is($school));
    }
}
```

### Feature Tests
```php
// tests/Feature/SchoolAuthTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\School;
use App\Models\User;

class SchoolAuthTest extends TestCase
{
    public function test_school_login_with_valid_credentials()
    {
        $school = School::factory()->create(['code' => 'TEST001']);
        $user = User::factory()->create(['school_id' => $school->id]);

        $response = $this->post('/login', [
            'school_code' => 'TEST001',
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertEquals($school->id, session('school_context'));
    }

    public function test_school_login_with_invalid_school_code()
    {
        $response = $this->post('/login', [
            'school_code' => 'INVALID',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors();
    }

    public function test_data_isolation_between_schools()
    {
        $school1 = School::factory()->create();
        $school2 = School::factory()->create();
        
        $user1 = User::factory()->create(['school_id' => $school1->id]);
        $user2 = User::factory()->create(['school_id' => $school2->id]);

        // Login as user from school1
        $this->actingAs($user1);
        
        // Should only see users from school1
        $users = User::all();
        $this->assertTrue($users->every(fn($user) => $user->school_id === $school1->id));
    }
}
```

### Integration Tests
```php
// tests/Feature/SuperAdminTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\School;

class SuperAdminTest extends TestCase
{
    public function test_super_admin_can_create_school()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $this->actingAs($superAdmin);

        $response = $this->post('/super-admin/schools', [
            'name' => 'Test School',
            'code' => 'TEST001',
            'password' => 'password123',
            'email' => 'admin@test.school',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('schools', ['code' => 'TEST001']);
    }

    public function test_super_admin_can_view_all_schools()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');
        
        School::factory()->count(3)->create();

        $this->actingAs($superAdmin);
        
        $response = $this->get('/super-admin/schools');
        $response->assertStatus(200);
        $response->assertViewHas('schools');
    }
}
```

---

## 📊 Performance Optimization

### Database Indexing
```sql
-- Add composite indexes for better performance
CREATE INDEX idx_users_school_email ON users(school_id, email);
CREATE INDEX idx_students_school_enrollment ON students(school_id, enrollment_number);
CREATE INDEX idx_marks_school_student ON marks(school_id, student_id);
CREATE INDEX idx_exams_school_date ON exams(school_id, exam_date);

-- Add indexes for common queries
CREATE INDEX idx_school_status ON schools(status);
CREATE INDEX idx_school_code_status ON schools(code, status);
```

### Query Optimization
```php
// Use eager loading to prevent N+1 queries
$schools = School::with(['users', 'students'])->get();

// Use database-level filtering
$activeSchools = School::where('status', 'active')->get();

// Optimize pagination for large datasets
$students = Student::forSchool($schoolId)->paginate(50);
```

### Caching Strategy
```php
// Cache school settings
Cache::remember("school_settings_{$schoolId}", 3600, function () use ($schoolId) {
    return InstituteSettings::where('school_id', $schoolId)->first();
});

// Cache user permissions per school
Cache::remember("user_permissions_{$userId}_{$schoolId}", 1800, function () use ($userId) {
    return User::find($userId)->getAllPermissions();
});
```

---

## 🔒 Security Considerations

### Data Isolation Verification
```php
// Add middleware to verify data isolation
class DataIsolationMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        // In development, verify no cross-school data leakage
        if (app()->environment('local')) {
            $this->verifyDataIsolation();
        }
        
        return $response;
    }
    
    private function verifyDataIsolation()
    {
        if (auth()->check() && !auth()->user()->hasRole('super-admin')) {
            $schoolId = auth()->user()->school_id;
            
            // Verify all loaded models belong to current school
            foreach (Model::getLoadedModels() as $model) {
                if (method_exists($model, 'getSchoolId')) {
                    assert($model->getSchoolId() === $schoolId, 
                           'Data isolation violation detected');
                }
            }
        }
    }
}
```

### Audit Logging
```php
// Log all cross-school access attempts
class SchoolAccessLogger
{
    public static function logAccess($action, $schoolId, $userId)
    {
        Log::info('School access', [
            'action' => $action,
            'school_id' => $schoolId,
            'user_id' => $userId,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);
    }
}
```

---

## 🚨 Rollback Plan

### Emergency Rollback Procedure
```bash
# 1. Put application in maintenance mode
php artisan down

# 2. Restore database from backup
mysql -u username -p database_name < backup_before_multiSchool_YYYYMMDD_HHMMSS.sql

# 3. Revert code changes
git checkout previous_stable_commit

# 4. Clear caches
php artisan cache:clear
php artisan config:clear

# 5. Bring application online
php artisan up
```

### Partial Rollback (Keep Multi-School Structure)
```php
// Migration to disable multi-school features
public function disableMultiSchool()
{
    // Remove global scopes temporarily
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    
    // Merge all schools into default
    $defaultSchool = School::where('code', 'DEFAULT001')->first();
    
    $tables = ['users', 'students', 'student_enrollments', /* ... */];
    foreach ($tables as $table) {
        DB::table($table)->update(['school_id' => $defaultSchool->id]);
    }
    
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
}
```

---

## 📈 Monitoring & Maintenance

### Health Checks
```php
// Add health check for multi-school system
Route::get('/health/schools', function () {
    $checks = [
        'active_schools' => School::where('status', 'active')->count(),
        'total_users' => User::count(),
        'data_isolation' => $this->verifyDataIsolation(),
        'database_performance' => $this->checkDatabasePerformance(),
    ];
    
    return response()->json($checks);
});
```

### Regular Maintenance Tasks
```bash
# Weekly data integrity check
php artisan school:verify-data-integrity

# Monthly performance optimization
php artisan school:optimize-database

# Quarterly backup verification
php artisan school:verify-backups
```

This comprehensive migration and deployment guide ensures a smooth transition to the multi-school system while maintaining data integrity and system stability.

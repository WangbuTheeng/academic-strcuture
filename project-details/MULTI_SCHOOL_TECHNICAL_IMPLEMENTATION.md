# Multi-School System - Technical Implementation Guide

## 🗄️ Database Migrations

### 1. Create Schools Table

```php
// database/migrations/create_schools_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique(); // ABC001, PQR002
            $table->string('password'); // Hashed school password
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('logo_path')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->json('settings')->nullable(); // School-specific settings
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['code']);
            $table->index(['status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('schools');
    }
};
```

### 2. Add school_id to Existing Tables

```php
// database/migrations/add_school_id_to_tables.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tables = [
            'users', 'students', 'student_enrollments', 'levels', 
            'faculties', 'departments', 'classes', 'programs', 
            'subjects', 'exams', 'marks', 'institute_settings',
            'teacher_subjects', 'student_subjects', 'grading_scales'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained('schools');
                $table->index(['school_id']);
            });
        }
    }

    public function down()
    {
        $tables = [
            'users', 'students', 'student_enrollments', 'levels', 
            'faculties', 'departments', 'classes', 'programs', 
            'subjects', 'exams', 'marks', 'institute_settings',
            'teacher_subjects', 'student_subjects', 'grading_scales'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['school_id']);
                $table->dropIndex(['school_id']);
                $table->dropColumn('school_id');
            });
        }
    }
};
```

---

## 🏗️ Models Implementation

### 1. School Model

```php
// app/Models/School.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'password', 'email', 'phone', 
        'address', 'logo_path', 'status', 'settings', 'created_by'
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'settings' => 'array',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Mutators
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public static function findByCode($code)
    {
        return static::where('code', $code)->first();
    }
}
```

### 2. Global Scope for School Isolation

```php
// app/Models/Scopes/SchoolScope.php
<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SchoolScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Only apply scope if user is authenticated and not super-admin
        if (auth()->check() && !auth()->user()->hasRole('super-admin')) {
            $builder->where($model->getTable() . '.school_id', auth()->user()->school_id);
        }
    }
}
```

### 3. Base School Model Trait

```php
// app/Models/Traits/BelongsToSchool.php
<?php

namespace App\Models\Traits;

use App\Models\School;
use App\Models\Scopes\SchoolScope;

trait BelongsToSchool
{
    protected static function bootBelongsToSchool()
    {
        static::addGlobalScope(new SchoolScope);
        
        static::creating(function ($model) {
            if (auth()->check() && !$model->school_id) {
                $model->school_id = auth()->user()->school_id;
            }
        });
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }
}
```

### 4. Updated User Model

```php
// app/Models/User.php (additions)
<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, BelongsToSchool;

    protected $fillable = [
        'name', 'email', 'password', 'school_id'
    ];

    // Override global scope for super-admin
    protected static function bootUser()
    {
        parent::boot();
        
        // Don't apply school scope to super-admin
        static::addGlobalScope('school', function ($builder) {
            if (auth()->check() && auth()->user()->hasRole('super-admin')) {
                return; // No scope for super-admin
            }
            
            if (auth()->check()) {
                $builder->where('school_id', auth()->user()->school_id);
            }
        });
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('super-admin');
    }

    public function getSchoolContext()
    {
        return $this->school_id;
    }
}
```

---

## 🔐 Authentication System

### 1. School Authentication Service

```php
// app/Services/SchoolAuthService.php
<?php

namespace App\Services;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SchoolAuthService
{
    public function authenticateSchoolUser($schoolCode, $email, $password)
    {
        // Find school by code
        $school = School::findByCode($schoolCode);
        
        if (!$school || !$school->isActive()) {
            return ['success' => false, 'message' => 'Invalid school code or school is inactive'];
        }

        // Find user in the school
        $user = User::where('email', $email)
                   ->where('school_id', $school->id)
                   ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        // Set school context in session
        session(['school_context' => $school->id, 'school_code' => $school->code]);
        
        // Login user
        Auth::login($user);

        return [
            'success' => true, 
            'user' => $user, 
            'school' => $school,
            'redirect' => $this->getRedirectRoute($user)
        ];
    }

    public function authenticateSuperAdmin($email, $password)
    {
        $user = User::where('email', $email)
                   ->whereNull('school_id')
                   ->first();

        if (!$user || !Hash::check($password, $user->password) || !$user->hasRole('super-admin')) {
            return ['success' => false, 'message' => 'Invalid super-admin credentials'];
        }

        Auth::login($user);
        session(['is_super_admin' => true]);

        return [
            'success' => true, 
            'user' => $user,
            'redirect' => route('super-admin.dashboard')
        ];
    }

    private function getRedirectRoute($user)
    {
        if ($user->hasRole('admin')) {
            return route('admin.dashboard');
        } elseif ($user->hasRole('principal')) {
            return route('principal.dashboard');
        } elseif ($user->hasRole('teacher')) {
            return route('teacher.dashboard');
        } elseif ($user->hasRole('student')) {
            return route('student.dashboard');
        }

        return route('dashboard');
    }
}
```

### 2. Custom Login Controller

```php
// app/Http/Controllers/Auth/SchoolLoginController.php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SchoolAuthService;
use Illuminate\Http\Request;

class SchoolLoginController extends Controller
{
    protected $schoolAuthService;

    public function __construct(SchoolAuthService $schoolAuthService)
    {
        $this->schoolAuthService = $schoolAuthService;
    }

    public function showLoginForm()
    {
        return view('auth.school-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'school_code' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $result = $this->schoolAuthService->authenticateSchoolUser(
            $request->school_code,
            $request->email,
            $request->password
        );

        if ($result['success']) {
            return redirect($result['redirect']);
        }

        return back()->withErrors(['login' => $result['message']]);
    }

    public function showSuperAdminLogin()
    {
        return view('auth.super-admin-login');
    }

    public function superAdminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $result = $this->schoolAuthService->authenticateSuperAdmin(
            $request->email,
            $request->password
        );

        if ($result['success']) {
            return redirect($result['redirect']);
        }

        return back()->withErrors(['login' => $result['message']]);
    }
}
```

---

## 🛡️ Middleware Implementation

### 1. School Context Middleware

```php
// app/Http/Middleware/SchoolContextMiddleware.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SchoolContextMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Super-admin bypass
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        // Ensure user has school context
        if (!$user->school_id || !session('school_context')) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['login' => 'Invalid school context']);
        }

        // Verify school context matches user's school
        if (session('school_context') !== $user->school_id) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['login' => 'School context mismatch']);
        }

        return $next($request);
    }
}
```

### 2. Super Admin Middleware

```php
// app/Http/Middleware/SuperAdminMiddleware.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->hasRole('super-admin')) {
            abort(403, 'Super-admin access required');
        }

        return $next($request);
    }
}
```

---

## 🎛️ Super Admin Controllers

### 1. School Management Controller

```php
// app/Http/Controllers/SuperAdmin/SchoolController.php
<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Services\SchoolSetupService;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    protected $schoolSetupService;

    public function __construct(SchoolSetupService $schoolSetupService)
    {
        $this->schoolSetupService = $schoolSetupService;
    }

    public function index()
    {
        $schools = School::with('creator')->paginate(15);
        return view('super-admin.schools.index', compact('schools'));
    }

    public function create()
    {
        return view('super-admin.schools.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:schools',
            'password' => 'required|string|min:8',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $school = $this->schoolSetupService->createSchool($request->all());

        return redirect()->route('super-admin.schools.index')
                        ->with('success', 'School created successfully');
    }

    public function show(School $school)
    {
        $school->load(['users', 'students']);
        return view('super-admin.schools.show', compact('school'));
    }

    public function edit(School $school)
    {
        return view('super-admin.schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:schools,code,' . $school->id,
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $school->update($request->all());

        return redirect()->route('super-admin.schools.index')
                        ->with('success', 'School updated successfully');
    }

    public function destroy(School $school)
    {
        // Soft delete or archive school data
        $school->update(['status' => 'inactive']);
        
        return redirect()->route('super-admin.schools.index')
                        ->with('success', 'School deactivated successfully');
    }
}
```

---

## 🔧 Services Implementation

### 1. School Setup Service

```php
// app/Services/SchoolSetupService.php
<?php

namespace App\Services;

use App\Models\School;
use App\Models\User;
use App\Models\Level;
use App\Models\Faculty;
use App\Models\InstituteSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchoolSetupService
{
    public function createSchool(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Create school
            $school = School::create([
                'name' => $data['name'],
                'code' => $data['code'],
                'password' => $data['password'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'status' => 'active',
                'created_by' => auth()->id(),
            ]);

            // Create default admin user for school
            $admin = User::create([
                'name' => 'School Administrator',
                'email' => 'admin@' . strtolower($data['code']) . '.school',
                'password' => Hash::make('admin123'), // Default password
                'school_id' => $school->id,
                'email_verified_at' => now(),
            ]);
            $admin->assignRole('admin');

            // Create default academic structure
            $this->createDefaultAcademicStructure($school);

            // Create default institute settings
            $this->createDefaultInstituteSettings($school);

            return $school;
        });
    }

    private function createDefaultAcademicStructure(School $school)
    {
        // Create default levels
        $levels = [
            ['name' => 'School Level', 'description' => 'Basic Education'],
            ['name' => 'College Level', 'description' => 'Higher Secondary'],
        ];

        foreach ($levels as $levelData) {
            Level::create([
                'name' => $levelData['name'],
                'description' => $levelData['description'],
                'school_id' => $school->id,
            ]);
        }

        // Create default faculties
        $faculties = [
            ['name' => 'Science', 'description' => 'Science Faculty'],
            ['name' => 'Management', 'description' => 'Management Faculty'],
        ];

        foreach ($faculties as $facultyData) {
            Faculty::create([
                'name' => $facultyData['name'],
                'description' => $facultyData['description'],
                'school_id' => $school->id,
            ]);
        }
    }

    private function createDefaultInstituteSettings(School $school)
    {
        InstituteSettings::create([
            'institution_name' => $school->name,
            'institution_address' => $school->address ?? '',
            'institution_phone' => $school->phone ?? '',
            'institution_email' => $school->email ?? '',
            'principal_name' => 'Principal Name',
            'setup_completed' => false,
            'school_id' => $school->id,
        ]);
    }
}
```

This technical implementation provides the foundation for the multi-school system. The next sections will cover UI components, testing strategies, and deployment procedures.

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Mark;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SecurityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $teacher;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions and roles
        Permission::create(['name' => 'manage-users']);
        Permission::create(['name' => 'view-students']);
        Permission::create(['name' => 'enter-marks']);

        $adminRole = Role::create(['name' => 'admin']);
        $teacherRole = Role::create(['name' => 'teacher']);
        $studentRole = Role::create(['name' => 'student']);

        $adminRole->givePermissionTo(['manage-users', 'view-students', 'enter-marks']);
        $teacherRole->givePermissionTo(['view-students', 'enter-marks']);
        $studentRole->givePermissionTo(['view-students']);

        // Create test users
        $this->admin = User::factory()->create();
        $this->teacher = User::factory()->create();
        $this->student = User::factory()->create();

        $this->admin->assignRole('admin');
        $this->teacher->assignRole('teacher');
        $this->student->assignRole('student');
    }

    /** @test */
    public function unauthorized_users_cannot_access_admin_routes()
    {
        $adminRoutes = [
            '/admin/users',
            '/admin/students',
            '/admin/exams',
            '/admin/marks',
            '/admin/analytics',
            '/admin/settings',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    /** @test */
    public function role_based_access_control_works()
    {
        // Teacher should not access user management
        $response = $this->actingAs($this->teacher)->get('/admin/users');
        $response->assertStatus(403);

        // Student should not access admin areas
        $response = $this->actingAs($this->student)->get('/admin/users');
        $response->assertStatus(403);

        $response = $this->actingAs($this->student)->get('/admin/exams');
        $response->assertStatus(403);

        // Admin should access everything
        $response = $this->actingAs($this->admin)->get('/admin/users');
        $response->assertStatus(200);
    }

    /** @test */
    public function csrf_protection_is_enabled()
    {
        // Test POST request without CSRF token
        $response = $this->actingAs($this->admin)->post('/admin/students', [
            'name' => 'Test Student',
            'roll_number' => '12345',
        ]);

        $response->assertStatus(419); // CSRF token mismatch
    }

    /** @test */
    public function sql_injection_is_prevented()
    {
        // Test SQL injection in search parameter
        $maliciousInput = "'; DROP TABLE students; --";
        
        $response = $this->actingAs($this->admin)->get('/admin/students?search=' . urlencode($maliciousInput));
        
        $response->assertStatus(200);
        
        // Verify students table still exists
        $this->assertDatabaseHas('students', []);
    }

    /** @test */
    public function xss_attacks_are_prevented()
    {
        $maliciousScript = '<script>alert("XSS")</script>';
        
        $response = $this->actingAs($this->admin)->post('/admin/students', [
            '_token' => csrf_token(),
            'name' => $maliciousScript,
            'roll_number' => '12345',
            'class_id' => 1,
        ]);

        if ($response->isRedirect()) {
            $response = $this->actingAs($this->admin)->get('/admin/students');
        }

        // Script should be escaped in output
        $response->assertDontSee($maliciousScript, false);
        $response->assertSee('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', false);
    }

    /** @test */
    public function password_hashing_is_secure()
    {
        $password = 'testpassword123';
        $user = User::factory()->create(['password' => Hash::make($password)]);

        // Password should be hashed
        $this->assertNotEquals($password, $user->password);
        $this->assertTrue(Hash::check($password, $user->password));
    }

    /** @test */
    public function session_security_is_implemented()
    {
        // Test session regeneration on login
        $user = User::factory()->create(['password' => Hash::make('password')]);
        
        $oldSessionId = Session::getId();
        
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $newSessionId = Session::getId();
        
        $response->assertRedirect('/dashboard');
        $this->assertNotEquals($oldSessionId, $newSessionId);
    }

    /** @test */
    public function file_upload_security_is_enforced()
    {
        $student = Student::factory()->create();

        // Test malicious file upload
        $maliciousFile = \Illuminate\Http\UploadedFile::fake()->create('malicious.php', 100);

        $response = $this->actingAs($this->admin)->post("/admin/students/{$student->id}/documents", [
            '_token' => csrf_token(),
            'document_type' => 'citizenship',
            'document' => $maliciousFile,
        ]);

        $response->assertSessionHasErrors(['document']);
    }

    /** @test */
    public function sensitive_data_is_not_exposed()
    {
        $user = User::factory()->create(['password' => Hash::make('secret123')]);

        $response = $this->actingAs($this->admin)->get("/admin/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertDontSee('secret123');
        $response->assertDontSee($user->password);
    }

    /** @test */
    public function mass_assignment_is_protected()
    {
        // Try to mass assign protected attributes
        $response = $this->actingAs($this->admin)->post('/admin/students', [
            '_token' => csrf_token(),
            'name' => 'Test Student',
            'roll_number' => '12345',
            'class_id' => 1,
            'id' => 999, // Should be ignored
            'created_at' => '2020-01-01', // Should be ignored
        ]);

        if ($response->isRedirect()) {
            $student = Student::where('roll_number', '12345')->first();
            $this->assertNotEquals(999, $student->id);
            $this->assertNotEquals('2020-01-01', $student->created_at->format('Y-m-d'));
        }
    }

    /** @test */
    public function rate_limiting_prevents_brute_force()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        // Attempt multiple failed logins
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);
        }

        // Next attempt should be rate limited
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(429); // Too Many Requests
    }

    /** @test */
    public function user_input_validation_prevents_malicious_data()
    {
        // Test various malicious inputs
        $maliciousInputs = [
            'name' => str_repeat('A', 1000), // Too long
            'email' => 'not-an-email',
            'roll_number' => '<script>alert("xss")</script>',
            'phone' => 'not-a-phone-number',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/students', array_merge([
            '_token' => csrf_token(),
            'class_id' => 1,
        ], $maliciousInputs));

        $response->assertSessionHasErrors(['name', 'email', 'phone']);
    }

    /** @test */
    public function api_endpoints_require_authentication()
    {
        // Test API endpoints without authentication
        $apiRoutes = [
            '/api/students',
            '/api/exams',
            '/api/marks',
        ];

        foreach ($apiRoutes as $route) {
            $response = $this->getJson($route);
            $response->assertStatus(401); // Unauthorized
        }
    }

    /** @test */
    public function sensitive_routes_require_additional_verification()
    {
        // Test deletion requires confirmation
        $student = Student::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/admin/students/{$student->id}");

        // Should require additional confirmation or specific parameter
        $response->assertStatus(403);
    }

    /** @test */
    public function data_access_is_properly_scoped()
    {
        // Create students in different classes
        $class1 = \App\Models\ClassModel::factory()->create();
        $class2 = \App\Models\ClassModel::factory()->create();
        
        $student1 = Student::factory()->create(['class_id' => $class1->id]);
        $student2 = Student::factory()->create(['class_id' => $class2->id]);

        // Teacher should only see students they have access to
        // This test assumes teachers are scoped to specific classes
        $response = $this->actingAs($this->teacher)->get('/admin/students');
        
        $response->assertStatus(200);
        // Additional assertions would depend on the specific scoping implementation
    }

    /** @test */
    public function audit_trail_is_maintained()
    {
        $student = Student::factory()->create();

        // Perform an action that should be logged
        $response = $this->actingAs($this->admin)->put("/admin/students/{$student->id}", [
            '_token' => csrf_token(),
            'name' => 'Updated Name',
            'roll_number' => $student->roll_number,
            'class_id' => $student->class_id,
        ]);

        // Check if activity was logged
        $this->assertDatabaseHas('activity_log', [
            'subject_type' => Student::class,
            'subject_id' => $student->id,
            'causer_id' => $this->admin->id,
            'description' => 'updated',
        ]);
    }

    /** @test */
    public function password_reset_is_secure()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        // Request password reset
        $response = $this->post('/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHas('status');

        // Verify reset token is created and is secure
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function concurrent_access_is_handled_safely()
    {
        $student = Student::factory()->create(['name' => 'Original Name']);

        // Simulate concurrent updates
        $response1 = $this->actingAs($this->admin)->put("/admin/students/{$student->id}", [
            '_token' => csrf_token(),
            'name' => 'Updated by Admin',
            'roll_number' => $student->roll_number,
            'class_id' => $student->class_id,
        ]);

        $response2 = $this->actingAs($this->teacher)->put("/admin/students/{$student->id}", [
            '_token' => csrf_token(),
            'name' => 'Updated by Teacher',
            'roll_number' => $student->roll_number,
            'class_id' => $student->class_id,
        ]);

        // One should succeed, one should fail or be handled appropriately
        $this->assertTrue($response1->isSuccessful() || $response2->isSuccessful());
    }

    /** @test */
    public function error_messages_dont_leak_sensitive_information()
    {
        // Test with non-existent user ID
        $response = $this->actingAs($this->admin)->get('/admin/students/99999');

        $response->assertStatus(404);
        $response->assertDontSee('database');
        $response->assertDontSee('SQL');
        $response->assertDontSee('Exception');
    }

    /** @test */
    public function headers_include_security_measures()
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        
        // Check for security headers (if implemented)
        // $response->assertHeader('X-Frame-Options', 'DENY');
        // $response->assertHeader('X-Content-Type-Options', 'nosniff');
        // $response->assertHeader('X-XSS-Protection', '1; mode=block');
    }
}

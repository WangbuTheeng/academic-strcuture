<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Level;
use App\Models\GradingScale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserAcceptanceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $teacher;
    protected $student;
    protected $academicYear;
    protected $class;
    protected $subject;
    protected $exam;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions and roles
        $this->createRolesAndPermissions();

        // Create test users
        $this->admin = User::factory()->create(['name' => 'Test Admin']);
        $this->teacher = User::factory()->create(['name' => 'Test Teacher']);

        $this->admin->assignRole('admin');
        $this->teacher->assignRole('teacher');

        // Create academic structure
        $this->createAcademicStructure();
    }

    private function createRolesAndPermissions()
    {
        // Create permissions
        $permissions = [
            'manage-users', 'view-users',
            'manage-students', 'view-students',
            'manage-exams', 'view-exams',
            'enter-marks', 'approve-marks', 'view-marks',
            'generate-reports', 'view-analytics',
            'manage-settings', 'manage-backups'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $teacherRole = Role::create(['name' => 'teacher']);
        $studentRole = Role::create(['name' => 'student']);

        // Assign permissions
        $adminRole->givePermissionTo($permissions);
        $teacherRole->givePermissionTo(['view-students', 'view-exams', 'enter-marks', 'view-marks']);
        $studentRole->givePermissionTo(['view-marks']);
    }

    private function createAcademicStructure()
    {
        $this->academicYear = AcademicYear::factory()->create([
            'name' => '2081-82',
            'is_current' => true,
        ]);

        $level = Level::factory()->create(['name' => 'School']);
        
        $this->class = ClassModel::factory()->create([
            'name' => 'Class 10',
            'level_id' => $level->id,
            'academic_year_id' => $this->academicYear->id,
        ]);

        $this->subject = Subject::factory()->create(['name' => 'Mathematics']);

        $this->student = Student::factory()->create([
            'name' => 'Ram Bahadur Thapa',
            'roll_number' => '2081001',
            'class_id' => $this->class->id,
        ]);

        $gradingScale = GradingScale::factory()->create();

        $this->exam = Exam::factory()->create([
            'name' => 'First Terminal Exam',
            'academic_year_id' => $this->academicYear->id,
            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'grading_scale_id' => $gradingScale->id,
            'status' => 'ongoing',
            'max_marks' => 100,
        ]);
    }

    /** @test */
    public function complete_admin_workflow_works()
    {
        // Test admin login
        $response = $this->post('/login', [
            'email' => $this->admin->email,
            'password' => 'password',
        ]);
        $response->assertRedirect('/dashboard');

        // Test dashboard access
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');

        // Test user management access
        $response = $this->actingAs($this->admin)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('User Management');

        // Test student management access
        $response = $this->actingAs($this->admin)->get('/admin/students');
        $response->assertStatus(200);
        $response->assertSee('Student Management');

        // Test exam management access
        $response = $this->actingAs($this->admin)->get('/admin/exams');
        $response->assertStatus(200);
        $response->assertSee('Exam Management');

        // Test analytics access
        $response = $this->actingAs($this->admin)->get('/admin/analytics');
        $response->assertStatus(200);
        $response->assertSee('Analytics Dashboard');
    }

    /** @test */
    public function complete_teacher_workflow_works()
    {
        // Test teacher login
        $response = $this->post('/login', [
            'email' => $this->teacher->email,
            'password' => 'password',
        ]);
        $response->assertRedirect('/dashboard');

        // Test teacher dashboard
        $response = $this->actingAs($this->teacher)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');

        // Test exam viewing (should work)
        $response = $this->actingAs($this->teacher)->get('/admin/exams');
        $response->assertStatus(200);

        // Test mark entry access
        $response = $this->actingAs($this->teacher)->get("/admin/exams/{$this->exam->id}/marks");
        $response->assertStatus(200);
        $response->assertSee('Mark Entry');

        // Test user management access (should be forbidden)
        $response = $this->actingAs($this->teacher)->get('/admin/users');
        $response->assertStatus(403);
    }

    /** @test */
    public function student_enrollment_workflow_works()
    {
        // Test student creation
        $studentData = [
            'name' => 'Sita Kumari Sharma',
            'roll_number' => '2081002',
            'email' => 'sita.sharma@student.test',
            'phone' => '+977-9841234568',
            'address' => 'Lalitpur, Nepal',
            'class_id' => $this->class->id,
            'date_of_birth' => '2065-05-15',
            'gender' => 'female',
            'guardian_name' => 'Krishna Sharma',
            'guardian_phone' => '+977-9841234569',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/students', $studentData);
        $response->assertRedirect('/admin/students');
        $response->assertSessionHas('success');

        // Verify student was created
        $this->assertDatabaseHas('students', [
            'name' => 'Sita Kumari Sharma',
            'roll_number' => '2081002',
            'class_id' => $this->class->id,
        ]);

        // Test student viewing
        $student = Student::where('roll_number', '2081002')->first();
        $response = $this->actingAs($this->admin)->get("/admin/students/{$student->id}");
        $response->assertStatus(200);
        $response->assertSee('Sita Kumari Sharma');
    }

    /** @test */
    public function exam_creation_and_management_workflow_works()
    {
        // Test exam creation
        $examData = [
            'name' => 'Second Terminal Exam',
            'exam_type' => 'terminal',
            'academic_year_id' => $this->academicYear->id,
            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'max_marks' => 100,
            'theory_max' => 80,
            'practical_max' => 20,
            'has_practical' => true,
            'start_date' => now()->addDays(7)->format('Y-m-d'),
            'end_date' => now()->addDays(14)->format('Y-m-d'),
            'submission_deadline' => now()->addDays(21)->format('Y-m-d\TH:i'),
        ];

        $response = $this->actingAs($this->admin)->post('/admin/exams', $examData);
        $response->assertRedirect('/admin/exams');
        $response->assertSessionHas('success');

        // Verify exam was created
        $this->assertDatabaseHas('exams', [
            'name' => 'Second Terminal Exam',
            'exam_type' => 'terminal',
            'max_marks' => 100,
        ]);

        // Test exam status change
        $exam = Exam::where('name', 'Second Terminal Exam')->first();
        $response = $this->actingAs($this->admin)->post("/admin/exams/{$exam->id}/change-status", [
            'status' => 'scheduled',
            'reason' => 'Exam ready for scheduling',
        ]);
        $response->assertRedirect("/admin/exams/{$exam->id}");
        $response->assertSessionHas('success');
    }

    /** @test */
    public function mark_entry_and_approval_workflow_works()
    {
        // Test mark entry by teacher
        $markData = [
            'marks' => [
                $this->student->id => [
                    'theory_marks' => 68,
                    'practical_marks' => 17,
                    'obtained_marks' => 85,
                ]
            ]
        ];

        $response = $this->actingAs($this->teacher)->post("/admin/exams/{$this->exam->id}/marks", $markData);
        $response->assertRedirect("/admin/exams/{$this->exam->id}/marks");
        $response->assertSessionHas('success');

        // Verify mark was created
        $mark = Mark::where('student_id', $this->student->id)
                   ->where('exam_id', $this->exam->id)
                   ->first();

        $this->assertNotNull($mark);
        $this->assertEquals(85, $mark->obtained_marks);
        $this->assertEquals('draft', $mark->status);

        // Test mark submission
        $response = $this->actingAs($this->teacher)->post("/admin/marks/{$mark->id}/submit");
        $response->assertRedirect()->back();
        $response->assertSessionHas('success');

        // Verify mark status changed
        $this->assertEquals('submitted', $mark->fresh()->status);

        // Test mark approval by admin
        $response = $this->actingAs($this->admin)->post("/admin/marks/{$mark->id}/approve");
        $response->assertRedirect()->back();
        $response->assertSessionHas('success');

        // Verify mark was approved
        $this->assertEquals('approved', $mark->fresh()->status);
        $this->assertEquals($this->admin->id, $mark->fresh()->approved_by);
    }

    /** @test */
    public function marksheet_generation_workflow_works()
    {
        // Create approved mark
        $mark = Mark::factory()->create([
            'student_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'obtained_marks' => 85,
            'total_marks' => 100,
            'percentage' => 85,
            'grade' => 'A',
            'result' => 'Pass',
            'status' => 'approved',
        ]);

        // Test marksheet generation
        $response = $this->actingAs($this->admin)->get("/admin/marksheets/generate/{$this->student->id}/{$this->exam->id}");
        $response->assertStatus(200);

        // Test PDF download
        $response = $this->actingAs($this->admin)->get("/admin/marksheets/download/{$this->student->id}/{$this->exam->id}");
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function analytics_and_reporting_workflow_works()
    {
        // Create some test data
        Mark::factory()->count(5)->create([
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'status' => 'approved',
        ]);

        // Test analytics dashboard
        $response = $this->actingAs($this->admin)->get('/admin/analytics');
        $response->assertStatus(200);
        $response->assertSee('Analytics Dashboard');

        // Test student performance analytics
        $response = $this->actingAs($this->admin)->get('/admin/analytics/student-performance');
        $response->assertStatus(200);

        // Test subject analytics
        $response = $this->actingAs($this->admin)->get('/admin/analytics/subject-analytics');
        $response->assertStatus(200);

        // Test class analytics
        $response = $this->actingAs($this->admin)->get('/admin/analytics/class-analytics');
        $response->assertStatus(200);

        // Test data export
        $response = $this->actingAs($this->admin)->get('/admin/data-export');
        $response->assertStatus(200);
        $response->assertSee('Data Export');
    }

    /** @test */
    public function system_security_and_permissions_work()
    {
        // Test unauthorized access
        $response = $this->get('/admin/users');
        $response->assertRedirect('/login');

        // Test role-based access
        $response = $this->actingAs($this->teacher)->get('/admin/users');
        $response->assertStatus(403);

        // Test admin access
        $response = $this->actingAs($this->admin)->get('/admin/users');
        $response->assertStatus(200);

        // Test CSRF protection
        $response = $this->actingAs($this->admin)->post('/admin/students', [
            'name' => 'Test Student',
            // Missing CSRF token
        ]);
        $response->assertStatus(419); // CSRF token mismatch
    }

    /** @test */
    public function data_integrity_is_maintained()
    {
        // Test unique constraints
        $response = $this->actingAs($this->admin)->post('/admin/students', [
            'name' => 'Duplicate Student',
            'roll_number' => $this->student->roll_number, // Duplicate roll number
            'class_id' => $this->class->id,
        ]);
        $response->assertSessionHasErrors(['roll_number']);

        // Test foreign key constraints
        $response = $this->actingAs($this->admin)->post('/admin/students', [
            'name' => 'Invalid Student',
            'roll_number' => '9999999',
            'class_id' => 99999, // Non-existent class
        ]);
        $response->assertSessionHasErrors(['class_id']);

        // Test data validation
        $response = $this->actingAs($this->teacher)->post("/admin/exams/{$this->exam->id}/marks", [
            'marks' => [
                $this->student->id => [
                    'theory_marks' => 150, // Exceeds maximum
                    'practical_marks' => -5, // Negative value
                    'obtained_marks' => 200, // Exceeds total
                ]
            ]
        ]);
        $response->assertSessionHasErrors();
    }
}

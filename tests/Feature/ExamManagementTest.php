<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Exam;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\GradingScale;
use App\Models\Level;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ExamManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $teacher;
    protected $academicYear;
    protected $semester;
    protected $class;
    protected $subject;
    protected $gradingScale;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        Permission::create(['name' => 'manage-exams']);
        Permission::create(['name' => 'view-exams']);

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $teacherRole = Role::create(['name' => 'teacher']);

        $adminRole->givePermissionTo(['manage-exams', 'view-exams']);
        $teacherRole->givePermissionTo(['view-exams']);

        // Create users
        $this->admin = User::factory()->create();
        $this->teacher = User::factory()->create();

        $this->admin->assignRole('admin');
        $this->teacher->assignRole('teacher');

        // Create related models
        $this->academicYear = AcademicYear::factory()->create();
        $this->semester = Semester::factory()->create(['academic_year_id' => $this->academicYear->id]);
        
        $level = Level::factory()->create();
        $this->class = ClassModel::factory()->create(['level_id' => $level->id]);
        
        $department = Department::factory()->create();
        $this->subject = Subject::factory()->create(['department_id' => $department->id]);
        
        $this->gradingScale = GradingScale::factory()->create();
    }

    /** @test */
    public function admin_can_view_exams_index()
    {
        $response = $this->actingAs($this->admin)->get('/admin/exams');

        $response->assertStatus(200);
        $response->assertViewIs('admin.exams.index');
    }

    /** @test */
    public function teacher_can_view_exams_index()
    {
        $response = $this->actingAs($this->teacher)->get('/admin/exams');

        $response->assertStatus(200);
        $response->assertViewIs('admin.exams.index');
    }

    /** @test */
    public function guest_cannot_access_exams()
    {
        $response = $this->get('/admin/exams');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function admin_can_view_create_exam_form()
    {
        $response = $this->actingAs($this->admin)->get('/admin/exams/create');

        $response->assertStatus(200);
        $response->assertViewIs('admin.exams.create');
    }

    /** @test */
    public function teacher_cannot_view_create_exam_form()
    {
        $response = $this->actingAs($this->teacher)->get('/admin/exams/create');

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_create_exam()
    {
        $examData = [
            'name' => 'First Terminal Exam',
            'exam_type' => 'terminal',
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'max_marks' => 100,
            'theory_max' => 80,
            'practical_max' => 20,
            'assess_max' => 0,
            'has_practical' => true,
            'has_assessment' => false,
            'start_date' => now()->addDays(7)->format('Y-m-d'),
            'end_date' => now()->addDays(14)->format('Y-m-d'),
            'submission_deadline' => now()->addDays(21)->format('Y-m-d\TH:i'),
            'grading_scale_id' => $this->gradingScale->id,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/exams', $examData);

        $response->assertRedirect('/admin/exams');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('exams', [
            'name' => 'First Terminal Exam',
            'exam_type' => 'terminal',
            'max_marks' => 100,
            'created_by' => $this->admin->id,
        ]);
    }

    /** @test */
    public function exam_creation_requires_valid_data()
    {
        $response = $this->actingAs($this->admin)->post('/admin/exams', [
            'name' => '',
            'exam_type' => 'invalid',
            'max_marks' => -10,
        ]);

        $response->assertSessionHasErrors(['name', 'exam_type', 'academic_year_id', 'max_marks']);
    }

    /** @test */
    public function admin_can_view_exam_details()
    {
        $exam = Exam::factory()->create([
            'academic_year_id' => $this->academicYear->id,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/exams/{$exam->id}");

        $response->assertStatus(200);
        $response->assertViewIs('admin.exams.show');
        $response->assertViewHas('exam', $exam);
    }

    /** @test */
    public function admin_can_edit_exam()
    {
        $exam = Exam::factory()->create([
            'academic_year_id' => $this->academicYear->id,
            'status' => 'draft',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/exams/{$exam->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('admin.exams.edit');
        $response->assertViewHas('exam', $exam);
    }

    /** @test */
    public function admin_can_update_exam()
    {
        $exam = Exam::factory()->create([
            'academic_year_id' => $this->academicYear->id,
            'status' => 'draft',
            'name' => 'Old Name',
            'created_by' => $this->admin->id,
        ]);

        $updateData = [
            'name' => 'Updated Exam Name',
            'exam_type' => 'assessment',
            'academic_year_id' => $this->academicYear->id,
            'max_marks' => 150,
            'theory_max' => 120,
            'practical_max' => 30,
            'assess_max' => 0,
            'has_practical' => true,
            'has_assessment' => false,
            'start_date' => now()->addDays(7)->format('Y-m-d'),
            'end_date' => now()->addDays(14)->format('Y-m-d'),
            'submission_deadline' => now()->addDays(21)->format('Y-m-d\TH:i'),
        ];

        $response = $this->actingAs($this->admin)->put("/admin/exams/{$exam->id}", $updateData);

        $response->assertRedirect("/admin/exams/{$exam->id}");
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('exams', [
            'id' => $exam->id,
            'name' => 'Updated Exam Name',
            'exam_type' => 'assessment',
            'max_marks' => 150,
        ]);
    }

    /** @test */
    public function cannot_edit_locked_exam()
    {
        $exam = Exam::factory()->create([
            'academic_year_id' => $this->academicYear->id,
            'status' => 'locked',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/exams/{$exam->id}/edit");

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_change_exam_status()
    {
        $exam = Exam::factory()->create([
            'academic_year_id' => $this->academicYear->id,
            'status' => 'draft',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/exams/{$exam->id}/change-status", [
            'status' => 'scheduled',
            'reason' => 'Exam is ready to be scheduled',
        ]);

        $response->assertRedirect("/admin/exams/{$exam->id}");
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('exams', [
            'id' => $exam->id,
            'status' => 'scheduled',
        ]);
    }

    /** @test */
    public function admin_can_delete_draft_exam()
    {
        $exam = Exam::factory()->create([
            'academic_year_id' => $this->academicYear->id,
            'status' => 'draft',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/exams/{$exam->id}");

        $response->assertRedirect('/admin/exams');
        $response->assertSessionHas('success');
        
        $this->assertSoftDeleted('exams', ['id' => $exam->id]);
    }

    /** @test */
    public function cannot_delete_non_draft_exam()
    {
        $exam = Exam::factory()->create([
            'academic_year_id' => $this->academicYear->id,
            'status' => 'ongoing',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/exams/{$exam->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('exams', ['id' => $exam->id]);
    }

    /** @test */
    public function exam_index_shows_filtered_results()
    {
        $exam1 = Exam::factory()->create([
            'academic_year_id' => $this->academicYear->id,
            'status' => 'draft',
            'exam_type' => 'terminal',
        ]);

        $exam2 = Exam::factory()->create([
            'academic_year_id' => $this->academicYear->id,
            'status' => 'ongoing',
            'exam_type' => 'assessment',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/exams?status=draft');

        $response->assertStatus(200);
        $response->assertViewHas('exams');
        
        $exams = $response->viewData('exams');
        $this->assertTrue($exams->contains($exam1));
        $this->assertFalse($exams->contains($exam2));
    }

    /** @test */
    public function exam_validation_prevents_invalid_dates()
    {
        $examData = [
            'name' => 'Test Exam',
            'exam_type' => 'terminal',
            'academic_year_id' => $this->academicYear->id,
            'max_marks' => 100,
            'theory_max' => 100,
            'start_date' => now()->addDays(14)->format('Y-m-d'),
            'end_date' => now()->addDays(7)->format('Y-m-d'), // End before start
            'submission_deadline' => now()->addDays(21)->format('Y-m-d\TH:i'),
        ];

        $response = $this->actingAs($this->admin)->post('/admin/exams', $examData);

        $response->assertSessionHasErrors(['end_date']);
    }

    /** @test */
    public function exam_marking_scheme_validation()
    {
        $examData = [
            'name' => 'Test Exam',
            'exam_type' => 'terminal',
            'academic_year_id' => $this->academicYear->id,
            'max_marks' => 100,
            'theory_max' => 60,
            'practical_max' => 30,
            'assess_max' => 20, // Total exceeds max_marks
            'has_practical' => true,
            'has_assessment' => true,
            'start_date' => now()->addDays(7)->format('Y-m-d'),
            'end_date' => now()->addDays(14)->format('Y-m-d'),
            'submission_deadline' => now()->addDays(21)->format('Y-m-d\TH:i'),
        ];

        $response = $this->actingAs($this->admin)->post('/admin/exams', $examData);

        $response->assertSessionHasErrors();
    }
}

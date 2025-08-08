<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\Level;
use App\Models\GradingScale;
use App\Models\GradeRange;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MarkEntryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $teacher;
    protected $student;
    protected $exam;
    protected $subject;
    protected $gradingScale;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        Permission::create(['name' => 'enter-marks']);
        Permission::create(['name' => 'approve-marks']);
        Permission::create(['name' => 'view-marks']);

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $teacherRole = Role::create(['name' => 'teacher']);

        $adminRole->givePermissionTo(['enter-marks', 'approve-marks', 'view-marks']);
        $teacherRole->givePermissionTo(['enter-marks', 'view-marks']);

        // Create users
        $this->admin = User::factory()->create();
        $this->teacher = User::factory()->create();

        $this->admin->assignRole('admin');
        $this->teacher->assignRole('teacher');

        // Create related models
        $academicYear = AcademicYear::factory()->create();
        $level = Level::factory()->create();
        $class = ClassModel::factory()->create(['level_id' => $level->id]);
        
        $this->student = Student::factory()->create(['class_id' => $class->id]);
        $this->subject = Subject::factory()->create();
        
        // Create grading scale with grade ranges
        $this->gradingScale = GradingScale::factory()->create();
        GradeRange::factory()->create([
            'grading_scale_id' => $this->gradingScale->id,
            'grade' => 'A+',
            'min_percentage' => 90,
            'max_percentage' => 100,
            'grade_point' => 4.0,
        ]);
        GradeRange::factory()->create([
            'grading_scale_id' => $this->gradingScale->id,
            'grade' => 'A',
            'min_percentage' => 80,
            'max_percentage' => 89,
            'grade_point' => 3.7,
        ]);
        
        $this->exam = Exam::factory()->create([
            'academic_year_id' => $academicYear->id,
            'class_id' => $class->id,
            'subject_id' => $this->subject->id,
            'grading_scale_id' => $this->gradingScale->id,
            'status' => 'ongoing',
            'max_marks' => 100,
            'theory_max' => 80,
            'practical_max' => 20,
        ]);
    }

    /** @test */
    public function teacher_can_view_mark_entry_page()
    {
        $response = $this->actingAs($this->teacher)->get("/admin/exams/{$this->exam->id}/marks");

        $response->assertStatus(200);
        $response->assertViewIs('admin.marks.entry');
        $response->assertViewHas('exam', $this->exam);
    }

    /** @test */
    public function admin_can_view_mark_entry_page()
    {
        $response = $this->actingAs($this->admin)->get("/admin/exams/{$this->exam->id}/marks");

        $response->assertStatus(200);
        $response->assertViewIs('admin.marks.entry');
    }

    /** @test */
    public function guest_cannot_access_mark_entry()
    {
        $response = $this->get("/admin/exams/{$this->exam->id}/marks");

        $response->assertRedirect('/login');
    }

    /** @test */
    public function teacher_can_enter_marks()
    {
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
        
        $this->assertDatabaseHas('marks', [
            'student_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'obtained_marks' => 85,
            'theory_marks' => 68,
            'practical_marks' => 17,
            'status' => 'draft',
            'entered_by' => $this->teacher->id,
        ]);
    }

    /** @test */
    public function marks_are_automatically_calculated()
    {
        $markData = [
            'marks' => [
                $this->student->id => [
                    'theory_marks' => 68,
                    'practical_marks' => 17,
                    'obtained_marks' => 85,
                ]
            ]
        ];

        $this->actingAs($this->teacher)->post("/admin/exams/{$this->exam->id}/marks", $markData);

        $mark = Mark::where('student_id', $this->student->id)
                   ->where('exam_id', $this->exam->id)
                   ->first();

        $this->assertEquals(85.0, $mark->percentage);
        $this->assertNotEmpty($mark->grade);
        $this->assertNotEmpty($mark->result);
    }

    /** @test */
    public function marks_validation_prevents_exceeding_maximum()
    {
        $markData = [
            'marks' => [
                $this->student->id => [
                    'theory_marks' => 90, // Exceeds theory_max of 80
                    'practical_marks' => 17,
                    'obtained_marks' => 107, // Exceeds max_marks of 100
                ]
            ]
        ];

        $response = $this->actingAs($this->teacher)->post("/admin/exams/{$this->exam->id}/marks", $markData);

        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('marks', [
            'student_id' => $this->student->id,
            'exam_id' => $this->exam->id,
        ]);
    }

    /** @test */
    public function marks_validation_prevents_negative_values()
    {
        $markData = [
            'marks' => [
                $this->student->id => [
                    'theory_marks' => -5,
                    'practical_marks' => 17,
                    'obtained_marks' => 12,
                ]
            ]
        ];

        $response = $this->actingAs($this->teacher)->post("/admin/exams/{$this->exam->id}/marks", $markData);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function teacher_can_update_existing_marks()
    {
        $mark = Mark::factory()->create([
            'student_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'obtained_marks' => 75,
            'status' => 'draft',
            'entered_by' => $this->teacher->id,
        ]);

        $markData = [
            'marks' => [
                $this->student->id => [
                    'theory_marks' => 70,
                    'practical_marks' => 18,
                    'obtained_marks' => 88,
                ]
            ]
        ];

        $response = $this->actingAs($this->teacher)->post("/admin/exams/{$this->exam->id}/marks", $markData);

        $response->assertRedirect("/admin/exams/{$this->exam->id}/marks");
        
        $this->assertDatabaseHas('marks', [
            'id' => $mark->id,
            'obtained_marks' => 88,
            'theory_marks' => 70,
            'practical_marks' => 18,
        ]);
    }

    /** @test */
    public function teacher_can_submit_marks_for_approval()
    {
        $mark = Mark::factory()->create([
            'student_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'status' => 'draft',
            'entered_by' => $this->teacher->id,
        ]);

        $response = $this->actingAs($this->teacher)->post("/admin/marks/{$mark->id}/submit");

        $response->assertRedirect()->back();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('marks', [
            'id' => $mark->id,
            'status' => 'submitted',
        ]);
    }

    /** @test */
    public function admin_can_approve_marks()
    {
        $mark = Mark::factory()->create([
            'student_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'status' => 'submitted',
            'entered_by' => $this->teacher->id,
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/marks/{$mark->id}/approve");

        $response->assertRedirect()->back();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('marks', [
            'id' => $mark->id,
            'status' => 'approved',
            'approved_by' => $this->admin->id,
        ]);
    }

    /** @test */
    public function teacher_cannot_approve_marks()
    {
        $mark = Mark::factory()->create([
            'student_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'status' => 'submitted',
            'entered_by' => $this->teacher->id,
        ]);

        $response = $this->actingAs($this->teacher)->post("/admin/marks/{$mark->id}/approve");

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_reject_marks()
    {
        $mark = Mark::factory()->create([
            'student_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'status' => 'submitted',
            'entered_by' => $this->teacher->id,
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/marks/{$mark->id}/reject", [
            'reason' => 'Marks need verification',
        ]);

        $response->assertRedirect()->back();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('marks', [
            'id' => $mark->id,
            'status' => 'rejected',
        ]);
    }

    /** @test */
    public function cannot_edit_approved_marks()
    {
        $mark = Mark::factory()->create([
            'student_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'status' => 'approved',
            'entered_by' => $this->teacher->id,
        ]);

        $markData = [
            'marks' => [
                $this->student->id => [
                    'theory_marks' => 70,
                    'practical_marks' => 18,
                    'obtained_marks' => 88,
                ]
            ]
        ];

        $response = $this->actingAs($this->teacher)->post("/admin/exams/{$this->exam->id}/marks", $markData);

        $response->assertStatus(403);
    }

    /** @test */
    public function bulk_mark_entry_works()
    {
        $student2 = Student::factory()->create(['class_id' => $this->student->class_id]);
        $student3 = Student::factory()->create(['class_id' => $this->student->class_id]);

        $markData = [
            'marks' => [
                $this->student->id => [
                    'theory_marks' => 68,
                    'practical_marks' => 17,
                    'obtained_marks' => 85,
                ],
                $student2->id => [
                    'theory_marks' => 72,
                    'practical_marks' => 18,
                    'obtained_marks' => 90,
                ],
                $student3->id => [
                    'theory_marks' => 60,
                    'practical_marks' => 15,
                    'obtained_marks' => 75,
                ],
            ]
        ];

        $response = $this->actingAs($this->teacher)->post("/admin/exams/{$this->exam->id}/marks", $markData);

        $response->assertRedirect("/admin/exams/{$this->exam->id}/marks");
        
        $this->assertDatabaseHas('marks', ['student_id' => $this->student->id, 'obtained_marks' => 85]);
        $this->assertDatabaseHas('marks', ['student_id' => $student2->id, 'obtained_marks' => 90]);
        $this->assertDatabaseHas('marks', ['student_id' => $student3->id, 'obtained_marks' => 75]);
    }

    /** @test */
    public function mark_entry_creates_audit_trail()
    {
        $markData = [
            'marks' => [
                $this->student->id => [
                    'theory_marks' => 68,
                    'practical_marks' => 17,
                    'obtained_marks' => 85,
                ]
            ]
        ];

        $this->actingAs($this->teacher)->post("/admin/exams/{$this->exam->id}/marks", $markData);

        // Check if activity log entry was created
        $this->assertDatabaseHas('activity_log', [
            'subject_type' => Mark::class,
            'causer_id' => $this->teacher->id,
            'description' => 'created',
        ]);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Mark;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MarkPassFailTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $school;
    protected $academicYear;
    protected $class;
    protected $student;
    protected $subject;
    protected $exam;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->school = School::factory()->create();
        $this->user = User::factory()->create(['school_id' => $this->school->id]);
        $this->academicYear = AcademicYear::factory()->create(['school_id' => $this->school->id]);
        $this->class = ClassModel::factory()->create(['school_id' => $this->school->id]);
        $this->student = Student::factory()->create(['school_id' => $this->school->id, 'class_id' => $this->class->id]);
        $this->subject = Subject::factory()->create(['school_id' => $this->school->id]);

        // Create exam with minimum passing marks
        $this->exam = Exam::create([
            'school_id' => $this->school->id,
            'name' => 'Test Exam',
            'exam_type' => 'terminal',
            'academic_year_id' => $this->academicYear->id,
            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'max_marks' => 100,
            'theory_max' => 60,
            'theory_pass_marks' => 20,
            'practical_max' => 40,
            'practical_pass_marks' => 13,
            'has_practical' => true,
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'submission_deadline' => now()->addDays(10),
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user);
    }

    /** @test */
    public function student_passes_when_all_components_above_minimum()
    {
        $mark = Mark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'theory_marks' => 45, // Above minimum of 20
            'practical_marks' => 30, // Above minimum of 13
            'created_by' => $this->user->id,
        ]);

        $mark->performCalculations();

        $this->assertEquals('Pass', $mark->result);
        $this->assertNotEquals('N/G', $mark->grade);
    }

    /** @test */
    public function student_fails_when_theory_below_minimum()
    {
        $mark = Mark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'theory_marks' => 15, // Below minimum of 20
            'practical_marks' => 35, // Above minimum of 13
            'created_by' => $this->user->id,
        ]);

        $mark->performCalculations();

        $this->assertEquals('Fail', $mark->result);
        $this->assertEquals('N/G', $mark->grade);
        $this->assertEquals(0.00, $mark->gpa);
    }

    /** @test */
    public function student_fails_when_practical_below_minimum()
    {
        $mark = Mark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'theory_marks' => 50, // Above minimum of 20
            'practical_marks' => 10, // Below minimum of 13
            'created_by' => $this->user->id,
        ]);

        $mark->performCalculations();

        $this->assertEquals('Fail', $mark->result);
        $this->assertEquals('N/G', $mark->grade);
        $this->assertEquals(0.00, $mark->gpa);
    }

    /** @test */
    public function student_fails_when_both_components_below_minimum()
    {
        $mark = Mark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'theory_marks' => 15, // Below minimum of 20
            'practical_marks' => 10, // Below minimum of 13
            'created_by' => $this->user->id,
        ]);

        $mark->performCalculations();

        $this->assertEquals('Fail', $mark->result);
        $this->assertEquals('N/G', $mark->grade);
        $this->assertEquals(0.00, $mark->gpa);
    }

    /** @test */
    public function student_passes_when_no_minimum_marks_set()
    {
        // Create exam without minimum passing marks
        $examNoMin = Exam::create([
            'school_id' => $this->school->id,
            'name' => 'Test Exam No Min',
            'exam_type' => 'terminal',
            'academic_year_id' => $this->academicYear->id,
            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'max_marks' => 100,
            'theory_max' => 60,
            'theory_pass_marks' => 0, // No minimum set
            'practical_max' => 40,
            'practical_pass_marks' => 0, // No minimum set
            'has_practical' => true,
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'submission_deadline' => now()->addDays(10),
            'created_by' => $this->user->id,
        ]);

        $mark = Mark::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student->id,
            'exam_id' => $examNoMin->id,
            'subject_id' => $this->subject->id,
            'theory_marks' => 15, // Would fail with minimum, but no minimum set
            'practical_marks' => 10, // Would fail with minimum, but no minimum set
            'created_by' => $this->user->id,
        ]);

        $mark->performCalculations();

        // Should not fail due to component minimums since none are set
        $this->assertNotEquals('N/G', $mark->grade);
        // Result will depend on overall percentage and grading scale
    }
}

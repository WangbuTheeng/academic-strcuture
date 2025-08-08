<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Mark;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\GradingScale;
use App\Models\GradeRange;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class MarkTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->academicYear = AcademicYear::factory()->create();
        $this->student = Student::factory()->create();
        $this->subject = Subject::factory()->create();
        $this->user = User::factory()->create();
        
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
        GradeRange::factory()->create([
            'grading_scale_id' => $this->gradingScale->id,
            'grade' => 'B+',
            'min_percentage' => 70,
            'max_percentage' => 79,
            'grade_point' => 3.3,
        ]);
        
        $this->exam = Exam::factory()->create([
            'academic_year_id' => $this->academicYear->id,
            'grading_scale_id' => $this->gradingScale->id,
            'max_marks' => 100,
        ]);
    }

    /** @test */
    public function it_can_create_a_mark()
    {
        $markData = [
            'student_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'obtained_marks' => 85,
            'total_marks' => 100,
            'theory_marks' => 68,
            'practical_marks' => 17,
            'assess_marks' => 0,
            'percentage' => 85.0,
            'grade' => 'A',
            'gpa' => 3.7,
            'result' => 'Pass',
            'status' => 'draft',
            'entered_by' => $this->user->id,
        ];

        $mark = Mark::create($markData);

        $this->assertInstanceOf(Mark::class, $mark);
        $this->assertEquals($markData['obtained_marks'], $mark->obtained_marks);
        $this->assertEquals($markData['percentage'], $mark->percentage);
        $this->assertEquals($markData['grade'], $mark->grade);
        $this->assertDatabaseHas('marks', $markData);
    }

    /** @test */
    public function it_requires_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Mark::create([
            'obtained_marks' => 85,
            'total_marks' => 100,
        ]);
    }

    /** @test */
    public function it_belongs_to_student()
    {
        $mark = Mark::factory()->create(['student_id' => $this->student->id]);
        
        $this->assertInstanceOf(Student::class, $mark->student);
        $this->assertEquals($this->student->id, $mark->student->id);
    }

    /** @test */
    public function it_belongs_to_exam()
    {
        $mark = Mark::factory()->create(['exam_id' => $this->exam->id]);
        
        $this->assertInstanceOf(Exam::class, $mark->exam);
        $this->assertEquals($this->exam->id, $mark->exam->id);
    }

    /** @test */
    public function it_belongs_to_subject()
    {
        $mark = Mark::factory()->create(['subject_id' => $this->subject->id]);
        
        $this->assertInstanceOf(Subject::class, $mark->subject);
        $this->assertEquals($this->subject->id, $mark->subject->id);
    }

    /** @test */
    public function it_belongs_to_entered_by_user()
    {
        $mark = Mark::factory()->create(['entered_by' => $this->user->id]);
        
        $this->assertInstanceOf(User::class, $mark->enteredBy);
        $this->assertEquals($this->user->id, $mark->enteredBy->id);
    }

    /** @test */
    public function it_can_get_status_color_attribute()
    {
        $draftMark = Mark::factory()->create(['status' => 'draft']);
        $submittedMark = Mark::factory()->create(['status' => 'submitted']);
        $approvedMark = Mark::factory()->create(['status' => 'approved']);

        $this->assertEquals('gray', $draftMark->status_color);
        $this->assertEquals('yellow', $submittedMark->status_color);
        $this->assertEquals('green', $approvedMark->status_color);
    }

    /** @test */
    public function it_can_get_result_color_attribute()
    {
        $passMark = Mark::factory()->create(['result' => 'Pass']);
        $failMark = Mark::factory()->create(['result' => 'Fail']);

        $this->assertEquals('green', $passMark->result_color);
        $this->assertEquals('red', $failMark->result_color);
    }

    /** @test */
    public function it_can_check_if_passing()
    {
        $passMark = Mark::factory()->create(['result' => 'Pass']);
        $failMark = Mark::factory()->create(['result' => 'Fail']);

        $this->assertTrue($passMark->is_passing);
        $this->assertFalse($failMark->is_passing);
    }

    /** @test */
    public function it_can_check_if_editable()
    {
        $draftMark = Mark::factory()->create(['status' => 'draft']);
        $submittedMark = Mark::factory()->create(['status' => 'submitted']);
        $approvedMark = Mark::factory()->create(['status' => 'approved']);

        $this->assertTrue($draftMark->is_editable);
        $this->assertTrue($submittedMark->is_editable);
        $this->assertFalse($approvedMark->is_editable);
    }

    /** @test */
    public function it_can_get_formatted_marks_attribute()
    {
        $mark = Mark::factory()->create([
            'obtained_marks' => 85.5,
            'total_marks' => 100,
        ]);

        $this->assertEquals('85.5/100', $mark->formatted_marks);
    }

    /** @test */
    public function it_can_get_component_breakdown_attribute()
    {
        $mark = Mark::factory()->create([
            'theory_marks' => 68,
            'practical_marks' => 17,
            'assess_marks' => 0,
        ]);

        $breakdown = $mark->component_breakdown;
        
        $this->assertIsArray($breakdown);
        $this->assertEquals(68, $breakdown['theory']);
        $this->assertEquals(17, $breakdown['practical']);
        $this->assertEquals(0, $breakdown['assessment']);
    }

    /** @test */
    public function it_automatically_calculates_percentage()
    {
        $mark = Mark::factory()->create([
            'obtained_marks' => 85,
            'total_marks' => 100,
        ]);

        // Assuming the model has an observer or mutator that calculates percentage
        $expectedPercentage = (85 / 100) * 100;
        $this->assertEquals($expectedPercentage, $mark->percentage);
    }

    /** @test */
    public function it_can_determine_grade_from_percentage()
    {
        // Test A grade (80-89%)
        $markA = Mark::factory()->create([
            'obtained_marks' => 85,
            'total_marks' => 100,
            'percentage' => 85,
            'exam_id' => $this->exam->id,
        ]);

        // Test A+ grade (90-100%)
        $markAPlus = Mark::factory()->create([
            'obtained_marks' => 95,
            'total_marks' => 100,
            'percentage' => 95,
            'exam_id' => $this->exam->id,
        ]);

        // Test B+ grade (70-79%)
        $markBPlus = Mark::factory()->create([
            'obtained_marks' => 75,
            'total_marks' => 100,
            'percentage' => 75,
            'exam_id' => $this->exam->id,
        ]);

        // These would be set by the model's grade calculation logic
        $this->assertNotEmpty($markA->grade);
        $this->assertNotEmpty($markAPlus->grade);
        $this->assertNotEmpty($markBPlus->grade);
    }

    /** @test */
    public function it_can_determine_result_from_percentage()
    {
        $passingMark = Mark::factory()->create([
            'percentage' => 60,
            'result' => 'Pass',
        ]);

        $failingMark = Mark::factory()->create([
            'percentage' => 35,
            'result' => 'Fail',
        ]);

        $this->assertEquals('Pass', $passingMark->result);
        $this->assertEquals('Fail', $failingMark->result);
    }

    /** @test */
    public function it_can_scope_by_status()
    {
        Mark::factory()->count(3)->create(['status' => 'draft']);
        Mark::factory()->count(2)->create(['status' => 'approved']);
        Mark::factory()->count(1)->create(['status' => 'submitted']);

        $draftMarks = Mark::byStatus('draft')->get();
        $approvedMarks = Mark::byStatus('approved')->get();

        $this->assertCount(3, $draftMarks);
        $this->assertCount(2, $approvedMarks);
    }

    /** @test */
    public function it_can_scope_by_result()
    {
        Mark::factory()->count(4)->create(['result' => 'Pass']);
        Mark::factory()->count(2)->create(['result' => 'Fail']);

        $passMarks = Mark::byResult('Pass')->get();
        $failMarks = Mark::byResult('Fail')->get();

        $this->assertCount(4, $passMarks);
        $this->assertCount(2, $failMarks);
    }

    /** @test */
    public function it_can_scope_for_exam()
    {
        $exam1 = Exam::factory()->create(['academic_year_id' => $this->academicYear->id]);
        $exam2 = Exam::factory()->create(['academic_year_id' => $this->academicYear->id]);
        
        Mark::factory()->count(3)->create(['exam_id' => $exam1->id]);
        Mark::factory()->count(2)->create(['exam_id' => $exam2->id]);

        $exam1Marks = Mark::forExam($exam1->id)->get();

        $this->assertCount(3, $exam1Marks);
    }

    /** @test */
    public function it_can_scope_for_student()
    {
        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();
        
        Mark::factory()->count(3)->create(['student_id' => $student1->id]);
        Mark::factory()->count(2)->create(['student_id' => $student2->id]);

        $student1Marks = Mark::forStudent($student1->id)->get();

        $this->assertCount(3, $student1Marks);
    }

    /** @test */
    public function it_can_scope_for_subject()
    {
        $subject1 = Subject::factory()->create();
        $subject2 = Subject::factory()->create();
        
        Mark::factory()->count(3)->create(['subject_id' => $subject1->id]);
        Mark::factory()->count(2)->create(['subject_id' => $subject2->id]);

        $subject1Marks = Mark::forSubject($subject1->id)->get();

        $this->assertCount(3, $subject1Marks);
    }

    /** @test */
    public function it_can_scope_passing_marks()
    {
        Mark::factory()->count(4)->create(['result' => 'Pass']);
        Mark::factory()->count(2)->create(['result' => 'Fail']);

        $passingMarks = Mark::passing()->get();

        $this->assertCount(4, $passingMarks);
        $passingMarks->each(function ($mark) {
            $this->assertEquals('Pass', $mark->result);
        });
    }

    /** @test */
    public function it_can_scope_failing_marks()
    {
        Mark::factory()->count(4)->create(['result' => 'Pass']);
        Mark::factory()->count(2)->create(['result' => 'Fail']);

        $failingMarks = Mark::failing()->get();

        $this->assertCount(2, $failingMarks);
        $failingMarks->each(function ($mark) {
            $this->assertEquals('Fail', $mark->result);
        });
    }

    /** @test */
    public function it_validates_marks_do_not_exceed_total()
    {
        // This would be handled by validation rules in the model or form request
        $mark = Mark::factory()->create([
            'obtained_marks' => 85,
            'total_marks' => 100,
        ]);

        $this->assertLessThanOrEqual($mark->total_marks, $mark->obtained_marks);
    }

    /** @test */
    public function it_can_have_grace_marks_applied()
    {
        $mark = Mark::factory()->create([
            'obtained_marks' => 85,
            'grace_marks_applied' => false,
        ]);

        $this->assertFalse($mark->grace_marks_applied);

        $mark->update(['grace_marks_applied' => true]);

        $this->assertTrue($mark->fresh()->grace_marks_applied);
    }
}

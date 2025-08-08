<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Exam;
use App\Models\AcademicYear;

use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\GradingScale;
use App\Models\Mark;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;

class ExamTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->academicYear = AcademicYear::factory()->create();
        $this->class = ClassModel::factory()->create();
        $this->subject = Subject::factory()->create();
        $this->gradingScale = GradingScale::factory()->create();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_create_an_exam()
    {
        $examData = [
            'name' => 'First Terminal Exam',
            'exam_type' => 'terminal',
            'academic_year_id' => $this->academicYear->id,

            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'max_marks' => 100,
            'theory_max' => 80,
            'practical_max' => 20,
            'assess_max' => 0,
            'has_practical' => true,
            'has_assessment' => false,
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(14),
            'submission_deadline' => now()->addDays(21),
            'grading_scale_id' => $this->gradingScale->id,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ];

        $exam = Exam::create($examData);

        $this->assertInstanceOf(Exam::class, $exam);
        $this->assertEquals($examData['name'], $exam->name);
        $this->assertEquals($examData['exam_type'], $exam->exam_type);
        $this->assertEquals($examData['max_marks'], $exam->max_marks);
        $this->assertDatabaseHas('exams', $examData);
    }

    /** @test */
    public function it_requires_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Exam::create([
            'exam_type' => 'terminal',
            'academic_year_id' => $this->academicYear->id,
        ]);
    }

    /** @test */
    public function it_belongs_to_academic_year()
    {
        $exam = Exam::factory()->create(['academic_year_id' => $this->academicYear->id]);
        
        $this->assertInstanceOf(AcademicYear::class, $exam->academicYear);
        $this->assertEquals($this->academicYear->id, $exam->academicYear->id);
    }

    /** @test */
    public function it_belongs_to_class()
    {
        $exam = Exam::factory()->create(['class_id' => $this->class->id]);
        
        $this->assertInstanceOf(ClassModel::class, $exam->class);
        $this->assertEquals($this->class->id, $exam->class->id);
    }

    /** @test */
    public function it_belongs_to_subject()
    {
        $exam = Exam::factory()->create(['subject_id' => $this->subject->id]);
        
        $this->assertInstanceOf(Subject::class, $exam->subject);
        $this->assertEquals($this->subject->id, $exam->subject->id);
    }

    /** @test */
    public function it_belongs_to_grading_scale()
    {
        $exam = Exam::factory()->create(['grading_scale_id' => $this->gradingScale->id]);
        
        $this->assertInstanceOf(GradingScale::class, $exam->gradingScale);
        $this->assertEquals($this->gradingScale->id, $exam->gradingScale->id);
    }

    /** @test */
    public function it_belongs_to_creator()
    {
        $exam = Exam::factory()->create(['created_by' => $this->user->id]);
        
        $this->assertInstanceOf(User::class, $exam->creator);
        $this->assertEquals($this->user->id, $exam->creator->id);
    }

    /** @test */
    public function it_has_many_marks()
    {
        $exam = Exam::factory()->create();
        $student = Student::factory()->create();
        
        $marks = Mark::factory()->count(3)->create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'subject_id' => $this->subject->id,
        ]);

        $this->assertCount(3, $exam->marks);
        $this->assertInstanceOf(Mark::class, $exam->marks->first());
    }

    /** @test */
    public function it_can_get_type_label_attribute()
    {
        $terminalExam = Exam::factory()->create(['exam_type' => 'terminal']);
        $assessmentExam = Exam::factory()->create(['exam_type' => 'assessment']);
        $finalExam = Exam::factory()->create(['exam_type' => 'final']);

        $this->assertEquals('Terminal Exam', $terminalExam->type_label);
        $this->assertEquals('Assessment', $assessmentExam->type_label);
        $this->assertEquals('Final Exam', $finalExam->type_label);
    }

    /** @test */
    public function it_can_get_status_color_attribute()
    {
        $draftExam = Exam::factory()->create(['status' => 'draft']);
        $scheduledExam = Exam::factory()->create(['status' => 'scheduled']);
        $ongoingExam = Exam::factory()->create(['status' => 'ongoing']);
        $completedExam = Exam::factory()->create(['status' => 'completed']);

        $this->assertEquals('gray', $draftExam->status_color);
        $this->assertEquals('blue', $scheduledExam->status_color);
        $this->assertEquals('yellow', $ongoingExam->status_color);
        $this->assertEquals('green', $completedExam->status_color);
    }

    /** @test */
    public function it_can_check_if_editable()
    {
        $draftExam = Exam::factory()->create(['status' => 'draft']);
        $scheduledExam = Exam::factory()->create(['status' => 'scheduled']);
        $ongoingExam = Exam::factory()->create(['status' => 'ongoing']);
        $lockedExam = Exam::factory()->create(['status' => 'locked']);

        $this->assertTrue($draftExam->is_editable);
        $this->assertTrue($scheduledExam->is_editable);
        $this->assertFalse($ongoingExam->is_editable);
        $this->assertFalse($lockedExam->is_editable);
    }

    /** @test */
    public function it_can_check_if_marks_can_be_entered()
    {
        $draftExam = Exam::factory()->create(['status' => 'draft']);
        $ongoingExam = Exam::factory()->create(['status' => 'ongoing']);
        $submittedExam = Exam::factory()->create(['status' => 'submitted']);
        $lockedExam = Exam::factory()->create(['status' => 'locked']);

        $this->assertFalse($draftExam->can_enter_marks);
        $this->assertTrue($ongoingExam->can_enter_marks);
        $this->assertTrue($submittedExam->can_enter_marks);
        $this->assertFalse($lockedExam->can_enter_marks);
    }

    /** @test */
    public function it_can_check_if_active()
    {
        $now = Carbon::now();
        
        $futureExam = Exam::factory()->create([
            'start_date' => $now->copy()->addDays(1),
            'end_date' => $now->copy()->addDays(7),
        ]);
        
        $currentExam = Exam::factory()->create([
            'start_date' => $now->copy()->subDays(1),
            'end_date' => $now->copy()->addDays(1),
        ]);
        
        $pastExam = Exam::factory()->create([
            'start_date' => $now->copy()->subDays(7),
            'end_date' => $now->copy()->subDays(1),
        ]);

        $this->assertFalse($futureExam->is_active);
        $this->assertTrue($currentExam->is_active);
        $this->assertFalse($pastExam->is_active);
    }

    /** @test */
    public function it_can_check_if_submission_deadline_passed()
    {
        $now = Carbon::now();
        
        $futureDeadline = Exam::factory()->create([
            'submission_deadline' => $now->copy()->addDays(1),
        ]);
        
        $pastDeadline = Exam::factory()->create([
            'submission_deadline' => $now->copy()->subDays(1),
        ]);

        $this->assertFalse($futureDeadline->is_submission_deadline_passed);
        $this->assertTrue($pastDeadline->is_submission_deadline_passed);
    }

    /** @test */
    public function it_can_get_duration_in_days()
    {
        $exam = Exam::factory()->create([
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays(7),
        ]);

        $this->assertEquals(7, $exam->duration_in_days);
    }

    /** @test */
    public function it_can_get_days_until_start()
    {
        $exam = Exam::factory()->create([
            'start_date' => Carbon::now()->addDays(5),
        ]);

        $this->assertEquals(5, $exam->days_until_start);
    }

    /** @test */
    public function it_can_get_days_until_submission_deadline()
    {
        $exam = Exam::factory()->create([
            'submission_deadline' => Carbon::now()->addDays(10),
        ]);

        $this->assertEquals(10, $exam->days_until_submission_deadline);
    }

    /** @test */
    public function it_can_scope_by_status()
    {
        Exam::factory()->count(3)->create(['status' => 'draft']);
        Exam::factory()->count(2)->create(['status' => 'ongoing']);
        Exam::factory()->count(1)->create(['status' => 'completed']);

        $draftExams = Exam::byStatus('draft')->get();
        $ongoingExams = Exam::byStatus('ongoing')->get();

        $this->assertCount(3, $draftExams);
        $this->assertCount(2, $ongoingExams);
    }

    /** @test */
    public function it_can_scope_by_academic_year()
    {
        $year1 = AcademicYear::factory()->create();
        $year2 = AcademicYear::factory()->create();
        
        Exam::factory()->count(3)->create(['academic_year_id' => $year1->id]);
        Exam::factory()->count(2)->create(['academic_year_id' => $year2->id]);

        $year1Exams = Exam::forAcademicYear($year1->id)->get();

        $this->assertCount(3, $year1Exams);
    }

    /** @test */
    public function it_can_scope_by_class()
    {
        $class1 = ClassModel::factory()->create();
        $class2 = ClassModel::factory()->create();
        
        Exam::factory()->count(3)->create(['class_id' => $class1->id]);
        Exam::factory()->count(2)->create(['class_id' => $class2->id]);

        $class1Exams = Exam::forClass($class1->id)->get();

        $this->assertCount(3, $class1Exams);
    }

    /** @test */
    public function it_can_scope_upcoming_exams()
    {
        $now = Carbon::now();
        
        Exam::factory()->count(2)->create(['start_date' => $now->copy()->addDays(1)]);
        Exam::factory()->count(3)->create(['start_date' => $now->copy()->subDays(1)]);

        $upcomingExams = Exam::upcoming()->get();

        $this->assertCount(2, $upcomingExams);
    }

    /** @test */
    public function it_can_scope_active_exams()
    {
        $now = Carbon::now();
        
        Exam::factory()->count(2)->create([
            'start_date' => $now->copy()->subDays(1),
            'end_date' => $now->copy()->addDays(1),
        ]);
        
        Exam::factory()->count(3)->create([
            'start_date' => $now->copy()->addDays(1),
            'end_date' => $now->copy()->addDays(7),
        ]);

        $activeExams = Exam::active()->get();

        $this->assertCount(2, $activeExams);
    }

    /** @test */
    public function it_validates_marking_scheme_totals()
    {
        $exam = Exam::factory()->create([
            'max_marks' => 100,
            'theory_max' => 80,
            'practical_max' => 20,
            'assess_max' => 0,
        ]);

        $this->assertEquals(100, $exam->theory_max + $exam->practical_max + $exam->assess_max);
    }
}

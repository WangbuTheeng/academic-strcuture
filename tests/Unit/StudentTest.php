<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Level;
use App\Models\Program;
use App\Models\Mark;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\AcademicYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class StudentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create necessary related models
        $this->level = Level::factory()->create();
        $this->class = ClassModel::factory()->create(['level_id' => $this->level->id]);
        $this->program = Program::factory()->create();
        $this->academicYear = AcademicYear::factory()->create();
        $this->subject = Subject::factory()->create();
        $this->exam = Exam::factory()->create(['academic_year_id' => $this->academicYear->id]);
    }

    /** @test */
    public function it_can_create_a_student()
    {
        $studentData = [
            'name' => $this->faker->name,
            'roll_number' => $this->faker->unique()->numerify('####'),
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'date_of_birth' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'class_id' => $this->class->id,
            'program_id' => $this->program->id,
            'status' => 'active',
        ];

        $student = Student::create($studentData);

        $this->assertInstanceOf(Student::class, $student);
        $this->assertEquals($studentData['name'], $student->name);
        $this->assertEquals($studentData['roll_number'], $student->roll_number);
        $this->assertEquals($studentData['email'], $student->email);
        $this->assertDatabaseHas('students', $studentData);
    }

    /** @test */
    public function it_requires_name_and_roll_number()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Student::create([
            'email' => $this->faker->email,
            'class_id' => $this->class->id,
        ]);
    }

    /** @test */
    public function it_requires_unique_roll_number()
    {
        $rollNumber = $this->faker->unique()->numerify('####');
        
        Student::factory()->create(['roll_number' => $rollNumber]);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Student::factory()->create(['roll_number' => $rollNumber]);
    }

    /** @test */
    public function it_belongs_to_a_class()
    {
        $student = Student::factory()->create(['class_id' => $this->class->id]);
        
        $this->assertInstanceOf(ClassModel::class, $student->class);
        $this->assertEquals($this->class->id, $student->class->id);
    }

    /** @test */
    public function it_belongs_to_a_program()
    {
        $student = Student::factory()->create(['program_id' => $this->program->id]);
        
        $this->assertInstanceOf(Program::class, $student->program);
        $this->assertEquals($this->program->id, $student->program->id);
    }

    /** @test */
    public function it_has_many_marks()
    {
        $student = Student::factory()->create();
        $marks = Mark::factory()->count(3)->create([
            'student_id' => $student->id,
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
        ]);

        $this->assertCount(3, $student->marks);
        $this->assertInstanceOf(Mark::class, $student->marks->first());
    }

    /** @test */
    public function it_can_get_full_name_attribute()
    {
        $student = Student::factory()->create([
            'name' => 'John Doe',
        ]);

        $this->assertEquals('John Doe', $student->full_name);
    }

    /** @test */
    public function it_can_get_display_name_attribute()
    {
        $student = Student::factory()->create([
            'name' => 'John Doe',
            'roll_number' => '1234',
        ]);

        $this->assertEquals('John Doe (1234)', $student->display_name);
    }

    /** @test */
    public function it_can_check_if_active()
    {
        $activeStudent = Student::factory()->create(['status' => 'active']);
        $inactiveStudent = Student::factory()->create(['status' => 'inactive']);

        $this->assertTrue($activeStudent->is_active);
        $this->assertFalse($inactiveStudent->is_active);
    }

    /** @test */
    public function it_can_get_age_attribute()
    {
        $birthDate = now()->subYears(20)->format('Y-m-d');
        $student = Student::factory()->create(['date_of_birth' => $birthDate]);

        $this->assertEquals(20, $student->age);
    }

    /** @test */
    public function it_can_get_marks_for_exam()
    {
        $student = Student::factory()->create();
        $exam = Exam::factory()->create(['academic_year_id' => $this->academicYear->id]);
        
        $marks = Mark::factory()->count(2)->create([
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'subject_id' => $this->subject->id,
        ]);

        $examMarks = $student->getMarksForExam($exam->id);
        
        $this->assertCount(2, $examMarks);
        $this->assertEquals($exam->id, $examMarks->first()->exam_id);
    }

    /** @test */
    public function it_can_calculate_overall_percentage_for_exam()
    {
        $student = Student::factory()->create();
        $exam = Exam::factory()->create(['academic_year_id' => $this->academicYear->id]);
        
        Mark::factory()->create([
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'subject_id' => $this->subject->id,
            'obtained_marks' => 80,
            'total_marks' => 100,
            'percentage' => 80,
        ]);

        Mark::factory()->create([
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'subject_id' => Subject::factory()->create()->id,
            'obtained_marks' => 90,
            'total_marks' => 100,
            'percentage' => 90,
        ]);

        $overallPercentage = $student->getOverallPercentageForExam($exam->id);
        
        $this->assertEquals(85.0, $overallPercentage); // (80 + 90) / 2
    }

    /** @test */
    public function it_can_determine_overall_result_for_exam()
    {
        $student = Student::factory()->create();
        $exam = Exam::factory()->create(['academic_year_id' => $this->academicYear->id]);
        
        // All passing marks
        Mark::factory()->create([
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'subject_id' => $this->subject->id,
            'result' => 'Pass',
        ]);

        Mark::factory()->create([
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'subject_id' => Subject::factory()->create()->id,
            'result' => 'Pass',
        ]);

        $overallResult = $student->getOverallResultForExam($exam->id);
        $this->assertEquals('Pass', $overallResult);

        // One failing mark
        Mark::factory()->create([
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'subject_id' => Subject::factory()->create()->id,
            'result' => 'Fail',
        ]);

        $overallResult = $student->getOverallResultForExam($exam->id);
        $this->assertEquals('Fail', $overallResult);
    }

    /** @test */
    public function it_can_scope_active_students()
    {
        Student::factory()->count(3)->create(['status' => 'active']);
        Student::factory()->count(2)->create(['status' => 'inactive']);

        $activeStudents = Student::active()->get();
        
        $this->assertCount(3, $activeStudents);
        $activeStudents->each(function ($student) {
            $this->assertEquals('active', $student->status);
        });
    }

    /** @test */
    public function it_can_scope_students_by_class()
    {
        $class1 = ClassModel::factory()->create(['level_id' => $this->level->id]);
        $class2 = ClassModel::factory()->create(['level_id' => $this->level->id]);
        
        Student::factory()->count(3)->create(['class_id' => $class1->id]);
        Student::factory()->count(2)->create(['class_id' => $class2->id]);

        $class1Students = Student::inClass($class1->id)->get();
        
        $this->assertCount(3, $class1Students);
        $class1Students->each(function ($student) use ($class1) {
            $this->assertEquals($class1->id, $student->class_id);
        });
    }

    /** @test */
    public function it_can_scope_students_by_academic_year()
    {
        $academicYear1 = AcademicYear::factory()->create();
        $academicYear2 = AcademicYear::factory()->create();
        
        $class1 = ClassModel::factory()->create([
            'level_id' => $this->level->id,
            'academic_year_id' => $academicYear1->id
        ]);
        $class2 = ClassModel::factory()->create([
            'level_id' => $this->level->id,
            'academic_year_id' => $academicYear2->id
        ]);
        
        Student::factory()->count(3)->create(['class_id' => $class1->id]);
        Student::factory()->count(2)->create(['class_id' => $class2->id]);

        $year1Students = Student::inAcademicYear($academicYear1->id)->get();
        
        $this->assertCount(3, $year1Students);
    }

    /** @test */
    public function it_soft_deletes()
    {
        $student = Student::factory()->create();
        $studentId = $student->id;

        $student->delete();

        $this->assertSoftDeleted('students', ['id' => $studentId]);
        $this->assertCount(0, Student::all());
        $this->assertCount(1, Student::withTrashed()->get());
    }
}

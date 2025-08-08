<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarkValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $exam;
    protected $student;
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->exam = Exam::factory()->create([
            'assess_max' => 20,
            'theory_max' => 60,
            'practical_max' => 40,
            'max_marks' => 120,
        ]);
        $this->student = Student::factory()->create();
        $this->subject = Subject::factory()->create();
    }

    /** @test */
    public function it_validates_marks_within_maximum_limits()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('admin.marks.store'), [
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'marks' => [
                [
                    'student_id' => $this->student->id,
                    'assess_marks' => 15, // Valid: within 20
                    'theory_marks' => 55, // Valid: within 60
                    'practical_marks' => 35, // Valid: within 40
                ]
            ]
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** @test */
    public function it_rejects_marks_exceeding_maximum_limits()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('admin.marks.store'), [
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'marks' => [
                [
                    'student_id' => $this->student->id,
                    'assess_marks' => 25, // Invalid: exceeds 20
                    'theory_marks' => 65, // Invalid: exceeds 60
                    'practical_marks' => 45, // Invalid: exceeds 40
                ]
            ]
        ]);

        $response->assertSessionHasErrors([
            'marks.0.assess_marks',
            'marks.0.theory_marks',
            'marks.0.practical_marks'
        ]);
    }

    /** @test */
    public function it_rejects_negative_marks()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('admin.marks.store'), [
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'marks' => [
                [
                    'student_id' => $this->student->id,
                    'assess_marks' => -5, // Invalid: negative
                    'theory_marks' => -10, // Invalid: negative
                    'practical_marks' => -2, // Invalid: negative
                ]
            ]
        ]);

        $response->assertSessionHasErrors([
            'marks.0.assess_marks',
            'marks.0.theory_marks',
            'marks.0.practical_marks'
        ]);
    }

    /** @test */
    public function it_allows_zero_marks()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('admin.marks.store'), [
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'marks' => [
                [
                    'student_id' => $this->student->id,
                    'assess_marks' => 0, // Valid: zero is allowed
                    'theory_marks' => 0, // Valid: zero is allowed
                    'practical_marks' => 0, // Valid: zero is allowed
                ]
            ]
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}

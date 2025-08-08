<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Mark;
use App\Models\Exam;
use Mockery;

class MarkPassFailLogicTest extends TestCase
{
    /** @test */
    public function hasFailedInComponents_returns_true_when_theory_below_minimum()
    {
        // Create mock exam
        $exam = Mockery::mock(Exam::class);
        $exam->shouldReceive('getAttribute')->with('theory_max')->andReturn(60);
        $exam->shouldReceive('getAttribute')->with('theory_pass_marks')->andReturn(20);
        $exam->shouldReceive('getAttribute')->with('has_practical')->andReturn(true);
        $exam->shouldReceive('getAttribute')->with('practical_max')->andReturn(40);
        $exam->shouldReceive('getAttribute')->with('practical_pass_marks')->andReturn(13);
        $exam->shouldReceive('getAttribute')->with('has_assessment')->andReturn(false);
        $exam->shouldReceive('getAttribute')->with('assess_max')->andReturn(0);
        $exam->shouldReceive('getAttribute')->with('assess_pass_marks')->andReturn(0);

        // Create mark with theory below minimum
        $mark = new Mark();
        $mark->theory_marks = 15; // Below minimum of 20
        $mark->practical_marks = 30; // Above minimum of 13

        // Mock the exam relationship
        $mark->setRelation('exam', $exam);

        $this->assertTrue($mark->hasFailedInComponents());
    }

    /** @test */
    public function hasFailedInComponents_returns_true_when_practical_below_minimum()
    {
        // Create mock exam
        $exam = Mockery::mock(Exam::class);
        $exam->shouldReceive('getAttribute')->with('theory_max')->andReturn(60);
        $exam->shouldReceive('getAttribute')->with('theory_pass_marks')->andReturn(20);
        $exam->shouldReceive('getAttribute')->with('has_practical')->andReturn(true);
        $exam->shouldReceive('getAttribute')->with('practical_max')->andReturn(40);
        $exam->shouldReceive('getAttribute')->with('practical_pass_marks')->andReturn(13);
        $exam->shouldReceive('getAttribute')->with('has_assessment')->andReturn(false);
        $exam->shouldReceive('getAttribute')->with('assess_max')->andReturn(0);
        $exam->shouldReceive('getAttribute')->with('assess_pass_marks')->andReturn(0);

        // Create mark with practical below minimum
        $mark = new Mark();
        $mark->theory_marks = 45; // Above minimum of 20
        $mark->practical_marks = 10; // Below minimum of 13

        // Mock the exam relationship
        $mark->setRelation('exam', $exam);

        $this->assertTrue($mark->hasFailedInComponents());
    }

    /** @test */
    public function hasFailedInComponents_returns_false_when_all_components_above_minimum()
    {
        // Create mock exam
        $exam = Mockery::mock(Exam::class);
        $exam->shouldReceive('getAttribute')->with('theory_max')->andReturn(60);
        $exam->shouldReceive('getAttribute')->with('theory_pass_marks')->andReturn(20);
        $exam->shouldReceive('getAttribute')->with('has_practical')->andReturn(true);
        $exam->shouldReceive('getAttribute')->with('practical_max')->andReturn(40);
        $exam->shouldReceive('getAttribute')->with('practical_pass_marks')->andReturn(13);
        $exam->shouldReceive('getAttribute')->with('has_assessment')->andReturn(false);
        $exam->shouldReceive('getAttribute')->with('assess_max')->andReturn(0);
        $exam->shouldReceive('getAttribute')->with('assess_pass_marks')->andReturn(0);

        // Create mark with all components above minimum
        $mark = new Mark();
        $mark->theory_marks = 45; // Above minimum of 20
        $mark->practical_marks = 30; // Above minimum of 13

        // Mock the exam relationship
        $mark->setRelation('exam', $exam);

        $this->assertFalse($mark->hasFailedInComponents());
    }

    /** @test */
    public function hasFailedInComponents_returns_false_when_no_minimum_marks_set()
    {
        // Create mock exam with no minimum marks
        $exam = Mockery::mock(Exam::class);
        $exam->shouldReceive('getAttribute')->with('theory_max')->andReturn(60);
        $exam->shouldReceive('getAttribute')->with('theory_pass_marks')->andReturn(0); // No minimum
        $exam->shouldReceive('getAttribute')->with('has_practical')->andReturn(true);
        $exam->shouldReceive('getAttribute')->with('practical_max')->andReturn(40);
        $exam->shouldReceive('getAttribute')->with('practical_pass_marks')->andReturn(0); // No minimum
        $exam->shouldReceive('getAttribute')->with('has_assessment')->andReturn(false);
        $exam->shouldReceive('getAttribute')->with('assess_max')->andReturn(0);
        $exam->shouldReceive('getAttribute')->with('assess_pass_marks')->andReturn(0);

        // Create mark with low scores but no minimums set
        $mark = new Mark();
        $mark->theory_marks = 15; // Would fail if minimum was set
        $mark->practical_marks = 10; // Would fail if minimum was set

        // Mock the exam relationship
        $mark->setRelation('exam', $exam);

        $this->assertFalse($mark->hasFailedInComponents());
    }

    /** @test */
    public function hasFailedInComponents_returns_true_when_assessment_below_minimum()
    {
        // Create mock exam with assessment
        $exam = Mockery::mock(Exam::class);
        $exam->shouldReceive('getAttribute')->with('theory_max')->andReturn(60);
        $exam->shouldReceive('getAttribute')->with('theory_pass_marks')->andReturn(20);
        $exam->shouldReceive('getAttribute')->with('has_practical')->andReturn(false);
        $exam->shouldReceive('getAttribute')->with('practical_max')->andReturn(0);
        $exam->shouldReceive('getAttribute')->with('practical_pass_marks')->andReturn(0);
        $exam->shouldReceive('getAttribute')->with('has_assessment')->andReturn(true);
        $exam->shouldReceive('getAttribute')->with('assess_max')->andReturn(40);
        $exam->shouldReceive('getAttribute')->with('assess_pass_marks')->andReturn(13);

        // Create mark with assessment below minimum
        $mark = new Mark();
        $mark->theory_marks = 45; // Above minimum of 20
        $mark->assess_marks = 10; // Below minimum of 13

        // Mock the exam relationship
        $mark->setRelation('exam', $exam);

        $this->assertTrue($mark->hasFailedInComponents());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

class PerformanceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $academicYear;
    protected $class;
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        Role::create(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        // Create academic structure
        $this->academicYear = AcademicYear::factory()->create();
        $level = Level::factory()->create();
        $this->class = ClassModel::factory()->create(['level_id' => $level->id]);
        $this->subject = Subject::factory()->create();
    }

    /** @test */
    public function dashboard_loads_within_acceptable_time()
    {
        $startTime = microtime(true);

        $response = $this->actingAs($this->admin)->get('/dashboard');

        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(2.0, $loadTime, 'Dashboard should load within 2 seconds');
    }

    /** @test */
    public function student_list_performs_well_with_large_dataset()
    {
        // Create 1000 students
        Student::factory()->count(1000)->create(['class_id' => $this->class->id]);

        $startTime = microtime(true);

        $response = $this->actingAs($this->admin)->get('/admin/students');

        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(3.0, $loadTime, 'Student list should load within 3 seconds with 1000 records');
    }

    /** @test */
    public function mark_entry_performs_well_with_bulk_data()
    {
        // Create 50 students
        $students = Student::factory()->count(50)->create(['class_id' => $this->class->id]);
        
        $exam = Exam::factory()->create([
            'academic_year_id' => $this->academicYear->id,
            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'status' => 'ongoing',
        ]);

        // Prepare bulk mark data
        $markData = ['marks' => []];
        foreach ($students as $student) {
            $markData['marks'][$student->id] = [
                'theory_marks' => rand(40, 80),
                'practical_marks' => rand(10, 20),
                'obtained_marks' => rand(50, 100),
            ];
        }

        $startTime = microtime(true);

        $response = $this->actingAs($this->admin)->post("/admin/exams/{$exam->id}/marks", $markData);

        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;

        $response->assertRedirect();
        $this->assertLessThan(5.0, $loadTime, 'Bulk mark entry should complete within 5 seconds for 50 students');
        
        // Verify all marks were created
        $this->assertEquals(50, Mark::where('exam_id', $exam->id)->count());
    }

    /** @test */
    public function analytics_dashboard_performs_well_with_large_dataset()
    {
        // Create large dataset
        $students = Student::factory()->count(500)->create(['class_id' => $this->class->id]);
        $exam = Exam::factory()->create([
            'academic_year_id' => $this->academicYear->id,
            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
        ]);

        // Create marks for all students
        foreach ($students as $student) {
            Mark::factory()->create([
                'student_id' => $student->id,
                'exam_id' => $exam->id,
                'subject_id' => $this->subject->id,
                'status' => 'approved',
            ]);
        }

        $startTime = microtime(true);

        $response = $this->actingAs($this->admin)->get('/admin/analytics');

        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(4.0, $loadTime, 'Analytics dashboard should load within 4 seconds with 500 students');
    }

    /** @test */
    public function database_queries_are_optimized()
    {
        // Create test data
        $students = Student::factory()->count(10)->create(['class_id' => $this->class->id]);
        $exam = Exam::factory()->create([
            'academic_year_id' => $this->academicYear->id,
            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
        ]);

        foreach ($students as $student) {
            Mark::factory()->create([
                'student_id' => $student->id,
                'exam_id' => $exam->id,
                'subject_id' => $this->subject->id,
            ]);
        }

        // Enable query logging
        DB::enableQueryLog();

        $response = $this->actingAs($this->admin)->get("/admin/exams/{$exam->id}/marks");

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $response->assertStatus(200);
        $this->assertLessThan(20, $queryCount, 'Mark entry page should use fewer than 20 queries');
    }

    /** @test */
    public function marksheet_generation_performs_well()
    {
        $student = Student::factory()->create(['class_id' => $this->class->id]);
        $exam = Exam::factory()->create([
            'academic_year_id' => $this->academicYear->id,
            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
        ]);

        // Create marks for multiple subjects
        for ($i = 0; $i < 5; $i++) {
            $subject = Subject::factory()->create();
            Mark::factory()->create([
                'student_id' => $student->id,
                'exam_id' => $exam->id,
                'subject_id' => $subject->id,
                'status' => 'approved',
            ]);
        }

        $startTime = microtime(true);

        $response = $this->actingAs($this->admin)->get("/admin/marksheets/download/{$student->id}/{$exam->id}");

        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(10.0, $loadTime, 'Marksheet generation should complete within 10 seconds');
    }

    /** @test */
    public function concurrent_mark_entry_handles_well()
    {
        $students = Student::factory()->count(5)->create(['class_id' => $this->class->id]);
        $exam = Exam::factory()->create([
            'academic_year_id' => $this->academicYear->id,
            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'status' => 'ongoing',
        ]);

        $teacher1 = User::factory()->create();
        $teacher2 = User::factory()->create();

        // Simulate concurrent mark entry
        $promises = [];
        
        foreach ($students as $index => $student) {
            $teacher = $index % 2 === 0 ? $teacher1 : $teacher2;
            $markData = [
                'marks' => [
                    $student->id => [
                        'theory_marks' => rand(40, 80),
                        'practical_marks' => rand(10, 20),
                        'obtained_marks' => rand(50, 100),
                    ]
                ]
            ];

            $startTime = microtime(true);
            
            $response = $this->actingAs($teacher)->post("/admin/exams/{$exam->id}/marks", $markData);
            
            $endTime = microtime(true);
            $loadTime = $endTime - $startTime;

            $this->assertLessThan(3.0, $loadTime, 'Concurrent mark entry should complete within 3 seconds');
        }

        // Verify all marks were created without conflicts
        $this->assertEquals(5, Mark::where('exam_id', $exam->id)->count());
    }

    /** @test */
    public function memory_usage_is_reasonable()
    {
        $initialMemory = memory_get_usage();

        // Create large dataset
        Student::factory()->count(100)->create(['class_id' => $this->class->id]);

        $response = $this->actingAs($this->admin)->get('/admin/students');

        $finalMemory = memory_get_usage();
        $memoryUsed = $finalMemory - $initialMemory;

        $response->assertStatus(200);
        $this->assertLessThan(50 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 50MB for 100 students');
    }

    /** @test */
    public function caching_improves_performance()
    {
        // Create test data
        Student::factory()->count(50)->create(['class_id' => $this->class->id]);

        // First request (no cache)
        Cache::flush();
        $startTime1 = microtime(true);
        $response1 = $this->actingAs($this->admin)->get('/admin/analytics');
        $endTime1 = microtime(true);
        $loadTime1 = $endTime1 - $startTime1;

        // Second request (with cache)
        $startTime2 = microtime(true);
        $response2 = $this->actingAs($this->admin)->get('/admin/analytics');
        $endTime2 = microtime(true);
        $loadTime2 = $endTime2 - $startTime2;

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        // Second request should be faster (if caching is implemented)
        // This test assumes caching is implemented for analytics
        $this->assertLessThan($loadTime1, $loadTime2 + 0.1, 'Cached request should be faster');
    }

    /** @test */
    public function file_upload_performance_is_acceptable()
    {
        // Create a test file (simulate 1MB file)
        $fileContent = str_repeat('a', 1024 * 1024);
        $tempFile = tmpfile();
        fwrite($tempFile, $fileContent);
        $tempPath = stream_get_meta_data($tempFile)['uri'];

        $uploadedFile = new \Illuminate\Http\UploadedFile(
            $tempPath,
            'test-document.pdf',
            'application/pdf',
            null,
            true
        );

        $student = Student::factory()->create(['class_id' => $this->class->id]);

        $startTime = microtime(true);

        $response = $this->actingAs($this->admin)->post("/admin/students/{$student->id}/documents", [
            'document_type' => 'citizenship',
            'document' => $uploadedFile,
        ]);

        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;

        $this->assertLessThan(30.0, $loadTime, 'File upload should complete within 30 seconds for 1MB file');

        fclose($tempFile);
    }

    /** @test */
    public function pagination_performs_well()
    {
        // Create 1000 students
        Student::factory()->count(1000)->create(['class_id' => $this->class->id]);

        $startTime = microtime(true);

        $response = $this->actingAs($this->admin)->get('/admin/students?page=10');

        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(2.0, $loadTime, 'Paginated results should load within 2 seconds');
    }

    /** @test */
    public function search_functionality_performs_well()
    {
        // Create students with searchable data
        Student::factory()->count(500)->create(['class_id' => $this->class->id]);
        
        $targetStudent = Student::factory()->create([
            'name' => 'John Doe Target Student',
            'roll_number' => 'SEARCH123',
            'class_id' => $this->class->id,
        ]);

        $startTime = microtime(true);

        $response = $this->actingAs($this->admin)->get('/admin/students?search=John Doe Target');

        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;

        $response->assertStatus(200);
        $response->assertSee('John Doe Target Student');
        $this->assertLessThan(3.0, $loadTime, 'Search should complete within 3 seconds with 500 records');
    }

    /** @test */
    public function export_functionality_performs_well()
    {
        // Create test data
        Student::factory()->count(100)->create(['class_id' => $this->class->id]);

        $startTime = microtime(true);

        $response = $this->actingAs($this->admin)->get('/admin/data-export/students?format=csv');

        $endTime = microtime(true);
        $loadTime = $endTime - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(15.0, $loadTime, 'CSV export should complete within 15 seconds for 100 students');
    }

    /** @test */
    public function system_handles_multiple_concurrent_users()
    {
        $users = User::factory()->count(10)->create();
        $students = Student::factory()->count(20)->create(['class_id' => $this->class->id]);

        $responses = [];
        $loadTimes = [];

        // Simulate concurrent requests
        foreach ($users as $user) {
            $startTime = microtime(true);
            
            $response = $this->actingAs($user)->get('/admin/students');
            
            $endTime = microtime(true);
            $loadTime = $endTime - $startTime;

            $responses[] = $response;
            $loadTimes[] = $loadTime;
        }

        // All requests should succeed
        foreach ($responses as $response) {
            $response->assertStatus(200);
        }

        // Average load time should be reasonable
        $averageLoadTime = array_sum($loadTimes) / count($loadTimes);
        $this->assertLessThan(5.0, $averageLoadTime, 'Average load time should be less than 5 seconds with concurrent users');
    }
}

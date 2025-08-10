<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\School;
use App\Models\Student;
use App\Imports\StudentsImport;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentImportTest extends TestCase
{
    use RefreshDatabase;

    protected $school;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test school
        $this->school = new School([
            'id' => 1,
            'name' => 'Test School',
            'code' => 'TEST001',
            'status' => 'active'
        ]);
        $this->school->id = 1;
    }

    /** @test */
    public function it_can_detect_duplicate_entries_within_import_file()
    {
        $import = new StudentsImport($this->school);
        
        // Test data with duplicates
        $data = [
            ['first_name', 'last_name', 'date_of_birth', 'gender', 'phone', 'email', 'address', 'guardian_name', 'guardian_phone', 'admission_date'],
            ['John', 'Doe', '2000-01-01', 'Male', '9800000001', 'john@test.com', 'Address 1', 'Guardian 1', '9800000002', '2024-01-01'],
            ['Jane', 'Smith', '2000-02-01', 'Female', '9800000003', 'jane@test.com', 'Address 2', 'Guardian 2', '9800000004', '2024-01-01'],
            ['John', 'Doe', '2000-01-01', 'Male', '9800000001', 'john@test.com', 'Address 1', 'Guardian 1', '9800000002', '2024-01-01'], // Duplicate
        ];

        $import->processData($data);
        $results = $import->getResults();

        $this->assertEquals(1, $results['success_count']);
        $this->assertEquals(1, $results['duplicate_count']);
        $this->assertCount(1, $results['errors']);
        $this->assertStringContainsString('Duplicate email found in import file', $results['errors'][0]['errors'][0]);
    }

    /** @test */
    public function it_can_detect_existing_students_in_database()
    {
        // Create an existing student
        Student::factory()->create([
            'school_id' => $this->school->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@test.com',
            'phone' => '9800000001',
            'date_of_birth' => '2000-01-01'
        ]);

        $import = new StudentsImport($this->school);
        
        // Test data with existing student
        $data = [
            ['first_name', 'last_name', 'date_of_birth', 'gender', 'phone', 'email', 'address', 'guardian_name', 'guardian_phone', 'admission_date'],
            ['John', 'Doe', '2000-01-01', 'Male', '9800000001', 'john@test.com', 'Address 1', 'Guardian 1', '9800000002', '2024-01-01'], // Existing
            ['Jane', 'Smith', '2000-02-01', 'Female', '9800000003', 'jane@test.com', 'Address 2', 'Guardian 2', '9800000004', '2024-01-01'], // New
        ];

        $import->processData($data);
        $results = $import->getResults();

        $this->assertEquals(1, $results['success_count']);
        $this->assertEquals(1, $results['duplicate_count']);
        $this->assertCount(1, $results['errors']);
        $this->assertStringContainsString('Student with email \'john@test.com\' already exists', $results['errors'][0]['errors'][0]);
    }

    /** @test */
    public function it_provides_detailed_validation_errors()
    {
        $import = new StudentsImport($this->school);
        
        // Test data with validation errors
        $data = [
            ['first_name', 'last_name', 'date_of_birth', 'gender', 'phone', 'email', 'address', 'guardian_name', 'guardian_phone', 'admission_date'],
            ['', 'Doe', '2000-01-01', 'Male', '9800000001', 'invalid-email', 'Address 1', 'Guardian 1', '9800000002', '2024-01-01'], // Missing first name, invalid email
            ['Jane', '', '2030-01-01', 'Invalid', '9800000003', 'jane@test.com', '', 'Guardian 2', '9800000004', '2024-01-01'], // Missing last name, future DOB, invalid gender, missing address
        ];

        $import->processData($data);
        $results = $import->getResults();

        $this->assertEquals(0, $results['success_count']);
        $this->assertEquals(2, $results['error_count']);
        $this->assertCount(2, $results['errors']);
        
        // Check that errors contain detailed information
        $this->assertArrayHasKey('data', $results['errors'][0]);
        $this->assertArrayHasKey('errors', $results['errors'][0]);
        $this->assertIsArray($results['errors'][0]['errors']);
    }

    /** @test */
    public function it_skips_empty_rows()
    {
        $import = new StudentsImport($this->school);
        
        // Test data with empty rows
        $data = [
            ['first_name', 'last_name', 'date_of_birth', 'gender', 'phone', 'email', 'address', 'guardian_name', 'guardian_phone', 'admission_date'],
            ['John', 'Doe', '2000-01-01', 'Male', '9800000001', 'john@test.com', 'Address 1', 'Guardian 1', '9800000002', '2024-01-01'],
            ['', '', '', '', '', '', '', '', '', ''], // Empty row
            ['', '', '', '', '', '', '', '', '', ''], // Another empty row
            ['Jane', 'Smith', '2000-02-01', 'Female', '9800000003', 'jane@test.com', 'Address 2', 'Guardian 2', '9800000004', '2024-01-01'],
        ];

        $import->processData($data);
        $results = $import->getResults();

        $this->assertEquals(2, $results['success_count']);
        $this->assertEquals(0, $results['error_count']);
        $this->assertEquals(2, $results['skipped_count']);
    }

    /** @test */
    public function it_provides_comprehensive_import_summary()
    {
        // Create an existing student
        Student::factory()->create([
            'school_id' => $this->school->id,
            'email' => 'existing@test.com',
            'phone' => '9800000000'
        ]);

        $import = new StudentsImport($this->school);
        
        // Mixed test data
        $data = [
            ['first_name', 'last_name', 'date_of_birth', 'gender', 'phone', 'email', 'address', 'guardian_name', 'guardian_phone', 'admission_date'],
            ['John', 'Doe', '2000-01-01', 'Male', '9800000001', 'john@test.com', 'Address 1', 'Guardian 1', '9800000002', '2024-01-01'], // Success
            ['', 'Invalid', '2000-01-01', 'Male', '9800000003', 'invalid@test.com', 'Address 2', 'Guardian 2', '9800000004', '2024-01-01'], // Validation error
            ['Jane', 'Smith', '2000-02-01', 'Female', '9800000000', 'existing@test.com', 'Address 3', 'Guardian 3', '9800000005', '2024-01-01'], // Duplicate
            ['', '', '', '', '', '', '', '', '', ''], // Empty row
            ['Bob', 'Wilson', '2000-03-01', 'Male', '9800000006', 'bob@test.com', 'Address 4', 'Guardian 4', '9800000007', '2024-01-01'], // Success
        ];

        $import->processData($data);
        $results = $import->getResults();

        $this->assertEquals(2, $results['success_count']);
        $this->assertEquals(1, $results['error_count']);
        $this->assertEquals(1, $results['duplicate_count']);
        $this->assertEquals(1, $results['skipped_count']);
        $this->assertEquals(4, $results['total_processed']); // success + error + duplicate
    }
}

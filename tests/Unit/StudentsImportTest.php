<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\School;
use App\Imports\StudentsImport;

class StudentsImportTest extends TestCase
{
    protected $school;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a School instance without database
        $this->school = new School();
        $this->school->id = 1;
        $this->school->name = 'Test School';
    }



    public function test_can_detect_duplicate_emails_within_import_file()
    {
        $import = new StudentsImport($this->school);
        
        // Test data with duplicate emails
        $data = [
            ['first_name', 'last_name', 'date_of_birth', 'gender', 'phone', 'email', 'address', 'guardian_name', 'guardian_phone', 'guardian_relation', 'admission_date', 'nationality', 'status'],
            ['John', 'Doe', '2000-01-01', 'Male', '9800000001', 'john@test.com', 'Address 1', 'Guardian 1', '9800000002', 'Father', '2024-01-01', 'Nepali', 'active'],
            ['Jane', 'Smith', '2000-02-01', 'Female', '9800000003', 'john@test.com', 'Address 2', 'Guardian 2', '9800000004', 'Mother', '2024-01-01', 'Nepali', 'active'], // Duplicate email
        ];

        // Use reflection to test the protected method
        $reflection = new \ReflectionClass($import);
        $method = $reflection->getMethod('checkForDuplicates');
        $method->setAccessible(true);

        // Process first row (should be fine)
        $rowData1 = $reflection->getMethod('mapRowToFields')->invoke($import, $data[1]);
        $result1 = $method->invoke($import, $rowData1, 2);
        $this->assertTrue($result1);

        // Add first email to processed list
        $processedEmailsProperty = $reflection->getProperty('processedEmails');
        $processedEmailsProperty->setAccessible(true);
        $processedEmailsProperty->setValue($import, ['john@test.com']);

        // Process second row (should detect duplicate)
        $rowData2 = $reflection->getMethod('mapRowToFields')->invoke($import, $data[2]);
        $result2 = $method->invoke($import, $rowData2, 3);
        $this->assertStringContainsString('Duplicate email found in import file', $result2);
    }

    public function test_can_detect_duplicate_phones_within_import_file()
    {
        $import = new StudentsImport($this->school);
        
        // Test data with duplicate phones
        $data = [
            ['first_name', 'last_name', 'date_of_birth', 'gender', 'phone', 'email', 'address', 'guardian_name', 'guardian_phone', 'guardian_relation', 'admission_date', 'nationality', 'status'],
            ['John', 'Doe', '2000-01-01', 'Male', '9800000001', 'john@test.com', 'Address 1', 'Guardian 1', '9800000002', 'Father', '2024-01-01', 'Nepali', 'active'],
            ['Jane', 'Smith', '2000-02-01', 'Female', '9800000001', 'jane@test.com', 'Address 2', 'Guardian 2', '9800000004', 'Mother', '2024-01-01', 'Nepali', 'active'], // Duplicate phone
        ];

        // Use reflection to test the protected method
        $reflection = new \ReflectionClass($import);
        $method = $reflection->getMethod('checkForDuplicates');
        $method->setAccessible(true);

        // Process first row (should be fine)
        $rowData1 = $reflection->getMethod('mapRowToFields')->invoke($import, $data[1]);
        $result1 = $method->invoke($import, $rowData1, 2);
        $this->assertTrue($result1);

        // Add first phone to processed list
        $processedPhonesProperty = $reflection->getProperty('processedPhones');
        $processedPhonesProperty->setAccessible(true);
        $processedPhonesProperty->setValue($import, ['9800000001']);

        // Process second row (should detect duplicate)
        $rowData2 = $reflection->getMethod('mapRowToFields')->invoke($import, $data[2]);
        $result2 = $method->invoke($import, $rowData2, 3);
        $this->assertStringContainsString('Duplicate phone number found in import file', $result2);
    }

    public function test_can_generate_row_summary()
    {
        $import = new StudentsImport($this->school);
        
        $rowData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@test.com',
            'phone' => '9800000001'
        ];

        // Use reflection to test the protected method
        $reflection = new \ReflectionClass($import);
        $method = $reflection->getMethod('getRowSummary');
        $method->setAccessible(true);

        $summary = $method->invoke($import, $rowData);
        
        $this->assertEquals('John Doe (john@test.com, 9800000001)', $summary);
    }

    public function test_can_generate_row_summary_with_missing_data()
    {
        $import = new StudentsImport($this->school);
        
        $rowData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '9800000001'
            // Missing email
        ];

        // Use reflection to test the protected method
        $reflection = new \ReflectionClass($import);
        $method = $reflection->getMethod('getRowSummary');
        $method->setAccessible(true);

        $summary = $method->invoke($import, $rowData);
        
        $this->assertEquals('John Doe (No email, 9800000001)', $summary);
    }

    public function test_can_map_row_to_fields()
    {
        $import = new StudentsImport($this->school);

        // Complete row with all expected fields: first_name, last_name, date_of_birth, gender, phone, email, address, guardian_name, guardian_phone, guardian_relation, admission_date, nationality, status
        $row = ['John', 'Doe', '2000-01-01', 'Male', '9800000001', 'john@test.com', 'Address 1', 'Guardian 1', '9800000002', 'Father', '2024-01-01', 'Nepali', 'active'];

        // Use reflection to test the protected method
        $reflection = new \ReflectionClass($import);
        $method = $reflection->getMethod('mapRowToFields');
        $method->setAccessible(true);

        $result = $method->invoke($import, $row);

        $this->assertEquals('John', $result['first_name']);
        $this->assertEquals('Doe', $result['last_name']);
        $this->assertEquals('2000-01-01', $result['date_of_birth']);
        $this->assertEquals('Male', $result['gender']);
        $this->assertEquals('9800000001', $result['phone']);
        $this->assertEquals('john@test.com', $result['email']);
        $this->assertEquals('Address 1', $result['address']);
        $this->assertEquals('Guardian 1', $result['guardian_name']);
        $this->assertEquals('9800000002', $result['guardian_phone']);
        $this->assertEquals('Father', $result['guardian_relation']);
        $this->assertEquals('2024-01-01', $result['admission_date']);
        $this->assertEquals('Nepali', $result['nationality']);
        $this->assertEquals('active', $result['status']);
    }

    public function test_results_structure_is_correct()
    {
        $import = new StudentsImport($this->school);
        
        $results = $import->getResults();
        
        $this->assertArrayHasKey('success_count', $results);
        $this->assertArrayHasKey('error_count', $results);
        $this->assertArrayHasKey('duplicate_count', $results);
        $this->assertArrayHasKey('skipped_count', $results);
        $this->assertArrayHasKey('errors', $results);
        $this->assertArrayHasKey('total_processed', $results);
        
        $this->assertEquals(0, $results['success_count']);
        $this->assertEquals(0, $results['error_count']);
        $this->assertEquals(0, $results['duplicate_count']);
        $this->assertEquals(0, $results['skipped_count']);
        $this->assertEquals([], $results['errors']);
        $this->assertEquals(0, $results['total_processed']);
    }
}

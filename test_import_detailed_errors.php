<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Imports\StudentsImport;
use App\Models\School;
use App\Models\Student;

// Create a test school
$school = new School();
$school->id = 1;
$school->name = 'Test School';

// Create the import instance
$import = new StudentsImport($school);

// Test data with various duplicate scenarios
$testData = [
    // Header row
    ['first_name', 'last_name', 'date_of_birth', 'gender', 'phone', 'email', 'address', 'guardian_name', 'guardian_phone', 'guardian_relation', 'admission_date', 'nationality', 'status'],
    
    // Valid student
    ['John', 'Doe', '2000-01-01', 'Male', '9800000001', 'john@test.com', 'Address 1', 'Guardian 1', '9800000002', 'Father', '2024-01-01', 'Nepali', 'active'],
    
    // Duplicate email within file
    ['Jane', 'Smith', '2000-02-01', 'Female', '9800000003', 'john@test.com', 'Address 2', 'Guardian 2', '9800000004', 'Mother', '2024-01-01', 'Nepali', 'active'],
    
    // Duplicate phone within file
    ['Bob', 'Wilson', '2000-03-01', 'Male', '9800000001', 'bob@test.com', 'Address 3', 'Guardian 3', '9800000005', 'Father', '2024-01-01', 'Nepali', 'active'],
];

echo "Testing StudentsImport with detailed error messages...\n\n";

// Process the data
$import->processData($testData);
$results = $import->getResults();

echo "Import Results:\n";
echo "- Success Count: " . $results['success_count'] . "\n";
echo "- Error Count: " . $results['error_count'] . "\n";
echo "- Duplicate Count: " . $results['duplicate_count'] . "\n";
echo "- Total Processed: " . $results['total_processed'] . "\n\n";

echo "Detailed Errors:\n";
foreach ($results['errors'] as $error) {
    echo "Row {$error['row']}: {$error['data']}\n";
    foreach ($error['errors'] as $errorMsg) {
        echo "  - {$errorMsg}\n";
    }
    echo "\n";
}

echo "✅ Test completed successfully!\n";

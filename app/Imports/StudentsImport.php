<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\School;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentsImport
{
    protected $school;
    protected $errors = [];
    protected $successCount = 0;
    protected $errorCount = 0;
    protected $duplicateCount = 0;
    protected $skippedCount = 0;
    protected $processedEmails = [];
    protected $processedPhones = [];
    protected $skipExistingStudents = true; // New flag to control behavior

    public function __construct(School $school, $skipExistingStudents = true)
    {
        $this->school = $school;
        $this->skipExistingStudents = $skipExistingStudents;
    }

    /**
     * Process the imported data
     */
    public function processData($data)
    {
        // Skip the header row
        $rows = array_slice($data, 1);

        // Process each row individually with transaction isolation
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of header row and 0-based index

            try {
                // Convert row to associative array
                $rowData = $this->mapRowToFields($row);

                // Skip empty rows (check if all required fields are empty)
                if ($this->isEmptyRow($rowData)) {
                    $this->skippedCount++;
                    continue;
                }

                // Check if student already exists in database FIRST
                $existingStudentCheck = $this->checkExistingStudent($rowData);
                if ($existingStudentCheck) {
                    $existingStudent = $existingStudentCheck['student'];
                    $matchType = $existingStudentCheck['match_type'];
                    $matchValue = $existingStudentCheck['match_value'];

                    if ($this->skipExistingStudents) {
                        // Skip existing students silently and count as skipped
                        $this->skippedCount++;
                        continue;
                    } else {
                        // Show error for existing students (old behavior)
                        $errorMessage = $this->buildDuplicateErrorMessage($existingStudent, $matchType, $matchValue);

                        $this->errors[] = [
                            'row' => $rowNumber,
                            'data' => $this->getRowSummary($rowData),
                            'errors' => [$errorMessage]
                        ];
                        $this->duplicateCount++;
                        continue;
                    }
                }

                // Check for duplicates within the import file
                $duplicateCheck = $this->checkForDuplicates($rowData, $rowNumber);
                if ($duplicateCheck !== true) {
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'data' => $this->getRowSummary($rowData),
                        'errors' => [$duplicateCheck]
                    ];
                    $this->duplicateCount++;
                    continue;
                }

                // Validate the row data first (outside transaction)
                $validator = $this->validateRow($rowData, $rowNumber);

                if ($validator->fails()) {
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'data' => $this->getRowSummary($rowData),
                        'errors' => $validator->errors()->all()
                    ];
                    $this->errorCount++;
                    continue;
                }

                // Additional custom validation for email uniqueness within school
                $customValidationErrors = $this->performCustomValidation($rowData, $rowNumber);
                if (!empty($customValidationErrors)) {
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'data' => $this->getRowSummary($rowData),
                        'errors' => $customValidationErrors
                    ];
                    $this->errorCount++;
                    continue;
                }

                // Process each row in its own transaction for isolation
                try {
                    DB::transaction(function () use ($rowData, $rowNumber) {
                        // Create the student
                        $studentData = $this->prepareStudentData($rowData);
                        Student::create($studentData);

                        // Track processed data for duplicate checking
                        if (!empty($rowData['email'])) {
                            $this->processedEmails[] = strtolower(trim($rowData['email']));
                        }
                        $this->processedPhones[] = trim($rowData['phone']);

                        $this->successCount++;
                    });
                } catch (\Exception $e) {
                    Log::error('Student import error for row ' . $rowNumber, [
                        'error' => $e->getMessage(),
                        'data' => $rowData,
                        'trace' => $e->getTraceAsString()
                    ]);

                    $this->errors[] = [
                        'row' => $rowNumber,
                        'data' => $this->getRowSummary($rowData),
                        'errors' => ['Database error: ' . $e->getMessage()]
                    ];
                    $this->errorCount++;
                }

            } catch (\Exception $e) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'data' => isset($rowData) ? $this->getRowSummary($rowData) : 'Unable to parse row data',
                    'errors' => ['Unexpected error: ' . $e->getMessage()]
                ];
                $this->errorCount++;
            }
        }
    }

    /**
     * Map row data to field names
     */
    protected function mapRowToFields($row)
    {
        // Expected column order based on template
        $fields = [
            'first_name', 'last_name', 'date_of_birth', 'gender', 'phone',
            'email', 'address', 'guardian_name', 'guardian_phone',
            'guardian_relation', 'admission_date', 'nationality', 'status'
        ];

        $mapped = [];
        foreach ($fields as $index => $field) {
            $mapped[$field] = isset($row[$index]) ? trim($row[$index]) : '';
        }

        return $mapped;
    }

    /**
     * Validate a single row of data
     */
    protected function validateRow(array $row, int $rowNumber)
    {
        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date|before_or_equal:today',
            'gender' => 'required|in:Male,Female,Other',
            'phone' => 'required|string|max:15',
            'email' => [
                'nullable',
                'email',
                'max:100'
            ],
            'address' => 'required|string',
            'guardian_name' => 'required|string|max:100',
            'guardian_phone' => 'required|string|max:15',
            'admission_date' => 'required|date',
            'status' => 'nullable|in:active,inactive,graduated,transferred,dropped',
        ];

        $messages = [
            'first_name.required' => "Row {$rowNumber}: First name is required",
            'last_name.required' => "Row {$rowNumber}: Last name is required",
            'date_of_birth.required' => "Row {$rowNumber}: Date of birth is required",
            'date_of_birth.date' => "Row {$rowNumber}: Date of birth must be a valid date",
            'gender.required' => "Row {$rowNumber}: Gender is required",
            'gender.in' => "Row {$rowNumber}: Gender must be Male, Female, or Other",
            'phone.required' => "Row {$rowNumber}: Phone is required",
            'email.email' => "Row {$rowNumber}: Email must be a valid email address",
            'address.required' => "Row {$rowNumber}: Address is required",
            'guardian_name.required' => "Row {$rowNumber}: Guardian name is required",
            'guardian_phone.required' => "Row {$rowNumber}: Guardian phone is required",
            'admission_date.required' => "Row {$rowNumber}: Admission date is required",
            'admission_date.date' => "Row {$rowNumber}: Admission date must be a valid date",
        ];

        return Validator::make($row, $rules, $messages);
    }

    /**
     * Prepare student data for creation
     */
    protected function prepareStudentData(array $row)
    {
        return [
            'school_id' => $this->school->id,
            'first_name' => trim($row['first_name']),
            'last_name' => trim($row['last_name']),
            'date_of_birth' => $row['date_of_birth'],
            'gender' => $row['gender'],
            'phone' => trim($row['phone']),
            'email' => !empty($row['email']) ? trim($row['email']) : null,
            'address' => trim($row['address']),
            'guardian_name' => trim($row['guardian_name']),
            'guardian_phone' => trim($row['guardian_phone']),
            'admission_date' => $row['admission_date'],
            'status' => $row['status'] ?? 'active',
            'nationality' => $row['nationality'] ?? 'Nepali',
            'guardian_relation' => $row['guardian_relation'] ?? 'Parent',
        ];
    }

    /**
     * Check for duplicates within the import file
     */
    protected function checkForDuplicates(array $row, int $rowNumber)
    {
        // Check email duplicates
        if (!empty($row['email'])) {
            $email = strtolower(trim($row['email']));
            if (in_array($email, $this->processedEmails)) {
                return "Duplicate email found in import file: {$row['email']}";
            }
        }

        // Check phone duplicates
        $phone = trim($row['phone']);
        if (in_array($phone, $this->processedPhones)) {
            return "Duplicate phone number found in import file: {$phone}";
        }

        return true;
    }

    /**
     * Check if student already exists in database
     */
    protected function checkExistingStudent(array $row)
    {
        $query = Student::where('school_id', $this->school->id);

        // Check by email if provided
        if (!empty($row['email'])) {
            $existing = $query->where('email', trim($row['email']))->first();
            if ($existing) {
                return [
                    'student' => $existing,
                    'match_type' => 'email',
                    'match_value' => trim($row['email'])
                ];
            }
        }

        // Check by phone number
        $existing = $query->where('phone', trim($row['phone']))->first();
        if ($existing) {
            return [
                'student' => $existing,
                'match_type' => 'phone',
                'match_value' => trim($row['phone'])
            ];
        }

        // Check by name and date of birth combination
        $existing = $query->where('first_name', trim($row['first_name']))
                         ->where('last_name', trim($row['last_name']))
                         ->where('date_of_birth', $row['date_of_birth'])
                         ->first();

        if ($existing) {
            return [
                'student' => $existing,
                'match_type' => 'name_and_dob',
                'match_value' => trim($row['first_name']) . ' ' . trim($row['last_name']) . ' (' . $row['date_of_birth'] . ')'
            ];
        }

        return null;
    }

    /**
     * Build detailed error message for duplicate students
     */
    protected function buildDuplicateErrorMessage($existingStudent, $matchType, $matchValue)
    {
        $existingStudentInfo = "{$existingStudent->first_name} {$existingStudent->last_name}";
        $existingStudentDetails = [];

        if ($existingStudent->email) {
            $existingStudentDetails[] = "Email: {$existingStudent->email}";
        }
        if ($existingStudent->phone) {
            $existingStudentDetails[] = "Phone: {$existingStudent->phone}";
        }
        if ($existingStudent->date_of_birth) {
            $existingStudentDetails[] = "DOB: {$existingStudent->date_of_birth}";
        }

        $detailsString = implode(', ', $existingStudentDetails);

        switch ($matchType) {
            case 'email':
                return "Student with email '{$matchValue}' already exists: {$existingStudentInfo} ({$detailsString})";
            case 'phone':
                return "Student with phone '{$matchValue}' already exists: {$existingStudentInfo} ({$detailsString})";
            case 'name_and_dob':
                return "Student with same name and date of birth already exists: {$existingStudentInfo} ({$detailsString})";
            default:
                return "Student already exists in database: {$existingStudentInfo} ({$detailsString})";
        }
    }

    /**
     * Get a summary of row data for error display
     */
    protected function getRowSummary(array $row)
    {
        $name = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
        $email = !empty($row['email']) ? $row['email'] : 'No email';
        $phone = $row['phone'] ?? 'No phone';

        return "{$name} ({$email}, {$phone})";
    }

    /**
     * Check if a row is empty (all required fields are empty)
     */
    private function isEmptyRow($rowData)
    {
        $requiredFields = ['first_name', 'last_name', 'email', 'phone'];

        foreach ($requiredFields as $field) {
            if (!empty(trim($rowData[$field] ?? ''))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Perform custom validation that can't be done with Laravel's built-in rules
     */
    private function performCustomValidation($rowData, $rowNumber)
    {
        $errors = [];

        // Check email uniqueness within the school (if email is provided)
        if (!empty($rowData['email'])) {
            $email = strtolower(trim($rowData['email'])); // Convert to lowercase for case-insensitive comparison

            // Check if email exists in database for this school
            $existingStudent = Student::where('school_id', $this->school->id)
                                    ->whereRaw('LOWER(email) = ?', [$email])
                                    ->first();

            if ($existingStudent) {
                $errors[] = "Row {$rowNumber}: Email '{$rowData['email']}' already exists in this school (Student: {$existingStudent->first_name} {$existingStudent->last_name})";
            }
        }

        return $errors;
    }

    /**
     * Get import results
     */
    public function getResults()
    {
        return [
            'success_count' => $this->successCount,
            'error_count' => $this->errorCount,
            'duplicate_count' => $this->duplicateCount,
            'skipped_count' => $this->skippedCount,
            'errors' => $this->errors,
            'total_processed' => $this->successCount + $this->errorCount + $this->duplicateCount + $this->skippedCount,
            'skip_existing_enabled' => $this->skipExistingStudents
        ];
    }
}

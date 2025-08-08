<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\School;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StudentsImport
{
    protected $school;
    protected $errors = [];
    protected $successCount = 0;
    protected $errorCount = 0;

    public function __construct(School $school)
    {
        $this->school = $school;
    }

    /**
     * Process the imported data
     */
    public function processData($data)
    {
        // Skip the header row
        $rows = array_slice($data, 1);

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of header row and 0-based index

            try {
                // Convert row to associative array
                $rowData = $this->mapRowToFields($row);

                // Skip empty rows
                if (empty(array_filter($rowData))) {
                    continue;
                }

                // Validate the row data
                $validator = $this->validateRow($rowData, $rowNumber);

                if ($validator->fails()) {
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'errors' => $validator->errors()->all()
                    ];
                    $this->errorCount++;
                    continue;
                }

                // Create the student
                $studentData = $this->prepareStudentData($rowData);
                Student::create($studentData);

                $this->successCount++;

            } catch (\Exception $e) {
                $this->errors[] = [
                    'row' => $rowNumber,
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
                'max:100',
                Rule::unique('students')->where(function ($query) {
                    return $query->where('school_id', $this->school->id);
                })
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
            'email.unique' => "Row {$rowNumber}: Email already exists in this school",
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
     * Get import results
     */
    public function getResults()
    {
        return [
            'success_count' => $this->successCount,
            'error_count' => $this->errorCount,
            'errors' => $this->errors,
            'total_processed' => $this->successCount + $this->errorCount
        ];
    }
}

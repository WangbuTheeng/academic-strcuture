<?php

namespace App\Services;

use App\Models\StudentEnrollment;
use App\Models\StudentSubject;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoSubjectEnrollmentService
{
    /**
     * Automatically enroll a student in subjects based on their program and class.
     * 
     * @param StudentEnrollment $enrollment
     * @param array $options
     * @return array
     */
    public function autoEnrollSubjects(StudentEnrollment $enrollment, array $options = [])
    {
        $options = array_merge([
            'compulsory_only' => false,  // If true, only enroll compulsory subjects
            'program_subjects' => true,   // Include program subjects
            'class_subjects' => true,     // Include class subjects
            'skip_existing' => true,      // Skip already enrolled subjects
        ], $options);

        $enrolledSubjects = [];
        $skippedSubjects = [];
        $errors = [];

        try {
            DB::beginTransaction();

            // Get subjects to enroll
            $subjectsToEnroll = $this->getSubjectsToEnroll($enrollment, $options);

            foreach ($subjectsToEnroll as $subjectData) {
                $result = $this->enrollStudentInSubject($enrollment, $subjectData);
                
                if ($result['success']) {
                    $enrolledSubjects[] = $result['subject'];
                } else {
                    $skippedSubjects[] = $result['subject'];
                }
            }

            DB::commit();

            Log::info('Auto subject enrollment completed', [
                'enrollment_id' => $enrollment->id,
                'student_id' => $enrollment->student_id,
                'enrolled_count' => count($enrolledSubjects),
                'skipped_count' => count($skippedSubjects)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Auto subject enrollment failed', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage()
            ]);
            $errors[] = $e->getMessage();
        }

        return [
            'success' => empty($errors),
            'enrolled' => $enrolledSubjects,
            'skipped' => $skippedSubjects,
            'errors' => $errors,
            'summary' => [
                'enrolled_count' => count($enrolledSubjects),
                'skipped_count' => count($skippedSubjects),
                'total_processed' => count($enrolledSubjects) + count($skippedSubjects)
            ]
        ];
    }

    /**
     * Get subjects that should be enrolled for the student.
     * 
     * @param StudentEnrollment $enrollment
     * @param array $options
     * @return \Illuminate\Support\Collection
     */
    protected function getSubjectsToEnroll(StudentEnrollment $enrollment, array $options)
    {
        $subjects = collect();

        // Get program subjects
        if ($options['program_subjects']) {
            $programSubjects = $this->getProgramSubjects($enrollment, $options['compulsory_only']);
            $subjects = $subjects->merge($programSubjects);
        }

        // Get class subjects
        if ($options['class_subjects']) {
            $classSubjects = $this->getClassSubjects($enrollment, $options['compulsory_only']);
            $subjects = $subjects->merge($classSubjects);
        }

        // Remove duplicates (prefer program subjects over class subjects)
        $subjects = $subjects->unique('id');

        // Filter out already enrolled subjects if requested
        if ($options['skip_existing']) {
            $existingSubjectIds = $enrollment->studentSubjects()->pluck('subject_id')->toArray();
            $subjects = $subjects->whereNotIn('id', $existingSubjectIds);
        }

        return $subjects;
    }

    /**
     * Get subjects from the program.
     * 
     * @param StudentEnrollment $enrollment
     * @param bool $compulsoryOnly
     * @return \Illuminate\Support\Collection
     */
    protected function getProgramSubjects(StudentEnrollment $enrollment, bool $compulsoryOnly = false)
    {
        $query = $enrollment->program->subjects()->active();

        if ($compulsoryOnly) {
            $query->wherePivot('is_compulsory', true);
        }

        return $query->get()->map(function ($subject) {
            return [
                'id' => $subject->id,
                'name' => $subject->name,
                'code' => $subject->code,
                'credit_hours' => $subject->pivot->credit_hours ?? $subject->credit_hours,
                'is_compulsory' => $subject->pivot->is_compulsory,
                'source' => 'program',
                'year_no' => $subject->pivot->year_no,
                'semester_id' => $subject->pivot->semester_id,
            ];
        });
    }

    /**
     * Get subjects from the class.
     * 
     * @param StudentEnrollment $enrollment
     * @param bool $compulsoryOnly
     * @return \Illuminate\Support\Collection
     */
    protected function getClassSubjects(StudentEnrollment $enrollment, bool $compulsoryOnly = false)
    {
        $query = $enrollment->class->subjects()->active();

        if ($compulsoryOnly) {
            $query->wherePivot('is_compulsory', true);
        }

        return $query->get()->map(function ($subject) {
            return [
                'id' => $subject->id,
                'name' => $subject->name,
                'code' => $subject->code,
                'credit_hours' => $subject->pivot->credit_hours ?? $subject->credit_hours,
                'is_compulsory' => $subject->pivot->is_compulsory,
                'source' => 'class',
                'year_no' => $subject->pivot->year_no,
                'semester_id' => $subject->pivot->semester_id,
            ];
        });
    }

    /**
     * Enroll a student in a specific subject.
     * 
     * @param StudentEnrollment $enrollment
     * @param array $subjectData
     * @return array
     */
    protected function enrollStudentInSubject(StudentEnrollment $enrollment, array $subjectData)
    {
        // Check if already enrolled
        $existing = StudentSubject::where('student_enrollment_id', $enrollment->id)
            ->where('subject_id', $subjectData['id'])
            ->first();

        if ($existing) {
            return [
                'success' => false,
                'subject' => $subjectData,
                'reason' => 'already_enrolled'
            ];
        }

        // Create the enrollment
        StudentSubject::create([
            'school_id' => $enrollment->student->school_id,
            'student_enrollment_id' => $enrollment->id,
            'subject_id' => $subjectData['id'],
            'date_added' => now(),
            'status' => 'active'
        ]);

        return [
            'success' => true,
            'subject' => $subjectData,
            'reason' => 'enrolled'
        ];
    }

    /**
     * Get a summary of what subjects would be enrolled (preview mode).
     * 
     * @param StudentEnrollment $enrollment
     * @param array $options
     * @return array
     */
    public function previewSubjectEnrollment(StudentEnrollment $enrollment, array $options = [])
    {
        $options['skip_existing'] = true; // Always skip existing in preview
        $subjectsToEnroll = $this->getSubjectsToEnroll($enrollment, $options);
        
        $compulsory = $subjectsToEnroll->where('is_compulsory', true);
        $elective = $subjectsToEnroll->where('is_compulsory', false);
        
        return [
            'total_subjects' => $subjectsToEnroll->count(),
            'compulsory_subjects' => $compulsory->count(),
            'elective_subjects' => $elective->count(),
            'subjects' => $subjectsToEnroll->values(),
            'compulsory_list' => $compulsory->values(),
            'elective_list' => $elective->values(),
        ];
    }
}

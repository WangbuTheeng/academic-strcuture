<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\FeeStructure;
use App\Models\Level;
use App\Models\Program;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class BulkGenerationDiagnosticController extends Controller
{
    public function diagnose(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $academicYearId = $request->get('academic_year_id');
        $levelId = $request->get('level_id');
        $programId = $request->get('program_id');
        $classId = $request->get('class_id');

        $diagnostics = [
            'school_id' => $schoolId,
            'criteria' => [
                'academic_year_id' => $academicYearId,
                'level_id' => $levelId,
                'program_id' => $programId,
                'class_id' => $classId,
            ]
        ];

        // Check academic year
        $academicYear = AcademicYear::find($academicYearId);
        $diagnostics['academic_year'] = [
            'exists' => $academicYear ? true : false,
            'name' => $academicYear?->name,
            'is_current' => $academicYear?->is_current ?? false
        ];

        // Check total students in school
        $totalStudents = Student::where('school_id', $schoolId)->count();
        $activeStudents = Student::active()->where('school_id', $schoolId)->count();
        
        $diagnostics['students'] = [
            'total_in_school' => $totalStudents,
            'active_in_school' => $activeStudents,
        ];

        // Check students with enrollments
        $studentsWithEnrollments = Student::active()
            ->where('school_id', $schoolId)
            ->whereHas('enrollments')
            ->count();
            
        $diagnostics['students']['with_enrollments'] = $studentsWithEnrollments;

        // Check students matching criteria
        $studentsQuery = Student::active()
            ->where('school_id', $schoolId)
            ->whereHas('enrollments', function ($q) use ($academicYearId, $levelId, $programId, $classId) {
                if ($academicYearId) {
                    $q->where('academic_year_id', $academicYearId);
                }
                
                if ($levelId) {
                    $q->whereHas('program', function ($pq) use ($levelId) {
                        $pq->where('level_id', $levelId);
                    });
                }
                
                if ($programId) {
                    $q->where('program_id', $programId);
                }
                
                if ($classId) {
                    $q->where('class_id', $classId);
                }
            });

        $matchingStudents = $studentsQuery->with(['currentEnrollment.program.level', 'currentEnrollment.class'])->get();
        
        $diagnostics['students']['matching_criteria'] = $matchingStudents->count();
        $diagnostics['students']['details'] = $matchingStudents->map(function ($student) {
            $enrollment = $student->currentEnrollment;
            return [
                'id' => $student->id,
                'name' => $student->full_name,
                'admission_number' => $student->admission_number,
                'level' => $enrollment?->program?->level?->name,
                'program' => $enrollment?->program?->name,
                'class' => $enrollment?->class?->name,
                'academic_year_id' => $enrollment?->academic_year_id,
            ];
        });

        // Check fee structures
        $feeStructures = FeeStructure::where('school_id', $schoolId)->get();
        $diagnostics['fee_structures'] = [
            'total' => $feeStructures->count(),
            'details' => $feeStructures->map(function ($fs) {
                return [
                    'id' => $fs->id,
                    'name' => $fs->fee_name,
                    'category' => $fs->fee_category,
                    'amount' => $fs->amount,
                ];
            })
        ];

        // Check levels, programs, classes
        $diagnostics['structure'] = [
            'levels' => Level::where('school_id', $schoolId)->count(),
            'programs' => Program::where('school_id', $schoolId)->count(),
            'classes' => ClassModel::where('school_id', $schoolId)->count(),
        ];

        return response()->json([
            'success' => true,
            'diagnostics' => $diagnostics,
            'recommendations' => $this->generateRecommendations($diagnostics)
        ]);
    }

    private function generateRecommendations($diagnostics)
    {
        $recommendations = [];

        if ($diagnostics['students']['total_in_school'] == 0) {
            $recommendations[] = "No students found in your school. Please add students first.";
        }

        if ($diagnostics['students']['active_in_school'] == 0) {
            $recommendations[] = "No active students found. Check student status.";
        }

        if ($diagnostics['students']['with_enrollments'] == 0) {
            $recommendations[] = "No students have enrollments. Please enroll students in programs.";
        }

        if ($diagnostics['students']['matching_criteria'] == 0) {
            $recommendations[] = "No students match your selected criteria. Try:";
            $recommendations[] = "• Remove some filters (level, program, class)";
            $recommendations[] = "• Check if students are enrolled in the selected academic year";
            $recommendations[] = "• Verify students are enrolled in the selected level/program/class";
        }

        if ($diagnostics['fee_structures']['total'] == 0) {
            $recommendations[] = "No fee structures found. Please create fee structures first.";
        }

        if (!$diagnostics['academic_year']['exists']) {
            $recommendations[] = "Selected academic year not found. Please select a valid academic year.";
        }

        if (empty($recommendations)) {
            $recommendations[] = "Everything looks good! You should be able to generate bills.";
        }

        return $recommendations;
    }
}

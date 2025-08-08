<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\AcademicYear;
use App\Models\Mark;
use App\Models\Exam;
use App\Models\Level;
use App\Models\PromotionRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class PromotionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage-promotions']);
    }

    /**
     * Display promotion management dashboard.
     */
    public function index(Request $request)
    {
        $query = Student::with(['class.level', 'academicYear']);

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year_id', $request->academic_year);
        } else {
            // Default to current academic year
            $currentYear = AcademicYear::where('is_current', true)->first();
            if ($currentYear) {
                $query->where('academic_year_id', $currentYear->id);
            }
        }

        // Filter by class
        if ($request->filled('class')) {
            $query->where('class_id', $request->class);
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->whereHas('class', function($q) use ($request) {
                $q->where('level_id', $request->level);
            });
        }

        // Filter by promotion status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'eligible':
                    $query->whereDoesntHave('promotionRecords', function($q) {
                        $q->where('academic_year_id', AcademicYear::where('is_current', true)->value('id'));
                    });
                    break;
                case 'promoted':
                    $query->whereHas('promotionRecords', function($q) {
                        $q->where('academic_year_id', AcademicYear::where('is_current', true)->value('id'))
                          ->where('status', 'promoted');
                    });
                    break;
                case 'retained':
                    $query->whereHas('promotionRecords', function($q) {
                        $q->where('academic_year_id', AcademicYear::where('is_current', true)->value('id'))
                          ->where('status', 'retained');
                    });
                    break;
            }
        }

        $students = $query->get()->sortBy('currentEnrollment.roll_no');

        // Paginate manually since we're sorting by relationship
        $currentPage = request()->get('page', 1);
        $perPage = 20;
        $students = new \Illuminate\Pagination\LengthAwarePaginator(
            $students->forPage($currentPage, $perPage),
            $students->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        // Get filter options
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $classes = ClassModel::with('level')->get();
        $levels = Level::all();

        // Get promotion statistics
        $currentYear = AcademicYear::where('is_current', true)->first();
        $stats = [
            'total_students' => Student::where('academic_year_id', $currentYear?->id)->count(),
            'eligible_for_promotion' => $this->getEligibleStudentsCount($currentYear?->id),
            'promoted' => PromotionRecord::where('academic_year_id', $currentYear?->id)
                                        ->where('status', 'promoted')->count(),
            'retained' => PromotionRecord::where('academic_year_id', $currentYear?->id)
                                        ->where('status', 'retained')->count(),
        ];

        return view('admin.promotions.index', compact(
            'students', 'academicYears', 'classes', 'levels', 'stats'
        ));
    }

    /**
     * Show promotion analysis for a specific student.
     */
    public function analyze(Student $student)
    {
        $student->load(['class.level', 'academicYear', 'marks.exam', 'marks.subject']);

        // Get all marks for the current academic year
        $marks = $student->marks()
                        ->whereHas('exam', function($q) use ($student) {
                            $q->where('academic_year_id', $student->academic_year_id);
                        })
                        ->with(['exam', 'subject'])
                        ->get();

        // Calculate promotion criteria
        $analysis = $this->analyzePromotionEligibility($student, $marks);

        return view('admin.promotions.analyze', compact('student', 'marks', 'analysis'));
    }

    /**
     * Show bulk promotion interface.
     */
    public function bulk(Request $request)
    {
        $query = Student::with(['class.level', 'academicYear']);

        // Filter by class
        if ($request->filled('class')) {
            $query->where('class_id', $request->class);
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->whereHas('class', function($q) use ($request) {
                $q->where('level_id', $request->level);
            });
        }

        // Only students eligible for promotion (not already promoted/retained)
        $currentYear = AcademicYear::where('is_current', true)->first();
        $query->whereDoesntHave('promotionRecords', function($q) use ($currentYear) {
            $q->where('academic_year_id', $currentYear?->id);
        });

        $students = $query->get();

        // Analyze each student for promotion eligibility
        $promotionData = [];
        foreach ($students as $student) {
            $marks = $student->marks()
                           ->whereHas('exam', function($q) use ($student) {
                               $q->where('academic_year_id', $student->academic_year_id);
                           })
                           ->with(['exam', 'subject'])
                           ->get();

            $analysis = $this->analyzePromotionEligibility($student, $marks);
            $promotionData[] = [
                'student' => $student,
                'analysis' => $analysis,
                'recommendation' => $analysis['overall_result'] === 'Pass' ? 'promote' : 'retain'
            ];
        }

        $classes = ClassModel::with('level')->get();
        $levels = Level::all();
        $nextAcademicYear = $this->getNextAcademicYear();

        return view('admin.promotions.bulk', compact(
            'promotionData', 'classes', 'levels', 'nextAcademicYear'
        ));
    }

    /**
     * Process individual student promotion.
     */
    public function promote(Request $request, Student $student)
    {
        $validated = $request->validate([
            'action' => 'required|in:promote,retain',
            'next_class_id' => 'required_if:action,promote|exists:classes,id',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $currentYear = AcademicYear::where('is_current', true)->first();
        $nextYear = $this->getNextAcademicYear();

        if (!$currentYear || !$nextYear) {
            return back()->with('error', 'Academic year configuration is incomplete.');
        }

        DB::transaction(function () use ($validated, $student, $currentYear, $nextYear) {
            // Create promotion record
            PromotionRecord::create([
                'student_id' => $student->id,
                'from_class_id' => $student->class_id,
                'to_class_id' => $validated['action'] === 'promote' ? $validated['next_class_id'] : $student->class_id,
                'from_academic_year_id' => $currentYear->id,
                'to_academic_year_id' => $nextYear->id,
                'status' => $validated['action'] === 'promote' ? 'promoted' : 'retained',
                'remarks' => $validated['remarks'],
                'promoted_by' => auth()->id(),
                'promoted_at' => now(),
            ]);

            // Update student record if promoted
            if ($validated['action'] === 'promote') {
                $student->update([
                    'class_id' => $validated['next_class_id'],
                    'academic_year_id' => $nextYear->id,
                ]);
            }

            // Log the action
            activity()
                ->performedOn($student)
                ->causedBy(auth()->user())
                ->withProperties([
                    'action' => $validated['action'],
                    'from_class' => $student->class->name,
                    'to_class' => $validated['action'] === 'promote' ?
                                 ClassModel::find($validated['next_class_id'])->name :
                                 $student->class->name,
                    'remarks' => $validated['remarks']
                ])
                ->log("Student {$validated['action']}d");
        });

        $action = $validated['action'] === 'promote' ? 'promoted' : 'retained';
        return back()->with('success', "Student has been {$action} successfully.");
    }

    /**
     * Process bulk promotions.
     */
    public function processBulk(Request $request)
    {
        $validated = $request->validate([
            'promotions' => 'required|array',
            'promotions.*.student_id' => 'required|exists:students,id',
            'promotions.*.action' => 'required|in:promote,retain',
            'promotions.*.next_class_id' => 'required_if:promotions.*.action,promote|exists:classes,id',
            'promotions.*.remarks' => 'nullable|string|max:500',
        ]);

        $currentYear = AcademicYear::where('is_current', true)->first();
        $nextYear = $this->getNextAcademicYear();

        if (!$currentYear || !$nextYear) {
            return back()->with('error', 'Academic year configuration is incomplete.');
        }

        $processed = 0;
        $errors = [];

        DB::transaction(function () use ($validated, $currentYear, $nextYear, &$processed, &$errors) {
            foreach ($validated['promotions'] as $promotionData) {
                try {
                    $student = Student::find($promotionData['student_id']);

                    // Check if already processed
                    $existingRecord = PromotionRecord::where('student_id', $student->id)
                                                   ->where('from_academic_year_id', $currentYear->id)
                                                   ->first();

                    if ($existingRecord) {
                        $errors[] = "Student {$student->name} has already been processed.";
                        continue;
                    }

                    // Create promotion record
                    PromotionRecord::create([
                        'student_id' => $student->id,
                        'from_class_id' => $student->class_id,
                        'to_class_id' => $promotionData['action'] === 'promote' ?
                                        $promotionData['next_class_id'] : $student->class_id,
                        'from_academic_year_id' => $currentYear->id,
                        'to_academic_year_id' => $nextYear->id,
                        'status' => $promotionData['action'] === 'promote' ? 'promoted' : 'retained',
                        'remarks' => $promotionData['remarks'] ?? null,
                        'promoted_by' => auth()->id(),
                        'promoted_at' => now(),
                    ]);

                    // Update student record if promoted
                    if ($promotionData['action'] === 'promote') {
                        $student->update([
                            'class_id' => $promotionData['next_class_id'],
                            'academic_year_id' => $nextYear->id,
                        ]);
                    }

                    $processed++;

                } catch (\Exception $e) {
                    $studentName = isset($student) ? $student->name : 'Unknown';
                    $errors[] = "Error processing student {$studentName}: " . $e->getMessage();
                }
            }

            // Log bulk promotion
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'processed_count' => $processed,
                    'errors_count' => count($errors)
                ])
                ->log("Bulk promotion processed: {$processed} students");
        });

        $message = "Successfully processed {$processed} students.";
        if (!empty($errors)) {
            $message .= " " . count($errors) . " errors occurred.";
        }

        return redirect()->route('admin.promotions.index')
                        ->with('success', $message)
                        ->with('errors', $errors);
    }

    /**
     * Show promotion history.
     */
    public function history(Request $request)
    {
        $query = PromotionRecord::with([
            'student',
            'fromClass.level',
            'toClass.level',
            'fromAcademicYear',
            'toAcademicYear',
            'promotedBy'
        ]);

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('from_academic_year_id', $request->academic_year);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by student name
        if ($request->filled('search')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('roll_number', 'like', "%{$request->search}%");
            });
        }

        $promotions = $query->orderBy('promoted_at', 'desc')->paginate(20);

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('admin.promotions.history', compact('promotions', 'academicYears'));
    }

    /**
     * Analyze promotion eligibility for a student.
     */
    private function analyzePromotionEligibility(Student $student, Collection $marks): array
    {
        $subjectMarks = $marks->groupBy('subject_id');
        $subjectResults = [];
        $totalSubjects = $subjectMarks->count();
        $passedSubjects = 0;
        $totalPercentage = 0;

        foreach ($subjectMarks as $subjectId => $subjectMarksList) {
            $bestMark = $subjectMarksList->where('status', 'approved')->sortByDesc('percentage')->first();

            if ($bestMark) {
                $subjectResults[] = [
                    'subject' => $bestMark->subject->name,
                    'percentage' => $bestMark->percentage,
                    'grade' => $bestMark->grade,
                    'result' => $bestMark->result,
                ];

                $totalPercentage += $bestMark->percentage;
                if ($bestMark->result === 'Pass') {
                    $passedSubjects++;
                }
            }
        }

        $averagePercentage = $totalSubjects > 0 ? $totalPercentage / $totalSubjects : 0;
        $passPercentage = $totalSubjects > 0 ? ($passedSubjects / $totalSubjects) * 100 : 0;

        // Determine overall result based on institution rules
        $overallResult = $passPercentage >= 60 ? 'Pass' : 'Fail'; // 60% subjects must pass

        return [
            'subject_results' => $subjectResults,
            'total_subjects' => $totalSubjects,
            'passed_subjects' => $passedSubjects,
            'failed_subjects' => $totalSubjects - $passedSubjects,
            'average_percentage' => round($averagePercentage, 2),
            'pass_percentage' => round($passPercentage, 2),
            'overall_result' => $overallResult,
            'recommendation' => $overallResult === 'Pass' ? 'Promote' : 'Retain',
        ];
    }

    /**
     * Get count of students eligible for promotion.
     */
    private function getEligibleStudentsCount(?int $academicYearId): int
    {
        if (!$academicYearId) return 0;

        return Student::where('academic_year_id', $academicYearId)
                     ->whereDoesntHave('promotionRecords', function($q) use ($academicYearId) {
                         $q->where('academic_year_id', $academicYearId);
                     })
                     ->count();
    }

    /**
     * Get the next academic year.
     */
    private function getNextAcademicYear(): ?AcademicYear
    {
        $currentYear = AcademicYear::where('is_current', true)->first();

        if (!$currentYear) return null;

        // Try to find existing next year
        $nextYear = AcademicYear::where('start_date', '>', $currentYear->end_date)
                               ->orderBy('start_date')
                               ->first();

        // If no next year exists, create one
        if (!$nextYear) {
            $nextYearName = $this->generateNextYearName($currentYear->name);
            $nextYear = AcademicYear::create([
                'name' => $nextYearName,
                'start_date' => $currentYear->start_date->addYear(),
                'end_date' => $currentYear->end_date->addYear(),
                'is_current' => false,
                'is_active' => true,
            ]);
        }

        return $nextYear;
    }

    /**
     * Generate next academic year name.
     */
    private function generateNextYearName(string $currentName): string
    {
        // Handle format like "2081-2082"
        if (preg_match('/(\d{4})-(\d{4})/', $currentName, $matches)) {
            $startYear = (int)$matches[1] + 1;
            $endYear = (int)$matches[2] + 1;
            return "{$startYear}-{$endYear}";
        }

        // Handle format like "2081"
        if (preg_match('/(\d{4})/', $currentName, $matches)) {
            $year = (int)$matches[1] + 1;
            return (string)$year;
        }

        // Fallback
        return $currentName . ' (Next)';
    }
}

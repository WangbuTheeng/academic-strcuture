<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GraceMark;
use App\Models\Mark;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\ClassModel;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GraceMarkController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage-grace-marks']);
    }

    /**
     * Display grace marks management dashboard.
     */
    public function index(Request $request)
    {
        $query = GraceMark::with([
            'mark.student',
            'mark.exam',
            'mark.subject',
            'requestedBy',
            'approvedBy',
            'rejectedBy'
        ]);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by exam
        if ($request->filled('exam')) {
            $query->whereHas('mark.exam', function($q) use ($request) {
                $q->where('id', $request->exam);
            });
        }

        // Filter by class
        if ($request->filled('class')) {
            $query->whereHas('mark.student', function($q) use ($request) {
                $q->where('class_id', $request->class);
            });
        }

        // Search by student name
        if ($request->filled('search')) {
            $query->whereHas('mark.student', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('roll_number', 'like', "%{$request->search}%");
            });
        }

        $graceMarks = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $exams = Exam::with('academicYear')->orderBy('created_at', 'desc')->get();
        $classes = ClassModel::with('level')->get();

        // Get statistics
        $stats = [
            'total_requests' => GraceMark::count(),
            'pending_requests' => GraceMark::where('status', 'pending')->count(),
            'approved_requests' => GraceMark::where('status', 'approved')->count(),
            'rejected_requests' => GraceMark::where('status', 'rejected')->count(),
        ];

        return view('admin.grace-marks.index', compact('graceMarks', 'exams', 'classes', 'stats'));
    }

    /**
     * Show form to request grace marks.
     */
    public function create(Request $request)
    {
        $query = Mark::with(['student.class', 'exam', 'subject'])
                    ->where('status', 'approved');

        // Filter by exam
        if ($request->filled('exam')) {
            $query->where('exam_id', $request->exam);
        }

        // Filter by class
        if ($request->filled('class')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('class_id', $request->class);
            });
        }

        // Only show marks that don't already have grace marks
        $query->whereDoesntHave('graceMark');

        $marks = $query->orderBy('percentage')->paginate(20);

        $exams = Exam::with('academicYear')->orderBy('created_at', 'desc')->get();
        $classes = ClassModel::with('level')->get();

        return view('admin.grace-marks.create', compact('marks', 'exams', 'classes'));
    }

    /**
     * Store grace mark request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mark_id' => 'required|exists:marks,id',
            'grace_marks' => 'required|numeric|min:0.01|max:10',
            'reason' => 'required|string|max:1000',
            'justification' => 'required|string|max:2000',
        ]);

        $mark = Mark::with(['student', 'exam', 'subject'])->findOrFail($validated['mark_id']);

        // Check if grace mark already exists
        if ($mark->graceMark) {
            return back()->with('error', 'Grace mark request already exists for this mark.');
        }

        // Check if grace marks would exceed maximum allowed
        $newTotal = $mark->obtained_marks + $validated['grace_marks'];
        if ($newTotal > $mark->total_marks) {
            return back()->with('error', 'Grace marks would exceed the maximum marks for this subject.');
        }

        DB::transaction(function () use ($validated, $mark) {
            GraceMark::create([
                'mark_id' => $validated['mark_id'],
                'grace_marks' => $validated['grace_marks'],
                'reason' => $validated['reason'],
                'justification' => $validated['justification'],
                'status' => 'pending',
                'requested_by' => auth()->id(),
                'requested_at' => now(),
            ]);

            // Log the request
            activity()
                ->performedOn($mark)
                ->causedBy(auth()->user())
                ->withProperties([
                    'grace_marks' => $validated['grace_marks'],
                    'reason' => $validated['reason'],
                    'student' => $mark->student->name,
                    'subject' => $mark->subject->name,
                ])
                ->log('Grace mark requested');
        });

        return redirect()->route('admin.grace-marks.index')
                        ->with('success', 'Grace mark request submitted successfully.');
    }

    /**
     * Show grace mark details.
     */
    public function show(GraceMark $graceMark)
    {
        $graceMark->load([
            'mark.student.class.level',
            'mark.exam.academicYear',
            'mark.subject',
            'requestedBy',
            'approvedBy',
            'rejectedBy'
        ]);

        return view('admin.grace-marks.show', compact('graceMark'));
    }

    /**
     * Approve grace mark request.
     */
    public function approve(Request $request, GraceMark $graceMark)
    {
        if ($graceMark->status !== 'pending') {
            return back()->with('error', 'Only pending grace mark requests can be approved.');
        }

        $validated = $request->validate([
            'approval_remarks' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($graceMark, $validated) {
            // Update grace mark status
            $graceMark->update([
                'status' => 'approved',
                'approval_remarks' => $validated['approval_remarks'],
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Update the original mark
            $mark = $graceMark->mark;
            $newObtainedMarks = $mark->obtained_marks + $graceMark->grace_marks;

            // Recalculate percentage and grade
            $newPercentage = ($newObtainedMarks / $mark->total_marks) * 100;

            // Get grading scale and calculate new grade
            $exam = $mark->exam;
            $gradingScale = $exam->gradingScale;
            $gradeInfo = $gradingScale ? $gradingScale->calculateGrade($newPercentage) : [
                'grade' => 'N/A',
                'gpa' => 0,
                'result' => $newPercentage >= 40 ? 'Pass' : 'Fail'
            ];

            $mark->update([
                'obtained_marks' => $newObtainedMarks,
                'percentage' => $newPercentage,
                'grade' => $gradeInfo['grade'],
                'gpa' => $gradeInfo['gpa'],
                'result' => $gradeInfo['result'],
                'grace_marks_applied' => true,
            ]);

            // Log the approval
            activity()
                ->performedOn($graceMark)
                ->causedBy(auth()->user())
                ->withProperties([
                    'grace_marks' => $graceMark->grace_marks,
                    'student' => $mark->student->name,
                    'subject' => $mark->subject->name,
                    'new_marks' => $newObtainedMarks,
                    'new_percentage' => $newPercentage,
                    'new_grade' => $gradeInfo['grade'],
                ])
                ->log('Grace mark approved and applied');
        });

        return back()->with('success', 'Grace mark request approved and applied successfully.');
    }

    /**
     * Reject grace mark request.
     */
    public function reject(Request $request, GraceMark $graceMark)
    {
        if ($graceMark->status !== 'pending') {
            return back()->with('error', 'Only pending grace mark requests can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $graceMark->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
        ]);

        // Log the rejection
        activity()
            ->performedOn($graceMark)
            ->causedBy(auth()->user())
            ->withProperties([
                'grace_marks' => $graceMark->grace_marks,
                'student' => $graceMark->mark->student->name,
                'subject' => $graceMark->mark->subject->name,
                'rejection_reason' => $validated['rejection_reason'],
            ])
            ->log('Grace mark rejected');

        return back()->with('success', 'Grace mark request rejected.');
    }

    /**
     * Bulk approve grace marks.
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'grace_mark_ids' => 'required|array',
            'grace_mark_ids.*' => 'exists:grace_marks,id',
            'bulk_approval_remarks' => 'nullable|string|max:1000',
        ]);

        $approvedCount = 0;
        $errors = [];

        DB::transaction(function () use ($validated, &$approvedCount, &$errors) {
            $graceMarks = GraceMark::whereIn('id', $validated['grace_mark_ids'])
                                  ->where('status', 'pending')
                                  ->with(['mark.student', 'mark.subject', 'mark.exam'])
                                  ->get();

            foreach ($graceMarks as $graceMark) {
                try {
                    // Update grace mark status
                    $graceMark->update([
                        'status' => 'approved',
                        'approval_remarks' => $validated['bulk_approval_remarks'],
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ]);

                    // Update the original mark
                    $mark = $graceMark->mark;
                    $newObtainedMarks = $mark->obtained_marks + $graceMark->grace_marks;
                    $newPercentage = ($newObtainedMarks / $mark->total_marks) * 100;

                    // Get grading scale and calculate new grade
                    $exam = $mark->exam;
                    $gradingScale = $exam->gradingScale;
                    $gradeInfo = $gradingScale ? $gradingScale->calculateGrade($newPercentage) : [
                        'grade' => 'N/A',
                        'gpa' => 0,
                        'result' => $newPercentage >= 40 ? 'Pass' : 'Fail'
                    ];

                    $mark->update([
                        'obtained_marks' => $newObtainedMarks,
                        'percentage' => $newPercentage,
                        'grade' => $gradeInfo['grade'],
                        'gpa' => $gradeInfo['gpa'],
                        'result' => $gradeInfo['result'],
                        'grace_marks_applied' => true,
                    ]);

                    $approvedCount++;

                } catch (\Exception $e) {
                    $studentName = $graceMark->mark->student->name ?? 'Unknown';
                    $errors[] = "Error processing grace mark for {$studentName}: " . $e->getMessage();
                }
            }

            // Log bulk approval
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'approved_count' => $approvedCount,
                    'errors_count' => count($errors)
                ])
                ->log("Bulk approved {$approvedCount} grace marks");
        });

        $message = "Successfully approved {$approvedCount} grace mark requests.";
        if (!empty($errors)) {
            $message .= " " . count($errors) . " errors occurred.";
        }

        return back()->with('success', $message)->with('errors', $errors);
    }

    /**
     * Show grace marks report.
     */
    public function report(Request $request)
    {
        $query = GraceMark::with([
            'mark.student.class.level',
            'mark.exam.academicYear',
            'mark.subject',
            'requestedBy',
            'approvedBy'
        ]);

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->whereHas('mark.exam', function($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $graceMarks = $query->orderBy('created_at', 'desc')->get();

        // Calculate statistics
        $statistics = [
            'total_requests' => $graceMarks->count(),
            'approved_requests' => $graceMarks->where('status', 'approved')->count(),
            'rejected_requests' => $graceMarks->where('status', 'rejected')->count(),
            'pending_requests' => $graceMarks->where('status', 'pending')->count(),
            'total_grace_marks' => $graceMarks->where('status', 'approved')->sum('grace_marks'),
            'average_grace_marks' => $graceMarks->where('status', 'approved')->avg('grace_marks'),
            'subject_wise' => $graceMarks->where('status', 'approved')
                                       ->groupBy('mark.subject.name')
                                       ->map(function($group) {
                                           return [
                                               'count' => $group->count(),
                                               'total_marks' => $group->sum('grace_marks'),
                                               'average_marks' => $group->avg('grace_marks'),
                                           ];
                                       }),
            'class_wise' => $graceMarks->where('status', 'approved')
                                     ->groupBy('mark.student.class.name')
                                     ->map(function($group) {
                                         return [
                                             'count' => $group->count(),
                                             'total_marks' => $group->sum('grace_marks'),
                                             'average_marks' => $group->avg('grace_marks'),
                                         ];
                                     }),
        ];

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('admin.grace-marks.report', compact('graceMarks', 'statistics', 'academicYears'));
    }
}

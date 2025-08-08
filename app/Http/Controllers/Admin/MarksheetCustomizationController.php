<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarksheetTemplate;
use App\Models\GradingScale;
use App\Models\InstituteSettings;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Mark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarksheetCustomizationController extends Controller
{
    /**
     * Display the marksheet customization dashboard.
     */
    public function index()
    {
        try {
            $templates = MarksheetTemplate::orderBy('created_at', 'desc')->get();
            $gradingScales = GradingScale::all();
            $instituteSettings = InstituteSettings::current() ?? (object) [
                'institution_name' => 'Academic Institution',
                'institution_address' => 'Institution Address',
                'institution_phone' => '+977-1-XXXXXXX',
                'institution_email' => 'info@institution.edu.np',
                'institution_website' => 'www.institution.edu.np',
                'principal_name' => 'Principal Name',
            ];

            return view('admin.marksheets.customize.index', compact('templates', 'gradingScales', 'instituteSettings'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading templates: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        $gradingScales = GradingScale::all();
        $instituteSettings = InstituteSettings::current();

        return view('admin.marksheets.customize.create', compact('gradingScales', 'instituteSettings'));
    }

    /**
     * Store a newly created template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'template_type' => 'required|in:modern,classic,minimal,custom',
            'grading_scale_id' => 'required|exists:grading_scales,id',
            'settings' => 'required|array',
            'settings.show_school_logo' => 'boolean',
            'settings.show_school_address' => 'boolean',
            'settings.show_principal_name' => 'boolean',
            'settings.show_theory_practical' => 'boolean',
            'settings.show_assessment_marks' => 'boolean',
            'settings.show_remarks' => 'boolean',
            'settings.show_grade_points' => 'boolean',
            'settings.header_color' => 'required|string|max:7',
            'settings.text_color' => 'required|string|max:7',
            'settings.border_style' => 'required|in:solid,dashed,dotted,none',
            'settings.font_family' => 'required|in:Arial,Times,Helvetica,Georgia',
            'settings.font_size' => 'required|integer|min:8|max:16',
            'custom_css' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset all other defaults
        if ($validated['is_default'] ?? false) {
            MarksheetTemplate::where('is_default', true)->update(['is_default' => false]);
        }

        $template = MarksheetTemplate::create($validated);

        return redirect()->route('admin.marksheets.customize.index')
                        ->with('success', 'Marksheet template created successfully.');
    }

    /**
     * Show the specified template.
     */
    public function show(MarksheetTemplate $template)
    {
        return view('admin.marksheets.customize.show', compact('template'));
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(MarksheetTemplate $template)
    {
        $gradingScales = GradingScale::all();
        $instituteSettings = InstituteSettings::current();

        return view('admin.marksheets.customize.edit', compact('template', 'gradingScales', 'instituteSettings'));
    }

    /**
     * Update the specified template.
     */
    public function update(Request $request, MarksheetTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'template_type' => 'required|in:modern,classic,minimal,custom',
            'grading_scale_id' => 'required|exists:grading_scales,id',
            'settings' => 'required|array',
            'settings.show_school_logo' => 'boolean',
            'settings.show_school_address' => 'boolean',
            'settings.show_principal_name' => 'boolean',
            'settings.show_theory_practical' => 'boolean',
            'settings.show_assessment_marks' => 'boolean',
            'settings.show_remarks' => 'boolean',
            'settings.show_grade_points' => 'boolean',
            'settings.header_color' => 'required|string|max:7',
            'settings.text_color' => 'required|string|max:7',
            'settings.border_style' => 'required|in:solid,dashed,dotted,none',
            'settings.font_family' => 'required|in:Arial,Times,Helvetica,Georgia',
            'settings.font_size' => 'required|integer|min:8|max:16',
            'custom_css' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset all other defaults
        if ($validated['is_default'] ?? false) {
            MarksheetTemplate::where('is_default', true)->where('id', '!=', $template->id)->update(['is_default' => false]);
        }

        $template->update($validated);

        return redirect()->route('admin.marksheets.customize.index')
                        ->with('success', 'Marksheet template updated successfully.');
    }

    /**
     * Remove the specified template.
     */
    public function destroy(MarksheetTemplate $template)
    {
        if ($template->is_default) {
            return back()->with('error', 'Cannot delete the default template.');
        }

        $template->delete();

        return redirect()->route('admin.marksheets.customize.index')
                        ->with('success', 'Marksheet template deleted successfully.');
    }

    /**
     * Preview the template with sample data.
     */
    public function preview(Request $request, MarksheetTemplate $template)
    {
        try {
            // Get sample data for preview
            $sampleStudent = $this->getSampleStudent();
            $sampleExam = $this->getSampleExam();
            $sampleMarks = $this->getSampleMarks();
            $instituteSettings = InstituteSettings::current() ?? (object) [
                'institution_name' => 'Academic Institution',
                'institution_address' => 'Institution Address',
                'institution_phone' => '+977-1-XXXXXXX',
                'institution_email' => 'info@institution.edu.np',
                'institution_website' => 'www.institution.edu.np',
                'principal_name' => 'Principal Name',
            ];

            $data = [
                'template' => $template,
                'student' => $sampleStudent,
                'exam' => $sampleExam,
                'marks' => $sampleMarks,
                'totalMarks' => $sampleMarks->sum('total_marks'),
                'maxMarks' => $sampleMarks->sum('max_marks'),
                'overallPercentage' => 85.5,
                'overallGrade' => 'A',
                'overallResult' => 'Pass',
                'overallRemarks' => 'Very good performance. Keep up the excellent work.',
                'instituteSettings' => $instituteSettings,
                'generatedAt' => now(),
                'bikramSambatDate' => $this->convertToBikramSambat(now()),
                'isPreview' => true,
            ];

            return view('admin.marksheets.customize.preview', $data);
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating preview: ' . $e->getMessage());
        }
    }

    /**
     * Show drag and drop template builder.
     */
    public function dragDropBuilder()
    {
        return view('admin.marksheets.customize.drag-drop-builder');
    }

    /**
     * Show advanced template editor.
     */
    public function advancedEditor()
    {
        return view('admin.marksheets.customize.advanced-editor');
    }

    /**
     * Live preview for template customization.
     */
    public function livePreview(Request $request)
    {
        $settings = $request->input('settings', []);
        $customCss = $request->input('custom_css', '');

        // Get sample data
        $sampleStudent = $this->getSampleStudent();
        $sampleExam = $this->getSampleExam();
        $sampleMarks = $this->getSampleMarks();
        $instituteSettings = InstituteSettings::current() ?? (object) [
            'institution_name' => 'Academic Institution',
            'institution_address' => 'Institution Address',
            'institution_phone' => '+977-1-XXXXXXX',
            'institution_email' => 'info@institution.edu.np',
            'institution_website' => 'www.institution.edu.np',
            'principal_name' => 'Principal Name',
        ];

        return view('admin.marksheets.customize.live-preview', compact(
            'settings', 'customCss', 'sampleStudent', 'sampleExam', 'sampleMarks', 'instituteSettings'
        ));
    }

    /**
     * Live preview for specific template editing.
     */
    public function templateLivePreview(Request $request, MarksheetTemplate $template)
    {
        // Update template with form data temporarily (don't save to database)
        $settings = $request->input('settings', []);
        $customCss = $request->input('custom_css', '');

        // Create a temporary template object with updated settings
        $tempTemplate = clone $template;
        $tempTemplate->settings = array_merge($template->settings ?? [], $settings);
        $tempTemplate->custom_css = $customCss;

        // Get sample data
        $sampleData = $this->getSampleMarksheetData();

        return view('admin.marksheets.customize.partials.live-preview-content', array_merge($sampleData, [
            'template' => $tempTemplate,
            'isLivePreview' => true
        ]));
    }

    /**
     * Get sample marksheet data for preview.
     */
    private function getSampleMarksheetData()
    {
        $sampleStudent = $this->getSampleStudent();
        $sampleExam = $this->getSampleExam();
        $sampleMarks = $this->getSampleMarks();
        $instituteSettings = InstituteSettings::current() ?? (object) [
            'institution_name' => 'Academic Institution',
            'institution_address' => 'Institution Address',
            'institution_phone' => '+977-1-XXXXXXX',
            'institution_email' => 'info@institution.edu.np',
            'institution_website' => 'www.institution.edu.np',
            'principal_name' => 'Principal Name',
        ];

        return [
            'student' => $sampleStudent,
            'exam' => $sampleExam,
            'marks' => $sampleMarks,
            'totalMarks' => $sampleMarks->sum('total_marks'),
            'maxMarks' => $sampleMarks->sum('max_marks'),
            'overallPercentage' => 85.5,
            'overallGrade' => 'A',
            'overallResult' => 'Pass',
            'overallRemarks' => 'Very good performance. Keep up the excellent work.',
            'instituteSettings' => $instituteSettings,
            'generatedAt' => now(),
            'bikramSambatDate' => $this->convertToBikramSambat(now()),
            'isPreview' => true,
        ];
    }

    /**
     * Show the table editor interface.
     */
    public function tableEditor()
    {
        return view('admin.marksheets.customize.table-editor');
    }

    /**
     * Show the column reorder interface.
     */
    public function columnReorder()
    {
        return view('admin.marksheets.customize.column-reorder');
    }

    /**
     * Show the smart demo interface.
     */
    public function smartDemo()
    {
        return view('admin.marksheets.customize.smart-demo');
    }

    /**
     * Set template as default.
     */
    public function setDefault(MarksheetTemplate $template)
    {
        // Unset all other defaults
        MarksheetTemplate::where('is_default', true)->update(['is_default' => false]);

        // Set this as default
        $template->update(['is_default' => true]);

        return back()->with('success', 'Template set as default successfully.');
    }

    /**
     * Duplicate a template.
     */
    public function duplicate(MarksheetTemplate $template)
    {
        $newTemplate = $template->replicate();
        $newTemplate->name = $template->name . ' (Copy)';
        $newTemplate->is_default = false;
        $newTemplate->save();

        return redirect()->route('admin.marksheets.customize.edit', $newTemplate)
                        ->with('success', 'Template duplicated successfully.');
    }

    /**
     * Get sample student data for preview.
     */
    private function getSampleStudent()
    {
        return (object) [
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'currentEnrollment' => (object) [
                'roll_no' => 'STU001',
                'class' => (object) [
                    'name' => 'Class 10',
                    'level' => (object) ['name' => 'Secondary']
                ],
                'program' => (object) [
                    'name' => 'Science Program'
                ]
            ]
        ];
    }

    /**
     * Get sample exam data for preview.
     */
    private function getSampleExam()
    {
        return (object) [
            'id' => 1,
            'name' => 'Final Examination 2024',
            'max_marks' => 100,
            'theory_max' => 60,
            'practical_max' => 25,
            'assess_max' => 15,
            'has_practical' => true,
            'has_assessment' => true,
            'pass_marks' => 40,
            'academicYear' => (object) ['name' => '2024-2025'],
            'class' => (object) ['name' => 'Class 10']
        ];
    }

    /**
     * Get sample marks data for preview.
     */
    private function getSampleMarks()
    {
        return collect([
            (object) [
                'subject' => (object) ['name' => 'Mathematics'],
                'theory_marks' => 75,
                'practical_marks' => 20,
                'assess_marks' => 10,
                'total_marks' => 85,
                'max_marks' => 100,
                'percentage' => 85,
                'grade' => 'A',
                'result' => 'Pass'
            ],
            (object) [
                'subject' => (object) ['name' => 'Science'],
                'theory_marks' => 70,
                'practical_marks' => 18,
                'assess_marks' => 8,
                'total_marks' => 86,
                'max_marks' => 100,
                'percentage' => 86,
                'grade' => 'A',
                'result' => 'Pass'
            ],
            (object) [
                'subject' => (object) ['name' => 'English'],
                'theory_marks' => 80,
                'practical_marks' => null,
                'assess_marks' => 5,
                'total_marks' => 85,
                'max_marks' => 100,
                'percentage' => 85,
                'grade' => 'A',
                'result' => 'Pass'
            ]
        ]);
    }

    /**
     * Convert Gregorian date to Bikram Sambat.
     */
    private function convertToBikramSambat($date)
    {
        $bsYear = $date->year + 57;
        return $bsYear . '-' . $date->format('m-d') . ' BS';
    }
}

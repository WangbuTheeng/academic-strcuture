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
            // Get current institute settings
            $currentInstitute = InstituteSettings::current();
            $instituteId = $currentInstitute ? $currentInstitute->id : null;

            // Get templates available for this institute (global + institute-specific)
            $templates = MarksheetTemplate::with('instituteSettings')
                ->availableForInstitute($instituteId)
                ->orderBy('is_global', 'desc') // Show global templates first
                ->orderBy('created_at', 'desc')
                ->get();

            $gradingScales = GradingScale::all();
            // Get real institute settings first
            $instituteSettings = InstituteSettings::current();

            // Only use fallback if no settings exist at all
            if (!$instituteSettings) {
                $instituteSettings = (object) [
                    'institution_name' => 'Academic Institution',
                    'institution_address' => 'Institution Address',
                    'institution_phone' => '+977-1-XXXXXXX',
                    'institution_email' => 'info@institution.edu.np',
                    'institution_website' => 'www.institution.edu.np',
                    'principal_name' => 'Principal Name',
                    'institution_logo' => null,
                    'institution_seal' => null,
                ];
            }

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
        // Check if this is a drag-drop builder request (JSON format)
        if ($request->isJson() || $request->header('Content-Type') === 'application/json') {
            return $this->storeDragDropTemplate($request);
        }

        // Handle regular form submission
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

        // Get current institute settings and set it for the template
        $currentInstitute = InstituteSettings::current();
        if ($currentInstitute) {
            $validated['institute_settings_id'] = $currentInstitute->id;
        }

        // Set as institute-specific template (not global)
        $validated['is_global'] = false;

        // If this is set as default, unset all other defaults for this institute
        if ($validated['is_default'] ?? false) {
            $query = MarksheetTemplate::where('is_default', true);
            if ($currentInstitute) {
                $query->where('institute_settings_id', $currentInstitute->id);
            }
            $query->update(['is_default' => false]);
        }

        $template = MarksheetTemplate::create($validated);

        return redirect()->route('admin.marksheets.customize.index')
                        ->with('success', 'Marksheet template created successfully.');
    }

    /**
     * Store template from drag-drop builder
     */
    private function storeDragDropTemplate(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'description' => 'nullable|string|max:500',
                'template_type' => 'required|string',
                'grading_scale_id' => 'nullable|integer',
                'orientation' => 'nullable|string|in:portrait,landscape',
                'fontFamily' => 'nullable|string',
                'fontSize' => 'nullable|string',
                'primaryColor' => 'nullable|string',
                'secondaryColor' => 'nullable|string',
                'sections' => 'nullable|array',
                'tableColumns' => 'nullable|array',
                'settings' => 'nullable|array',
            ]);

            // Get current institute settings
            $currentInstitute = InstituteSettings::current();

            // Transform drag-drop data to match database schema
            $templateData = [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? 'Created with Drag & Drop Builder',
                'template_type' => 'custom',
                'grading_scale_id' => $validated['grading_scale_id'] ?? 1,
                'institute_settings_id' => $currentInstitute?->id,
                'is_global' => false,
                'is_default' => false,
                'settings' => [
                    'show_school_logo' => $validated['settings']['show_school_logo'] ?? true,
                    'show_school_name' => $validated['settings']['show_school_name'] ?? true,
                    'show_school_address' => $validated['settings']['show_school_address'] ?? true,
                    'show_contact_info' => $validated['settings']['show_contact_info'] ?? true,
                    'show_principal_name' => $validated['settings']['show_principal_name'] ?? true,
                    'show_theory_practical' => $validated['settings']['show_theory_practical'] ?? true,
                    'show_assessment_marks' => $validated['settings']['show_assessment_marks'] ?? true,
                    'show_remarks' => $validated['settings']['show_remarks'] ?? true,
                    'show_grade_points' => $validated['settings']['show_grade_points'] ?? true,
                    'header_color' => $validated['primaryColor'] ?? '#000000',
                    'text_color' => $validated['secondaryColor'] ?? '#1f2937',
                    'border_style' => 'solid',
                    'font_family' => $validated['fontFamily'] ?? 'Arial',
                    'font_size' => (int)($validated['fontSize'] ?? 12),
                    'orientation' => $validated['orientation'] ?? 'portrait',
                    'sections' => $validated['sections'] ?? [],
                    'table_columns' => $validated['tableColumns'] ?? [],
                ],
            ];

            $template = MarksheetTemplate::create($templateData);

            return response()->json([
                'success' => true,
                'message' => 'Template saved successfully!',
                'template' => $template
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error saving drag-drop template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the specified template.
     */
    public function show(MarksheetTemplate $template)
    {
        // Check if user can access this template
        $this->authorizeTemplateAccess($template);

        return view('admin.marksheets.customize.show', compact('template'));
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(MarksheetTemplate $template)
    {
        // Check if user can access this template
        $this->authorizeTemplateAccess($template);

        $gradingScales = GradingScale::all();
        $instituteSettings = InstituteSettings::current();

        return view('admin.marksheets.customize.edit', compact('template', 'gradingScales', 'instituteSettings'));
    }

    /**
     * Update the specified template.
     */
    public function update(Request $request, MarksheetTemplate $template)
    {
        // Check if user can access this template
        $this->authorizeTemplateAccess($template);
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

        // If this is set as default, unset all other defaults for this institute
        if ($validated['is_default'] ?? false) {
            $query = MarksheetTemplate::where('is_default', true)->where('id', '!=', $template->id);
            if ($template->institute_settings_id) {
                $query->where('institute_settings_id', $template->institute_settings_id);
            }
            $query->update(['is_default' => false]);
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
        // Check if user can access this template
        $this->authorizeTemplateAccess($template);

        if ($template->is_default) {
            return back()->with('error', 'Cannot delete the default template.');
        }

        if ($template->is_global) {
            return back()->with('error', 'Cannot delete global templates.');
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
        // Check if user can access this template
        $this->authorizeTemplateAccess($template);

        try {
            // Get sample data for preview
            $sampleStudent = $this->getSampleStudent();
            $sampleExam = $this->getSampleExam();
            $sampleMarks = $this->getSampleMarks();
            // Get real institute settings first
            $instituteSettings = InstituteSettings::current();

            // Only use fallback if no settings exist at all
            if (!$instituteSettings) {
                $instituteSettings = (object) [
                    'institution_name' => 'Academic Institution',
                    'institution_address' => 'Institution Address',
                    'institution_phone' => '+977-1-XXXXXXX',
                    'institution_email' => 'info@institution.edu.np',
                    'institution_website' => 'www.institution.edu.np',
                    'principal_name' => 'Principal Name',
                    'institution_logo' => null,
                    'institution_seal' => null,
                ];
            }

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
        // Get current school context
        $schoolId = session('school_context');

        // Get current institute settings for this school
        $currentInstitute = InstituteSettings::current();

        // Get templates available for this institute (global + institute-specific)
        $templates = MarksheetTemplate::with('instituteSettings')
            ->availableForInstitute($schoolId)
            ->orderBy('is_global', 'desc') // Show global templates first
            ->orderBy('created_at', 'desc')
            ->get();

        // Get institute settings for school branding
        $instituteSettings = $currentInstitute;
        if (!$instituteSettings) {
            // Create default settings object with school-specific fallbacks
            $instituteSettings = (object) [
                'institution_name' => 'Academic Institution',
                'institution_address' => 'Institution Address',
                'institution_phone' => '01-1234567',
                'institution_email' => 'info@institution.edu.np',
                'institution_website' => 'www.institution.edu.np',
                'institution_logo' => null,
                'school_id' => $schoolId,
            ];
        }

        return view('admin.marksheets.customize.drag-drop-builder', compact('templates', 'instituteSettings'));
    }

    /**
     * Show advanced template editor.
     */
    public function advancedEditor()
    {
        return view('admin.marksheets.customize.advanced-editor');
    }

    /**
     * Get template data for drag-drop builder.
     */
    public function getTemplateData(MarksheetTemplate $template)
    {
        // Check if user can access this template
        $this->authorizeTemplateAccess($template);

        return response()->json([
            'success' => true,
            'template' => [
                'id' => $template->id,
                'name' => $template->name,
                'description' => $template->description,
                'template_type' => $template->template_type,
                'settings' => $template->settings,
                'is_global' => $template->is_global,
            ]
        ]);
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
        // Get real institute settings first
        $instituteSettings = InstituteSettings::current();

        // Only use fallback if no settings exist at all
        if (!$instituteSettings) {
            $instituteSettings = (object) [
                'institution_name' => 'Academic Institution',
                'institution_address' => 'Institution Address',
                'institution_phone' => '+977-1-XXXXXXX',
                'institution_email' => 'info@institution.edu.np',
                'institution_website' => 'www.institution.edu.np',
                'principal_name' => 'Principal Name',
                'institution_logo' => null,
                'institution_seal' => null,
            ];
        }

        return view('admin.marksheets.customize.live-preview', compact(
            'settings', 'customCss', 'sampleStudent', 'sampleExam', 'sampleMarks', 'instituteSettings'
        ));
    }

    /**
     * Live preview for specific template editing.
     */
    public function templateLivePreview(Request $request, MarksheetTemplate $template)
    {
        // Check if user can access this template
        $this->authorizeTemplateAccess($template);
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
        // Get real institute settings first
        $instituteSettings = InstituteSettings::current();

        // Only use fallback if no settings exist at all
        if (!$instituteSettings) {
            $instituteSettings = (object) [
                'institution_name' => 'Academic Institution',
                'institution_address' => 'Institution Address',
                'institution_phone' => '+977-1-XXXXXXX',
                'institution_email' => 'info@institution.edu.np',
                'institution_website' => 'www.institution.edu.np',
                'principal_name' => 'Principal Name',
                'institution_logo' => null,
                'institution_seal' => null,
            ];
        }

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
        // Check if user can access this template
        $this->authorizeTemplateAccess($template);
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
        // Check if user can access this template
        $this->authorizeTemplateAccess($template);
        $newTemplate = $template->replicate();
        $newTemplate->name = $template->name . ' (Copy)';
        $newTemplate->is_default = false;
        $newTemplate->is_global = false; // Duplicated templates are always institute-specific

        // Set to current institute
        $currentInstitute = InstituteSettings::current();
        if ($currentInstitute) {
            $newTemplate->institute_settings_id = $currentInstitute->id;
        }

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

    /**
     * Check if the current user can access the given template.
     */
    private function authorizeTemplateAccess(MarksheetTemplate $template)
    {
        $currentInstitute = InstituteSettings::current();
        $instituteId = $currentInstitute ? $currentInstitute->id : null;

        // Allow access if template is global
        if ($template->is_global) {
            return;
        }

        // Allow access if template belongs to current institute
        if ($template->institute_settings_id === $instituteId) {
            return;
        }

        // Deny access
        abort(403, 'You do not have permission to access this template.');
    }
}

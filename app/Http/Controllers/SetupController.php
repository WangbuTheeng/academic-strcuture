<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Level;
use App\Models\AcademicYear;
use App\Models\GradingScale;
use App\Models\GradeRange;
use App\Models\InstituteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class SetupController extends Controller
{
    /**
     * Check if setup is required.
     */
    public function index()
    {
        // Check if setup has already been completed
        if ($this->isSetupComplete()) {
            return redirect()->route('admin.dashboard')
                           ->with('info', 'System setup has already been completed.');
        }

        return view('setup.index');
    }

    /**
     * Show step 1: Institution Information
     */
    public function step1()
    {
        if ($this->isSetupComplete()) {
            return redirect()->route('admin.dashboard');
        }

        return view('setup.step1');
    }

    /**
     * Process step 1: Institution Information
     */
    public function processStep1(Request $request)
    {
        $validated = $request->validate([
            'institution_name' => 'required|string|max:255',
            'institution_address' => 'required|string|max:500',
            'institution_phone' => 'nullable|string|max:20',
            'institution_email' => 'nullable|email|max:255',
            'institution_website' => 'nullable|url|max:255',
            'institution_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'institution_seal' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'principal_name' => 'required|string|max:255',
            'principal_phone' => 'nullable|string|max:20',
            'principal_email' => 'nullable|email|max:255',
        ]);

        // Store institution information in session
        $request->session()->put('setup_step1', $validated);

        // Handle file uploads
        if ($request->hasFile('institution_logo')) {
            $logoPath = $request->file('institution_logo')->store('institution', 'public');
            $request->session()->put('setup_step1.institution_logo', $logoPath);
        }

        if ($request->hasFile('institution_seal')) {
            $sealPath = $request->file('institution_seal')->store('institution', 'public');
            $request->session()->put('setup_step1.institution_seal', $sealPath);
        }

        return redirect()->route('setup.step2');
    }

    /**
     * Show step 2: Academic Configuration
     */
    public function step2()
    {
        if ($this->isSetupComplete()) {
            return redirect()->route('admin.dashboard');
        }

        if (!session()->has('setup_step1')) {
            return redirect()->route('setup.step1')
                           ->with('error', 'Please complete step 1 first.');
        }

        $levels = Level::all();
        return view('setup.step2', compact('levels'));
    }

    /**
     * Process step 2: Academic Configuration
     */
    public function processStep2(Request $request)
    {
        $validated = $request->validate([
            'current_academic_year' => 'required|string|max:20',
            'academic_year_start_month' => 'required|integer|min:1|max:12',
            'academic_year_end_month' => 'required|integer|min:1|max:12',
            'default_grading_scale' => 'required|in:standard,high_school,university',
            'levels' => 'required|array|min:1',
            'levels.*' => 'exists:levels,id',
        ]);

        // Store academic configuration in session
        $request->session()->put('setup_step2', $validated);

        return redirect()->route('setup.step3');
    }

    /**
     * Show step 3: Admin Account Creation
     */
    public function step3()
    {
        if ($this->isSetupComplete()) {
            return redirect()->route('admin.dashboard');
        }

        if (!session()->has('setup_step1') || !session()->has('setup_step2')) {
            return redirect()->route('setup.step1')
                           ->with('error', 'Please complete previous steps first.');
        }

        return view('setup.step3');
    }

    /**
     * Process step 3: Admin Account Creation
     */
    public function processStep3(Request $request)
    {
        $validated = $request->validate([
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'admin_phone' => 'nullable|string|max:20',
        ]);

        // Store admin account information in session
        $request->session()->put('setup_step3', $validated);

        return redirect()->route('setup.step4');
    }

    /**
     * Show step 4: Review and Confirm
     */
    public function step4()
    {
        if ($this->isSetupComplete()) {
            return redirect()->route('admin.dashboard');
        }

        if (!session()->has('setup_step1') ||
            !session()->has('setup_step2') ||
            !session()->has('setup_step3')) {
            return redirect()->route('setup.step1')
                           ->with('error', 'Please complete all previous steps first.');
        }

        $step1Data = session('setup_step1');
        $step2Data = session('setup_step2');
        $step3Data = session('setup_step3');

        return view('setup.step4', compact('step1Data', 'step2Data', 'step3Data'));
    }

    /**
     * Complete the setup process
     */
    public function complete(Request $request)
    {
        if ($this->isSetupComplete()) {
            return redirect()->route('admin.dashboard');
        }

        if (!session()->has('setup_step1') ||
            !session()->has('setup_step2') ||
            !session()->has('setup_step3')) {
            return redirect()->route('setup.step1')
                           ->with('error', 'Setup data is incomplete. Please start over.');
        }

        try {
            DB::transaction(function () use ($request) {
                // Step 1: Create Institute Settings
                $step1Data = session('setup_step1');
                $instituteSettings = InstituteSettings::create([
                    'institution_name' => $step1Data['institution_name'],
                    'institution_address' => $step1Data['institution_address'],
                    'institution_phone' => $step1Data['institution_phone'],
                    'institution_email' => $step1Data['institution_email'],
                    'institution_website' => $step1Data['institution_website'],
                    'institution_logo' => $step1Data['institution_logo'] ?? null,
                    'institution_seal' => $step1Data['institution_seal'] ?? null,
                    'principal_name' => $step1Data['principal_name'],
                    'principal_phone' => $step1Data['principal_phone'],
                    'principal_email' => $step1Data['principal_email'],
                    'setup_completed' => true,
                    'setup_completed_at' => now(),
                ]);

                // Step 2: Create Academic Year
                $step2Data = session('setup_step2');
                $academicYear = AcademicYear::create([
                    'name' => $step2Data['current_academic_year'],
                    'start_date' => now()->month((int)$step2Data['academic_year_start_month'])->startOfMonth(),
                    'end_date' => now()->month((int)$step2Data['academic_year_end_month'])->endOfMonth(),
                    'is_current' => true,
                    'is_active' => true,
                ]);

                // Step 3: Create Default Grading Scale
                $this->createDefaultGradingScale($step2Data['default_grading_scale']);

                // Step 4: Create Admin User
                $step3Data = session('setup_step3');
                $adminUser = User::create([
                    'name' => $step3Data['admin_name'],
                    'email' => $step3Data['admin_email'],
                    'password' => Hash::make($step3Data['admin_password']),
                    'phone' => $step3Data['admin_phone'],
                    'email_verified_at' => now(),
                ]);

                // Assign admin role
                $adminRole = Role::firstOrCreate(['name' => 'admin']);
                $adminUser->assignRole($adminRole);

                // Log setup completion
                activity()
                    ->causedBy($adminUser)
                    ->withProperties([
                        'institution_name' => $step1Data['institution_name'],
                        'academic_year' => $step2Data['current_academic_year'],
                        'grading_scale' => $step2Data['default_grading_scale'],
                    ])
                    ->log('System setup completed');
            });

            // Clear setup session data
            $request->session()->forget(['setup_step1', 'setup_step2', 'setup_step3']);

            return redirect()->route('setup.success');

        } catch (\Exception $e) {
            return back()->with('error', 'Setup failed: ' . $e->getMessage());
        }
    }

    /**
     * Show setup success page
     */
    public function success()
    {
        if (!$this->isSetupComplete()) {
            return redirect()->route('setup.index');
        }

        return view('setup.success');
    }

    /**
     * Redirect GET requests for /setup/complete to step4
     */
    public function redirectToStep4()
    {
        return redirect()->route('setup.step4')
                       ->with('info', 'Please review your setup information and submit the form to complete setup.');
    }

    /**
     * Check if setup is complete
     */
    private function isSetupComplete(): bool
    {
        return InstituteSettings::where('setup_completed', true)->exists();
    }

    /**
     * Create default grading scale based on selection
     */
    private function createDefaultGradingScale(string $type): void
    {
        $gradingScales = [
            'standard' => [
                'name' => 'Standard Grading Scale',
                'description' => 'Default grading scale for all levels',
                'pass_mark' => 40.00,
                'grades' => [
                    ['grade' => 'A+', 'min' => 90, 'max' => 100, 'gpa' => 4.0, 'description' => 'Outstanding'],
                    ['grade' => 'A', 'min' => 80, 'max' => 89, 'gpa' => 3.6, 'description' => 'Excellent'],
                    ['grade' => 'B+', 'min' => 70, 'max' => 79, 'gpa' => 3.2, 'description' => 'Very Good'],
                    ['grade' => 'B', 'min' => 60, 'max' => 69, 'gpa' => 2.8, 'description' => 'Good'],
                    ['grade' => 'C+', 'min' => 50, 'max' => 59, 'gpa' => 2.4, 'description' => 'Satisfactory'],
                    ['grade' => 'C', 'min' => 40, 'max' => 49, 'gpa' => 2.0, 'description' => 'Acceptable'],
                    ['grade' => 'D', 'min' => 30, 'max' => 39, 'gpa' => 1.6, 'description' => 'Poor'],
                    ['grade' => 'F', 'min' => 0, 'max' => 29, 'gpa' => 0.0, 'description' => 'Fail'],
                ]
            ],
            'high_school' => [
                'name' => 'High School Grading Scale',
                'description' => 'Grading scale for high school level',
                'pass_mark' => 35.00,
                'grades' => [
                    ['grade' => 'A+', 'min' => 90, 'max' => 100, 'gpa' => 4.0, 'description' => 'Distinction'],
                    ['grade' => 'A', 'min' => 80, 'max' => 89, 'gpa' => 3.6, 'description' => 'First Division'],
                    ['grade' => 'B+', 'min' => 70, 'max' => 79, 'gpa' => 3.2, 'description' => 'Second Division'],
                    ['grade' => 'B', 'min' => 60, 'max' => 69, 'gpa' => 2.8, 'description' => 'Second Division'],
                    ['grade' => 'C+', 'min' => 50, 'max' => 59, 'gpa' => 2.4, 'description' => 'Third Division'],
                    ['grade' => 'C', 'min' => 35, 'max' => 49, 'gpa' => 2.0, 'description' => 'Third Division'],
                    ['grade' => 'D', 'min' => 25, 'max' => 34, 'gpa' => 1.0, 'description' => 'Compartment'],
                    ['grade' => 'F', 'min' => 0, 'max' => 24, 'gpa' => 0.0, 'description' => 'Fail'],
                ]
            ],
            'university' => [
                'name' => 'University Grading Scale',
                'description' => 'Grading scale for university level',
                'pass_mark' => 45.00,
                'grades' => [
                    ['grade' => 'A+', 'min' => 85, 'max' => 100, 'gpa' => 4.0, 'description' => 'Excellent'],
                    ['grade' => 'A', 'min' => 75, 'max' => 84, 'gpa' => 3.7, 'description' => 'Very Good'],
                    ['grade' => 'B+', 'min' => 65, 'max' => 74, 'gpa' => 3.3, 'description' => 'Good'],
                    ['grade' => 'B', 'min' => 55, 'max' => 64, 'gpa' => 3.0, 'description' => 'Above Average'],
                    ['grade' => 'C+', 'min' => 50, 'max' => 54, 'gpa' => 2.7, 'description' => 'Average'],
                    ['grade' => 'C', 'min' => 45, 'max' => 49, 'gpa' => 2.0, 'description' => 'Below Average'],
                    ['grade' => 'D', 'min' => 35, 'max' => 44, 'gpa' => 1.0, 'description' => 'Poor'],
                    ['grade' => 'F', 'min' => 0, 'max' => 34, 'gpa' => 0.0, 'description' => 'Fail'],
                ]
            ]
        ];

        $scaleData = $gradingScales[$type];

        $gradingScale = GradingScale::create([
            'name' => $scaleData['name'],
            'description' => $scaleData['description'],
            'level_id' => null,
            'program_id' => null,
            'pass_mark' => $scaleData['pass_mark'],
            'max_marks' => 100.00,
            'is_default' => true,
            'is_active' => true,
            'created_by' => 1, // Will be updated after admin user creation
        ]);

        foreach ($scaleData['grades'] as $gradeData) {
            GradeRange::create([
                'grading_scale_id' => $gradingScale->id,
                'grade' => $gradeData['grade'],
                'min_percentage' => $gradeData['min'],
                'max_percentage' => $gradeData['max'],
                'gpa' => $gradeData['gpa'],
                'description' => $gradeData['description'],
                'is_passing' => $gradeData['min'] >= $scaleData['pass_mark'],
            ]);
        }
    }
}

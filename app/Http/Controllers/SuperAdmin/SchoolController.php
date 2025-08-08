<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Services\SchoolSetupService;
use App\Services\SchoolCreationService;
use App\Services\AuditLogger;
use App\Http\Requests\StoreSchoolRequest;
use App\Http\Requests\UpdateSchoolRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SchoolController extends Controller
{
    protected $schoolSetupService;
    protected $schoolCreationService;
    protected $auditLogger;

    public function __construct(
        SchoolSetupService $schoolSetupService,
        SchoolCreationService $schoolCreationService,
        AuditLogger $auditLogger
    ) {
        $this->middleware(['auth', 'role:super-admin']);
        $this->schoolSetupService = $schoolSetupService;
        $this->schoolCreationService = $schoolCreationService;
        $this->auditLogger = $auditLogger;
    }

    /**
     * Display a listing of schools.
     */
    public function index(Request $request)
    {
        $this->auditLogger->logActivity('schools_list_accessed', [
            'category' => 'super_admin',
            'severity' => 'info'
        ]);

        $query = School::with(['creator', 'statistics'])->withCount(['users', 'students']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $schools = $query->latest()->paginate(15);

        return view('super-admin.schools.index', compact('schools'));
    }

    /**
     * Show the form for creating a new school.
     */
    public function create()
    {
        return view('super-admin.schools.create');
    }

    /**
     * Store a newly created school.
     */
    public function store(StoreSchoolRequest $request)
    {
        try {
            $result = $this->schoolCreationService->createSchool($request->validated());

            $this->auditLogger->logActivity('school_created_via_interface', [
                'resource_type' => 'school',
                'resource_id' => $result['school']->id,
                'new_values' => [
                    'name' => $result['school']->name,
                    'code' => $result['school']->code
                ],
                'category' => 'super_admin',
                'severity' => 'info'
            ]);

            return redirect()->route('super-admin.schools.show', $result['school'])
                           ->with('success', 'School created successfully!')
                           ->with('credentials', $result['credentials']);
        } catch (\Exception $e) {
            $this->auditLogger->logActivity('school_creation_failed', [
                'new_values' => ['error' => $e->getMessage()],
                'category' => 'super_admin',
                'severity' => 'error'
            ]);

            return back()->withErrors(['error' => 'Failed to create school: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Display the specified school.
     */
    public function show(School $school)
    {
        $school->load(['users', 'students', 'creator']);
        $stats = $this->schoolSetupService->getSchoolStats($school);

        return view('super-admin.schools.show', compact('school', 'stats'));
    }

    /**
     * Show the form for editing the specified school.
     */
    public function edit(School $school)
    {
        // Get current levels for this school
        $currentLevels = $school->levels()->pluck('name')->map(function($name) {
            return strtolower($name);
        })->toArray();

        return view('super-admin.schools.edit', compact('school', 'currentLevels'));
    }

    /**
     * Update the specified school.
     */
    public function update(UpdateSchoolRequest $request, School $school)
    {
        try {
            // Update school basic information
            $this->schoolSetupService->updateSchool($school, $request->validated());

            // Update educational levels if they have changed
            $currentLevels = $school->levels()->pluck('name')->map(function($name) {
                return strtolower($name);
            })->sort()->values()->toArray();

            $newLevels = collect($request->levels)->sort()->values()->toArray();

            if ($currentLevels !== $newLevels) {
                $this->updateSchoolLevels($school, $request->levels);
            }

            // Store new password in session if it was updated
            $successMessage = 'School updated successfully!';
            if ($request->filled('password')) {
                session(['new_password' => $request->password]);
                $successMessage .= ' Password has been updated.';

                $this->auditLogger->logActivity('school_password_updated', [
                    'resource_type' => 'school',
                    'resource_id' => $school->id,
                    'category' => 'super_admin',
                    'severity' => 'warning'
                ]);
            }

            return redirect()->route('super-admin.schools.show', $school)
                           ->with('success', $successMessage);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update school: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Remove the specified school from storage.
     */
    public function destroy(School $school)
    {
        try {
            // Soft delete by deactivating
            $this->schoolSetupService->deactivateSchool($school);

            return redirect()->route('super-admin.schools.index')
                           ->with('success', 'School deactivated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to deactivate school: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate a unique school code
     */
    public function generateCode(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $code = $this->schoolCreationService->generateSchoolCode($request->name);

        return response()->json([
            'success' => $code !== null,
            'code' => $code,
            'message' => $code ? 'Code generated successfully' : 'Unable to generate unique code'
        ]);
    }

    /**
     * Update school educational levels
     */
    private function updateSchoolLevels(School $school, array $selectedLevels)
    {
        // Delete existing levels for this school
        $school->levels()->delete();

        // Create new levels based on selection
        $levelMapping = [
            'school' => ['name' => 'School', 'order' => 1],
            'college' => ['name' => 'College', 'order' => 2],
            'bachelor' => ['name' => 'Bachelor', 'order' => 3],
        ];

        foreach ($selectedLevels as $levelKey) {
            if (isset($levelMapping[$levelKey])) {
                $levelData = $levelMapping[$levelKey];

                \App\Models\Level::create([
                    'name' => $levelData['name'],
                    'order' => $levelData['order'],
                    'school_id' => $school->id
                ]);
            }
        }

        // Log the level update
        $this->auditLogger->logActivity('school_levels_updated', [
            'resource_type' => 'school',
            'resource_id' => $school->id,
            'new_values' => ['levels' => $selectedLevels],
            'category' => 'super_admin',
            'severity' => 'info'
        ]);
    }

    /**
     * Update school status
     */
    public function updateStatus(Request $request, School $school)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,suspended'
        ]);

        $oldStatus = $school->status;
        $school->update(['status' => $request->status]);

        $this->auditLogger->logActivity('school_status_updated', [
            'resource_type' => 'school',
            'resource_id' => $school->id,
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => $request->status],
            'category' => 'super_admin',
            'severity' => 'info'
        ]);

        return redirect()->back()->with('success', "School status updated to {$request->status}.");
    }

    /**
     * Reset school password
     */
    public function resetPassword(School $school)
    {
        $newPassword = \Illuminate\Support\Str::random(12) . rand(100, 999) . '!';
        // Don't use Hash::make here because the model's mutator will handle it
        $school->update(['password' => $newPassword]);

        $this->auditLogger->logActivity('school_password_reset', [
            'resource_type' => 'school',
            'resource_id' => $school->id,
            'category' => 'super_admin',
            'severity' => 'warning'
        ]);

        return redirect()->back()
            ->with('success', 'School password has been reset.')
            ->with('new_password', $newPassword)
            ->with('school_code', $school->code);
    }
}

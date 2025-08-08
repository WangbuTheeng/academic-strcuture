<?php

namespace App\Services;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SchoolContextService
{
    /**
     * Get the current school context
     */
    public function getCurrentSchool()
    {
        // For super-admin, return null (no school context)
        if ($this->isSuperAdmin()) {
            return null;
        }

        // Get school from session first
        $schoolId = Session::get('school_context');
        
        if ($schoolId) {
            return School::find($schoolId);
        }

        // Fallback to user's school
        if (Auth::check() && Auth::user()->school_id) {
            $school = School::find(Auth::user()->school_id);
            if ($school) {
                $this->setSchoolContext($school);
                return $school;
            }
        }

        return null;
    }

    /**
     * Get the current school ID
     */
    public function getCurrentSchoolId()
    {
        $school = $this->getCurrentSchool();
        return $school ? $school->id : null;
    }

    /**
     * Set the school context
     */
    public function setSchoolContext(School $school)
    {
        Session::put([
            'school_context' => $school->id,
            'school_code' => $school->code,
            'school_name' => $school->name
        ]);
    }

    /**
     * Clear the school context
     */
    public function clearSchoolContext()
    {
        Session::forget(['school_context', 'school_code', 'school_name']);
    }

    /**
     * Check if current user is super-admin
     */
    public function isSuperAdmin()
    {
        return Auth::check() && Auth::user()->hasRole('super-admin');
    }

    /**
     * Check if current user belongs to a specific school
     */
    public function belongsToSchool($schoolId)
    {
        if ($this->isSuperAdmin()) {
            return true; // Super-admin has access to all schools
        }

        return Auth::check() && Auth::user()->school_id === $schoolId;
    }

    /**
     * Validate school context for current user
     */
    public function validateSchoolContext()
    {
        if (!Auth::check()) {
            return false;
        }

        // Super-admin doesn't need school context validation
        if ($this->isSuperAdmin()) {
            return true;
        }

        $user = Auth::user();
        $sessionSchoolId = Session::get('school_context');

        // User must have a school
        if (!$user->school_id) {
            return false;
        }

        // Session school context must match user's school
        if (!$sessionSchoolId || $sessionSchoolId !== $user->school_id) {
            return false;
        }

        // Verify school exists and is active
        $school = School::find($user->school_id);
        if (!$school || !$school->isActive()) {
            return false;
        }

        return true;
    }

    /**
     * Get school context information for display
     */
    public function getSchoolContextInfo()
    {
        if ($this->isSuperAdmin()) {
            return [
                'is_super_admin' => true,
                'school_name' => 'Super Administrator',
                'school_code' => 'SUPER',
                'school_id' => null
            ];
        }

        $school = $this->getCurrentSchool();
        
        if (!$school) {
            return [
                'is_super_admin' => false,
                'school_name' => 'No School Context',
                'school_code' => 'NONE',
                'school_id' => null
            ];
        }

        return [
            'is_super_admin' => false,
            'school_name' => $school->name,
            'school_code' => $school->code,
            'school_id' => $school->id
        ];
    }

    /**
     * Switch school context (for super-admin only)
     */
    public function switchSchoolContext($schoolId)
    {
        if (!$this->isSuperAdmin()) {
            throw new \Exception('Only super-admin can switch school context');
        }

        $school = School::find($schoolId);
        if (!$school) {
            throw new \Exception('School not found');
        }

        $this->setSchoolContext($school);
        return $school;
    }

    /**
     * Get all accessible schools for current user
     */
    public function getAccessibleSchools()
    {
        if ($this->isSuperAdmin()) {
            return School::all();
        }

        if (Auth::check() && Auth::user()->school_id) {
            return School::where('id', Auth::user()->school_id)->get();
        }

        return collect();
    }

    /**
     * Check if user can access a specific school
     */
    public function canAccessSchool($schoolId)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return Auth::check() && Auth::user()->school_id === $schoolId;
    }

    /**
     * Get school statistics for current context
     */
    public function getSchoolStats()
    {
        $school = $this->getCurrentSchool();
        
        if (!$school) {
            return [];
        }

        return [
            'total_users' => $school->users()->count(),
            'total_students' => $school->students()->count(),
            'active_users' => $school->users()->whereNotNull('email_verified_at')->count(),
            'admin_users' => $school->users()->role('admin')->count(),
            'teacher_users' => $school->users()->role('teacher')->count(),
            'student_users' => $school->users()->role('student')->count(),
        ];
    }

    /**
     * Ensure user has proper school context
     */
    public function ensureSchoolContext()
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (!$this->validateSchoolContext()) {
            $this->clearSchoolContext();
            Auth::logout();
            return false;
        }

        return true;
    }

    /**
     * Get school branding information
     */
    public function getSchoolBranding()
    {
        $school = $this->getCurrentSchool();
        
        if (!$school) {
            return [
                'logo' => null,
                'name' => 'Academic Management System',
                'colors' => [
                    'primary' => '#3B82F6',
                    'secondary' => '#6B7280'
                ]
            ];
        }

        return [
            'logo' => $school->logo_path,
            'name' => $school->name,
            'colors' => $school->settings['branding']['colors'] ?? [
                'primary' => '#3B82F6',
                'secondary' => '#6B7280'
            ]
        ];
    }
}

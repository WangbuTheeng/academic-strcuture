<?php

namespace App\Services;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SchoolAuthService
{
    /**
     * Authenticate a school with school ID and password
     */
    public function authenticateSchool($schoolId, $password)
    {
        // Find school by code (school ID)
        $school = School::findByCode($schoolId);

        if (!$school || !$school->isActive()) {
            return [
                'success' => false,
                'message' => 'Invalid school ID or school is inactive'
            ];
        }

        // Check school password
        if (!Hash::check($password, $school->password)) {
            return [
                'success' => false,
                'message' => 'Invalid school credentials'
            ];
        }

        // Find the school's admin user (or create a session-based auth)
        $adminUser = User::where('school_id', $school->id)
                        ->whereHas('roles', function($query) {
                            $query->where('name', 'admin');
                        })
                        ->first();

        if (!$adminUser) {
            return [
                'success' => false,
                'message' => 'No admin user found for this school'
            ];
        }

        // Set school context in session
        session([
            'school_context' => $school->id,
            'school_code' => $school->code,
            'school_name' => $school->name,
            'school_authenticated' => true
        ]);

        // Login the admin user
        Auth::login($adminUser);

        return [
            'success' => true,
            'user' => $adminUser,
            'school' => $school,
            'redirect' => $this->getRedirectRoute($adminUser)
        ];
    }

    /**
     * Authenticate super-admin user
     */
    public function authenticateSuperAdmin($email, $password)
    {
        $user = User::where('email', $email)
                   ->whereNull('school_id')
                   ->first();

        if (!$user || !Hash::check($password, $user->password) || !$user->hasRole('super-admin')) {
            return [
                'success' => false, 
                'message' => 'Invalid super-admin credentials'
            ];
        }

        Auth::login($user);
        session(['is_super_admin' => true]);

        return [
            'success' => true, 
            'user' => $user,
            'redirect' => route('super-admin.dashboard')
        ];
    }

    /**
     * Authenticate individual user (teacher, principal, student) with email and password
     */
    public function authenticateUser($email, $password)
    {
        // Find user by email (excluding super-admin users)
        $user = User::where('email', $email)
                   ->whereNotNull('school_id')
                   ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }

        // Check if user's school is active
        if (!$user->school || !$user->school->isActive()) {
            return [
                'success' => false,
                'message' => 'Your school account is inactive. Please contact administration.'
            ];
        }

        // Set school context in session
        session([
            'school_context' => $user->school_id,
            'school_code' => $user->school->code,
            'school_name' => $user->school->name,
            'school_authenticated' => true
        ]);

        // Login the user
        Auth::login($user);

        return [
            'success' => true,
            'user' => $user,
            'school' => $user->school,
            'redirect' => $this->getRedirectRoute($user)
        ];
    }

    /**
     * Get redirect route based on user role
     */
    private function getRedirectRoute($user)
    {
        if ($user->hasRole('admin')) {
            return route('admin.dashboard');
        } elseif ($user->hasRole('principal')) {
            return route('principal.dashboard');
        } elseif ($user->hasRole('teacher')) {
            return route('teacher.dashboard');
        } elseif ($user->hasRole('student')) {
            return route('student.dashboard');
        }

        return route('dashboard');
    }

    /**
     * Logout and clear school context
     */
    public function logout()
    {
        session()->forget(['school_context', 'school_code', 'school_name', 'is_super_admin']);
        Auth::logout();
    }

    /**
     * Get current school context
     */
    public function getCurrentSchool()
    {
        $schoolId = session('school_context');
        return $schoolId ? School::find($schoolId) : null;
    }

    /**
     * Check if current user is super-admin
     */
    public function isSuperAdmin()
    {
        return auth()->check() && auth()->user()->hasRole('super-admin');
    }

    /**
     * Validate school context for current user
     */
    public function validateSchoolContext()
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        // Super-admin bypass
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Check if user has school context
        if (!$user->school_id || !session('school_context')) {
            return false;
        }

        // Verify school context matches user's school
        return session('school_context') === $user->school_id;
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SchoolAuthService;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolLoginController extends Controller
{
    protected $schoolAuthService;
    protected $auditLogger;

    public function __construct(SchoolAuthService $schoolAuthService, AuditLogger $auditLogger)
    {
        $this->middleware('guest')->except('logout');
        $this->schoolAuthService = $schoolAuthService;
        $this->auditLogger = $auditLogger;
    }

    /**
     * Show the school login form
     */
    public function showLoginForm()
    {
        return view('auth.school-login');
    }

    /**
     * Handle login request (supports both school and individual user login)
     */
    public function login(Request $request)
    {
        // Determine login type based on input format
        $loginField = $request->input('login_field');
        $password = $request->input('password');

        // If login_field contains @ symbol, treat as email login
        if (strpos($loginField, '@') !== false) {
            return $this->handleUserLogin($request, $loginField, $password);
        } else {
            return $this->handleSchoolLogin($request, $loginField, $password);
        }
    }

    /**
     * Handle individual user login (teacher, principal, student)
     */
    private function handleUserLogin(Request $request, $email, $password)
    {
        $request->validate([
            'login_field' => 'required|email|max:255',
            'password' => 'required|string',
        ]);

        // Log user login attempt
        $this->auditLogger->logAuthEvent('user_login_attempt', [
            'new_values' => [
                'email' => $email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]
        ]);

        $result = $this->schoolAuthService->authenticateUser($email, $password);

        if ($result['success']) {
            // Log successful login
            $this->auditLogger->logAuthEvent('user_login_success', [
                'new_values' => [
                    'email' => $email,
                    'user_id' => $result['user']->id,
                    'school_name' => $result['school']->name ?? null,
                    'user_role' => $result['user']->roles->pluck('name')->implode(', '),
                    'redirect_to' => $result['redirect']
                ]
            ]);

            $request->session()->regenerate();
            return redirect()->intended($result['redirect']);
        }

        // Log failed login
        $this->auditLogger->logAuthEvent('user_login_failed', [
            'new_values' => [
                'email' => $email,
                'reason' => $result['message']
            ],
            'severity' => 'warning'
        ]);

        return back()->withErrors([
            'login' => $result['message']
        ])->withInput($request->except('password'));
    }

    /**
     * Handle school-based login (admin access)
     */
    private function handleSchoolLogin(Request $request, $schoolId, $password)
    {
        $request->validate([
            'login_field' => 'required|string|max:50',
            'password' => 'required|string',
        ]);

        // Log login attempt
        $this->auditLogger->logAuthEvent('school_login_attempt', [
            'new_values' => [
                'school_id' => $schoolId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]
        ]);

        $result = $this->schoolAuthService->authenticateSchool($schoolId, $password);

        if ($result['success']) {
            // Log successful login
            $this->auditLogger->logAuthEvent('school_login_success', [
                'new_values' => [
                    'school_id' => $schoolId,
                    'school_name' => $result['school']->name ?? null,
                    'redirect_to' => $result['redirect']
                ]
            ]);

            $request->session()->regenerate();
            return redirect()->intended($result['redirect']);
        }

        // Log failed login
        $this->auditLogger->logAuthEvent('school_login_failed', [
            'new_values' => [
                'school_id' => $schoolId,
                'reason' => $result['message']
            ],
            'severity' => 'warning'
        ]);

        return back()->withErrors([
            'login' => $result['message']
        ])->withInput($request->except('password'));
    }

    /**
     * Show super-admin login form
     */
    public function showSuperAdminLogin()
    {
        return view('auth.super-admin-login');
    }

    /**
     * Handle super-admin login request
     */
    public function superAdminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string',
        ]);

        // Log super admin login attempt
        $this->auditLogger->logAuthEvent('super_admin_login_attempt', [
            'new_values' => [
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]
        ]);

        $result = $this->schoolAuthService->authenticateSuperAdmin(
            $request->email,
            $request->password
        );

        if ($result['success']) {
            // Log successful super admin login
            $this->auditLogger->logAuthEvent('super_admin_login_success', [
                'new_values' => [
                    'email' => $request->email,
                    'user_id' => $result['user']->id ?? null,
                    'redirect_to' => $result['redirect']
                ],
                'severity' => 'info'
            ]);

            $request->session()->regenerate();
            return redirect()->intended($result['redirect']);
        }

        // Log failed super admin login
        $this->auditLogger->logAuthEvent('super_admin_login_failed', [
            'new_values' => [
                'email' => $request->email,
                'reason' => $result['message']
            ],
            'severity' => 'warning'
        ]);

        return back()->withErrors([
            'login' => $result['message']
        ])->withInput($request->except('password'));
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        $user = auth()->user();
        $userType = $user ? ($user->hasRole('super-admin') ? 'super-admin' : 'school-user') : 'unknown';

        // Log logout event
        $this->auditLogger->logAuthEvent('user_logout', [
            'new_values' => [
                'user_type' => $userType,
                'user_id' => $user?->id,
                'school_id' => session('school_context')
            ]
        ]);

        $this->schoolAuthService->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}

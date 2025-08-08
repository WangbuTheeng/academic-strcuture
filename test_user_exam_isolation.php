<?php

require_once 'vendor/autoload.php';

use App\Models\School;
use App\Models\User;
use App\Models\Exam;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Testing User and Exam Data Isolation\n";
echo "======================================\n\n";

// Get all schools
$schools = School::all();
echo "📋 Available Schools:\n";
foreach ($schools as $school) {
    echo "  - {$school->name} ({$school->code}) - ID: {$school->id}\n";
}
echo "\n";

// Test User isolation
echo "🧪 Test 1: User Model School Scoping\n";
echo "-----------------------------------\n";

foreach ($schools->take(3) as $school) {
    // Set school context
    session(['school_context' => $school->id]);
    
    // Find a user from this school to authenticate as
    $testUser = User::withoutGlobalScope('user_school_scope')
                   ->where('school_id', $school->id)
                   ->first();
    
    if ($testUser) {
        Auth::login($testUser);
        
        // Query users with the global scope active
        $scopedUsers = User::all();
        
        // Query users without the global scope to see all users
        $allUsers = User::withoutGlobalScope('user_school_scope')->get();
        
        echo "🏫 {$school->name} (School ID: {$school->id}):\n";
        echo "   Users visible with scope: {$scopedUsers->count()}\n";
        echo "   Total users in system: {$allUsers->count()}\n";
        
        // Check if scoped users all belong to this school
        $wrongSchoolUsers = $scopedUsers->where('school_id', '!=', $school->id);
        if ($wrongSchoolUsers->count() > 0) {
            echo "   ❌ ERROR: Found {$wrongSchoolUsers->count()} users from other schools!\n";
        } else {
            echo "   ✅ SUCCESS: All visible users belong to school {$school->id}\n";
        }
        
        Auth::logout();
    } else {
        echo "🏫 {$school->name}: No users found for testing\n";
    }
    echo "\n";
}

// Test Exam isolation
echo "🧪 Test 2: Exam Model School Scoping\n";
echo "-----------------------------------\n";

foreach ($schools->take(3) as $school) {
    // Set school context
    session(['school_context' => $school->id]);
    
    // Find a user from this school to authenticate as
    $testUser = User::withoutGlobalScope('user_school_scope')
                   ->where('school_id', $school->id)
                   ->first();
    
    if ($testUser) {
        Auth::login($testUser);
        
        // Query exams with the global scope active
        $scopedExams = Exam::all();
        
        // Query exams without the global scope to see all exams
        $allExams = Exam::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->get();
        
        echo "🏫 {$school->name} (School ID: {$school->id}):\n";
        echo "   Exams visible with scope: {$scopedExams->count()}\n";
        echo "   Total exams in system: {$allExams->count()}\n";
        
        // Check if scoped exams all belong to this school
        $wrongSchoolExams = $scopedExams->where('school_id', '!=', $school->id);
        if ($wrongSchoolExams->count() > 0) {
            echo "   ❌ ERROR: Found {$wrongSchoolExams->count()} exams from other schools!\n";
        } else {
            echo "   ✅ SUCCESS: All visible exams belong to school {$school->id}\n";
        }
        
        Auth::logout();
    } else {
        echo "🏫 {$school->name}: No users found for testing\n";
    }
    echo "\n";
}

// Test Super Admin access
echo "🧪 Test 3: Super Admin Access (Should see all data)\n";
echo "--------------------------------------------------\n";

// Find a super admin user
$superAdmin = User::withoutGlobalScope('user_school_scope')
                 ->whereNull('school_id')
                 ->whereHas('roles', function($query) {
                     $query->where('name', 'super-admin');
                 })
                 ->first();

if ($superAdmin) {
    Auth::login($superAdmin);
    session()->forget('school_context'); // Clear school context
    
    // Super admin should see all data
    $allUsers = User::withoutGlobalScope('user_school_scope')->get();
    $allExams = Exam::withoutGlobalScope(\App\Models\Scopes\SchoolScope::class)->get();
    
    echo "👑 Super Admin Access:\n";
    echo "   Total users (all schools): {$allUsers->count()}\n";
    echo "   Total exams (all schools): {$allExams->count()}\n";
    echo "   ✅ Super admin can access all data\n";
    
    Auth::logout();
} else {
    echo "👑 No super admin user found for testing\n";
}

echo "\n🎯 User and Exam Isolation Test Complete!\n";

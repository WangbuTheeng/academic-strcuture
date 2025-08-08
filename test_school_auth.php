<?php

require_once 'vendor/autoload.php';

use App\Models\School;
use App\Models\User;
use App\Services\SchoolAuthService;
use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔐 Testing School Authentication System\n";
echo "======================================\n\n";

// Get all schools
$schools = School::all();
echo "📋 Available Schools:\n";
foreach ($schools as $school) {
    echo "  - {$school->name} ({$school->code}) - Status: {$school->status}\n";
    echo "    Password Hash: " . substr($school->password, 0, 20) . "...\n";
    
    // Check if admin user exists
    $adminUser = User::where('school_id', $school->id)
                    ->whereHas('roles', function($query) {
                        $query->where('name', 'admin');
                    })
                    ->first();
    
    if ($adminUser) {
        echo "    ✅ Admin user exists: {$adminUser->name} ({$adminUser->email})\n";
    } else {
        echo "    ❌ No admin user found!\n";
    }
    echo "\n";
}

// Test authentication for each school
$authService = app(SchoolAuthService::class);

foreach ($schools as $school) {
    echo "🔐 Testing authentication for {$school->name} ({$school->code})...\n";
    
    // Test with the school's actual password (we'll try common passwords)
    $testPasswords = ['password123', 'admin123', $school->code . '123'];
    
    foreach ($testPasswords as $password) {
        echo "  🔑 Trying password: {$password}\n";
        
        $result = $authService->authenticateSchool($school->code, $password);
        
        if ($result['success']) {
            echo "    ✅ Authentication SUCCESS!\n";
            echo "    👤 User: {$result['user']->name}\n";
            echo "    🏫 School: {$result['school']->name}\n";
            echo "    🔗 Redirect: {$result['redirect']}\n";
            break;
        } else {
            echo "    ❌ Authentication FAILED: {$result['message']}\n";
        }
    }
    echo "\n";
}

// Test creating a new school with proper authentication
echo "🏗️ Testing new school creation with authentication...\n";

try {
    $schoolSetupService = app(\App\Services\SchoolSetupService::class);
    
    $testSchoolData = [
        'name' => 'Test Auth School',
        'code' => 'TESTAUTH',
        'password' => 'testauth123',
        'email' => 'admin@testauth.school',
        'admin_name' => 'Test Admin',
        'admin_email' => 'admin@testauth.school',
        'admin_password' => 'admin123'
    ];
    
    // Check if school already exists
    $existingSchool = School::where('code', 'TESTAUTH')->first();
    if ($existingSchool) {
        echo "  ℹ️  School TESTAUTH already exists, using existing school\n";
        $school = $existingSchool;
    } else {
        $result = $schoolSetupService->createSchool($testSchoolData);
        $school = $result['school'];
        echo "  ✅ Created new school: {$school->name}\n";
        echo "  📧 Admin email: {$result['admin_email']}\n";
        echo "  🔑 Admin password: {$result['admin_password']}\n";
    }
    
    // Test authentication with the new school
    echo "  🔐 Testing authentication with new school...\n";
    
    $authResult = $authService->authenticateSchool('TESTAUTH', 'testauth123');
    
    if ($authResult['success']) {
        echo "    ✅ New school authentication SUCCESS!\n";
        echo "    👤 User: {$authResult['user']->name}\n";
        echo "    🔗 Redirect: {$authResult['redirect']}\n";
    } else {
        echo "    ❌ New school authentication FAILED: {$authResult['message']}\n";
        
        // Debug: Check what's wrong
        $debugSchool = School::findByCode('TESTAUTH');
        if ($debugSchool) {
            echo "    🔍 Debug - School found: {$debugSchool->name}\n";
            echo "    🔍 Debug - School status: {$debugSchool->status}\n";
            echo "    🔍 Debug - Password check: " . (Hash::check('testauth123', $debugSchool->password) ? 'PASS' : 'FAIL') . "\n";
            
            $debugAdmin = User::where('school_id', $debugSchool->id)
                             ->whereHas('roles', function($query) {
                                 $query->where('name', 'admin');
                             })
                             ->first();
            
            if ($debugAdmin) {
                echo "    🔍 Debug - Admin user found: {$debugAdmin->name}\n";
            } else {
                echo "    🔍 Debug - No admin user found!\n";
                
                // List all users for this school
                $allUsers = User::where('school_id', $debugSchool->id)->get();
                echo "    🔍 Debug - All users for school:\n";
                foreach ($allUsers as $user) {
                    $roles = $user->roles->pluck('name')->toArray();
                    echo "      - {$user->name} ({$user->email}) - Roles: " . implode(', ', $roles) . "\n";
                }
            }
        } else {
            echo "    🔍 Debug - School not found!\n";
        }
    }
    
} catch (\Exception $e) {
    echo "  ❌ Error creating/testing school: " . $e->getMessage() . "\n";
    echo "  📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n";

// Summary
echo "🎯 Authentication Test Summary\n";
echo "=============================\n";
echo "📊 Total schools tested: " . $schools->count() . "\n";
echo "🔐 Authentication system status: ";

$workingSchools = 0;
foreach ($schools as $school) {
    $adminExists = User::where('school_id', $school->id)
                      ->whereHas('roles', function($query) {
                          $query->where('name', 'admin');
                      })
                      ->exists();
    
    if ($adminExists && $school->isActive()) {
        $workingSchools++;
    }
}

if ($workingSchools === $schools->count()) {
    echo "✅ FULLY OPERATIONAL\n";
} else {
    echo "⚠️  NEEDS ATTENTION ({$workingSchools}/{$schools->count()} schools ready)\n";
}

echo "\n";
echo "💡 Next steps:\n";
echo "   1. Ensure all schools have admin users\n";
echo "   2. Verify school passwords are properly hashed\n";
echo "   3. Test login through web interface\n";
echo "   4. Check dashboard accessibility\n";

<?php

require_once 'vendor/autoload.php';

use App\Models\School;
use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 Force Updating School Passwords\n";
echo "==================================\n\n";

$schoolsToUpdate = [
    'ABC001' => 'password123',
    'PQR002' => 'password123', 
    'BAJRA' => 'password123',
    'WANGBU' => 'password123',
    'TESTAUTH' => 'password123'
];

foreach ($schoolsToUpdate as $code => $password) {
    $school = School::where('code', $code)->first();
    
    if ($school) {
        $school->password = Hash::make($password);
        $school->save();
        
        echo "✅ Updated {$code}: {$school->name}\n";
        
        // Verify the update
        if (Hash::check($password, $school->fresh()->password)) {
            echo "   ✅ Password verification: SUCCESS\n";
        } else {
            echo "   ❌ Password verification: FAILED\n";
        }
    } else {
        echo "❌ School not found: {$code}\n";
    }
    echo "\n";
}

echo "🎯 Password Update Complete\n";
echo "===========================\n";

// Test all schools now
echo "🔐 Testing all school logins...\n\n";

$authService = app(\App\Services\SchoolAuthService::class);

$allCredentials = [
    'DEFAULT001' => 'default123',
    'ABC001' => 'password123',
    'PQR002' => 'password123',
    'BAJRA' => 'password123',
    'TESTFIX' => 'password123',
    'WANGBU' => 'password123',
    'TESTAUTH' => 'password123'
];

$workingCount = 0;
foreach ($allCredentials as $schoolCode => $password) {
    $result = $authService->authenticateSchool($schoolCode, $password);
    
    if ($result['success']) {
        echo "✅ {$schoolCode}: LOGIN SUCCESS\n";
        $workingCount++;
    } else {
        echo "❌ {$schoolCode}: LOGIN FAILED - {$result['message']}\n";
    }
}

echo "\n";
echo "📊 Final Status: {$workingCount}/" . count($allCredentials) . " schools working\n";

if ($workingCount === count($allCredentials)) {
    echo "🎉 ALL SCHOOLS CAN NOW LOGIN!\n";
} else {
    echo "⚠️  Some schools still need attention.\n";
}

echo "\n";
echo "📋 Complete Login Credentials:\n";
echo "==============================\n";

foreach ($allCredentials as $schoolCode => $password) {
    $school = School::findByCode($schoolCode);
    if ($school) {
        echo "🏫 {$school->name}\n";
        echo "   School ID: {$schoolCode}\n";
        echo "   Password: {$password}\n";
        echo "   Login URL: " . url('/login') . "\n";
        echo "\n";
    }
}

echo "🚀 Ready for production use!\n";

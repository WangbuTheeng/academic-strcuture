<?php

require_once 'vendor/autoload.php';

use App\Models\School;
use App\Services\SchoolAuthService;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔐 Final School Authentication Test\n";
echo "==================================\n\n";

$authService = app(SchoolAuthService::class);

// Test each school with the correct passwords
$schoolCredentials = [
    'DEFAULT001' => 'default123',
    'ABC001' => 'password123',
    'PQR002' => 'password123',
    'BAJRA' => 'password123',
    'TESTFIX' => 'password123',
    'WANGBU' => 'password123',
    'TESTAUTH' => 'password123'
];

foreach ($schoolCredentials as $schoolCode => $password) {
    echo "🔐 Testing {$schoolCode} with password '{$password}'...\n";
    
    $result = $authService->authenticateSchool($schoolCode, $password);
    
    if ($result['success']) {
        echo "  ✅ Authentication SUCCESS!\n";
        echo "  👤 User: {$result['user']->name}\n";
        echo "  🏫 School: {$result['school']->name}\n";
        echo "  🔗 Redirect: {$result['redirect']}\n";
    } else {
        echo "  ❌ Authentication FAILED: {$result['message']}\n";
    }
    echo "\n";
}

echo "🎯 Final Authentication Status\n";
echo "==============================\n";

$successCount = 0;
$totalCount = count($schoolCredentials);

foreach ($schoolCredentials as $schoolCode => $password) {
    $result = $authService->authenticateSchool($schoolCode, $password);
    if ($result['success']) {
        $successCount++;
    }
}

echo "✅ Working schools: {$successCount}/{$totalCount}\n";

if ($successCount === $totalCount) {
    echo "🎉 ALL SCHOOLS CAN LOGIN SUCCESSFULLY!\n";
    echo "\n";
    echo "📋 Ready for Production:\n";
    echo "========================\n";
    foreach ($schoolCredentials as $schoolCode => $password) {
        $school = School::findByCode($schoolCode);
        echo "🏫 {$school->name}\n";
        echo "   Login URL: " . url('/login') . "\n";
        echo "   School ID: {$schoolCode}\n";
        echo "   Password: {$password}\n";
        echo "\n";
    }
} else {
    echo "⚠️  Some schools still have authentication issues.\n";
}

echo "🔗 Login URLs:\n";
echo "==============\n";
echo "🏫 School Login: " . url('/login') . "\n";
echo "👑 Super Admin Login: " . url('/super-admin/login') . "\n";
echo "\n";
echo "🎯 School authentication system is ready!\n";

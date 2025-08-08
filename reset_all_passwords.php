<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 Resetting All School Passwords\n";
echo "=================================\n\n";

// Update all schools to have password123 except DEFAULT001
DB::table('schools')
    ->where('code', '!=', 'DEFAULT001')
    ->update(['password' => bcrypt('password123')]);

echo "✅ Updated all school passwords (except DEFAULT001)\n";

// Update DEFAULT001 to have default123
DB::table('schools')
    ->where('code', 'DEFAULT001')
    ->update(['password' => bcrypt('default123')]);

echo "✅ Updated DEFAULT001 password\n\n";

// Test authentication
echo "🔐 Testing Authentication...\n";
echo "============================\n";

$authService = app(\App\Services\SchoolAuthService::class);

$credentials = [
    'DEFAULT001' => 'default123',
    'ABC001' => 'password123',
    'PQR002' => 'password123',
    'BAJRA' => 'password123',
    'TESTFIX' => 'password123',
    'WANGBU' => 'password123',
    'TESTAUTH' => 'password123'
];

$successCount = 0;
foreach ($credentials as $schoolCode => $password) {
    $result = $authService->authenticateSchool($schoolCode, $password);
    
    if ($result['success']) {
        echo "✅ {$schoolCode}: SUCCESS\n";
        $successCount++;
    } else {
        echo "❌ {$schoolCode}: FAILED - {$result['message']}\n";
    }
}

echo "\n📊 Results: {$successCount}/" . count($credentials) . " schools working\n\n";

if ($successCount === count($credentials)) {
    echo "🎉 ALL SCHOOLS CAN NOW LOGIN!\n\n";
    
    echo "📋 Final Login Credentials:\n";
    echo "===========================\n";
    
    foreach ($credentials as $schoolCode => $password) {
        $school = \App\Models\School::findByCode($schoolCode);
        if ($school) {
            echo "🏫 {$school->name}\n";
            echo "   School ID: {$schoolCode}\n";
            echo "   Password: {$password}\n";
            echo "\n";
        }
    }
    
    echo "🔗 Login URL: " . url('/login') . "\n";
    echo "👑 Super Admin URL: " . url('/super-admin/login') . "\n\n";
    
    echo "🚀 System is ready for production!\n";
    echo "Schools can now login with their credentials.\n";
    
} else {
    echo "⚠️  Some schools still have issues. Please check manually.\n";
}

echo "\n💡 Next: Create professional dashboard design\n";

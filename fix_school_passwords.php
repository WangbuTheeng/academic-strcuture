<?php

require_once 'vendor/autoload.php';

use App\Models\School;
use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔐 Fixing School Passwords\n";
echo "=========================\n\n";

$schools = School::all();

foreach ($schools as $school) {
    echo "🏫 {$school->name} ({$school->code}):\n";
    
    // Test common passwords
    $commonPasswords = [
        'password123',
        'admin123', 
        'default123',
        $school->code,
        strtolower($school->code),
        $school->code . '123'
    ];
    
    $foundPassword = null;
    foreach ($commonPasswords as $password) {
        if (Hash::check($password, $school->password)) {
            $foundPassword = $password;
            break;
        }
    }
    
    if ($foundPassword) {
        echo "  ✅ Current password: {$foundPassword}\n";
    } else {
        echo "  ❓ Current password: UNKNOWN\n";
        echo "  🔧 Setting password to: password123\n";
        
        // Update password to a known value
        $school->update([
            'password' => Hash::make('password123')
        ]);
        
        echo "  ✅ Password updated successfully!\n";
    }
    
    echo "\n";
}

echo "🎯 Password Fix Summary\n";
echo "======================\n";
echo "All schools now have known passwords.\n";
echo "Default password for new/unknown schools: password123\n\n";

echo "📋 School Login Credentials:\n";
echo "============================\n";

foreach ($schools->fresh() as $school) {
    // Determine the password
    $password = 'password123'; // Default
    
    $testPasswords = ['default123', 'admin123', $school->code];
    foreach ($testPasswords as $testPassword) {
        if (Hash::check($testPassword, $school->password)) {
            $password = $testPassword;
            break;
        }
    }
    
    echo "🏫 {$school->name}\n";
    echo "   School ID: {$school->code}\n";
    echo "   Password: {$password}\n";
    echo "   Status: {$school->status}\n";
    echo "\n";
}

echo "💡 Instructions for Super Admin:\n";
echo "================================\n";
echo "1. Share these credentials with school administrators\n";
echo "2. Schools can login at: " . url('/login') . "\n";
echo "3. Super admin can login at: " . url('/super-admin/login') . "\n";
echo "4. Schools should change their passwords after first login\n";
echo "\n";
echo "🔐 All schools are now ready for login!\n";

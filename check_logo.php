<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\InstituteSettings;

echo "=== LOGO DEBUG INFO ===\n";

// Check if storage link exists
$storageLinkPath = public_path('storage');
echo "Storage link exists: " . (is_link($storageLinkPath) || is_dir($storageLinkPath) ? 'YES' : 'NO') . "\n";
echo "Storage link path: " . $storageLinkPath . "\n";

// Check institute settings
$settings = InstituteSettings::first();
if ($settings) {
    echo "Institution logo path: " . ($settings->institution_logo ?? 'NULL') . "\n";
    
    if ($settings->institution_logo) {
        $fullPath = storage_path('app/public/' . $settings->institution_logo);
        echo "Full file path: " . $fullPath . "\n";
        echo "File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
        
        $publicPath = public_path('storage/' . $settings->institution_logo);
        echo "Public path: " . $publicPath . "\n";
        echo "Public file accessible: " . (file_exists($publicPath) ? 'YES' : 'NO') . "\n";
        
        echo "Asset URL: " . asset('storage/' . $settings->institution_logo) . "\n";
    }
} else {
    echo "No institute settings found\n";
}

// List files in storage/app/public
echo "\n=== FILES IN STORAGE/APP/PUBLIC ===\n";
$storagePublicPath = storage_path('app/public');
if (is_dir($storagePublicPath)) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($storagePublicPath));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            echo $file->getPathname() . "\n";
        }
    }
} else {
    echo "Storage public directory not found\n";
}

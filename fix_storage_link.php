<?php
// Fix storage link by copying files

$sourceDir = __DIR__ . '/storage/app/public';
$targetDir = __DIR__ . '/public/storage';

echo "Source: $sourceDir\n";
echo "Target: $targetDir\n";

// Create target directory if it doesn't exist
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
    echo "Created target directory\n";
}

// Function to copy directory recursively
function copyDirectory($src, $dst) {
    if (!is_dir($src)) {
        echo "Source directory doesn't exist: $src\n";
        return false;
    }
    
    if (!is_dir($dst)) {
        mkdir($dst, 0755, true);
    }
    
    $dir = opendir($src);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            $srcFile = $src . '/' . $file;
            $dstFile = $dst . '/' . $file;
            
            if (is_dir($srcFile)) {
                copyDirectory($srcFile, $dstFile);
            } else {
                copy($srcFile, $dstFile);
                echo "Copied: $file\n";
            }
        }
    }
    closedir($dir);
    return true;
}

// Copy all files
if (copyDirectory($sourceDir, $targetDir)) {
    echo "Files copied successfully!\n";
} else {
    echo "Failed to copy files\n";
}

// List copied files
echo "\nFiles in public/storage:\n";
if (is_dir($targetDir)) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($targetDir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            echo $file->getPathname() . "\n";
        }
    }
}
?>

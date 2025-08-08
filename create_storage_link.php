<?php
// Create storage link manually

$target = realpath(__DIR__ . '/storage/app/public');
$link = __DIR__ . '/public/storage';

echo "Target: $target\n";
echo "Link: $link\n";

// Remove existing link/directory if it exists
if (file_exists($link)) {
    if (is_link($link)) {
        unlink($link);
        echo "Removed existing symlink\n";
    } elseif (is_dir($link)) {
        rmdir($link);
        echo "Removed existing directory\n";
    }
}

// Create the symlink
if (symlink($target, $link)) {
    echo "Storage link created successfully!\n";
} else {
    echo "Failed to create storage link\n";
    
    // Try copying files instead
    echo "Attempting to copy files...\n";
    
    function copyDirectory($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    copyDirectory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
    
    if (is_dir($target)) {
        copyDirectory($target, $link);
        echo "Files copied successfully!\n";
    }
}

// Check if it worked
if (file_exists($link)) {
    echo "Storage link/directory now exists!\n";
} else {
    echo "Storage link/directory still doesn't exist\n";
}

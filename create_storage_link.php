<?php

// Create storage symbolic link using PHP
$target = __DIR__ . '/storage/app/public';
$link = __DIR__ . '/public/storage';

// Check if target exists
if (!file_exists($target)) {
    echo "Error: Target directory does not exist: $target\n";
    exit(1);
}

// Remove existing link if it exists
if (file_exists($link)) {
    if (is_link($link)) {
        unlink($link);
        echo "Removed existing link.\n";
    } elseif (is_dir($link)) {
        rmdir($link);
        echo "Removed existing directory.\n";
    }
}

// Try to create symbolic link using symlink()
if (PHP_OS_FAMILY === 'Windows') {
    // On Windows, use junction if symlink fails
    $target = str_replace('/', '\\', realpath($target));
    $link = str_replace('/', '\\', $link);
    
    // Try symlink first
    if (@symlink($target, $link)) {
        echo "Success! Symbolic link created using symlink().\n";
        exit(0);
    }
    
    // If symlink fails, try using mklink via exec (requires admin)
    $command = 'mklink /D "' . $link . '" "' . $target . '"';
    exec($command, $output, $returnCode);
    
    if ($returnCode === 0 || file_exists($link)) {
        echo "Success! Symbolic link created using mklink.\n";
        exit(0);
    } else {
        echo "Error: Could not create symbolic link. You may need to run this script as Administrator.\n";
        echo "Command attempted: $command\n";
        exit(1);
    }
} else {
    // On Linux/Unix
    if (@symlink($target, $link)) {
        echo "Success! Symbolic link created.\n";
        exit(0);
    } else {
        echo "Error: Could not create symbolic link.\n";
        exit(1);
    }
}

<?php

/**
 * سكريبت اختبار للتحقق من رابط storage
 */

echo "========================================\n";
echo "  اختبار رابط Storage\n";
echo "========================================\n\n";

$publicStoragePath = __DIR__ . '/public/storage';
$targetStoragePath = __DIR__ . '/storage/app/public';

echo "1. فحص وجود public/storage:\n";
if (file_exists($publicStoragePath)) {
    echo "   ✓ موجود\n";
    
    if (is_link($publicStoragePath)) {
        echo "   ✓ هو رابط رمزي (symbolic link)\n";
        $realPath = readlink($publicStoragePath);
        echo "   → يشير إلى: {$realPath}\n";
    } elseif (is_dir($publicStoragePath)) {
        echo "   ⚠ هو مجلد عادي (ليس رابط رمزي)\n";
    }
} else {
    echo "   ✗ غير موجود\n";
}

echo "\n2. فحص وجود storage/app/public:\n";
if (file_exists($targetStoragePath)) {
    echo "   ✓ موجود\n";
    $fileCount = count(glob($targetStoragePath . '/*'));
    echo "   → يحتوي على {$fileCount} عنصر\n";
} else {
    echo "   ✗ غير موجود\n";
}

echo "\n3. فحص الملفات في storage/app/public:\n";
if (file_exists($targetStoragePath)) {
    $coverImagesPath = $targetStoragePath . '/cover_images';
    $audioBooksPath = $targetStoragePath . '/audio_books';
    
    if (is_dir($coverImagesPath)) {
        $coverCount = count(glob($coverImagesPath . '/*'));
        echo "   ✓ cover_images: {$coverCount} ملف\n";
    } else {
        echo "   ✗ cover_images: غير موجود\n";
    }
    
    if (is_dir($audioBooksPath)) {
        $audioCount = count(glob($audioBooksPath . '/*'));
        echo "   ✓ audio_books: {$audioCount} ملف\n";
    } else {
        echo "   ✗ audio_books: غير موجود\n";
    }
}

echo "\n4. اختبار الوصول عبر public/storage:\n";
$testCoverPath = $publicStoragePath . '/cover_images';
if (file_exists($testCoverPath)) {
    echo "   ✓ يمكن الوصول إلى public/storage/cover_images\n";
    $testFiles = glob($testCoverPath . '/*');
    if (!empty($testFiles)) {
        $testFile = basename($testFiles[0]);
        echo "   → مثال: {$testFile}\n";
        echo "   → URL يجب أن يكون: http://localhost/library/public/storage/cover_images/{$testFile}\n";
    }
} else {
    echo "   ✗ لا يمكن الوصول إلى public/storage/cover_images\n";
    echo "   → الرابط الرمزي قد لا يعمل بشكل صحيح\n";
}

echo "\n========================================\n";
echo "النتيجة:\n";
if (file_exists($publicStoragePath) && is_link($publicStoragePath) && file_exists($targetStoragePath)) {
    echo "✓ الرابط الرمزي يعمل بشكل صحيح!\n";
    echo "  إذا كانت الملفات لا تظهر، المشكلة قد تكون في:\n";
    echo "  1. مسارات خاطئة في قاعدة البيانات\n";
    echo "  2. الكتب تشير لملفات غير موجودة\n";
    echo "  3. مشكلة في إعدادات Apache/Nginx\n";
} else {
    echo "⚠ الرابط الرمزي غير موجود أو لا يعمل!\n";
    echo "  يجب تشغيل: php artisan storage:link\n";
    echo "  أو استخدام: fix_storage.bat\n";
}
echo "========================================\n";


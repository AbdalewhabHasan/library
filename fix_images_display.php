<?php

/**
 * سكريبت لفحص وإصلاح عرض الصور
 * يتحقق من أن جميع الملفات موجودة ويعرض تقريراً مفصلاً
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AudioBook;
use Illuminate\Support\Facades\Storage;

echo "========================================\n";
echo "  فحص وإصلاح عرض الصور\n";
echo "========================================\n\n";

// التحقق من رابط storage
$publicStoragePath = public_path('storage');
$targetStoragePath = storage_path('app/public');

echo "1. فحص رابط storage:\n";
if (file_exists($publicStoragePath)) {
    if (is_link($publicStoragePath)) {
        echo "   ✓ الرابط الرمزي موجود\n";
        $linkTarget = readlink($publicStoragePath);
        echo "   → يشير إلى: {$linkTarget}\n";
    } else {
        echo "   ⚠ public/storage موجود لكنه ليس رابط رمزي!\n";
        echo "   يجب إنشاء رابط رمزي باستخدام: php artisan storage:link\n";
    }
} else {
    echo "   ✗ public/storage غير موجود!\n";
    echo "   يجب إنشاء رابط رمزي باستخدام: php artisan storage:link\n";
}

echo "\n2. فحص الكتب الصوتية:\n";
$books = AudioBook::all();
$totalBooks = $books->count();
$booksWithIssues = [];
$booksOk = 0;

echo "   جاري فحص {$totalBooks} كتاب...\n\n";

foreach ($books as $book) {
    $issues = [];
    
    // فحص صورة الغلاف
    if ($book->cover_image_path) {
        if (!Storage::disk('public')->exists($book->cover_image_path)) {
            $issues[] = 'صورة الغلاف مفقودة';
        } else {
            // التحقق من المسار الكامل
            $fullPath = Storage::disk('public')->path($book->cover_image_path);
            if (!file_exists($fullPath)) {
                $issues[] = 'صورة الغلاف غير موجودة في المسار الفعلي';
            }
        }
    } else {
        $issues[] = 'لا يوجد مسار لصورة الغلاف';
    }
    
    // فحص الملف الصوتي
    if ($book->file_path) {
        if (!Storage::disk('public')->exists($book->file_path)) {
            $issues[] = 'الملف الصوتي مفقود';
        }
    } else {
        $issues[] = 'لا يوجد مسار للملف الصوتي';
    }
    
    if (!empty($issues)) {
        $booksWithIssues[] = [
            'id' => $book->id,
            'title' => $book->title,
            'cover_path' => $book->cover_image_path,
            'file_path' => $book->file_path,
            'issues' => $issues,
        ];
    } else {
        $booksOk++;
    }
}

echo "   ✓ كتب سليمة: {$booksOk}\n";
echo "   ⚠ كتب بها مشاكل: " . count($booksWithIssues) . "\n\n";

if (!empty($booksWithIssues)) {
    echo "3. الكتب التي بها مشاكل:\n\n";
    foreach ($booksWithIssues as $book) {
        echo "   ID: {$book['id']}\n";
        echo "   العنوان: {$book['title']}\n";
        echo "   صورة الغلاف: " . ($book['cover_path'] ?: 'غير موجود') . "\n";
        echo "   الملف الصوتي: " . ($book['file_path'] ?: 'غير موجود') . "\n";
        echo "   المشاكل:\n";
        foreach ($book['issues'] as $issue) {
            echo "     - {$issue}\n";
        }
        echo "\n";
    }
}

echo "4. فحص الملفات الموجودة في storage:\n";
$coverImagesPath = storage_path('app/public/cover_images');
$audioBooksPath = storage_path('app/public/audio_books');

if (is_dir($coverImagesPath)) {
    $coverFiles = glob($coverImagesPath . '/*');
    echo "   ✓ cover_images: " . count($coverFiles) . " ملف\n";
} else {
    echo "   ✗ cover_images: المجلد غير موجود\n";
}

if (is_dir($audioBooksPath)) {
    $audioFiles = glob($audioBooksPath . '/*');
    echo "   ✓ audio_books: " . count($audioFiles) . " ملف\n";
} else {
    echo "   ✗ audio_books: المجلد غير موجود\n";
}

echo "\n5. اختبار URL للصور:\n";
if (!empty($booksWithIssues) && isset($booksWithIssues[0]['cover_path']) && $booksWithIssues[0]['cover_path']) {
    $testPath = $booksWithIssues[0]['cover_path'];
    $testUrl = asset('storage/' . $testPath);
    echo "   مثال URL: {$testUrl}\n";
    
    $fullPath = Storage::disk('public')->path($testPath);
    if (file_exists($fullPath)) {
        echo "   ✓ الملف موجود فعلياً في: {$fullPath}\n";
        echo "   → يجب أن يكون متاحاً عبر: {$testUrl}\n";
    } else {
        echo "   ✗ الملف غير موجود في: {$fullPath}\n";
    }
}

echo "\n========================================\n";
echo "التوصيات:\n";
if (!empty($booksWithIssues)) {
    echo "1. استخدم cleanup_audiobooks.php لحذف الكتب التي لا تملك ملفات\n";
}
if (!file_exists($publicStoragePath) || !is_link($publicStoragePath)) {
    echo "2. أنشئ رابط storage باستخدام: php artisan storage:link\n";
    echo "   أو استخدم: fix_storage.bat\n";
}
echo "========================================\n";


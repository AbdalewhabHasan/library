<?php

/**
 * السكريبت الشامل لحل مشكلة Storage
 * 
 * هذا السكريبت يقوم بـ:
 * 1. التحقق من رابط storage وإنشاؤه إذا لزم الأمر
 * 2. فحص جميع الكتب وإظهار المشاكل
 * 3. حذف الكتب التي لا تملك ملفات (اختياري)
 * 4. إصلاح عرض الصور
 * 
 * الاستخدام:
 *   php FINAL_FIX_STORAGE.php
 *   php FINAL_FIX_STORAGE.php --delete-missing  (لحذف الكتب بدون ملفات)
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AudioBook;
use Illuminate\Support\Facades\Storage;

$deleteMissing = in_array('--delete-missing', $argv);

echo "========================================\n";
echo "  السكريبت الشامل لإصلاح Storage\n";
echo "========================================\n\n";

// الخطوة 1: التحقق من رابط storage
echo "الخطوة 1: فحص رابط storage...\n";
$publicStoragePath = public_path('storage');
$targetStoragePath = storage_path('app/public');

if (!file_exists($publicStoragePath)) {
    echo "   ✗ public/storage غير موجود\n";
    echo "   → محاولة إنشاء الرابط الرمزي...\n";
    
    if (PHP_OS_FAMILY === 'Windows') {
        $target = str_replace('/', '\\', realpath($targetStoragePath));
        $link = str_replace('/', '\\', $publicStoragePath);
        
        // محاولة إنشاء الرابط
        if (@symlink($target, $link)) {
            echo "   ✓ تم إنشاء الرابط الرمزي بنجاح!\n";
        } else {
            // محاولة استخدام mklink
            $command = 'mklink /D "' . $link . '" "' . $target . '"';
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0 || file_exists($publicStoragePath)) {
                echo "   ✓ تم إنشاء الرابط الرمزي بنجاح باستخدام mklink!\n";
            } else {
                echo "   ⚠ فشل إنشاء الرابط الرمزي. يرجى تشغيل: php artisan storage:link\n";
                echo "   أو استخدام: fix_storage.bat\n";
            }
        }
    } else {
        if (@symlink($targetStoragePath, $publicStoragePath)) {
            echo "   ✓ تم إنشاء الرابط الرمزي بنجاح!\n";
        } else {
            echo "   ⚠ فشل إنشاء الرابط الرمزي. يرجى تشغيل: php artisan storage:link\n";
        }
    }
} elseif (is_link($publicStoragePath)) {
    echo "   ✓ الرابط الرمزي موجود ويعمل\n";
} else {
    echo "   ⚠ public/storage موجود لكنه ليس رابط رمزي!\n";
    echo "   → يجب حذفه وإنشاء رابط رمزي بدلاً منه\n";
}

echo "\n";

// الخطوة 2: فحص الكتب الصوتية
echo "الخطوة 2: فحص الكتب الصوتية...\n";
$books = AudioBook::all();
$totalBooks = $books->count();
$booksWithMissingFiles = [];
$booksOk = 0;

foreach ($books as $book) {
    $issues = [];
    
    // فحص صورة الغلاف
    if ($book->cover_image_path) {
        if (!Storage::disk('public')->exists($book->cover_image_path)) {
            $issues[] = 'cover_image';
        }
    } else {
        $issues[] = 'no_cover_path';
    }
    
    // فحص الملف الصوتي
    if ($book->file_path) {
        if (!Storage::disk('public')->exists($book->file_path)) {
            $issues[] = 'audio_file';
        }
    } else {
        $issues[] = 'no_audio_path';
    }
    
    if (!empty($issues)) {
        $booksWithMissingFiles[] = [
            'book' => $book,
            'issues' => $issues,
        ];
    } else {
        $booksOk++;
    }
}

echo "   ✓ كتب سليمة: {$booksOk}\n";
echo "   ⚠ كتب بها ملفات مفقودة: " . count($booksWithMissingFiles) . "\n\n";

if (!empty($booksWithMissingFiles)) {
    echo "الكتب التي بها ملفات مفقودة:\n";
    echo str_repeat("-", 80) . "\n";
    
    foreach ($booksWithMissingFiles as $item) {
        $book = $item['book'];
        echo "ID: {$book->id} | العنوان: {$book->title}\n";
        echo "  المشاكل: " . implode(', ', $item['issues']) . "\n";
        if ($book->cover_image_path) {
            echo "  صورة الغلاف: {$book->cover_image_path}\n";
        }
        if ($book->file_path) {
            echo "  الملف الصوتي: {$book->file_path}\n";
        }
        echo "\n";
    }
    
    if ($deleteMissing) {
        echo "\nجاري حذف الكتب التي لا تملك ملفات...\n";
        $deletedCount = 0;
        
        foreach ($booksWithMissingFiles as $item) {
            $book = $item['book'];
            $bookId = $book->id;
            $bookTitle = $book->title;
            
            // حذف الملفات المتبقية إن وجدت
            if ($book->cover_image_path && Storage::disk('public')->exists($book->cover_image_path)) {
                Storage::disk('public')->delete($book->cover_image_path);
            }
            if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
                Storage::disk('public')->delete($book->file_path);
            }
            if ($book->pdf_path && Storage::disk('public')->exists($book->pdf_path)) {
                Storage::disk('public')->delete($book->pdf_path);
            }
            
            $book->delete();
            $deletedCount++;
            echo "  ✓ تم حذف: {$bookTitle} (ID: {$bookId})\n";
        }
        
        echo "\n✓ تم حذف {$deletedCount} كتاب بنجاح.\n";
    } else {
        echo "\n⚠ لاحظ: لم يتم حذف أي كتب.\n";
        echo "لحذف هذه الكتب، شغّل السكريبت مع: --delete-missing\n";
    }
}

// الخطوة 3: إحصائيات الملفات
echo "\nالخطوة 3: إحصائيات الملفات في storage...\n";
$coverImagesPath = storage_path('app/public/cover_images');
$audioBooksPath = storage_path('app/public/audio_books');
$pdfBooksPath = storage_path('app/public/pdf_books');

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

if (is_dir($pdfBooksPath)) {
    $pdfFiles = glob($pdfBooksPath . '/*');
    echo "   ✓ pdf_books: " . count($pdfFiles) . " ملف\n";
} else {
    echo "   ✗ pdf_books: المجلد غير موجود\n";
}

// الخطوة 4: اختبار URL
echo "\nالخطوة 4: اختبار الوصول للملفات...\n";
if (file_exists($publicStoragePath)) {
    $testCoverPath = $publicStoragePath . '/cover_images';
    if (is_dir($testCoverPath)) {
        $testFiles = glob($testCoverPath . '/*');
        if (!empty($testFiles)) {
            $testFile = basename($testFiles[0]);
            $testUrl = url('storage/cover_images/' . $testFile);
            echo "   ✓ يمكن الوصول للملفات\n";
            echo "   → مثال URL: {$testUrl}\n";
            echo "   → افتح هذا الرابط في المتصفح للتحقق\n";
        } else {
            echo "   ⚠ مجلد cover_images فارغ\n";
        }
    } else {
        echo "   ⚠ لا يمكن الوصول إلى public/storage/cover_images\n";
    }
} else {
    echo "   ✗ public/storage غير موجود - الملفات لن تكون متاحة عبر الويب\n";
}

echo "\n========================================\n";
echo "الخلاصة:\n";
echo "========================================\n";

if (file_exists($publicStoragePath) && (is_link($publicStoragePath) || is_dir($publicStoragePath))) {
    echo "✓ رابط storage: موجود\n";
} else {
    echo "✗ رابط storage: غير موجود - يجب إنشاؤه\n";
}

echo "✓ الكتب السليمة: {$booksOk}\n";
echo "⚠ الكتب التي بها مشاكل: " . count($booksWithMissingFiles) . "\n";

if (!empty($booksWithMissingFiles) && !$deleteMissing) {
    echo "\nلحذف الكتب التي لا تملك ملفات:\n";
    echo "  php FINAL_FIX_STORAGE.php --delete-missing\n";
}

echo "\n========================================\n";


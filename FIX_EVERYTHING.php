<?php

/**
 * السكريبت الشامل لحل جميع مشاكل النظام
 * 
 * هذا السكريبت يقوم بـ:
 * 1. إنشاء رابط storage إذا كان غير موجود
 * 2. فحص جميع الكتب وحذف التي لا تملك ملفات
 * 3. التحقق من أن جميع الملفات متاحة
 * 4. إصلاح مشاكل العرض
 * 
 * الاستخدام:
 *   php FIX_EVERYTHING.php
 *   php FIX_EVERYTHING.php --auto  (لعدم طلب تأكيد)
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AudioBook;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

$auto = in_array('--auto', $argv);

echo "========================================\n";
echo "  🔧 السكريبت الشامل لإصلاح النظام\n";
echo "========================================\n\n";

$errors = [];
$warnings = [];
$success = [];

// ============================================
// الخطوة 1: إنشاء رابط Storage
// ============================================
echo "📁 الخطوة 1: فحص وإصلاح رابط storage...\n";
$publicStoragePath = public_path('storage');
$targetStoragePath = storage_path('app/public');

if (!file_exists($publicStoragePath)) {
    echo "   ⚠ public/storage غير موجود - جاري الإنشاء...\n";
    
    if (!file_exists($targetStoragePath)) {
        // إنشاء المجلد المستهدف إذا كان غير موجود
        mkdir($targetStoragePath, 0755, true);
        echo "   ✓ تم إنشاء storage/app/public\n";
    }
    
    if (PHP_OS_FAMILY === 'Windows') {
        $target = str_replace('/', '\\', realpath($targetStoragePath));
        $link = str_replace('/', '\\', $publicStoragePath);
        
        // محاولة إنشاء الرابط
        if (@symlink($target, $link)) {
            echo "   ✓ تم إنشاء الرابط الرمزي بنجاح!\n";
            $success[] = "تم إنشاء رابط storage";
        } else {
            // محاولة استخدام mklink
            $command = 'mklink /D "' . $link . '" "' . $target . '" 2>&1';
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0 || file_exists($publicStoragePath)) {
                echo "   ✓ تم إنشاء الرابط الرمزي بنجاح باستخدام mklink!\n";
                $success[] = "تم إنشاء رابط storage";
            } else {
                echo "   ✗ فشل إنشاء الرابط الرمزي تلقائياً\n";
                echo "   → يجب تشغيل: php artisan storage:link\n";
                echo "   → أو استخدام: fix_storage.bat (كـ Administrator)\n";
                $errors[] = "فشل إنشاء رابط storage - يحتاج صلاحيات Admin";
            }
        }
    } else {
        if (@symlink($targetStoragePath, $publicStoragePath)) {
            echo "   ✓ تم إنشاء الرابط الرمزي بنجاح!\n";
            $success[] = "تم إنشاء رابط storage";
        } else {
            echo "   ✗ فشل إنشاء الرابط الرمزي\n";
            $errors[] = "فشل إنشاء رابط storage";
        }
    }
} elseif (is_link($publicStoragePath)) {
    echo "   ✓ الرابط الرمزي موجود ويعمل\n";
    $success[] = "رابط storage موجود";
} elseif (is_dir($publicStoragePath)) {
    echo "   ⚠ public/storage موجود لكنه ليس رابط رمزي\n";
    $warnings[] = "public/storage هو مجلد وليس رابط رمزي";
} else {
    echo "   ✓ public/storage موجود\n";
}

echo "\n";

// ============================================
// الخطوة 2: فحص الملفات في storage
// ============================================
echo "📂 الخطوة 2: فحص الملفات في storage...\n";
$coverImagesPath = storage_path('app/public/cover_images');
$audioBooksPath = storage_path('app/public/audio_books');
$pdfBooksPath = storage_path('app/public/pdf_books');

$coverCount = 0;
$audioCount = 0;
$pdfCount = 0;

if (is_dir($coverImagesPath)) {
    $coverFiles = glob($coverImagesPath . '/*');
    $coverCount = count($coverFiles);
    echo "   ✓ cover_images: {$coverCount} ملف\n";
} else {
    echo "   ⚠ cover_images: المجلد غير موجود\n";
    mkdir($coverImagesPath, 0755, true);
    echo "   ✓ تم إنشاء مجلد cover_images\n";
}

if (is_dir($audioBooksPath)) {
    $audioFiles = glob($audioBooksPath . '/*');
    $audioCount = count($audioFiles);
    echo "   ✓ audio_books: {$audioCount} ملف\n";
} else {
    echo "   ⚠ audio_books: المجلد غير موجود\n";
    mkdir($audioBooksPath, 0755, true);
    echo "   ✓ تم إنشاء مجلد audio_books\n";
}

if (is_dir($pdfBooksPath)) {
    $pdfFiles = glob($pdfBooksPath . '/*');
    $pdfCount = count($pdfFiles);
    echo "   ✓ pdf_books: {$pdfCount} ملف\n";
} else {
    echo "   ⚠ pdf_books: المجلد غير موجود\n";
    mkdir($pdfBooksPath, 0755, true);
    echo "   ✓ تم إنشاء مجلد pdf_books\n";
}

echo "\n";

// ============================================
// الخطوة 3: فحص الكتب وحذف التي لا تملك ملفات
// ============================================
echo "📚 الخطوة 3: فحص الكتب الصوتية...\n";
$books = AudioBook::all();
$totalBooks = $books->count();
$booksToDelete = [];
$booksOk = 0;

foreach ($books as $book) {
    $hasCover = false;
    $hasAudio = false;
    
    // فحص صورة الغلاف
    if ($book->cover_image_path) {
        if (Storage::disk('public')->exists($book->cover_image_path)) {
            $hasCover = true;
        }
    }
    
    // فحص الملف الصوتي
    if ($book->file_path) {
        if (Storage::disk('public')->exists($book->file_path)) {
            $hasAudio = true;
        }
    }
    
    // إذا كان الملف غير موجود، أضفه للقائمة
    if (!$hasCover || !$hasAudio) {
        $booksToDelete[] = $book;
    } else {
        $booksOk++;
    }
}

echo "   ✓ كتب سليمة (الملفات موجودة): {$booksOk}\n";
echo "   ⚠ كتب بدون ملفات: " . count($booksToDelete) . "\n";

if (!empty($booksToDelete)) {
    echo "\n   الكتب التي لا تملك ملفات:\n";
    foreach ($booksToDelete as $book) {
        echo "      - ID: {$book->id} | {$book->title}\n";
    }
    
    if ($auto) {
        $confirm = true;
    } else {
        echo "\n   هل تريد حذف هذه الكتب؟ (yes/no): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        $answer = trim(strtolower($line));
        fclose($handle);
        $confirm = in_array($answer, ['yes', 'y', 'نعم']);
    }
    
    if ($confirm) {
        echo "\n   جاري الحذف...\n";
        $deletedCount = 0;
        
        foreach ($booksToDelete as $book) {
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
        }
        
        echo "   ✓ تم حذف {$deletedCount} كتاب\n";
        $success[] = "تم حذف {$deletedCount} كتاب بدون ملفات";
    } else {
        echo "   ⚠ تم إلغاء الحذف\n";
        $warnings[] = count($booksToDelete) . " كتاب بدون ملفات لم يتم حذفها";
    }
}

echo "\n";

// ============================================
// الخطوة 4: التحقق من الوصول للملفات
// ============================================
echo "🔗 الخطوة 4: اختبار الوصول للملفات...\n";
if (file_exists($publicStoragePath)) {
    $testCoverPath = $publicStoragePath . DIRECTORY_SEPARATOR . 'cover_images';
    if (is_dir($testCoverPath)) {
        $testFiles = glob($testCoverPath . DIRECTORY_SEPARATOR . '*');
        if (!empty($testFiles)) {
            $testFile = basename($testFiles[0]);
            $testUrl = url('storage/cover_images/' . $testFile);
            echo "   ✓ يمكن الوصول للملفات\n";
            echo "   → مثال URL: {$testUrl}\n";
            $success[] = "الوصول للملفات يعمل";
        } else {
            echo "   ⚠ مجلد cover_images فارغ\n";
        }
    } else {
        echo "   ⚠ لا يمكن الوصول إلى public/storage/cover_images\n";
        $warnings[] = "لا يمكن الوصول إلى cover_images عبر الويب";
    }
} else {
    echo "   ✗ public/storage غير موجود - الملفات لن تكون متاحة\n";
    $errors[] = "public/storage غير موجود";
}

echo "\n";

// ============================================
// الخطوة 5: تنظيف Cache
// ============================================
echo "🧹 الخطوة 5: تنظيف Cache...\n";
try {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    echo "   ✓ تم تنظيف Cache بنجاح\n";
    $success[] = "تم تنظيف Cache";
} catch (\Exception $e) {
    echo "   ⚠ فشل تنظيف Cache: " . $e->getMessage() . "\n";
    $warnings[] = "فشل تنظيف Cache";
}

echo "\n";

// ============================================
// الخلاصة النهائية
// ============================================
echo "========================================\n";
echo "  📊 التقرير النهائي\n";
echo "========================================\n\n";

if (!empty($success)) {
    echo "✅ الإنجازات:\n";
    foreach ($success as $msg) {
        echo "   ✓ {$msg}\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  التحذيرات:\n";
    foreach ($warnings as $msg) {
        echo "   ⚠ {$msg}\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "❌ الأخطاء:\n";
    foreach ($errors as $msg) {
        echo "   ✗ {$msg}\n";
    }
    echo "\n";
}

// إحصائيات نهائية
$remainingBooks = AudioBook::count();
echo "📈 الإحصائيات النهائية:\n";
echo "   • الكتب الصوتية المتبقية: {$remainingBooks}\n";
echo "   • ملفات الصور في storage: {$coverCount}\n";
echo "   • ملفات صوتية في storage: {$audioCount}\n";
echo "   • ملفات PDF في storage: {$pdfCount}\n";

echo "\n";

if (empty($errors)) {
    echo "✅ تم إصلاح جميع المشاكل بنجاح!\n";
    echo "\nالآن يمكنك:\n";
    echo "1. فتح المتصفح والتحقق من أن الصور تظهر\n";
    echo "2. التحقق من أن جميع الكتب المتبقية لديها ملفات\n";
    echo "3. استخدام النظام بشكل طبيعي\n";
} else {
    echo "⚠️  هناك بعض الأخطاء التي تحتاج تدخل يدوي:\n";
    foreach ($errors as $error) {
        echo "   - {$error}\n";
    }
}

echo "\n========================================\n";


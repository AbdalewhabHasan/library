<?php

/**
 * سكريبت لفحص وحذف الكتب التي لا تملك ملفات في storage
 * يتحقق من وجود ملفات كل كتاب في storage/app/public
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AudioBook;
use Illuminate\Support\Facades\Storage;

echo "========================================\n";
echo "  فحص وحذف الكتب بدون ملفات\n";
echo "========================================\n\n";

$books = AudioBook::all();
$totalBooks = $books->count();
$booksToDelete = [];
$booksOk = 0;

echo "جاري فحص {$totalBooks} كتاب...\n\n";

foreach ($books as $book) {
    $hasCover = false;
    $hasAudio = false;
    $issues = [];
    
    // فحص صورة الغلاف
    if ($book->cover_image_path) {
        if (Storage::disk('public')->exists($book->cover_image_path)) {
            $hasCover = true;
        } else {
            $issues[] = "صورة الغلاف غير موجودة: {$book->cover_image_path}";
        }
    } else {
        $issues[] = "لا يوجد مسار لصورة الغلاف";
    }
    
    // فحص الملف الصوتي
    if ($book->file_path) {
        if (Storage::disk('public')->exists($book->file_path)) {
            $hasAudio = true;
        } else {
            $issues[] = "الملف الصوتي غير موجود: {$book->file_path}";
        }
    } else {
        $issues[] = "لا يوجد مسار للملف الصوتي";
    }
    
    // إذا كان الملف غير موجود، أضفه للقائمة
    if (!$hasCover || !$hasAudio) {
        $booksToDelete[] = [
            'book' => $book,
            'has_cover' => $hasCover,
            'has_audio' => $hasAudio,
            'issues' => $issues,
        ];
    } else {
        $booksOk++;
        echo "✓ {$book->title} (ID: {$book->id}) - الملفات موجودة\n";
    }
}

echo "\n========================================\n";
echo "النتائج:\n";
echo "========================================\n";
echo "✓ كتب سليمة (الملفات موجودة): {$booksOk}\n";
echo "⚠ كتب بدون ملفات (سيتم حذفها): " . count($booksToDelete) . "\n\n";

if (empty($booksToDelete)) {
    echo "جميع الكتب لديها ملفات! لا يوجد شيء للحذف.\n";
    exit(0);
}

// عرض الكتب التي سيتم حذفها
echo "الكتب التي سيتم حذفها:\n";
echo str_repeat("-", 80) . "\n";

foreach ($booksToDelete as $item) {
    $book = $item['book'];
    echo "ID: {$book->id}\n";
    echo "  العنوان: {$book->title}\n";
    echo "  المؤلف: {$book->author}\n";
    echo "  صورة الغلاف: " . ($item['has_cover'] ? '✓ موجودة' : '✗ غير موجودة') . "\n";
    echo "  الملف الصوتي: " . ($item['has_audio'] ? '✓ موجود' : '✗ غير موجود') . "\n";
    if (!empty($item['issues'])) {
        echo "  المشاكل:\n";
        foreach ($item['issues'] as $issue) {
            echo "    - {$issue}\n";
        }
    }
    echo "\n";
}

// طلب التأكيد
echo "========================================\n";
echo "⚠ تحذير: سيتم حذف " . count($booksToDelete) . " كتاب نهائياً!\n";
echo "========================================\n\n";
echo "هل تريد المتابعة؟ اكتب 'نعم احذف' للتأكيد: ";

$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$answer = trim($line);
fclose($handle);

if ($answer !== 'نعم احذف') {
    echo "\nتم إلغاء العملية.\n";
    exit(0);
}

// بدء الحذف
echo "\nجاري الحذف...\n\n";

$deletedCount = 0;
foreach ($booksToDelete as $item) {
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
    
    // حذف سجل الكتاب من قاعدة البيانات
    $book->delete();
    $deletedCount++;
    
    echo "✓ تم حذف: {$bookTitle} (ID: {$bookId})\n";
}

echo "\n========================================\n";
echo "✓ تم بنجاح!\n";
echo "========================================\n";
echo "تم حذف {$deletedCount} كتاب بدون ملفات.\n";
echo "الكتب المتبقية ({$booksOk}) جميعها لديها ملفات موجودة.\n";
echo "========================================\n";


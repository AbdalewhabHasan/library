<?php

/**
 * سكريبت لحذف الكتب الصوتية التي لا تملك ملفات
 * 
 * الاستخدام:
 *   php cleanup_audiobooks.php
 *   php cleanup_audiobooks.php --dry-run  (لعرض الكتب فقط بدون حذف)
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AudioBook;
use Illuminate\Support\Facades\Storage;

$dryRun = in_array('--dry-run', $argv);
$force = in_array('--force', $argv);

echo "========================================\n";
echo "  تنظيف الكتب الصوتية بدون ملفات\n";
echo "========================================\n\n";

$audioBooks = AudioBook::all();
$booksToDelete = [];
$totalChecked = 0;

echo "جاري فحص الكتب الصوتية...\n";

foreach ($audioBooks as $book) {
    $totalChecked++;
    $issues = [];

    // Check cover image
    if ($book->cover_image_path) {
        if (!Storage::disk('public')->exists($book->cover_image_path)) {
            $issues[] = 'صورة الغلاف مفقودة: ' . $book->cover_image_path;
        }
    } else {
        $issues[] = 'لا يوجد مسار لصورة الغلاف';
    }

    // Check audio file
    if ($book->file_path) {
        if (!Storage::disk('public')->exists($book->file_path)) {
            $issues[] = 'الملف الصوتي مفقود: ' . $book->file_path;
        }
    } else {
        $issues[] = 'لا يوجد مسار للملف الصوتي';
    }

    if (!empty($issues)) {
        $booksToDelete[] = [
            'book' => $book,
            'issues' => $issues,
        ];
    }
}

echo "✓ تم فحص {$totalChecked} كتاب صوتي.\n\n";

if (empty($booksToDelete)) {
    echo "✓ جميع الكتب الصوتية لديها ملفات صحيحة.\n";
    exit(0);
}

echo "⚠ تم العثور على " . count($booksToDelete) . " كتاب بدون ملفات:\n\n";

// Display books with issues
foreach ($booksToDelete as $item) {
    $book = $item['book'];
    echo "ID: {$book->id}\n";
    echo "  العنوان: {$book->title}\n";
    echo "  المؤلف: {$book->author}\n";
    echo "  المشاكل:\n";
    foreach ($item['issues'] as $issue) {
        echo "    - {$issue}\n";
    }
    echo "\n";
}

if ($dryRun) {
    echo "\n[DRY RUN] تم عرض الكتب فقط، لم يتم حذف أي شيء.\n";
    echo "لحذف هذه الكتب، شغّل السكريبت بدون --dry-run\n";
    exit(0);
}

echo "\n========================================\n";
if (!$force) {
    echo "هل تريد حذف هذه الكتب؟ (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $answer = trim(strtolower($line));
    fclose($handle);
    
    if ($answer !== 'yes' && $answer !== 'y' && $answer !== 'نعم') {
        echo "تم إلغاء العملية.\n";
        exit(0);
    }
}

echo "جاري الحذف...\n\n";
$deletedCount = 0;

foreach ($booksToDelete as $item) {
    $book = $item['book'];
    $bookId = $book->id;
    $bookTitle = $book->title;
    
    // Delete associated files if they exist
    if ($book->cover_image_path && Storage::disk('public')->exists($book->cover_image_path)) {
        Storage::disk('public')->delete($book->cover_image_path);
    }
    if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
        Storage::disk('public')->delete($book->file_path);
    }
    if ($book->pdf_path && Storage::disk('public')->exists($book->pdf_path)) {
        Storage::disk('public')->delete($book->pdf_path);
    }

    // Delete the book record
    $book->delete();
    $deletedCount++;
    echo "✓ تم حذف: {$bookTitle} (ID: {$bookId})\n";
}

echo "\n========================================\n";
echo "✓ تم حذف {$deletedCount} كتاب بنجاح.\n";
echo "========================================\n";


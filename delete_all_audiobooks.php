<?php

/**
 * سكريبت لحذف جميع الكتب الصوتية من قاعدة البيانات
 * 
 * ⚠ تحذير: هذا سيحذف جميع الكتب الصوتية نهائياً!
 * 
 * الاستخدام:
 *   php delete_all_audiobooks.php
 *   php delete_all_audiobooks.php --confirm  (للتأكيد المباشر)
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AudioBook;
use Illuminate\Support\Facades\Storage;

$confirm = in_array('--confirm', $argv);

echo "========================================\n";
echo "  ⚠ حذف جميع الكتب الصوتية ⚠\n";
echo "========================================\n\n";

$totalBooks = AudioBook::count();

if ($totalBooks === 0) {
    echo "لا توجد كتب صوتية في قاعدة البيانات.\n";
    exit(0);
}

echo "⚠ تحذير: سيتم حذف جميع الكتب الصوتية ({$totalBooks} كتاب)!\n";
echo "هذا الإجراء لا يمكن التراجع عنه!\n\n";

if (!$confirm) {
    echo "هل أنت متأكد تماماً؟ اكتب 'نعم احذف الكل' للتأكيد: ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $answer = trim($line);
    fclose($handle);
    
    if ($answer !== 'نعم احذف الكل') {
        echo "\nتم إلغاء العملية.\n";
        exit(0);
    }
}

echo "\nجاري الحذف...\n\n";

$deletedCount = 0;
$books = AudioBook::all();

foreach ($books as $book) {
    $bookId = $book->id;
    $bookTitle = $book->title;
    
    // حذف الملفات المرتبطة
    if ($book->cover_image_path && Storage::disk('public')->exists($book->cover_image_path)) {
        Storage::disk('public')->delete($book->cover_image_path);
    }
    if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
        Storage::disk('public')->delete($book->file_path);
    }
    if ($book->pdf_path && Storage::disk('public')->exists($book->pdf_path)) {
        Storage::disk('public')->delete($book->pdf_path);
    }
    
    // حذف سجل الكتاب
    $book->delete();
    $deletedCount++;
    
    echo "✓ تم حذف: {$bookTitle} (ID: {$bookId})\n";
}

echo "\n========================================\n";
echo "✓ تم حذف {$deletedCount} كتاب بنجاح.\n";
echo "========================================\n";


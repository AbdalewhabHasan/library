<?php

namespace App\Console\Commands;

use App\Models\AudioBook;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupAudioBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audiobooks:cleanup 
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove audio books that have missing files (cover images or audio files)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('جاري فحص الكتب الصوتية...');
        $this->newLine();

        $audioBooks = AudioBook::all();
        $booksToDelete = [];
        $totalChecked = 0;

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
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'issues' => $issues,
                ];
            }
        }

        $this->info("تم فحص {$totalChecked} كتاب صوتي.");
        $this->newLine();

        if (empty($booksToDelete)) {
            $this->info('✓ جميع الكتب الصوتية لديها ملفات صحيحة.');
            return Command::SUCCESS;
        }

        $this->warn("تم العثور على " . count($booksToDelete) . " كتاب بدون ملفات:");
        $this->newLine();

        // Display books with issues
        $headers = ['ID', 'العنوان', 'المؤلف', 'المشاكل'];
        $rows = [];

        foreach ($booksToDelete as $book) {
            $rows[] = [
                $book['id'],
                $book['title'],
                $book['author'],
                implode(' | ', $book['issues']),
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();

        if ($dryRun) {
            $this->info('--dry-run: تم عرض الكتب فقط، لم يتم حذف أي شيء.');
            return Command::SUCCESS;
        }

        if (!$force) {
            if (!$this->confirm('هل تريد حذف هذه الكتب؟', true)) {
                $this->info('تم إلغاء العملية.');
                return Command::SUCCESS;
            }
        }

        $deletedCount = 0;
        $bar = $this->output->createProgressBar(count($booksToDelete));
        $bar->start();

        foreach ($booksToDelete as $bookData) {
            $book = AudioBook::find($bookData['id']);
            if ($book) {
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
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✓ تم حذف {$deletedCount} كتاب بنجاح.");

        return Command::SUCCESS;
    }
}


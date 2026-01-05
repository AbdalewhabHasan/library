<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\AudioBook;
use App\Models\Category;

return new class extends Migration
{
    /**
     * تشغيل الهجرة لتحديث جدول الكتب وربطه بالفئات.
     */
    public function up(): void
    {
        // الخطوة 1: إضافة حقل category_id الجديد إلى جدول audio_books
        // نجعله قابلاً للـ null مؤقتاً لتسهيل عملية التحديث
        Schema::table('audio_books', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
        });

        // الخطوة 2: تحديث البيانات - ربط الكتب القديمة بالفئات الجديدة
        // أولاً، نحصل على كل الفئات الموجودة مع الـ ID الخاص بها ونضعها في مصفوفة سهلة البحث
        $categoriesMap = Category::pluck('id', 'name')->all();

        // ثانياً، نمر على كل الكتب الصوتية دفعة واحدة لتحديثها
        AudioBook::whereNotNull('category')->where('category', '!=', '')->chunkById(100, function ($audiobooks) use ($categoriesMap) {
            foreach ($audiobooks as $book) {
                // نبحث عن الـ ID المطابق لاسم الفئة القديم
                $categoryId = $categoriesMap[$book->category] ?? null;

                // إذا وجدنا ID مطابق، نقوم بتحديث الكتاب
                if ($categoryId) {
                    $book->category_id = $categoryId;
                    $book->save();
                }
            }
        });

        // الخطوة 3: حذف حقل 'category' النصي القديم
        Schema::table('audio_books', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    /**
     * التراجع عن الهجرة (للسلامة فقط، في حال حدوث خطأ).
     */
    public function down(): void
    {
        Schema::table('audio_books', function (Blueprint $table) {
            // نعيد إضافة الحقل النصي القديم
            $table->string('category')->nullable();
            // نحذف الحقل الجديد والعلاقة
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB; // <-- 1. استيراد DB Facade

return new class extends Migration
{
    /**
     * تشغيل الهجرة لترحيل بيانات الفئات القديمة.
     */
    public function up(): void
    {
        // 2. جلب كل أسماء الفئات القديمة الفريدة وغير الفارغة من جدول الكتب
        $oldCategories = DB::table('audio_books')
                            ->whereNotNull('category')
                            ->where('category', '!=', '')
                            ->distinct()
                            ->pluck('category');

        // 3. تجهيز البيانات لإدراجها في الجدول الجديد
        $newCategories = $oldCategories->map(function ($categoryName) {
            return [
                'name' => $categoryName,
                'created_at' => now(), // تحديد تاريخ الإنشاء الحالي
                'updated_at' => now(), // تحديد تاريخ التحديث الحالي
            ];
        })->all();

        // 4. إدراج الفئات الجديدة في جدول 'categories' دفعة واحدة
        // نستخدم 'insertOrIgnore' لتجنب أي أخطاء في حال كانت الفئة موجودة مسبقاً
        if (!empty($newCategories)) {
            DB::table('categories')->insertOrIgnore($newCategories);
        }
    }

    /**
     * التراجع عن الهجرة (لا نفعل شيئاً هنا لأنها عملية بيانات).
     */
    public function down(): void
    {
        // لا حاجة لكتابة كود هنا، لأن التراجع عن هذه العملية قد يكون غير مرغوب فيه
        // إذا أردنا حذف هذه الفئات، يجب أن يتم ذلك يدوياً من لوحة التحكم
    }
};

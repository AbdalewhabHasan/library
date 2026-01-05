<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('playlists', function (Blueprint $table) {
            // إضافة عمود الوصف
            $table->text('description')->nullable()->after('name'); // 'nullable' يعني ممكن يكون فاضي، 'after('name')' عشان يجي بعد عمود الاسم
            // إضافة عمود للخصوصية
            $table->boolean('is_public')->default(true)->after('description'); // 'boolean' يعني يا 0 يا 1 (صحيح/خطأ)، 'default(true)' يعني القيمة الافتراضية عامة
            // إضافة عمود لمسار صورة الغلاف
            $table->string('cover_image')->nullable()->after('is_public'); // 'string' للنصوص القصيرة (مسار الصورة)، 'nullable' يعني ممكن يكون فاضي
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('playlists', function (Blueprint $table) {
            // لما بدك تتراجع عن الـ Migration، لازم تحذف الأعمدة بنفس الترتيب العكسي للإضافة
            $table->dropColumn('cover_image');
            $table->dropColumn('is_public');
            $table->dropColumn('description');
        });
    }
};
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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الخطة (مثال: "الخطة الذهبية الشهرية")
            $table->string('slug')->unique(); // معرف فريد للاستخدام في الروابط (مثال: "gold-monthly")
            $table->decimal('price', 8, 2); // السعر (مثال: 9.99)
            $table->integer('duration_in_days'); // مدة الخطة بالأيام (مثال: 30 يومًا للخطة الشهرية)
            $table->text('description')->nullable(); // وصف بسيط لميزات الخطة
            $table->boolean('is_active')->default(true); // لتحديد ما إذا كانت الخطة متاحة للمستخدمين أم لا
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};

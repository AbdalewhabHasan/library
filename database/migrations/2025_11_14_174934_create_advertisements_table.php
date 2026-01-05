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
    Schema::create('advertisements', function (Blueprint $table) {
        $table->id();
        $table->string('image_path'); // لتخزين مسار صورة الإعلان
        $table->string('link_url');   // لتخزين الرابط الذي سينتقل إليه المستخدم عند الضغط على الإعلان
        $table->boolean('is_active')->default(true); // لتحديد ما إذا كان الإعلان فعالاً أم لا
        $table->timestamps(); // لإضافة حقلي created_at و updated_at تلقائياً
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};

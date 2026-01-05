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
    Schema::table('achievements', function (Blueprint $table) {
        // إضافة العمود المفقود
        // يمكن أن يكون null لأن ليس كل الإنجازات تحتاجه (مثل إنجاز عدد الكتب العام)
        $table->unsignedBigInteger('related_id')->nullable()->after('value');
    });
}

    /**
     * Reverse the migrations.
     */
   public function down(): void
{
    Schema::table('achievements', function (Blueprint $table) {
        $table->dropColumn('related_id');
    });
}

};

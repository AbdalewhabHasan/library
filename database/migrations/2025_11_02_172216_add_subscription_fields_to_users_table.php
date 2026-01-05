<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ▼▼▼ هذا هو الكود الجديد ▼▼▼
            // يسمح بقيم فارغة (nullable) لأن ليس كل المستخدمين مشتركين
            // onDelete('set null') تعني أنه إذا تم حذف خطة من النظام، يصبح اشتراك المستخدم فارغاً ولا يتم حذف المستخدم
            $table->foreignId('plan_id')->nullable()->constrained()->onDelete('set null');
            
            // تاريخ انتهاء الاشتراك، يمكن أن يكون فارغاً أيضاً
            $table->timestamp('subscription_ends_at')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};

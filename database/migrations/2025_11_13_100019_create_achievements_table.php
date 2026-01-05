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
    Schema::create('achievements', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // اسم الإنجاز، مثال: "قارئ نهم"
        $table->text('description'); // وصف الإنجاز
        $table->string('icon'); // اسم أيقونة FontAwesome، مثال: "fa-star"
        $table->string('type'); // نوع الإنجاز (مثال: count, category)
        $table->integer('value'); // القيمة المطلوبة لتحقيق الإنجاز (مثال: 10 كتب)
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};

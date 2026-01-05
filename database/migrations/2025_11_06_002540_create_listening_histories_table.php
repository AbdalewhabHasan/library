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
        // ▼▼▼ هذا هو الكود الصحيح 100% ▼▼▼
        Schema::create('listening_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listener_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('audio_book_id')->constrained('audio_books')->onDelete('cascade');
            $table->timestamp('listened_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listening_histories');
    }
};

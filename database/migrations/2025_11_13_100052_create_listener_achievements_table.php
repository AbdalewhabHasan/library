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
    Schema::create('listener_achievements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('listener_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('achievement_id')->constrained('achievements')->onDelete('cascade');
        $table->timestamp('unlocked_at');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listener_achievements');
    }
};

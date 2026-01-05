<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audio_books', function (Blueprint $table) {
            if (!Schema::hasColumn('audio_books', 'status')) {
                $table->string('status')->default('pending')->after('language');
            }
            if (!Schema::hasColumn('audio_books', 'cover_image_path')) {
                $table->string('cover_image_path')->nullable()->after('cover_image');
            }
        });

        // Copy existing cover_image values to cover_image_path if any
        if (Schema::hasColumn('audio_books', 'cover_image')) {
            DB::table('audio_books')
                ->whereNotNull('cover_image')
                ->where('cover_image', '!=', '')
                ->update(['cover_image_path' => DB::raw('cover_image')]);
        }
    }

    public function down(): void
    {
        Schema::table('audio_books', function (Blueprint $table) {
            if (Schema::hasColumn('audio_books', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('audio_books', 'cover_image_path')) {
                $table->dropColumn('cover_image_path');
            }
        });
    }
};

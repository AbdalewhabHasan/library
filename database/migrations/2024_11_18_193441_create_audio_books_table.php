<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAudioBooksTable extends Migration
{
    public function up()
    {
        Schema::create('audio_books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('narrator')->nullable();
            $table->string('file_path');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->integer('duration');
            $table->string('category')->nullable();
            $table->string('language')->nullable();
            $table->foreignId('publisher_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('audio_books');
    }
}


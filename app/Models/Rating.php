<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;


        protected $fillable = ['listener_id', 'audio_book_id', 'rating'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function audioBook()
    {
        return $this->belongsTo(AudioBook::class);
    }
}

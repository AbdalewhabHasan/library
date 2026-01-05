<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    use HasFactory;

    protected $fillable = ['listener_id', 'audio_book_id', 'time'];

    // Ensure you have the proper relationship methods defined
    public function audioBook()
    {
        return $this->belongsTo(AudioBook::class);
    }

    public function listener()
    {
        return $this->belongsTo(User::class, 'listener_id');
    }

}

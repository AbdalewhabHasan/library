<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    use HasFactory;

    protected $fillable = ['listener_id', 'audio_book_id'];

    public function audioBook()
    {
        return $this->belongsTo(AudioBook::class);
    }

    // Define the relationship to the User (listener)
    public function user()
    {
        return $this->belongsTo(User::class, 'listener_id');
    }
}

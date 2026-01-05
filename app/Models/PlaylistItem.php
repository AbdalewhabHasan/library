<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaylistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'playlist_id', 'audio_book_id',
    ];

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    public function audioBook()
    {
        return $this->belongsTo(AudioBook::class);
    }
}

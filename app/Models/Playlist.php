<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'listener_id',
        'description',   // هذا لازم يكون موجود!
        'is_public',     // وهذا كمان لازم يكون موجود!
        'cover_image',   // وهذا كمان لازم يكون موجود!
    ];

    public function items()
    {
        return $this->hasMany(PlaylistItem::class);
    }

    public function listener()
    {
        return $this->belongsTo(User::class, 'listener_id');
    }

    public function audioBooks()
    {
        return $this->belongsToMany(AudioBook::class, 'playlist_items', 'playlist_id', 'audio_book_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListeningHistory extends Model
{
    use HasFactory;

    // ▼▼▼ السطر السحري: إجبار المودل على استخدام اسم الجدول الصحيح ▼▼▼
    protected $table = 'listening_histories';

    // ▼▼▼ الأعمدة المسموح بتعبئتها ▼▼▼
    protected $fillable = [
        'listener_id',
        'audio_book_id',
        'listened_at',
    ];
}

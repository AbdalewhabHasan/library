<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'listener_id', 'publisher_id',
    ];

    // Listener who subscribed
    public function listener()
    {
        return $this->belongsTo(User::class, 'listener_id');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'publisher_id');
    }
}

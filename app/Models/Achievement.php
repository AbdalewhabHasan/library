<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'type',
        'value',
        'related_id',
    ];

    public function listeners()
    {
        return $this->belongsToMany(User::class, 'listener_achievements', 'achievement_id', 'listener_id')
                    ->withTimestamps()->withPivot('unlocked_at');
    }
}

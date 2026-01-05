<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح بتعبئتها بشكل جماعي.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image_path',
        'link_url',
        'is_active',
    ];
}

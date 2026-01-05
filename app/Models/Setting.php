<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * ▼▼▼ هذا هو الجزء الأهم ▼▼▼
     * الحقول التي نسمح بتعبئتها بشكل جماعي (Mass Assignment).
     * هذا ضروري لكي تعمل دالة updateOrCreate() التي استخدمناها في المتحكم.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
    ];
}

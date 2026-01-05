<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    /**
     * السماح بتعبئة كل الحقول.
     */
    protected $guarded = [];

    /**
     * ▼▼▼ هذا هو السطر الحاسم الذي يحل المشكلة الأخيرة ▼▼▼
     *
     * هذا السطر يخبر Laravel بأن عمود 'data' هو مصفوفة،
     * فيقوم تلقائياً بتحويلها إلى نص JSON عند الحفظ في قاعدة البيانات.
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * العلاقة الديناميكية التي تسمح للإشعار بالارتباط بأي موديل.
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    // =================================================================
    // الكود الذي يحل مشكلة الـ ID (يبقى كما هو)
    // =================================================================

    protected static function booted()
    {
        static::creating(function ($notification) {
            if (empty($notification->id)) {
                $notification->id = (string) Str::uuid();
            }
        });
    }

    public function getKeyName()
    {
        return 'id';
    }

    public function getKeyType()
    {
        return 'string';
    }

    public function getIncrementing()
    {
        return false;
    }
}
